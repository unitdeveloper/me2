<?php

namespace admin\modules\approval\controllers;

use Yii;
use common\models\Approval;
use admin\modules\approval\models\AppSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\Cheque;

/**
 * AppController implements the CRUD actions for Approval model.
 */
class AppController extends Controller
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
     * Lists all Approval models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AppSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Approval model.
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
     * Creates a new Approval model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Approval();

        if ($model->load(Yii::$app->request->post())) {

            if(Yii::$app->request->isAjax){

                $cheque = Cheque::findOne(Yii::$app->session->get('source'));

                $MoreCheque = Cheque::find()
                ->where(['source_id' => $cheque->source_id])
                ->andwhere(['not', ['source_id' => null]]);

                //$TotalBalance = $MoreCheque->sum('balance');

                foreach ($MoreCheque->all() as $cheque) {

                    $models = new Approval();

                    $models->source_id      = $cheque->id;
                    $models->table_name     = 'cheque';
                    $models->field_name     = 'id';
                    $models->field_data     = $cheque->apply_to_no;
                    $models->ip_address     = $_SERVER['REMOTE_ADDR'];
                    $models->document_type  = 'Payment';
                    $models->sent_by        = $cheque->user_id;
                    $models->sent_time      = $cheque->create_date;
                    $models->approve_date   = date('Y-m-d H:i:s');
                    $models->approve_by     = Yii::$app->user->identity->id;
                    $models->comp_id        = Yii::$app->session->get('Rules')['comp_id'];
                    $models->approve_status = '1';
                    $models->gps            = '';
                    $models->balance        = $cheque->balance;

                    if(!$models->save())
                    {
                        print_r($models->getErrors());
                    }



                    // <!---- Save Log ----->
                    $tracking   = new \common\models\OrderTracking();



                    $tracking->event_date       = date('Y-m-d H:i:s');
                    $tracking->doc_type         = 'Payment';
                    $tracking->doc_id           = $cheque->id;
                    $tracking->doc_no           = $cheque->bank;
                    $tracking->doc_status       = 'Approved';
                    $tracking->amount           = $cheque->balance;
                    $tracking->remark           = $cheque->type.',STATUS :  Approved ,NO : '.$cheque->apply_to_no.', GROUP : '.$cheque->source_id;
                    $tracking->ip_address       = $_SERVER['REMOTE_ADDR'];
                    $tracking->lat_long         = '';
                    $tracking->create_by        = Yii::$app->user->identity->id;
                    $tracking->comp_id          = Yii::$app->session->get('Rules')['comp_id'];
                    $tracking->track_for_table  = 'sale_header';
                    $tracking->track_for_id     = $cheque->getSaleHeader();


                    $tracking->save();

                    // <!---- /.Save Log ----->



                }

                return $this->renderAjax('view',['model' => $models]);

            }else {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            if(Yii::$app->request->isAjax){
                return $this->renderAjax('_form', [
                    'model' => $model,
                ]);
            }
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Approval model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {



            if(Yii::$app->request->isAjax){
                return $this->renderAjax('view',['model' => $model]);
            }else {
                return $this->redirect(['view', 'id' => $model->id]);
            }
            //return $this->redirect(['view', 'id' => $model->id]);
        } else {

            if(Yii::$app->request->isAjax){
                return $this->renderAjax('_form', [
                    'model' => $model,
                ]);
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Approval model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
         $MoreCheque = Cheque::find()
            ->where(['source_id' => $id])
            ->andwhere(['not', ['source_id' => null]]);

            //$TotalBalance = $MoreCheque->sum('balance');

        foreach ($MoreCheque->all() as $cheque) {
            $this->findModel($cheque->id)->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Approval model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Approval the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Approval::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
