<?php

namespace admin\modules\Manufacturing\controllers;

use Yii;
use admin\modules\Manufacturing\models\KitbomLine;
use admin\modules\Manufacturing\models\KitbomHeader;
use admin\modules\Manufacturing\models\BomSearch;
use admin\modules\Manufacturing\models\KitbomSearch;


use admin\modules\Manufacturing\models\ProdBomSearch;
use common\models\BomHeader;
use common\models\BomLine;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


use yii\web\UploadedFile;

/**
 * BomController implements the CRUD actions for KitbomLine model.
 */
class BomController extends Controller
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
                ],
            ],
        ];
    }

    /**
     * Lists all KitbomLine models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BomSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy(['Multiple' => SORT_ASC,'code'=> SORT_ASC]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionHeader()
    {
        $searchModel = new ProdBomSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
         

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KitbomLine model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $searchModel = new KitbomSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->query->andWhere(['kitbom_no'=> $id]);


        return $this->render('view', [
            'model' => $this->findModelHead($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new KitbomLine model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($multiple = null)
    {
        $session = Yii::$app->session;

        $model = new KitbomHeader();

        if ($model->load(Yii::$app->request->post())) {

            $model->photo = $model->upload($model,'photo');
            
            if($model->multiple == 1){
                if(is_array($model->item_set)) { 
                    $model->item_set    = implode(',',$model->item_set);   
                }
                
            }else{

                $model->item_set = strpos(",", $model->item_set) !== false 
                                    ? implode(',',$model->item_set)
                                    : $model->item_set;
              
            } 
             
            $model->user_id     = $session->get('Rules')['user_id'];
            $model->comp_id     = $session->get('Rules')['comp_id'];
            if(!$model->save(false)){
                Yii::$app->session->setFlash('error', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)); 
            } 
            return $this->redirect(['view', 'id' => $model->id]);

        } else {
            $model->multiple = Yii::$app->request->get('multiple') ? Yii::$app->request->get('multiple') : 1;

            return $this->render('create', [        
                'model' => $model
            ]);
        }
    }

    /**
     * Updates an existing KitbomLine model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModelHead($id);

        if($model !=null){

       
            if ($model->load(Yii::$app->request->post()))
            {

                $model->photo = $model->upload($model,'photo');

                //var_dump($model->item_set); exit;
            
                if($model->multiple == 1){
                    if(is_array($model->item_set)) { 
                        $model->item_set    = implode(',',$model->item_set);   
                    }
                    
                }else{

                                       
                    $model->item_set = strpos(",", $model->item_set) !== false 
                                        ? implode(',',$model->item_set)
                                        : $model->item_set;
                    
                } 


                
                $model->save(false);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Deletes an existing KitbomLine model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        //$this->findModel($id)->delete();

        return $this->redirect(['view', 'id' => $model->kitbom_no]);
        //return $this->redirect(['index']);
    }


    public function actionDeleteHeader($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {

            //Delete Line First
            KitbomLine::deleteAll(['kitbom_no' => $id]);
            
            // Delete Header
            $model = $this->findModelHead($id);
            $model->delete();

            try{                     
                // Line Notify
                $bot =  \common\models\LineBot::findOne(5);
                $msg = "\r\nDELETE BOM\r\n";
                $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
                
                $msg.= $id;
                $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
    
                $bot->notify_message($msg);					
    
            } catch (\Exception $e) {					 
                
                
            }
             
            $transaction->commit();
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect(['index']);
    }





    public function actionCreateBomLine()
    {
        $session = Yii::$app->session;
        $model = new KitbomLine();
        
        if (Yii::$app->request->post())
        {
            $model->kitbom_no   = $_POST['param']['id'];
            $model->name        = $_POST['param']['name'];
            $model->item        = Yii::$app->request->post('param')['itemId'];
            $model->item_no     = trim($_POST['param']['item']);
            $model->description = $_POST['param']['desc'];
            $model->quantity    = $_POST['param']['qty'];
            $model->color_style = $_POST['param']['color'];
            $model->user_id     = $session->get('Rules')['user_id'];
            $model->comp_id     = $session->get('Rules')['comp_id'];
            $model->save(false);
        }
        
        // 27/06/2020
        // UPDATE `kitbom_line` k INNER JOIN items i ON k.item_no = i.No SET k.item = i.id WHERE k.item <= 0

        $searchModel    = new KitbomSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->query->andWhere(['kitbom_no'=> $_POST['param']['id']]);


        //var_dump($_POST);
        return $this->renderAjax('_bom_line', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
 
         
    }
    /**
     * Finds the KitbomLine model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return KitbomLine the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelHead($id)
    {
        if (($model = KitbomHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    protected function findModel($id)
    {
        if (($model = KitbomLine::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
