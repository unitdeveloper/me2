<?php

namespace admin\modules\warehousemoving\controllers;

use Yii;
use common\models\Address;
use admin\modules\warehousemoving\models\AddressSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AddressController implements the CRUD actions for Address model.
 */
class AddressController extends Controller
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
     * Lists all Address models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AddressSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Address model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    // public function actionAjaxView()
    // {
    //     $model = Address::find()->where(['source_id' => $form['source_id']])->all();
    //     return $this->renderAjax('_form_shipment', [
    //             'model' => $model,
    //         ]);

    // }

    /**
     * Creates a new Address model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Address();
        $Address = Address::find()->where(['source_id' => $_POST['id']])->all();

        if ($model->load(Yii::$app->request->post())) {

            $model->country         = 'Thailand';
            $model->status          = '1';
            $model->create_date     = date('Y-m-d H:i:s');
            $model->user_id         = Yii::$app->user->identity->id;

            $model->save();


            return $this->redirect(['view', 'id' => $model->id]);

        } else {

            if(Yii::$app->request->isAjax) {






                return $this->renderAjax('_form_shipment', [
                    'model' => $model,
                    'addr' => $Address,
                ]);




            }else {
                return $this->render('_form_shipment', [
                    'model' => $model,
                    'addr' => $Address,
                ]);
            }

        }


    }

    public function actionAjaxCreate()
    {
        $form = $_POST['Address'];
        $model = new Address();

        $model->source_type     = 'Customer';
        $model->source_id       = $form['source_id'];
        $model->source_name     = $form['source_name'];
        $model->transport       = $form['transport'];
        $model->address         = $form['address'];
        $model->address2        = $form['address2'];
        $model->district        = $form['district'];
        $model->city            = $form['city'];
        $model->province        = $form['province'];
        $model->postcode        = $form['postcode'];
        $model->country         = 'Thailand';
        $model->status          = '1';
        $model->create_date     = date('Y-m-d H:i:s');
        $model->user_id         = Yii::$app->user->identity->id;

        //if($form['district']!='maxlength'){
        if(!$model->save()){
            //Yii::$app->session->addFlash('warning', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
            return json_encode([
                'status' => 500,
                'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
            ]);
        }
        //} 

        //var_dump($_POST['Address']);

        $Address = Address::find()->where(['source_id' => $model->source_id])->all();
        return $this->renderAjax('_form_shipment', [
                'model' => $model,
                'addr' => $Address,
            ]);
    }

    /**
     * Updates an existing Address model.
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

    public function actionAjaxUpdate($id)
    {
        $form = $_POST['Address'];
        $model = $this->findModel($id);

        $model->source_type     = 'Customer';
        $model->source_id       = $form['source_id'];
        $model->source_name     = $form['source_name'];
        $model->transport       = $form['transport'];
        $model->address         = $form['address'];
        $model->address2        = $form['address2'];
        $model->district        = $form['district'];
        $model->city            = $form['city'];
        $model->province        = $form['province'];
        $model->postcode        = $form['postcode'];
        $model->country         = 'Thailand';
        $model->status          = '1';
        $model->create_date     = date('Y-m-d H:i:s');
        $model->user_id         = Yii::$app->user->identity->id;

        $model->remark          = $form['remark'];
        $model->comment         = $form['comment'];


        if($form['district']!='maxlength'){
          if(!$model->save()){
            print_r($model->getErrors());
          }
        }

        //var_dump($_POST['Address']);

        $Address = Address::find()->where(['source_id' => $form['source_id']])->all();
        return $this->renderAjax('_form_shipment', [
                'model' => $model,
                'addr' => $Address,
            ]);
    }

    /**
     * Deletes an existing Address model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionAjaxDelete()
    {
        $model = new Address();
        if($_POST['id']!='')
        $this->findModel($_POST['id'])->delete();

        // $Address = Address::find()->where(['source_id' => $_POST['source_id']])->all();
        // return $this->renderAjax('_form_shipment', [
        //         'model' => $model,
        //         'addr' => $Address,
        //     ]);

    }


    /**
     * Finds the Address model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Address the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Address::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
