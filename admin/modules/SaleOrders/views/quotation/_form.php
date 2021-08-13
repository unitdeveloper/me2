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
$company        = $session->get('Rules')['comp_id'];
$fade           = ($model->customer_id)?: 'in';
 


?>
<?php $this->registerCssFile('css/sale_orders.css?v=4.06.06');?>
<div class="warper">
    <?php $form = ActiveForm::begin([
        'id' => 'Form-Quotation',
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
    <div class="row" id="accordion">
        <div class="panel-heading" style="position: relative;">
            <div class="row">
                <div class="col-xs-offset-6 text-right ">
                    <div class="col-xs-12 text-info">
                        <div><?=$model->no?></div>
                        <div><?=$model->sales_people?></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <a role="button" data-toggle="collapse"  href="#collapseOne" id="SaleOrder" ew-so-id="<?=$_GET['id']?>">
                        
                        <?php
                        if($model->customer_id==''){
                            echo '<i class="far fa-address-card"></i> '.Yii::t('common','Customer'); 
                        }else{
                            echo '<i class="far fa-address-card fa-2x text-green"></i> '.$model->customer->name.' ('.$model->customer->provincetb->PROVINCE_NAME.')';
                        }
                        ?> 
                    </a>
                </div>
                
            </div>
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
                                                    ->andWhere(new Expression('FIND_IN_SET(:owner_sales, owner_sales)'))->addParams([':owner_sales' => $session->get('Rules')['sale_code']])
                                                    ->orWhere(['id' => 909])
                                                    ->orderBy(['code' => SORT_ASC])
                                                    ->all();

                                        }




                                        //$List = arrayHelper::map($data,'id','name');
                                        $List = arrayHelper::map($data,'id', function ($element) {
                                                    return '['.$element['code'] .'] :  ' .$element['name'].' ('. $element->provincetb->PROVINCE_NAME .')';

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
                                <button type="button" class="form-control btn btn-default rule-xs-mac" data-toggle="modal" data-target="#ewPickCustomer" id="ew-modal-pick-cust"><?=$Customer ?></button>
                                <span class="input-group-btn">
                                    <button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#ewPickCustomer" id="ew-modal-pick-cust"><i class="fa fa-upload" aria-hidden="true"></i></button>
                                </span>
                                </div>
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
                                                                <a href="index.php?r=SaleOrders/quotation&SalehearderSearch[customer_id]='.$model->customer->id.'" target="_blank">
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
                            <div class="col-sm-6 col-xs-12 margin-top">
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
            
                <div class="row hidden-xs">
                    <div class="col-sm-12 text-right">

                        
                    
                    <button type="button" id="import-file" class="btn btn-default btn-flat"><i class="fa fa-hand-o-down text-warning"></i> <?=Yii::t('common','Import File')?> </button>
                    
                        
                    </div>
                </div>
            <?php } ?>
            
            <div class="SaleLine">
                <?=$this->render('_saleline',['dataProvider' => $dataProvider]);?>
            </div>
            
            
           

            <div class="row"><hr></div>
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
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model,'remark')->textarea(['rows' => '2','placeholder' => Yii::t('common','Remark')]) ?>
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
                            <div class="col-sm-5 col-xs-4">
                                <?php                                   
                                    $dataVat = VatType::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();                            
                                    $ListVat = arrayHelper::map($dataVat,'vat_value', 'name');
                                ?>
                                <?= $form->field($model,'vat_percent') ->dropDownList($ListVat)->label(Yii::t('common','Vat')); ?>
                            </div>
                            <div class="col-sm-7 col-xs-8">
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

            <div class="row hidden">
                <div class="col-sm-offset-6">
                    <div class="col-sm-8 col-xs-12">
                        
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <?php
                            
                        ?>
                        <?= $form->field($model,'status')->dropDownList([                                                
                                                'Release' => Yii::t('common','Release Status'),
                                                 
                                            ]); ?>
                    </div>
                </div>
            </div>
            <div class="row"><hr></div>
            <div class="row hidden-sm hidden-md hidden-lg submit-btn-zone">
                <div class="col-xs-12">  
                
                <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Create') : '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save'),
                    [
                        'class' => 'btn btn-success   text-center',
                        'style' => 'width:100% !important; height:40px;',
                        'data-rippleria' => true,
                ]) ?>   
                </div>               
            </div>
            
            <div class="sale-header-form"> 
                           
                <div class="row  hidden-xs">     
                    
                    <div class="col-sm-12">                         
                        <div class="form-group pull-right">
                            <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Create') : '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save'),
                                [
                                    'class' => 'btn btn-success ',
                                    'style' => 'width:150px; height:40px; font-size:16px;',
                                    'data-rippleria' => true,
                            ]) ?>
                        </div>                        
                    </div>
                     
                </div>
                <div class="row"><hr></div>
            </div>
        </div>
    </div>    
    <?php ActiveForm::end(); ?>
</div>
 

 <!-- <div class="row hidden-xs">
    <div class="col-sm-12 text-right">
        <button type="button" id="clear-line" class="btn btn-default btn-flat"><i class="fa fa-eraser text-danger"></i> <?=Yii::t('common','Clear')?></button>
    </div>
</div> -->

