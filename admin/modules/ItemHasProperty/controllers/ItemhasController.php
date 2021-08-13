<?php

namespace admin\modules\ItemHasProperty\controllers;

use Yii;
use yii\filters\AccessControl;
use common\models\ItemsHasProperty;
use admin\modules\ItemHasProperty\models\SearchItemhas;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;




/**
 * ItemhasController implements the CRUD actions for ItemsHasProperty model.
 */
class ItemhasController extends Controller
{
    public $Items_No;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['login', 'logout', 'signup'], 
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index','delete', 'delete-line','view','create','update','property','ptcreate','ptview','ptdelete','ajax-create'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-line' => ['POST'],
                    'ptdelete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ItemsHasProperty models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchItemhas();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=1000;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ItemsHasProperty model.
     * @param string $Items_No
     * @param integer $property_id
     * @return mixed
     */
    public function actionView($Items_No, $property_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($Items_No, $property_id),
        ]);
    }


    public function actionPtview($Items_No)
    {
        $model = new ItemsHasProperty();
        $this->Items_No = $_GET['Items_No'];

        if(($_GET['pid']!=0) AND ($_GET['pval']!="")){
            $model->Items_No        = $this->Items_No;
            $model->property_id     = $_GET['pid'];
            $model->values          = $_GET['pval'];
            $model->save();
        }



        $dataProvider = $model->find()
        ->where(['Items_No' => $this->Items_No])
        ->all();

        return $this->renderpartial('__view', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAjaxCreate($Items_No)
    {

        $model          = new ItemsHasProperty();
        $this->Items_No = Yii::$app->request->get('Items_No');


        $dataProvider = $model->find()
            ->where(['Items_No' => $this->Items_No])
            ->all();


        
        $count = ItemsHasProperty::find()
            ->where(['Items_No' => $Items_No])
            ->andwhere(['property_id' => Yii::$app->request->get('pid')])
            ->count();
        // already exists
        if($count > 0)
        {
            $ptValue = trim(Yii::$app->request->get('param')['property']);

            $model = $this->findModel($this->Items_No, Yii::$app->request->get('pid'));
            $model->values          = $ptValue;
            $model->item            = Yii::$app->request->get('param')['itemid'];

            // Delete When Null value.
            if($ptValue==""){
                $this->findModel($this->Items_No, Yii::$app->request->get('pid'))->delete();
            }else {
               $model->save(); 
            }
                
        }else {

            if(($_GET['pid']!=0) AND ($_GET['pval']!="")){
                $model->Items_No        = $this->Items_No;
                $model->property_id     = $_GET['pid'];
                $model->values          = $_GET['pval'];
                $model->item            = Yii::$app->request->get('param')['itemid'];
                $model->save();
            }

          
        }

        $dataProvider = $model->find()
        ->where(['Items_No' => $this->Items_No])
        ->all();


        return $this->renderpartial('__view', [
            'dataProvider' => $dataProvider,
        ]);
        
    }
    /**
     * Creates a new ItemsHasProperty model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ItemsHasProperty();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'Items_No' => $model->Items_No, 'property_id' => $model->property_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionPtcreate()
    {
        $model = new ItemsHasProperty();


        $dataProvider = $model->find()
        ->where(['Items_No' => $this->Items_No])
        ->all();

        

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->renderpartial(['view', 'Items_No' => $model->Items_No, 'property_id' => $model->property_id]);
        } else {
            return $this->renderpartial('__create', [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'Items_No' => $this->Items_No,
            ]);
        }
    }

    /**
     * Updates an existing ItemsHasProperty model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $Items_No
     * @param integer $property_id
     * @return mixed
     */
    public function actionUpdate($Items_No, $property_id)
    {
        $model = $this->findModel($Items_No, $property_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'Items_No' => $model->Items_No, 'property_id' => $model->property_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ItemsHasProperty model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $Items_No
     * @param integer $property_id
     * @return mixed
     */

    public function actionDeleteLine($id)
    {
        $model = ItemsHasProperty::findOne($id);
        if($model!=null){
            $model->delete();
        }
        return $this->redirect(['index']);
    }

    public function actionDelete($Items_No, $property_id)
    {
        $this->findModel($Items_No, $property_id)->delete();

        return $this->redirect(['index']);
    }

    public function actionPtdelete($id)
    {
        //$model = ItemsHasProperty::find($id);
        //$model->delete();
        $model = ItemsHasProperty::findOne(['id' => $id])->delete();

        //return $this->redirect(['index']);
    }

    /**
     * Finds the ItemsHasProperty model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $Items_No
     * @param integer $property_id
     * @return ItemsHasProperty the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($Items_No, $property_id)
    {
        if (($model = ItemsHasProperty::findOne(['Items_No' => $Items_No, 'property_id' => $property_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    
}
