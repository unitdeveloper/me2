<?php

namespace admin\modules\salepeople\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



use common\models\SalesPeople;
use admin\modules\salepeople\models\SearchPeople;
use admin\modules\salepeople\models\SearchCustomer;


/**
 * PeopleController implements the CRUD actions for SalesPeople model.
 */
class PeopleController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                // 'only' => ['login', 'logout', 'signup'],
                'rules' => [
                    // [
                    //     'allow' => true,
                    //     'actions' => ['index','view','byc'],
                    //     'roles' => ['?'],
                    // ],
                    [
                        'allow' => true,
                        'actions' => ['index','view','update','delete','update-status', 'read-only'],
                        'roles' => ['@'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['create','ajax-update'],
                        'roles' => ['@'],
                        //'roleParams' => ['postId' => Yii::$app->session->get('Rules')['rules_id']],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'update-status' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SalesPeople models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchPeople();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(["comp_id" => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->pagination->pageSize=100;
      
        // $auth = Yii::$app->authManager;
        // $createPost = $auth->createPermission('createPost');
        // $createPost->description = 'Create a post';
        // $auth->add($createPost);
        //echo Yii::$app->session->get('Rules')['rules_id'];

        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAjaxUpdate()
    {

        $status = 0;

        $model = SalesPeople::findOne($_POST['id']);

        if($_POST['val']=='true') {   $status = 1; };
        if($_POST['val']=='false') {  $status = 0; };

        $model->status = $status;
        if(!$model->save(false))
        {
            return 'Error.';
        }

    }

    /**
     * Displays a single SalesPeople model.
     * @param integer $id
     * @return mixed
     */
    public function actionReadOnly()
    {
        $searchModel = new SearchPeople();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(["comp_id" => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->pagination->pageSize=100;
      
        return $this->render('read-only', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $searchModel = new SearchCustomer();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=100;
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new SalesPeople model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SalesPeople();

        if ($model->load(Yii::$app->request->post())) {

            if($model->sale_group != ''){
                $model->sale_group  = implode(',',$model->sale_group);
            }
            $model->sign_img    = $model->upload($model,'sign_img');
            $model->photo       = $model->upload($model,'photo');
           
            
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SalesPeople model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if($model->sale_group != ''){
                $model->sale_group  = implode(',',$model->sale_group);
            }
            
            //if($model->sign_img!='')    unlink($model->unsign);
            //if($model->photo!='')       unlink($model->unsign);

            $model->sign_img    = $model->upload($model,'sign_img');
            $model->photo       = $model->upload($model,'photo');
           
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }



    /**
     * Deletes an existing SalesPeople model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {

        if(\common\models\SaleHeader::find()->where(['sale_id' => $id])->exists()){

            Yii::$app->session->setFlash('danger', Yii::t('common','Error ! Transactions'));
            return $this->redirect(['index']);
        }else {
            $this->findModel($id)->delete();
            Yii::$app->session->setFlash('info', Yii::t('common','Delete !'));
            return $this->redirect(['index']);
        }
        
    }

    /**
     * Finds the SalesPeople model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalesPeople the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalesPeople::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdateStatus($id)
    {
         
        $model = $this->findModel($id);


        if($_POST['param']['val']=='true')
        {
            $model->status = 1;
            $model->save(false);
        }else {
            $model->status = 0;
            $model->save(false);
        }

        return json_encode([
                'status' => 200,
                'name' => $model->name,
                'message' => 'Saved',
        ]);
         
    	 
    }
}
