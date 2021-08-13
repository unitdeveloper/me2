<?php

namespace admin\modules\apps_rules\controllers;

use Yii;
use common\models\AppsRules;
use common\models\User;
use admin\modules\apps_rules\models\SearchRules;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\web\Response;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
/**
 * RulesController implements the CRUD actions for AppsRules model.
 */
class RulesController extends Controller
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
     * Lists all AppsRules models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchRules();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pagesize=100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AppsRules model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AppsRules model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $common_code    = '';
        $userid         = '';
        $model = new AppsRules();

        
 
        //Ajax Validation Start
        $request = Yii::$app->getRequest();
        if ($request->isPost && $request->post('ajax') !== null) {
            
            $model->load(Yii::$app->request->post());
           
            Yii::$app->response->format = Response::FORMAT_JSON; //กำหนดให้ข้อมูล response ในรูปแบบ JSON

            // Format ถูกต้องหรือไม่
            if(strpos($model->users, '-') !== false) {

                list($sprit_code,$userid) = explode('-',$model->users);

                // มี User อยู่แล้วหรือไม่
                if(AppsRules::find()->where(['user_id' => $userid])->exists()){
                    return ['appsrules-users' => [Yii::t('common','User ID already exists')]];
                }else {
                    // User มีจริงไหม
                    if(User::findOne($userid) === NULL){
                        return ['appsrules-users' => [Yii::t('common','The user ID does not exist.')]];
                    }else{
                        return [];
                    }
                    
                }

            }else {
                return ['appsrules-users' => [Yii::t('common','The format not correct.')]];
 
            } 

            
            
            
        }
        //Ajax Validation End

        if ($model->load(Yii::$app->request->post())) {

            $transaction = Yii::$app->db->beginTransaction();

            try {

 

          
                if(strpos($model->users, '-') !== false) {

                    list($sprit_code,$userid) = explode('-',$model->users);

                    $model->user_id         = $userid;
                    $model->sprit_code      = $sprit_code;
                    $model->date_created    = date('Y-m-d H:i:s');

                    if($model->sale_id != ''){
                        $model->sale_code   = $model->sale->code;
                        $model->sales_id    = $model->sale->code;
                    } 

                    $model->save();

                    $transaction->commit();

                    return $this->redirect(['view', 'id' => $model->id]);

                }else {
                    throw new NotFoundHttpException('THe format not colect.');
                    return $this->redirect(['create']);
                } 
                
            
            } catch (Exception $e) {
                $transaction->rollBack();
            }
            
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AppsRules model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model  = $this->findModel($id);
        

        

         
        if ($model->load(Yii::$app->request->post())) {


             
                if(strpos($model->users, '-') !== false) {

                    list($sprit_code,$userid) = explode('-',$model->users);

                    $model->user_id         = $userid;
                    $model->sprit_code      = $sprit_code;
                    $model->date_created    = date('Y-m-d H:i:s');

                    if($model->sale_id != ''){
                        $model->sale_code   = $model->sale->code;
                        $model->sales_id    = $model->sale->code;
                    } 

                    if($model->save()){
                       
                    }

                     

                }  
                
           
            

                return $this->redirect(['view', 'id' => $model->id]);

           
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AppsRules model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AppsRules model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AppsRules the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AppsRules::findOne($id)) !== null) {
            return $model;
        } else {
            // Create
            
            $model          = new AppsRules();
            $model->user_id = $id;
            $model->users   = 'ew';
            if($model->save()){
                return $model;
            }else{
                throw new NotFoundHttpException(Yii::t('common','Error : {:err}', [':err' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)]));
            }

            
            //throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
