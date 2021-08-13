<?php

namespace admin\controllers;

use Yii;
use common\models\NumberSeries;
use admin\models\SeriesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


use admin\models\Generater;

use common\models\RuningNoseries;
/**
 * SeriesController implements the CRUD actions for NumberSeries model.
 */
class SeriesController extends Controller
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
                    'ajax_update' => ['GET'],
                    'new-series' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all NumberSeries models.
     * @return mixed
     */

    public function actionAjax_noseries($id,$code) {
        $model = RuningNoseries::find()
                ->where(['no_series' => $id])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->all();
       
        return $this->renderpartial('ajax_noseries',['model' => $model,'id' => $id,'code'=>$code ,'group' => null]);

    }

    public function actionGetAjaxNoseries($id){

        $SeriesGroup = \common\models\FormMapNumberSeries::findOne(['id' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);


        $NumberSeries = NumberSeries::findOne(($SeriesGroup->number_series)? $SeriesGroup->number_series : 1);
        
        $model = RuningNoseries::find()
                ->where(['no_series' => $NumberSeries->id])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->all();

        return json_encode([
            'status' => 200,
            'html' => $this->renderpartial('ajax_noseries',['model' => $model,'id' => $NumberSeries->id,'code'=>$NumberSeries->starting_char,'group' => $SeriesGroup->id])
        ]);
    }

    public function actionAjaxClear(){

        $model = RuningNoseries::deleteAll('no_series = '.$_POST['id']);

        if($model){
            echo '
                <div class="col-xs-12">
                    <div class="alert alert-warning">
                        <h4>Delete</h4>
                        <p>Delete Successful</p>
                    </div>
                </div>';

            echo "<script>

                    $('body').attr('style','overflow:auto; margin-right:0px; padding-right:0px !important;');

                    setTimeout(function(){
                        $('#RunNoSeries').modal('hide');
                    }, 1000);
                </script>";
        }


    }

    public function actionAjaxFindNoseries($id,$code)
    {
        $model = RuningNoseries::find()
                ->where(['no_series' => $id])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->all();

        if($model) {
            return json_encode([
                'status' => 200,
                'message' => 'done',
                'value' => [
                    'id' => $id,
                    'code' => $code
                ]
            ]);
        }else {

            if(Generater::newGenerate($id)){
                return json_encode([
                    'status' => 200,
                    'message' => 'done',
                    'value' => [
                        'id' => $id,
                        'code' => $code
                    ]
                ]);
            }else{

                return json_encode([
                    'status' => 201,
                    'message' => 'done',
                    //'html' => 'new'
                    'html' => $this->renderAjax('_ajax_noseries',['model' => $model,'id' => $id,'code'=>$code])
                ]);
            }

        }


    }


    public function actionAjax_update(){
        $status     = 200;
        $message    = Yii::t('common','Success');
        $field      = @$_GET['param']['fieldname'];
        $value      = '';
        $id         = @$_GET['param']['id'];

        $transaction = Yii::$app->db->beginTransaction();
        try {  

            $value = $_GET['param']['fieldname'] == 'start_date' 
                        ? date('Y-m-d', strtotime(trim($_GET['param']['txt'])))
                        : trim($_GET['param']['txt']);

            $sql = "UPDATE runing_noseries SET ".$_GET['param']['fieldname']."='".$value."' WHERE id= ".$_GET['param']['id']." ";

            Yii::$app->db->createCommand($sql)->execute();


            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','{:e}',[':e' => $e]);
        }

        return json_encode([
            'status' => $status,
            'message'   => $message,
            'data'      => [
                'id' => $id,
                'field' => $field,
                'value' => $value
            ]

        ]);
    }

    public function actionAjax_autogenseries($code,$char,$digit,$NoSeries)
    {



        if(Generater::newGenerate($NoSeries))
        {
            echo '<br>
                <div class="col-xs-12">
                    <div class="alert alert-success">
                        <h4>Generate</h4>
                        <p>Generate Successful</p>
                    </div>
                </div>';

            echo "<script>

                    $('body').attr('style','overflow:auto; margin-right:0px; padding-right:0px !important;');

                    setTimeout(function(){
                        $('#RunNoSeries').modal('hide');
                    }, 1000);

                    </script>";
        }

         
    }


    public function actionEditableDemo() {
        $model = new Demo; // your model can be loaded here

        // Check if there is an Editable ajax request
        if (isset($_POST['hasEditable'])) {
            // use Yii's response format to encode output as JSON
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            // read your posted model attributes
            if ($model->load($_POST)) {
                // read or convert your posted information
                $value = $model->description;

                // return JSON encoded output in the below format
                return ['output'=>$value, 'message'=>''];

                // alternatively you can return a validation error
                // return ['output'=>'', 'message'=>'Validation error'];
            }
            // else if nothing to do always return an empty JSON encoded output
            else {
                return ['output'=>'', 'message'=>''];
            }
        }

        // Else return to rendering a normal view
        return $this->render('view', ['model'=>$model]);
    }

    public function actionIndex()
    {
        $searchModel = new SeriesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->query->orderBy(['name' => SORT_ASC]);

        $dataProvider->pagination->pageSize=100;

        if(Yii::$app->request->post('hasEditable'))

        {


            $model = NumberSeries::findOne(Yii::$app->request->post('editableKey'));

            $out = json_encode(['output' => '', 'message' => '']);

            //$post = [];
            $posted = current($_POST['NumberSeries']);
            //$post['NumberSeries'] = $posted;
            $post   = ['NumberSeries' => $posted];

            if($model->load($post))
            {

                $model->save();


            }

            echo $out;

            return;


        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NumberSeries model.
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
     * Creates a new NumberSeries model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {



        $model = new NumberSeries();

        $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];

        if(Yii::$app->request->isAjax) {


            if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            // if ($model->load(Yii::$app->request->post()) && $model->save()) {


                $model->save();
                //echo json_encode(array('redirect' => $this->createUrl('/series/view')));
            } else {



                return $this->renderAjax('_form', [
                    'model' => $model,
                ]);
            }

        }else {

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

        }

    }

    public function actionCreateAjax()
    {

        $model = new NumberSeries();

        if (Yii::$app->request->isAjax)
        {

            $model->name            = $_POST['name'];
            $model->description     = $_POST['desc'];
            $model->starting_char   = $_POST['char'];
            $model->table_name      = $_POST['table'];
            $model->field_name      = $_POST['field'];
            $model->separate        = $_POST['sep'];
            $model->format_gen      = $_POST['gen'];
            $model->format_type     = $_POST['type'];
            $model->cond            = $_POST['cond'];

            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];


            if($model->save())
            {
                return json_encode([
                    'id' => $model->id,
                    'code' => $model->format_gen,
                    'field' => $model->field_name,

                ]);
                

            }else {
                print_r($model->getError());
            }

        }else {

            echo 'Error...';

        }
    }

    /**
     * Updates an existing NumberSeries model.
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
     * Deletes an existing NumberSeries model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if($model->delete()){
            Yii::$app->session->setFlash('sucess', Yii::t('common','Success'));
            try{

                $status 		= 200;
                $message 		= Yii::t('common','DANGEROUS');

                // Line Notify
                $bot =  \common\models\LineBot::findOne(1);
                $msg = $message."\r\n";
                $msg.= 'Delete number series : ['.$model->name. '] ID : '.$model->id."\r\n";
                $msg.= $model->description."\r\n";
                $msg.= 'By User id : ' .Yii::$app->user->identity->id."\r\n";
                $msg.= 'Company : ' .Yii::$app->session->get('Rules')['comp_id']."\r\n";
                 
                $bot->notify_message($msg);					

            } catch (\Exception $e) {					 
                $status 		= 500;
                $message 		= Yii::t('common','{:e}',[':e' => $e]);	
                Yii::$app->session->setFlash('error', $message);
            }

        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the NumberSeries model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NumberSeries the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NumberSeries::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionNextRuningSeries($id,$dash)
    {
      $model = $this->findModel($id);
      $GenSeries        = new Generater();
      switch ($dash) {
        case 'true':
            //$NoSeries         = $GenSeries->NextRuning($model->table_name,$model->field_name,$model->cond,true);
            $NoSeries         = $GenSeries->getRuning($model->table_name,$model->field_name,$model->cond);
            break;
        case 'false':
            //$NoSeries         = $GenSeries->getRuning($model->table_name,$model->field_name,$model->cond,false);
            $NoSeries         = $GenSeries->getRuning($model->table_name,$model->field_name,$model->cond);
            break;

        default:
            //$NoSeries         = $GenSeries->getRuning($model->table_name,$model->field_name,$model->cond,true);
            $NoSeries         = $GenSeries->getRuning($model->table_name,$model->field_name,$model->cond);
            break;
      }


      return $NoSeries;
    }

    public function actionAjaxGetVendorNo($str,$count='03',$dash=NULL){



        $JSON = json_encode([
                'code' => $str.'001',
                'message' => ''
            ]);

        $model = \common\models\Vendors::find()
                ->where('code like ("'.$str.'%")')
                ->orderBy(['code' => SORT_DESC])
                ->one();



        if($model){

            preg_match('/[^0-9]*([0-9]+)[^0-9]*/', $model->code, $DocNo);

            $end    = intval(end($DocNo) + 1) ;
            $EndNo  = sprintf("%'".$count."d\n", $end);

            $JSON = json_encode([
                'code' => $str.$dash.$EndNo,
                'message' => ''
            ]);

        }


        return $JSON;



    }

    public function actionGetCurentSeries(){

        $Generater      = new Generater();
        $param          = $_POST['module'];
        $query = NumberSeries::find()
        ->where(['table_name' => $param['table']]) 
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
       
        if(isset($param['field'])){
            $query->andWhere(['field_name' => $param['field']]);     
        }

        if($query->count() > 0){

            $data = '<ul class="series-list">';
            foreach ($query->all() as $key => $model) {

                $data.= '<li>
                            <div class="row">
                                <div class="col-xs-10">
                                    <span class="selected" key="'.$model->id.'"  data="'.$Generater->getRuning($model->table_name,$model->field_name,$model->cond).'" >
                                        <i class="fa fa-caret-right" aria-hidden="true"></i> '.$model->description.'
                                    </span>
                                </div>
                                <div class="col-xs-2">
                                    <span data="'.$model->id.'" code="'.$model->starting_char.'" class="edit-Runing-Series">
                                        <x class="fa fa-pencil-square-o" aria-hidden="true"></x>
                                    </span>
                                </div>
                            </div>
                        </li>';
            }

            $data.= '</ul>';
            $data.= '<div class="fit-footer">
                      <div class="row"><hr>
                        <div class="col-xs-12">
                            <span id="AddSeries">+  Add New</span>
                        </div>
                      </div>
                    </div>';

            return $data;
        }else {
            $data = '<div class="fit-footer">
                      <div class="row"><hr>
                        <div class="col-xs-12">
                            <span id="AddSeries">+  Add New</span>
                        </div>
                      </div>
                    </div>';

            return $data;
        }

    }

    public function actionNewSeries(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        return json_encode([
            'status' => 200,
            'no'    => \admin\models\Series::invoiceNo('view_rc_invoice', 'no_', 'all', 'Sale', $data->no)
        ]);
    }
}
