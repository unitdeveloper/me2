<?php

namespace admin\modules\Manufacturing\controllers;

use Yii;
use common\models\BomHeader;
use yii\data\ActiveDataProvider;
use admin\modules\Manufacturing\models\ProdBomSearch;
use admin\modules\Manufacturing\models\ProdBomLineSearch;

use common\models\BomLine;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\Items;

/**
 * ProdbomController implements the CRUD actions for BomHeader model.
 */
class ProdbomController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-bom-line' => ['POST'],
                    'view-ajax' => ['GET']
                ],
            ],
        ];
    }

    /**
     * Lists all BomHeader models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProdBomSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BomHeader model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        


        $searchModel = new ProdBomLineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->query->andWhere(['bom_no' => $id]);
         

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
         
    }

    public function actionViewAjax($id)
    {
        


        $searchModel = new ProdBomLineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->query->andWhere(['bom_no' => $id]);
         

        return $this->renderpartial('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
         
    }

    public function actionViewOnly($id)
    {
        $searchModel = new ProdBomLineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->query->andWhere(['bom_no' => $id]);
         
        return $this->render('view-only', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
         
    }

    /**
     * Creates a new BomHeader model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model                  = new BomHeader();
        if ($model->load(Yii::$app->request->post())) {

           
            $transaction = Yii::$app->db->beginTransaction();

            try {

                $Items                  = Items::findOne($model->item);
                $exists                 = BomHeader::find()->where(['code' => $Items->master_code])->exists();

                if($exists){
                    Yii::$app->session->setFlash('warning', Yii::t('common','The name "{name}" already exists.', ['name' => $Items->master_code]));   
                }else{
                    
                    $model->priority        = 1;
                    $model->user_id         = Yii::$app->user->identity->id;
                    $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
                    $model->create_date     = date('Y-m-d H:i:s');           
                    $model->item            = $Items->id; 
                    $model->name            = $Items->description_th;
                    $model->description     = $Items->Description;
                    $model->code            = $Items->master_code;

                    if($model->save()){
                        
                        
                        $Items->ProductionBom   = $model->id;
                        if($Items->save()){
                            $transaction->commit();
                            
                            return $this->redirect(['view', 'id' => $model->id]);

                        }else{
                            $transaction->rollBack();

                            Yii::$app->session->setFlash('warning', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));   

                        
                            // return $this->asJson([
                            //     'status' => 500,
                            //     'message' => json_encode($Items->getErrors(),JSON_UNESCAPED_UNICODE)
                            // ]);
                        }
                    }else{
                        $transaction->rollBack();

                        Yii::$app->session->setFlash('warning', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)); 

                    
                        // return $this->asJson([
                        //     'status' => 500,
                        //     'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
                        // ]);
                    }

                    
                }
                
                
            } catch (\Exception $e) {
                $transaction->rollBack();

                Yii::$app->session->setFlash('warning', Yii::t('common','{:e}',[':e' => $e])); 
                // return json_encode([
                //     'status' => 500,
                //     'message' => Yii::t('common','Error'),
                //     'suggestion' => Yii::t('common','{:e}',[':e' => $e])
                // ]); 
                
            }
            
            
            return $this->render('create', [
                'model' => $model,
            ]);
            
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionCreateBomLine()
    {

        
        $line               = $_POST['param'];
        
        
        
        // ห้ามใส่ item ตัวเอง
        $BomExists = BomHeader::find()->where([
                        'item' => $line['id'],
                        'id' => $line['bomid'],
                        'comp_id' => Yii::$app->session->get('Rules')['comp_id']
                    ])->exists();

        if($BomExists){

            echo "<script>swal('".Yii::t('common','Please select another product.')."', '".Yii::t('common','That is not like the main product')."', 'warning');</script>";
            
            // return $this->renderAjax('__bom_line', [
            //     'model' => $this->findModel($line['bomid']),
            //     'searchModel' => $searchModel,
            //     'dataProvider' => $dataProvider
            // ]);
            exit;
            
        }else{

            $transaction = Yii::$app->db->beginTransaction();
            try {
                
                $model = new BomLine();
        
                $model->bom_no      = $line['bomid'];
                $model->item        = $line['id'];
                $model->item_no     = $line['itemno'];
                $model->name        = $line['desc'];
                $model->description = $line['desc'];
                $model->quantity    = $line['amount'];
                $model->color_style = '#000';
                $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
                $model->user_id     = Yii::$app->user->identity->id;
                $model->base_unit   = $line['amount'];
                $model->measure     = $line['measure'];

                $model->save();

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('warning', Yii::t('common','{:e}',[':e' => $e]));       
            }
            

            // $searchModel = new ProdBomLineSearch();
            // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            // $dataProvider->query->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            // $dataProvider->query->andWhere(['bom_no' => $line['bomid']]);
            $query   = BomLine::find()
                        ->where(['bom_no' => $line['bomid']])
                        ->andwhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false
            ]);
            
            return $this->renderAjax('__bom_line', [
                //'model' => $this->findModel($line['bomid']),
                //'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
                
        }
    }

    /**
     * Updates an existing BomHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing BomHeader model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
             // ไม่ให้ลบ ถ้าผูกกับสินค้าอยู่ (เพื่อป้องกันไม่ให้คำนวนบิลผิดพลาด เพราะบิลคำนวนจาก BOM)
            $items = Items::find()->where(['ProductionBom' => $id])->one();
            if($items){
                Yii::$app->session->setFlash('warning', Yii::t('common','You must delete item before delete bom.'));   
                return $this->redirect(['view', 'id' => $id]);
                exit;
            }else{
                // clear bom line
                BomLine::deleteAll(['bom_no' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    
                // Cancel 21/10/2019
                // clear bom in item relations
                // $items = Items::find()->where(['ProductionBom' => $id])->all();
                // foreach ($items as $key => $value) {
                //     $item = Items::findOne($value->id);
                //     $item->ProductionBom = NULL;
                //     $item->save();
                // }
                $this->findModel($id)->delete();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('warning', Yii::t('common','{:e}',[':e' => $e]));       
        }
        return $this->redirect(['index']);
    }

    public function actionDeleteBomLine()
    {
        // var_dump($_POST['param']);
        // exit(); 
        $BomLine = BomLine::findOne($_POST['param']['lineno']);
        $itemCode= $BomLine->header->code;
        $itemName= $BomLine->header->name;

        if($BomLine->delete()){
            try{                     
                // Line Notify
                $bot =  \common\models\LineBot::findOne(5);
                $msg = "\r\n\r\nDelete Bom Line \r\n";
                $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
                
                $msg.= $itemCode."\r\n";	
                $msg.= $itemName."\r\n\r\n";

                $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
    
                $bot->notify_message($msg);					
    
            } catch (\Exception $e) {					 
                Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
            }
        }

        $searchModel = new ProdBomLineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->query->andWhere(['bom_no' => $_POST['param']['bomid']]);         

        return $this->renderAjax('__bom_line', [
            'model' => $this->findModel($_POST['param']['bomid']),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }


    /**
     * Finds the BomHeader model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BomHeader the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelLine($id)
    {
        if (($model = BomLine::find()->where(['bom_no' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    protected function findModel($id)
    {
        if (($model = BomHeader::find()->where(['id' => $id, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->one()) !== null) {
            return $model;
        } else {            
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
