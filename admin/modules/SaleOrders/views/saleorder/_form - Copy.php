<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use kartik\icons\Icon;

use kartik\grid\GridView;
use common\models\Customer;
use common\models\SalesPeople;
use common\models\VatType;

use common\models\AppsRules;

use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
/* @var $this yii\web\View */
/* @var $model common\models\SaleHeader */
/* @var $form yii\widgets\ActiveForm */
//use yii\widgets\Pjax;


$company    = Yii::$app->session->get('Rules')['comp_id'];
$Me         = AppsRules::find()->where(['user_id' => Yii::$app->user->identity->id])->one();

function favolite($model)
{   
    if($model->interesting=='Enable'){
        $star = '<div class="star-div"><i class="fa fa-star" aria-hidden="true" style="color: #f4d341;  "></i></div>';
    }else {
        $star = NULL;
    }
    return $star;

}


if(isset($_GET['SearchPicItems']['Isearch']))
{
   $fade = NULL;
}else {
    $fade = 'in';
}  

?>

<?php $form = ActiveForm::begin(); ?>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>

<div class="panel panel-danger" id="accordion" ng-init="Title='Sale Order'">
    <div class="panel-heading">
    <a role="button" data-toggle="collapse"  href="#collapseOne" id="SaleOrder" ew-so-id="<?=$_GET['id']?>"><?= Icon::show('align-justify') ?> Sale Order </a>
    </div>
     
    <div class="panel-body">
    <div id="collapseOne" class="panel-collapse collapse <?=$fade;?>" role="tabpanel" aria-labelledby="headingOne">    
    <div class="nav-tabs-custom" ng-init="noseries='<?= $model->no ?>'; orderdate='<?= date('d/m/Y', strtotime($model->order_date))?>'">
        <ul class="nav nav-tabs">
            <li class="active">
            <a href="#General" data-toggle="tab" aria-expanded="true"> 
                <?= Icon::show('user', [], Icon::BSG) ?>
                General</a>
            </li>
            <li class=""><a href="#Invoicing" data-toggle="tab" aria-expanded="false">
                <?= Icon::show('barcode', [], Icon::BSG) ?>
                Invoicing</a></li>
            <li class=""><a href="#Shipping" data-toggle="tab" aria-expanded="false">
                <?= Icon::show('shopping-cart', [], Icon::BSG) ?>
            Shipping</a></li>

             
        </ul>
        <div class="tab-content">
        <div class="tab-pane  active" id="General">
            <div class="row">
                <div class="col-md-7">
                    <div class="row">
                         
                        <?= $form->field($model, 'no')->hiddenInput(['ng-model'=> 'noseries','readonly' => true])->label(false) ?>
                          
                         
                        <div class="col-md-5">                         
                            <?PHP $model->customer_code = $model->customer_id; ?>  
                            <?php 
                                    $data = Customer::find()
                                            ->where(['comp_id' => $company])
                                            ->all();
                                    //$List = arrayHelper::map($data,'id','name'); 
                                    $List = arrayHelper::map($data,'id', function ($element) {
                                                return $element['code'] .'  ' .$element['name'];
                                                 
                                            }); 
                                     
                                    
                            ?>  
                            <?= $form->field($model, 'customer_code')
                                            ->dropDownList($List,[
                                                'data-live-search'=> "true",
                                                'class' => 'selectpicker customer_code',
                                                'prompt'=>'เลือกลูกค้า',
                                            ]
                                        );
                                        

                            ?>

 
                            
                        </div>
                        <div class="col-md-3">
                            <?php 
                                    $dataVat = VatType::find()
                                            ->where(['comp_id' => $company])
                                            ->all();
                                    //$List = arrayHelper::map($data,'id','name'); 
                                    $ListVat = arrayHelper::map($dataVat,'id', 'name'); 
                                     
                                    
                            ?>  
                            <?= $form->field($model,'vat_type') ->dropDownList($ListVat); ?>
                        </div>
                        <div class="col-md-4"> 
                            <?php 
                                if($model->sales_people=='') $model->sales_people = '006';
                                 
                                $Sales = SalesPeople::find()
                                    ->where(['comp_id' => $company])
                                    ->all();

                                $salespeople = arrayHelper::map($Sales,'code', function ($element) {
                                                return $element['code'] .'  ' .$element['name'];
                                                 
                                            }); 

                            ?>
                            <?= $form->field($model, 'sales_people') ->dropDownList($salespeople,
                                            [
                                                'data-live-search'=> "true",
                                                'class' => 'selectpicker sales_code',
                                                'prompt'=>'- เลือก Sales -',
                                            ]
                                        ); ?>  
                        </div>
                    </div>
                    <div class="row">
                       
                        <div class="col-md-12">
                        <?= $form->field($model, 'customer_id',['enableAjaxValidation' => true])->hiddenInput()->label(false) ?>   
                        <div><?PHP if(!empty($model->customer->district)){

                                        $distric = $model->customer->districttb->DISTRICT_NAME;
                                        $city = $model->customer->citytb->AMPHUR_NAME;
                                        $province = $model->customer->provincetb->PROVINCE_NAME;
                                        $postcode = $model->customer->postcode;

                                        $ShipAddress = $model->customer->address.'<br>';
                                        $ShipAddress.= ' ต.'.$distric.' อ.'.$city.' จ.'.$province.' '.$postcode;

                                      echo '<b>Customer Code : </b>'.$model->customer->code.'<br>';  
                                      echo '<b>Customer Name : </b>'.$model->customer->name.'<br>';
                                      echo '<b>Address : </b>'.$model->customer->address;
                                      //echo ' ต.'.$distric.' อ.'.$city.' จ.'.$province.' '.$postcode.;
                                      echo  '<br>  Phone : '.$model->customer->phone. ' Fax : '.$model->customer->fax;
                                    }else {
                                        $ShipAddress = $model->sale_address;
                                    }   
                              if($model->sale_address == ""){
                                $ShipAdd = $ShipAddress;
                              }else {
                                $ShipAdd = $model->sale_address;
                              }

                              if($model->bill_address == ""){
                                $BillAdd = $model->sale_address;
                              }else {
                                $BillAdd = $model->bill_address;
                              }
                             ?> 
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    
                    <div class="col-xs-4 col-sm-6">Order No. :</div><div class="col-xs-8 col-sm-6"> <b>{{ noseries }}</b></div>
                    <div class="col-xs-4 col-sm-6">Order Date. :</div><div class="col-xs-8 col-sm-6"> <b>{{ orderdate }}</b></div>
                    <div class="col-xs-4 col-sm-6">Payment Due. :</div><div class="col-xs-8 col-sm-6"> <b>{{ PaymentDue }}</b></div>
                     

                </div>
            </div>
        </div>

        <!-- /.tab-pane -->
        <div class="tab-pane fade" id="Invoicing">
                <!-- The timeline -->
                <div class="row">
                <div class="col-md-6">
                     <?= $form->field($model, 'sale_address')->textInput(['value' => $ShipAdd]) ?>

                    <?= $form->field($model, 'bill_address')->textInput(['value' => $BillAdd]) ?>

                    

                     
                     

                   


                
                </div>
                <div class="col-md-6">
                     
                <?= $form->field($model,'payment_term')->textInput(); ?>
                <?= $form->field($model,'ext_document')->textInput(); ?>
                </div>
                </div>

        </div>

        <!-- /.tab-pane -->

        <div class="tab-pane fade" id="Shipping">
            <div class="row">
                <div class="col-md-6"> 
                <?php echo $form->field($model, 'ship_date')->widget(DatePicker::classname(), [
                            'options' => ['placeholder' => 'Enter Ship date ...'],
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'autoclose'=>true
                            ]
                        ]); ?>
                <?= $form->field($model, 'ship_address')->textInput(['value' => $ShipAddress]) ?>
                </div>
            </div>
        </div>
         <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
    </div>

</div>
 
<div class="SaleLine"> 
    
   <?php echo $this->render('_saleline',['dataProvider' => $dataProvider]); ?>
  
</div>


    
    <div class="sale-header-form">

       
         

        <div class="form-group text-right">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

 

    </div>
</div>
</div>
<?php ActiveForm::end(); ?>



<div id="menuFilter" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne"> 
    <!-- Menu Filter -->
    <?= $this->render('_FilterProduct'); ?>
</div>

<div class="col-xs-12">
  <a role="button" data-toggle="collapse"  href="#menuFilter">
  <i class="fa fa-arrow-up" aria-hidden="true"></i> <?= Yii::t('common','Filter Product'); ?></a>
</div>
<br>

<div class="FilterResource">    
    <?php echo  $this->render('_FilterProductResource') ?>
</div> 







 

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog" >
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Product Infomation</h4>
      </div>
      <div class="Smooth-Ajax">
      <div class="modal-body" >

        <p>Something wrong.</p>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>



<?php echo  $this->render('_script_js',['model' => $model]) ?>




 
 