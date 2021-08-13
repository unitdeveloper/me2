<?php

namespace admin\modules\Itemset\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

use common\models\Itemset;
use common\models\Items;
use admin\modules\Itemset\models\ItemsetSearch;
use admin\modules\Itemset\models\ItemsetkitSearch;
use admin\modules\Manufacturing\models\KitbomHeader;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\ItemsHasProperty;
use common\models\TmpMenuGroup;
use admin\modules\Itemset\models\FunctionItemset;
use common\models\WarehouseMoving;
//use admin\modules\Manufacturing\models\KitbomHeader;

class BomsetController extends Controller
{
    public function actionDelete()
    {
        return $this->render('delete');
    }

    public function actionIndex()
    {
        $searchModel = new ItemsetkitSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['kitbom_header.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider->query->orderBy(['kitbom_header.name'=> SORT_ASC]); 
        $dataProvider->pagination->pageSize=100;


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionCreate()
    {
        $session = Yii::$app->session;

        $model = new \admin\modules\Manufacturing\models\KitbomHeader();

        if ($model->load(Yii::$app->request->post())) 
        {


            //if(count($model->item_set) > 1) { 
                //$model->item_set    = implode(',',$model->item_set);   
            $model->item_set    = implode(',',$_POST['KitbomHeader']['item_set']); 
            //}

             
            $model->user_id     = $session->get('Rules')['user_id'];
            $model->comp_id     = $session->get('Rules')['comp_id'];
            $model->save(false);

            return $this->redirect(['/Manufacturing/bom/view', 'id' => $model->id]);
        } else {
            return $this->render('create', [        
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        // fixed
        $query = \admin\modules\Manufacturing\models\KitbomHeader::find();
        $query->andWhere(new Expression('FIND_IN_SET(:item_set, item_set)'));
        $query->addParams([':item_set' => $id]);

        $query->orderBy(['Multiple' => SORT_ASC,'code'=> SORT_ASC]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

       

        $dataProvider->query->andFilterWhere(['multiple' => '0']);
        
         // Multiple
        $query2 = \admin\modules\Manufacturing\models\KitbomHeader::find();
        $query2->andWhere(new Expression('FIND_IN_SET(:item_set, item_set)'));
        $query2->addParams([':item_set' => $id]);

        $query2->orderBy(['Multiple' => SORT_ASC,'code'=> SORT_ASC]);

         $dataProvider2 = new ActiveDataProvider([
            'query' => $query2,
        ]);

         $dataProvider2->query->andFilterWhere(['multiple' => '1']); 
        //$dataProvider->query->orderBy(['Multiple' => SORT_ASC,'code'=> SORT_ASC]);

        //var_dump($this->findKitBomHeader($_GET['id']));
        //exit();
        if(Yii::$app->request->isAjax) {
            return $this->renderAjax('update', [
                'model' => $this->findModel($id),
                'dataProvider' => $dataProvider,
                'dataProvider2' => $dataProvider2,
            ]);
        }else 
        {
            return $this->render('update', [
                'model' => $this->findModel($id),
                'dataProvider' => $dataProvider,
                'dataProvider2' => $dataProvider2,
            ]);
        }
        //return $this->render('update');

    }

    public function actionView($id)
    {

        if(Yii::$app->request->isAjax) {
             
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
            ]);
        }else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }

    }

    protected function findModel($id)
    {
        if (($model = Itemset::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findKitBomHeader($id)
    {
        $query = KitbomHeader::find();
        $query->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(new Expression('FIND_IN_SET(:item_set, item_set)'))
                ->addParams([':item_set' => $id]);


        // $query->rightJoin('kitbom_header', '(itemset.id IN (kitbom_header.item_set))');

        return $query;        
        // if (($model = KitbomHeader::findOne($id)) !== null) {

        //     return $model;
        // } else {
        //     throw new NotFoundHttpException('The requested page does not exist.');
        // }
    }


    public function actionViewitem()
    {
        $session = Yii::$app->session;
        $session->set('ew-set', (object)['pval' => NULL,'pid' => NULL]);  // Clear pval

        #1 Set นี้มี Item อะไรบ้าง
        #2 ในแต่ละ Item มี Property อะไรบ้าง
            #2.1 Property มีค่าเท่าไหร่บ้าง   

        $InSet = Items::find()->where(['itemset' => $_POST['param']['itemset']])->all();

        /*
        * Clear Temp
        */
        $Temp = \common\models\TmpMenuGroup::deleteAll(['session_id' => Yii::$app->session->getId()]);


        // Create set (Insert to table).
        foreach ($InSet as $items) {
            FunctionItemset::ItemSet($items->No);
        }

        $dataProvider = ItemsHasProperty::find()
        ->where(['property_id' => $_POST['param']['itemset']])
        ->where(['Items_No' => $_POST['param']['itemno']])
        ->orderBy(['priority' => SORT_ASC])
        ->all();

        return $this->renderpartial('modal_pickitem',[
            'dataProvider' => $dataProvider,
            'orderno' => $_POST['param']['docno'],
            ]);

    }

    public function actionItemGetdata()
    {
        //return $_POST['param']['item'];
        $model = Items::find()->where(['No'=>$_POST['param']['item']])->one();

        $Query = WarehouseMoving::find()->where(['ItemNo' => $model->No]);
        $RealInven = $Query->sum('Quantity');


        $Remaining = $model->Inventory + $RealInven;

        // Get Last Price
        if($model->CostGP<=0){
            $model->CostGP = FunctionItemset::lastprice($model->No)->unit_price *1;
        }
        // ถ้าไม่มีราคาล่าสุด ให้ไปใช้ราคาขาย
        if($model->CostGP<=0){
            $model->CostGP = $model->StandardCost;
        }

        $data = [
                    'id'        => $model->id,
                    'item'      => $model->No,
                    'ig'        => $model->ItemGroup,
                    'Photo'     => $model->Photo,
                    'std'       => $model->CostGP,
                    'desc'      => Yii::t('common','Product Name (en)').' : '.$model->Description.'<br><br> '.Yii::t('common','Product Name (th)').' : '.$model->description_th.'',
                    'desc_th'   => $model->description_th,
                    'desc_en'   => $model->Description,
                    'code'      => $model->master_code,
                    'inven'     => $Remaining,                     
                ];
        return json_encode($data);
    }


    public function actionUpdateStatus($id){
        $model = Itemset::findOne($id);
        $status = 200;
        $message = '';

        if($model != null){
            $model->status = Yii::$app->request->post('param')['val'] == 'true' 
                                ? 1
                                : 0;
            if(!$model->save()){
                $status = 500;
                $message= json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }

        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
    }

}
