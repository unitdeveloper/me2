<?php

use yii\helpers\Html;
use yii\db\Expression;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\icons\Icon;
//use yii\grid\GridView;
//use kartik\grid\GridView;
use common\models\Customer;
use common\models\SalesPeople;
use common\models\VatType;
use common\models\AppsRules;
use admin\modules\SaleOrders\models\FunctionSaleOrder;

use kartik\widgets\DatePicker;
use admin\modules\apps_rules\models\SysRuleModels;
use yii\bootstrap\Modal;

$session = Yii::$app->session;
$rules_id = $session->get('Rules')['rules_id'];
/* @var $this yii\web\View */
/* @var $model common\models\SaleHeader */
/* @var $form yii\widgets\ActiveForm */
//use yii\widgets\Pjax;

$SalePeople     = $session->get('Rules')['sale_id'];
$company        = Yii::$app->session->get('Rules')['comp_id'];
$fade           = (isset($_GET['SearchPicItems']['Isearch'])) ?: 'in';


$Fnc            = new FunctionSaleOrder();

 
?>
<?php $this->registerCssFile('css/sale_orders.css?v=3.03.12');?>
<div class="row-">
    <?php $form = ActiveForm::begin([
        'id' => 'Form-SaleOrder',
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
        'options' => [
            'data' => ['pjax' => true],
            //'enctype' => 'multipart/form-data',
            'onsubmit' => 'return validateForm()',
            'data-key' => $model->id,
            //'name' => 'FormSaleorder'
            ]]); 
    ?>
    <div class="panel panel-danger" id="accordion">
        <div class="panel-heading" style="position: relative;">
            <a role="button" data-toggle="collapse"  href="#collapseOne" id="SaleOrder" ew-so-id="<?=$_GET['id']?>"><?= Icon::show('align-justify') ?> <?=Yii::t('common','Sale Order'); ?>  {{ orderdate }}</a>
                <!-- Next Previous -->
            <div style="position: absolute; right: 10px; top: -15px;  z-index: 500;">
                <?php
                    if(in_array($rules_id,['3'])){
                        $Prev = common\models\SaleHeader::find()->where(['<','id',$model->id])
                        ->andWhere(['comp_id' => $company])
                        ->andWhere(['sale_header.sale_id' => $SalePeople])
                        ->orderBy(['id' => SORT_DESC])->limit(1)->all();

                        $Next = common\models\SaleHeader::find()->where(['>','id',$model->id])
                        ->andWhere(['comp_id' => $company])
                        ->andWhere(['sale_header.sale_id' => $SalePeople])
                        ->orderBy(['id' => SORT_ASC])->limit(1)->all();
                    }else {
                        $Prev = common\models\SaleHeader::find()->where(['<','id',$model->id])
                        ->andWhere(['comp_id' => $company])
                        ->orderBy(['id' => SORT_DESC])->limit(1)->all();

                        $Next = common\models\SaleHeader::find()->where(['>','id',$model->id])
                        ->andWhere(['comp_id' => $company])
                        ->orderBy(['id' => SORT_ASC])->limit(1)
                        ->all();
                    }
                    $Previous = 0;
                    $PreID = 0;
                    foreach ($Prev as $value) {
                        $Previous = 'index.php?r=SaleOrders/saleorder/view&id='.$value->id;
                        $PreID = $value->id;
                    }
                    if($PreID==0) $Previous = '#';
                    $NextBt = 0;
                    $NexId  = 0;
                    foreach ($Next as $value) {
                        $NextBt = 'index.php?r=SaleOrders/saleorder/view&id='.$value->id;
                        $NexId = $value->id;
                    }
                    if($NexId==0) $NextBt = '#';
                ?>
                <ul class="page-btn">
                    <li><a href="<?=$Previous;?>" class="btn btn-xs btn-flat btn-default-ew" data-rippleria><i class="fa fa-step-backward" aria-hidden="true"></i></a></li>
                    <li><a href="<?=$NextBt;?>" class="btn btn-xs btn-flat btn-default-ew" data-rippleria><i class="fa fa-step-forward" aria-hidden="true"></i></a></li>
                </ul>
            </div>
            <!-- /.Next Previous -->
        </div>
        <div class="panel-body">
                <div id="collapseOne" class="panel-collapse collapse <?=$fade;?>" role="tabpanel" aria-labelledby="headingOne">
                <div class="nav-tabs-custom" ng-init="noseries='<?= $model->no ?>'; orderdate='<?= date('d/m/Y', strtotime($model->order_date))?>'">

                    <ul class="nav nav-tabs">
                        <li class="active">
                        <a href="#General" data-toggle="tab" aria-expanded="true">
                            <?= Icon::show('user', [], Icon::BSG) ?>
                            <?=Yii::t('common','Gen<span class="hidden-xs">eral</span>'); ?> </a>
                        </li>
                        <li class=""><a href="#Invoicing" data-toggle="tab" aria-expanded="false">
                            <?= Icon::show('barcode', [], Icon::BSG) ?>
                            <?=Yii::t('common','Inv<span class="hidden-xs">oicing</span>'); ?> </a></li>
                        <li class=""><a href="#Shipping" data-toggle="tab" aria-expanded="false">
                            <?= Icon::show('shopping-cart', [], Icon::BSG) ?>
                            <?=Yii::t('common','Ship<span class="hidden-xs">ping</span>'); ?></a></li>


                    </ul>
                    <?= $form->field($model, 'no')->hiddenInput(['ng-model'=> 'noseries','readonly' => true])->label(false) ?>
                    <div class="tab-content">
                    <div class="tab-pane  active" id="General">
                        <div class="row">

                            <div class="col-sm-6 col-xs-12">
                                <div class=" ">

                                <?PHP $model->customer_code = $model->customer_id; ?>
                                    <?php


                                        // function search
                                        if(in_array($rules_id,SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','search'))){
                                        //if($rules_id==1 || $rules_id == 4 || $rules_id == 7){ // 4 Sale admin,7 Sale Manager

                                            $data = Customer::find()
                                                    ->where(['comp_id' => $company])
                                                    ->andWhere(['<>','status','0'])
                                                    ->orderBy(['code' => SORT_ASC])
                                                    ->all();
                                        }else {
                                            $data = Customer::find()
                                                    ->where(['comp_id' => $company])
                                                    ->andWhere(['<>','status','0'])
                                                    ->andWhere(new Expression('FIND_IN_SET(:owner_sales, owner_sales)'))
                                                    ->addParams([':owner_sales' => $session->get('Rules')['sale_code']])
                                                    ->orderBy(['code' => SORT_ASC])
                                                    ->all();

                                        }




                                        //$List = arrayHelper::map($data,'id','name');
                                        $List = arrayHelper::map($data,'id', function ($element) {
                                                    return '['.$element['code'] .'] : ' .$element['name'].' ('.$element->provincetb->PROVINCE_NAME.')';

                                                });


                                    ?>
                                    <?=$form->field($model, 'customer_id')
                                                    ->dropDownList($List,[
                                                        'class' => 'customer_id',
                                                        'prompt'=>'เลือกลูกค้า',
                                                        //'data-toggle'=>"modal" ,
                                                        //'data-target'=>"#ewPickCustomer",
                                                        //'id'=>"ew-modal-pick-cust",
                                                    ]
                                                )->label(Yii::t('common','Customer'));


                                    ?>
                                    </div>
                            </div>

                            <div class="col-sm-6 col-xs-12">

                                    <?php
                                        if($model->customer_id!=NULL)
                                        {
                                            $Customer = $model->customer->name;
                                        }else {
                                            $Customer = Yii::t('common','Select Customer');
                                        }
                                    ?>

                                    <label><?=Yii::t('common','Select Customer');?></label>
                                    <div class="input-group">
                                    <button type="button" class="form-control btn btn-default" data-toggle="modal" data-target="#ewPickCustomer" id="ew-modal-pick-cust"><?=$Customer ?></button>
                                    <span class="input-group-btn">
                                        <button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#ewPickCustomer" id="ew-modal-pick-cust"><i class="fa fa-upload" aria-hidden="true"></i></button>
                                    </span>
                                    </div>


                                <!--  <a href="#" class="form-control btn btn-default" data-toggle="modal" data-target="#ewPickCustomer" id="ew-modal-pick-cust">เลือกลูกค้า</a> -->




                            </div>
                        </div>


                        <div class="row">
                            <div class="col-sm-6">


                                <div class=" ">

                                    <div class="col-md-12">
                                        <div class="row">


                                            <?php

                                                $tumbol = '';
                                                $amphur = '';
                                                $province = '';
                                                $zipcode = '';

                                                if(!empty($model->customer->district)){
                                                    $tumbol = 'ต.'.$model->customer->districttb->DISTRICT_NAME;

                                                }

                                                if(!empty($model->customer->city)){
                                                    $amphur = 'อ.'.$model->customer->citytb->AMPHUR_NAME;

                                                }

                                                if(!empty($model->customer->province)){
                                                    $province = 'จ.'.$model->customer->provincetb->PROVINCE_NAME;

                                                }

                                                if(!empty($model->customer->postcode)){
                                                    $zipcode = $model->customer->postcode;

                                                }

                                                if($model->customer_id != '')
                                                {
                                                    $ShipAddr = $model->customer->address.' '. $model->customer->address2;
                                                    $ShipAddr.= $tumbol.''.$amphur.''.$province.''.$zipcode;
                                                }else {
                                                    $ShipAddr = NULL;
                                                }



                                            ?>


                                            <?PHP
                                            if(!empty($model->customer->district)){

                                                $distric = $model->customer->districttb->DISTRICT_NAME;
                                                $city = $model->customer->citytb->AMPHUR_NAME;
                                                $province = $model->customer->provincetb->PROVINCE_NAME;
                                                $postcode = $model->customer->postcode;

                                                $ShipAddress = $model->customer->address;
                                                $ShipAddress.= ' ต.'.$distric.' อ.'.$city.' จ.'.$province.' '.$postcode;

                                                echo '<div class=" " style="margin: 10px 0px 10px 0px;">
                                                        <a href="#"  data-toggle="collapse" data-target="#customer-infomation">
                                                            <i class="fa fa-info-circle" aria-hidden="true"></i> '.Yii::t('common','Infomation').'
                                                        </a>
                                                    </div>';
                                                echo '<div class="well collapse" id="customer-infomation">';
                                                //echo '   <h4>Infomation</h4>';
                                                echo '  <div><b>CODE : '.$model->customer->code.'</b></div>';
                                                echo '  <div>'.$model->customer->name.'</div>';
                                                echo '  <div>'.$model->customer->address.' ต.'.$distric.' อ.'.$city.' จ.'.$province.' '.$postcode.'</div>';
                                                echo '  <div> Phone : '.$model->customer->phone. ' Fax : '.$model->customer->fax.'</div>';
                                                echo '  <h4>'.Yii::t('common','Credit').'</h4>';
                                                echo '  <div class="row">';
                                                echo '      <div class="col-xs-4"><b>'.Yii::t('common','Limit').' : </b></div>
                                                            <div class="col-xs-8 text-right">'.number_format($model->customer->credit_limit,2).'</div>';

                                                echo '      <div class="col-xs-4"><b>'.Yii::t('common','Usage').' : </b></div>
                                                            <div class="col-xs-8 text-right">
                                                                <a href="index.php?r=SaleOrders/saleorder&SalehearderSearch[customer_id]='.$model->customer->id.'" target="_blank">
                                                                '.number_format($model->customer->getCredit()->PayIn,2).'
                                                                </a>
                                                            </div>';

                                                echo '      <div class="col-xs-4"><b>'.Yii::t('common','Available').' : </b></div>
                                                            <div class="col-xs-8 text-right">'.$model->getCreditAvailable().'</div>';
                                                    
                                                echo '<label class="col-xs-4 margin-top">'.Yii::t('common','Po. Customer').' : </label>
                                                    <div class="col-xs-8">'.$form->field($model,'ext_document')
                                                        ->textInput(['placeholder' => Yii::t('common','P/O of customer.'),'class' => 'pull-right'])
                                                        ->label(false).'</div>';
                                                echo '  </div>';
                                                echo '</div>';

                                            }
                                            if($model->sale_address == ""){
                                                $model->sale_address = $ShipAddr;
                                            }

                                            if($model->bill_address == ""){
                                                $model->bill_address = $model->sale_address;
                                            }

                                            if($model->ship_address == ""){
                                                $model->ship_address = $ShipAddr;
                                            }
                                            ?>

                                        </div>
                                    </div>
                                    <!-- col-sm-12 -->



                                </div>


                            </div>





                            <div class="col-xs-6">
                                <div class="<?=($rules_id == 3)? 'hidden' : '' ?>">
                                    
                                    <?php
                                        //if($model->sales_people=='') $model->sales_people = '006';


                                        $Sales = SalesPeople::find()
                                        ->where(['comp_id' => $company])
                                        ->andWhere(['status' => 1])
                                        ->orderBy(['code' => SORT_ASC])
                                        ->all();



                                        $salespeople = arrayHelper::map($Sales,'id', function ($element) {
                                                        return '['.$element['code'] .']  ' .$element['name'];
                                                    });



                                    echo $form->field($model, 'sale_id') ->dropDownList($salespeople,
                                                    [
                                                        'class' => 'sale_id',
                                                        "disabled"=> ($rules_id == 3),
                                                        'prompt'=>'- เลือก Sales -',
                                                    ]
                                                )->label(Yii::t('common','Sales'));

                                    // "disabled"=> ($rules_id != 7 && $rules_id != 4 && $rules_id != 1 ),
                                    ?>
                                </div>



                            </div>




                        </div>


                    </div>



                    <!-- /.tab-pane -->
                    <div class="tab-pane fade" id="Invoicing">
                        <!-- The timeline -->
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <div class="row">

                                    <div class="col-xs-6">
                                        <?PHP

                                            // Remove

                                            // if($model->payment_term === '')
                                            // {
                                            //     $term = 30;
                                            // }else {
                                            //     $term = $model->payment_term;
                                            // }

                                        ?>
                                        <div class="input-group">
                                            <?php
                                            //  $form->field($model,'payment_term',[
                                            //     'addon' => ['append' => ['content'=> Yii::t('common','Day')]]
                                            //     ])->textInput(['class'=>'form-control',
                                            // 'placeholder' => '30 Day',
                                            // 'value' => $term,
                                            // ])
                                            ?>


                                        </div>

                                    </div>
                                    <div class="col-xs-6">

                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">

                                <?= $form->field($model, 'bill_address')->textInput() ?>
                                <?= $form->field($model, 'sale_address')->hiddenInput()->label(false) ?>
                            </div>


                        </div>

                    </div>

                    <!-- /.tab-pane -->

                    <div class="tab-pane fade" id="Shipping">
                        <div class="row">
                            <div class="col-md-6">
                            <?php echo $form->field($model, 'ship_date')->widget(DatePicker::classname(), [
                                        'options' => ['placeholder' => 'Enter Ship date ...'],
                                        'value' => $model->ship_date,
                                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                        'pluginOptions' => [
                                            'format' => 'yyyy-mm-dd',
                                            //'format' => 'dd/mm/yyyy',
                                            'autoclose'=>true
                                        ]
                                    ]); ?>
                            <?= $form->field($model, 'ship_address')->textInput() ?>
                            </div>

                        </div>
                        <div class="row">

                            <div class="col-sm-12">
                                Create By : [<?= $model->user_id ?>] <?= $model->users->username ?>
                            </div>

                            <div class="col-sm-12">
                                Update By : [<?= $model->update_by ?>] <?= $model->update_date ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>

            </div>
            <?php 
            $AdminModerntrade = SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Modern-Trade');
            if(in_array(Yii::$app->session->get('Rules')['rules_id'],$AdminModerntrade)){   ?>
            
                <div class="row">
                    <div class="col-sm-12 text-right">

                        
                    
                    <button type="button" id="import-file" class="btn btn-default btn-flat"><i class="fa fa-hand-o-down text-warning"></i> <?=Yii::t('common','Import File')?> </button>
                    
                        
                    </div>
                </div>
            <?php } ?>

            <div class="SaleLine">
                <?=$this->render('_saleline_editable',['dataProvider' => $dataProvider]);?>
            </div>
            <div class="row">
                <div class="col-sm-12" style="margin-top:-14px;">
                    <button type="button" id="clear-line" class="btn btn-default btn-flat"><i class="fa fa-eraser text-danger"></i> <?=Yii::t('common','Clear')?></button>
                </div>
            </div>
            <hr>
            <div class="render-search-item"></div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="row">

                        <div class="col-xs-12" style="margin: 10px 0px 10px 0px;">
                            <a href="#payment"  data-toggle="collapse" data-target="#customer-payment">
                                <i class="fa fa-credit-card" aria-hidden="true"></i> <?=Yii::t('common','Payment')?>
                            </a>
                        </div>
                        <div id="customer-payment" class="collapse fade">
                            <div class="col-sm-5 col-xs-5">
                                <?= $form->field($model, 'payment_term')->dropDownList([
                                    '0'=> Yii::t('common','Cash'),
                                    '7'=> '7 '.Yii::t('common','Day'),
                                    '15' => '15 '.Yii::t('common','Day'),
                                    '30' => '30 '.Yii::t('common','Day'),
                                    '45' => '45 '.Yii::t('common','Day'),
                                    '60' => '60 '.Yii::t('common','Day'),
                                    '90' => '90 '.Yii::t('common','Day'),
                                    ]) ?>
                            </div>

                            <div class="col-sm-7 col-xs-7">
                            <?php
                            // if (strpos($ua['userAgent'], 'iPhone') !== false)
                            // {

                            //     echo $form->field($model, 'paymentdue')->textInput(['type'=>'date']);

                            // }else {
                            if($model->isNewRecord) $model->paymentdue = date('Y-m-d');

                                    echo $form->field($model, 'paymentdue')->widget(DatePicker::classname(), [
                                    'options' => ['placeholder' => 'Payment due date ...','style' => 'background-color:rgba(221, 114, 101, 0.78);'],
                                    'value' => $model->paymentdue,
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'pluginOptions' => [
                                        //'format' => 'dd/mm/yyyy',
                                        'format' => 'yyyy-mm-dd',
                                        'autoclose'=>true
                                    ]
                                ]);
                            //}
                            ?>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-sm-6" style=" ">

                    <div class="row">
                        <div class="col-xs-12" style="margin: 10px 0px 10px 0px;">
                            <a href="#payment"  data-toggle="collapse" data-target="#order-summary" class="text-red">
                                <i class="fa fa-credit-card" aria-hidden="true"></i> <?=Yii::t('common','Summary')?>
                            </a>
                        </div>
                    </div>
                    <div id="order-summary" class="collapse in">
                        <div class="row">
                            <div class="col-sm-5 col-xs-5">
                                <?php                                
                                    $dataVat = VatType::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();                            
                                    $ListVat = arrayHelper::map($dataVat,'vat_value', 'name');
                                ?>
                                <?= $form->field($model,'vat_percent') ->dropDownList($ListVat)->label(Yii::t('common','Vat')); ?>
                            </div>
                            <div class="col-sm-7 col-xs-6">
                                <span id="Exc-vat" >
                                    <?=$form->field($model, 'include_vat')->dropDownList(['0' => Yii::t('common','Include Vat'),'1' => Yii::t('common','Exclude Vat')]);?>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="ew-sum-line">
                                <?php echo $this->render('_sum_line',['model' => $model,'dataProvider' => $dataProvider]); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-offset-6">
                    <div class="col-xs-8">
                        <?= $form->field($model,'remark')->textarea(['rows' => '2','placeholder' => Yii::t('common','Remark')]) ?>
                    </div>
                    <div class="col-xs-4">
                        <?php
                            $Rules = Yii::$app->session->get('Rules')['rules_id'];

                            //var_dump(SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','Allow-Sent-Approve'));

                            // function Allow Sent Approve
                            if(in_array($Rules,SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','Allow-Sent-Approve'))){
                            //if($Rules == 3 ){
                                $SatusList = [
                                                'Open' => Yii::t('common','Open Status'),
                                                'Release' => Yii::t('common','Release Status'),
                                                'Cancel' => Yii::t('common','Cancel'),
                                            ];
                            }else {
                                $SatusList = [
                                                'Open' => Yii::t('common','Open Status'),
                                                'Release' => Yii::t('common','Release Status'),
                                                'Checking' => Yii::t('common','Checking'),
                                                //'Shiped' => Yii::t('common','Shiped'),
                                                //'Invoiced' => Yii::t('common','Invoiced'),
                                                'Cancel' => Yii::t('common','Cancel'),
                                            ];
                            }
                        ?>
                        <?= $form->field($model,'status')->dropDownList($SatusList); ?>
                    </div>
                </div>
            </div>
            <div class="sale-header-form">
                <div class="row">
                    
                     
                    <div class="col-sm-offset-6">
                        <div class="col-xs-8  col-sm-10"></div>
                        <div class="col-xs-4 col-sm-2">
                            

                            <div class="form-group pull-right ">
                                <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Create') : '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save'),
                                    [
                                        'class' => 'btn btn-success ',
                                        //'onclick' => "$('form[id=Form-SaleOrder]').submit()",
                                        'data-rippleria' => true,
                                        //'name' => 'btn-submit'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>
<div id="menuFilter" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
    <!-- Menu Filter -->
    <?= $this->render('_FilterProduct'); ?>
</div>
<!-- <div class="col-xs-12">
    <a role="button" data-toggle="collapse"  href="#menuFilter">
        <i class="fa fa-arrow-up" aria-hidden="true"></i> <?= Yii::t('common','Filter Product'); ?>
    </a>
</div> -->
<br>

<div class="FilterResource">
    <?php echo  $this->render('_FilterProductResource') ?>
</div>
 

 


<?php
 
$Yii = 'Yii';
$js =<<<JS


    $('body').on('click','#import-file',function(){
        
        PopupCenter('index.php?r=SaleOrders/saleorder/import-file&id='+$('#SaleOrder').attr('ew-so-id'),'Import',1024,800);
        
         
        
    });

    function popitup(url,windowName, w, h) {

        newwindow=window.open(url,windowName,'height='+w+',width='+h+',top=0, left=20');
        if (window.focus) {newwindow.focus()}
        return false;
    }  


    function PopupCenter(url, title, w, h) {
        // Fixes dual-screen position                         Most browsers      Firefox
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

        var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;
        var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

        // Puts focus on the newWindow
        if (window.focus) {
            newWindow.focus();
        }
    }

    $('body').on('click','#clear-line',function(){
        if (confirm('{$Yii::t("common","Do you want to clear all the data?")}')) {
            $.ajax({
                url:'index.php?r=SaleOrders/saleorder/clear-sale-line',
                async:true,
                type: 'POST',
                data:{id:$('#SaleOrder').attr('ew-so-id')},
                success:function(response){
                    var obj = $.parseJSON(response);
                    
                    if(obj.status == 200){
                        $('.SaleLine').find('table > tbody').remove();
                    }
                }
            });
        }
    });


    var t = 0; 
    function validateForm(){    
        var d = new Date();
        var n = d.getSeconds();        
        if($('#saleheader-customer_id').val() === ''){
            if(t-n!=0){
                swal(
                        'ดูเหมือนว่า "ยังไม่ได้เลือกลูกค้า"',
                        'กรุณาเลือกลูกค้า',
                        'warning'
                        );              
                route("index.php?r=customers/customer/pick-customer",'GET',{search:'',id:$('#SaleOrder').attr('ew-so-id')},'ew-Pick-Customer');
                $('#ewPickCustomer').modal('show');
                t = n+1;
            }     
            console.log(t+'='+n);    
            return false;
        }else if($('.beforediscount').attr('data') <= 0){
            
            if(t!=n){
                
                swal(
                    '"ใบงาน ไม่มีมูลค่า"',
                    'กรุณาเลือกสินค้า และ ใส่ราคาให้ถูกต้อง',
                    'warning'
                    );
                    t = n+1;
            
            } 
            return false;
        }else{
            return true;        
            
        }
        

    }

JS;

$this->registerJS($js,\yii\web\View::POS_END);

?>


<?php $this->registerJsFile('js/item-picker.js?v=3.03.23');?>
<?php $this->registerJsFile('js/saleorders/saleorder-form.js?v=5.05.28');?>
<?php $this->registerJsFile('js/manufacturing/item_set.js?v=3.03.23');?>


<?php echo  $this->render('../modal/_pickitem',['model' => $model]) ?>
<?php echo  $this->render('../modal/__modal_pick_customer',['model' => $model]) ?>