<div id="menuFilter" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
    <!-- Menu Filter -->
    <?= $this->render('_FilterProduct'); ?>
</div>

<br>

<div class="FilterResource" >
    <?php echo  $this->render('_FilterProductResource') ?>
</div>
<?php echo  $this->render('../modal/_pickitem',['model' => $model]) ?>
<?php echo  $this->render('../modal/__modal_pick_customer',['model' => $model]) ?>




<?php $this->registerJsFile('js/item-picker.js?v=3.03.23');?>
<?php $this->registerJsFile('js/saleorders/salequote-form.js?v=5.08.04');?>
<?php $this->registerJsFile('js/manufacturing/item_set.js?v=3.03.23');?>


<?php
 
$Yii = 'Yii';
$js =<<<JS

    const loadingDiv = `
        <div class="loading-absolute" style="display:none;">
            <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>           
        </div>`;

    $(document).ready(function() {
        setTimeout(() => {
            $('body').prepend(loadingDiv);
        }, 500);        
    });

    $('body').on('click','#import-file',function(){
        
        PopupCenter('index.php?r=SaleOrders/quotation/import-file&id='+$('#Form-Quotation').attr('data-key'),'Import',1024,800);
        
         
        
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




    var t = 0; 
    function validateForm(){    
        var d = new Date();
        var n = d.getSeconds();        
        if($('#salequoteheader-customer_id').val() === ''){
            if(t-n!=0){
                swal(
                        'ดูเหมือนว่า "ยังไม่ได้เลือกลูกค้า"',
                        'กรุณาเลือกลูกค้า',
                        'warning'
                        );              
                route("index.php?r=customers/customer/pick-customer",'GET',{search:'',id:$('#Form-Quotation').attr('data-key')},'ew-Pick-Customer');
                $('#ewPickCustomer').modal('show');
                t = n+1;
            }     
            //console.log(t+'='+n);    
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

 
    $(document).click(function(e) {

        // check that your clicked
        // element has no id=info
         
        if((e.target.type == 'number') || (e.target.type == 'text')) {
            $('.submit-btn-zone').slideUp();        
        }else{            
            $('.submit-btn-zone').slideDown();    
        }
    });
    


    $('body').on('click', '#clear-line', function () {
        if (confirm('{$Yii::t("common","Do you want to clear all the data?")}')) {
            $.ajax({
                url: 'index.php?r=SaleOrders/quotation/clear-sale-line',
                async: true,
                type: 'POST',
                data: { id: $('#Form-Quotation').attr('data-key') },
                success: function (response) {
                    var obj = $.parseJSON(response);

                    if (obj.status == 200) {
                        $('.SaleLine').find('table > tbody').remove();
                    }
                }
            });
        }
    });
    


 //---ITEMBOX CHANGE---

$('body').on('click','#back-btn',function(){
    $('.item-detail').toggle("slide", { direction: "right" }, 500);
    $("body").css({ overflow: 'inherit' });
    // $("html, body").animate({ scrollTop: 150 }, "slow");
})


$('body').on('keyup','input.update-field',function(){
    var sum = $('input.item-price').val() * $('input.item-qty').val();
    $('.item-line-amount').val(number_format(sum.toFixed(2)));
});


$('body').on('keyup','input.item-qty',function(e){
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
        $('body').find('input.item-price').select();
    }   
})

$('body').on('keyup','input.item-price',function(e){
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
        $('a#complete-btn').click();
        $(this).blur();
    }   
})

$('body').on('click','a#complete-btn',function(){
    var dataArr = new Array();
    $('input.update-field').each(function(){
        var item = {};
            item['key']     = $(this).attr('data-key');
            item['name']    = $(this).attr('name');
            item['val']     = $(this).val();
            
            dataArr.push(item);
    })

    $.ajax({
        url:'index.php?r=SaleOrders%2Fquotation%2Fupdate-line-box&id='+btoa($('#Form-Quotation').attr('data-key')),
        type:'POST',
        data: {data:dataArr},
        dataType:'JSON',
        success:function(response){
            
            $("body").css({ overflow: 'inherit' });
            $('tr[data-key="'+response.value.id+'"]').find('input').eq(0).val(response.value.data[0].val);
            $('tr[data-key="'+response.value.id+'"]').find('input[name="price"]').val(response.value.data[1].val);
            $('tr[data-key="'+response.value.id+'"]').find('input.update-desc').val(response.value.name);
            $('tr[data-key="'+response.value.id+'"]').find('.line-amount').html(number_format(response.value.sumline.toFixed(2)));
             
            $('tr[data-key="'+response.value.id+'"]').find('.text-show-calulate').html(number_format(response.value.data[0].val) + ' x '+response.value.data[1].val);
            $('tr[data-key="'+response.value.id+'"]').find('.text-show-total').html(number_format(response.value.sumline.toFixed(2)));
            
            getSumLine($('#ew-discount-amount').val(),'discount');
            $('.item-detail').toggle("slide", { direction: "right" }, 500);
        }
    })
});

//--- /.ITEMBOX CHANGE---
JS;

$this->registerJS($js,\Yii\web\View::POS_END);

?>

<?php $this->registerJsFile('https://code.jquery.com/ui/1.12.1/jquery-ui.js',['depends' => [\yii\web\JqueryAsset::className()]]);?>


