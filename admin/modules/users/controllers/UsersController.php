<?php

namespace admin\modules\users\controllers;

use Yii;
use common\models\User;
use common\models\Profile;
use common\models\AppsRules;
use common\models\AuthAssignment;
use common\models\Authentication;
use admin\modules\users\models\SearchUsers;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use admin\models\FunctionCenter;
use yii\base\Model;



/**
 * UsersController implements the CRUD actions for User model.
 */
class UsersController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    { 
        $fn = new FunctionCenter();
        $fn->RegisterRule();
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error','help'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['create', 'view', 'delete', 'index','update','ajax-get','ajax-change-passwd'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'ajax-get' => ['POST'],
                    'ajax-change-passwd' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchUsers();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'appRule' => $this->findAppsRules($id)
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model      = new User();
        $profile    = new Profile();
        $appRule    = new AppsRules();

        if ($model->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post()) && $appRule->load(Yii::$app->request->post())) {

            if(User::find()->where(['or' ,['username' =>  $model->username],['email' =>  $model->email]])->exists()){
                Yii::$app->getSession()->addFlash('danger',Yii::t('common','User name'). " " . Yii::t('common','or') . " " . Yii::t('common','email'). " " .Yii::t('common','Already Exists'));       
                return $this->render('create', [
                    'model' => $model,
                    'profile' => $profile,
                    'appRule' => $appRule
                ]);           
            }else{

                $transaction = Yii::$app->db->beginTransaction();
                try {
                
                    $model->password            = Yii::$app->request->post('User')['password'];
                    
                    if($model->password){
                        $model->password_hash   = \Yii::$app->security->generatePasswordHash($model->password, Yii::$app->getModule('user')->cost);
                    }

                    //$rules_name = Yii::$app->request->post('rules_name');
                    //var_dump($rules_name); exit();
                    $model->auth_key            = Yii::$app->security->generateRandomString();
                    $model->created_at          = strtotime(date('Y-m-d H:i:s'));
                    $model->updated_at          = strtotime(date('Y-m-d H:i:s'));
                    if($model->save()){
                        // Create Profile
                        $profile->user_id       = $model->id;
                        $profile->name          = Yii::$app->request->post('Profile')['name'];
                        $profile->gender        = Yii::$app->request->post('Profile')['gender'];
                        $profile->save();

                        // Create rules                             
                        $appRule->user_id       = $model->id;
                        $appRule->comp_id       = Yii::$app->request->post('AppsRules')['comp_id'];
                        $appRule->date_created  = date('Y-m-d H:i:s');
                        $appRule->rules_id      = Yii::$app->request->post('AppsRules')['rules_id'];
                        $appRule->sprit_code    = "ew";
                        $appRule->sales_id      = Yii::$app->request->post('AppsRules')['sale_id'];
                        $appRule->name          = $profile->name;
                        $appRule->status        = Yii::$app->request->post('AppsRules')['status'];
                        $appRule->users         = $profile->name;
                        if(!$appRule->save()){
                            
                            $transaction->rollBack();
                            Yii::$app->getSession()->addFlash('danger',json_encode($appRule->getErrors(),JSON_UNESCAPED_UNICODE));
                            return $this->redirect(['create']);
                        }

                        // Create Permission
                        $Auth                   = new AuthAssignment();
                        $Auth->item_name        = Yii::$app->request->post('rules_name');
                        $Auth->user_id          = $model->id;            
                        $Auth->created_at       = strtotime(date('Y-m-d H:i:s'));
                        $Auth->save();
                    }
                        

                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', Yii::t('common','Error : "{:this}"',[':this' => $e])); 	
                    return $this->redirect(['index']);
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $appRule->status = 1;
            return $this->render('create', [
                'model'     => $model,
                'profile'   => $profile,
                'appRule'   => $appRule
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model      = $this->findModel($id);
        $profile    = $this->findProfile($model->id);
        $appRule    = $this->findAppsRules($model->id);

        if ($model->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post()) && $appRule->load(Yii::$app->request->post())) {
            
            $transaction = Yii::$app->db->beginTransaction();
            try {

                

                if(Yii::$app->request->post('password')){
                    if(Yii::$app->request->post('password') !== $model->password_hash){
                        $model->password_hash   = Yii::$app->security->generatePasswordHash(Yii::$app->request->post('password'), Yii::$app->getModule('user')->cost);
                    }
                    
                }
                if(!$model->auth_key){
                    $model->auth_key        = Yii::$app->security->generateRandomString();
                }
                
                
                $model->updated_at      = strtotime(date('Y-m-d H:i:s'));
                

                if($model->save()){

                    $profile->name = $_POST['Profile']['name'];

                    if($profile->save()){
                        Yii::$app->getSession()->addFlash('success','<i class="far fa-save"></i> '.Yii::t('common','Saved'));  
                    }else{
                        Yii::$app->getSession()->addFlash('danger',json_encode($profile->getErrors(),JSON_UNESCAPED_UNICODE));
                    }  

                }else{

                    Yii::$app->getSession()->addFlash('danger',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)); 

                }
                
                // Update rules                             
                $appRule->comp_id       = Yii::$app->request->post('AppsRules')['comp_id'];
                $appRule->rules_id      = Yii::$app->request->post('AppsRules')['rules_id'];
                $appRule->sales_id      = Yii::$app->request->post('AppsRules')['sale_id'];
                $appRule->name          = $profile->name;
                $appRule->status        = Yii::$app->request->post('AppsRules')['status'];
                $appRule->users         = $profile->name;
                if(!$appRule->save()){
                    
                    $transaction->rollBack();
                    Yii::$app->getSession()->addFlash('danger',json_encode($appRule->getErrors(),JSON_UNESCAPED_UNICODE));
                    return $this->redirect(['create']);
                }

                
                
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('common','Error : "{:this}"',[':this' => $e])); 	
                return $this->redirect(['index']);
            }


            
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model'     => $model,
                'profile'   => $profile,
                'appRule'   => $appRule
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    protected function checkFk($id){
        $customer   = \common\models\Customer::findOne(['user_id' => $id]);
        $status     = false;
        $message    = '';
        if($customer!=null){
            $message = Yii::t('common','Customer').' : '.$customer->name;
        }else{
            $status     = true;
        }
        return (Object)[
            'status'    => $status,
            'message'   => $message
        ];
    }
    public function actionDelete($id)
    {
        $link   = $this->checkFk($id);
        if(!$link->status){
            Yii::$app->session->setFlash('error', Yii::t('common','Please delete "{:this}" first',[':this' => $link->message]));
        }else{

            $transaction = Yii::$app->db->beginTransaction();
			try {
	

				if ($this->findProfile($id)){
                    $this->findProfile($id)->delete();
                    
                    if($this->findAssignment($id)){
                        $this->findAssignment($id)->delete();
                    }
                            
                    if($this->findAppsRules($id)){
                        $this->findAppsRules($id)->delete();
                    }
                                                        
                    if($this->findAuth($id)){
                        $this->findAuth($id)->delete();
                    }

                    $this->findModel($id)->delete(); 

                    // if ($this->findProfile($id)->delete()){
                    //     if($this->findAssignment($id)->delete()){
                    //         if($this->findAppsRules($id)->delete()){                         
                    //             if($this->findAuth($id)){
                    //                 $this->findAuth($id)->delete();
                    //             }
                    //             $this->findModel($id)->delete(); 
                    //         }                           
                    //     }                       
                    // }
                } else{           
                    if($this->findAuth($id)){
                        $this->findAuth($id)->delete();
                    }
                    $this->findModel($id)->delete();                                                            
                }   

				Yii::$app->session->setFlash('success','<i class="far fa-save"></i> '.Yii::t('common','Success')); 
				$transaction->commit();

			} catch (\Exception $e) {
				 
				$transaction->rollBack();
				Yii::$app->session->setFlash('error', Yii::t('common','Error : "{:this}"',[':this' => $e])); 	 
			}
            
        }    

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findProfile($id)
    {
        if (($model = Profile::findOne($id)) !== null) {
            return $model;
        } else {
           // throw new NotFoundHttpException('The requested page does not exist.');
            return false;
        }
    }

    protected function findAppsRules($id)
    {
        if (($model = AppsRules::find()->where(['user_id' => $id])->one()) !== null) {
            return $model;
        } else {
            //throw new NotFoundHttpException('The requested page does not exist.');
            return false;
        }
    }

    protected function findAssignment($id)
    {
        if (($model = AuthAssignment::find()->where(['user_id' => $id])->one()) !== null) {
            return $model;
        } else {
            return false;
        }
    }

    protected function findAuth($id)
    {
        if (($model = Authentication::find()->where(['user_id' => $id])->one()) !== null) {
            return $model;
        } else {
            return false;
        }
    }

    

    public function actionAjaxGet($id){
        
        if(Yii::$app->user->identity->id!==1){
            return $this->redirect(['site/index']);
        }

        $model = $this->findModel($id);
        return json_encode([
            'status' => 200,
            'data' => [
                'id' => $model->id,
                'name' => $model->profile->name,
                'username' => $model->username,
                'email' => $model->email,
                'status' => $model->status
            ]
        ]);
    }

    public function actionAjaxChangePasswd($id){
        
        if(Yii::$app->user->identity->id!==1){
            return $this->redirect(['site/index']);
        }

        $model = $this->findModel($id);
        $model->password_hash   = Yii::$app->security->generatePasswordHash($_POST['password'], Yii::$app->getModule('user')->cost);
        $model->updated_at      = strtotime(date('Y-m-d H:i:s'));
        if($model->save()){
            return json_encode([
                'status' => 200,
                'data' => [
                    'id' => $model->id,
                    'name' => $model->profile->name,
                    'username' => $model->username,
                    'email' => $model->email,
                    'status' => $model->status
                ]
            ]);
        }else{
            return json_encode([
                'status' => 500,
                'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
            ]);
        }


        
    }
    
}
