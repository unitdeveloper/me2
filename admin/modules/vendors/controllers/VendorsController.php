<?php

namespace admin\modules\vendors\controllers;

use Yii;
use common\models\Vendors;
use admin\modules\vendors\models\VendorsSearch;
use admin\modules\vendors\models\CustomerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use admin\models\Generater;
/**
 * VendorsController implements the CRUD actions for Vendors model.
 */
class VendorsController extends Controller
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
                    'ajax-find-vendor' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all Vendors models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VendorsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Vendors model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {


        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionAjaxFindVendor(){

      // ถ้าไม่ได้ค้นหา ให้แสดงรายการที่ใช้บ่อย 10 รายการ

      $query = Vendors::find()
      ->where(['or',
            ['comp_id' => Yii::$app->session->get('Rules')['comp_id']],
            ['id' => 1]
        ])
      ->orderBy(['id' => SORT_ASC])
      ->limit(10)
      ->all();

      //if(isset($_POST['cond'])){
        if(Yii::$app->request->post('cond')!=''){
          $query = Vendors::find()
          ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
          ->andWhere(['or' ,
            ['like','name', explode(' ',Yii::$app->request->post('cond'))],
            ['like','code', Yii::$app->request->post('cond')]
            ])
          ->limit(20)
          ->all();
        }

      //}

      if($query){

          $data = '<ul class="vendor-list">';
          foreach ($query as $key => $model) {

            $data.= '<li>
                      <div class="row">
                        <div class="col-xs-12">
                          <span class="selected row" key="'.$model->id.'" data="'.$model->name.'" style="cursor:pointer;" ng-click="pickCompany()" title="'.$model->code.'" alt="'.$model->code.'">
                            <div class="col-xs-1"><i class="fa fa-id-card-o text-aqua" aria-hidden="true"></i></div> <div class="col-xs-11">'.$model->name.'</div>
                          </span>
                        </div>

                      </div>
                    </li>';
          }
          $data.= '</ul>';
          $data.= '<div class="fit-footer">
                    <div class="row"><hr>
                      <div class="col-xs-12">
                          <span id="searchVendorList" style="cursor:pointer;"><i class="fa fa-search" aria-hidden="true"></i>  ค้นหาจาก "ฐานข้อมูลส่วนกลาง"</span>
                      </div>
                    </div>
                  </div>';

          return json_encode(['html' => $data]);
      }else {


      $data =
<<<HTML
            <ul class="addNewVendor">
              <li>
                <div class="row">
                  <div class="col-xs-12">
                      <span id="addNewVendor">
                        <a href="index.php?r=vendors%2Fvendors%2Fcreate" target="_blank"><i class="fa fa-plus" aria-hidden="true"></i> สร้างใหม่</a>
                      </span>
                  </div>
                </div>
              </li>
            </ul>


            <div class="fit-footer">
              <div class="row"><hr></div>
              <div class="row">
                <div class="col-xs-12">
                    <span id="searchVendorList" style="cursor:pointer;"><i class="fa fa-search" aria-hidden="true"></i>  ค้นหาจาก "ฐานข้อมูลส่วนกลาง"</span>
                </div>
              </div>
            </div>
HTML;


          return json_encode(['html' => $data]);
      }

    }

    public function actionAjaxFindCustomer(){

      $searchModel = new CustomerSearch();
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
      $dataProvider->pagination->pageSize=5;
      $dataProvider->query->andWhere(['<>','status','0']);
      $dataProvider->query->andWhere(['<>','id','909']);
 
      if(Yii::$app->request->post()){

        //var_dump($_GET); exit();
        if($_POST['cond']!=''){
            $dataProvider->query->andWhere(['like','name',explode(' ',$_POST['cond'])]);
        }
        if(isset($_GET['SearchCustomer'])){             
            if($_GET['SearchCustomer']['name']!=''){
                $dataProvider->query->andWhere(['like','name',explode(' ',$_GET['SearchCustomer']['name'])]);
            }
        }
      }

      if(Yii::$app->request->isAjax){
        return $this->renderAjax('findcustomer', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
      }

      // ป้องกันการเข้าหน้านี้โดยตรง
      // ถ้ากำลังแก้ไข จะ Redirect ไป Update
      // ถ้ากำลังสร้าง จะ Redirect ไป หน้า Create

      if(Yii::$app->session->get('P-Order')!=''){
        return $this->redirect(['/Purchase/order/update', 'id' => Yii::$app->session->get('P-Order')]);
      }else {
        return $this->redirect(['/Purchase/order/create']);
      }


    }

    public function actionAjaxGetVendorInfo(){

      $model = $this->findModel($_POST['id']);

      $obj = [
        'id'    => $model->id,
        'name'  => $model->name,
        'code'  => $model->code,
        'address'       => $model->address,
        'phone'         => $model->phone,
        'fax'           => $model->fax,
        'contact'       => $model->contact,
        'email'         => $model->email,
        'vat_regis'     => $model->vat_regis,
        'branch_name'   => $model->branch_name,
        'payment_term'  => $model->payment_term
      ];

      return json_encode($obj,JSON_UNESCAPED_UNICODE);
    }

    /**
     * Creates a new Vendors model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Vendors();

        if ($model->load(Yii::$app->request->post())) {

            $model->logo    = $model->upload($model,'logo');
            $model->photo   = $model->upload($model,'photo');

            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];


            $model->save(false);

            $UpdateSeries       = Generater::UpdateSeries('vendors','vatbus_posting_group',$model->vatbus_posting_group,$model->code);

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Vendors model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        if(Yii::$app->user->identity->id !=1){

            if($id == 1){
                Yii::$app->getSession()->setFlash('alert',[
                    'body'=>'<i class="fa fa-times-circle text-red"></i> '.Yii::t('common','You do not have permission to edit vendor CASH.'),
                    'options'=>['class'=>'bg-danger']
                ]);
                return $this->redirect(['index']);
            }

        }




        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->logo    = $model->upload($model,'logo');
            $model->photo   = $model->upload($model,'photo');
            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];


            if(!$model->save()){
              Yii::$app->getSession()->setFlash('alert',[
                  'body'=>'<i class="fa fa-times-circle text-red"></i> '.json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                  'options'=>['class'=>'bg-danger']
              ]);
              return $this->render('update', [
                  'model' => $model,
              ]);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }




    }

    /**
     * Deletes an existing Vendors model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */



    public function actionDelete($id)
    {
        if($id == 1){


            Yii::$app->getSession()->setFlash('alert',[
                'body'=>'<i class="fa fa-times-circle text-red"></i> '.Yii::t('common','You do not have permission to remove this vendor.'),
                'options'=>['class'=>'bg-danger']
            ]);


            return $this->redirect(['index']);


        }else {

            $Order = \common\models\PurchaseHeader::find()->where(['vendor_id' => $id]);

            if($Order->exists()){
              $ThisOrder = $Order->one();
              Yii::$app->getSession()->setFlash('alert',[
                  'body'=>'<i class="fa fa-info text-info"></i> '.Yii::t('common','This vendor has in purchase order.').' '.
                  \yii\helpers\Html::a('<span class="text-info">'.$ThisOrder->doc_no.'</span>',['/Purchase/order/update','id'=>$ThisOrder->id]),
                  'options'=>['class'=>'bg-warning']
              ]);
              return $this->redirect(['view', 'id' => $id]);

            }else {
              $this->findModel($id)->delete();

              return $this->redirect(['index']);
            }



        }

    }

    /**
     * Finds the Vendors model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vendors the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vendors::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
