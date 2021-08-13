<?php

use yii\helpers\Html;
use yii\db\Expression;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\icons\Icon;
use common\models\Customer;
use common\models\SalesPeople;
use common\models\VatType;
use kartik\widgets\DatePicker;
use admin\modules\apps_rules\models\SysRuleModels;

$session        = Yii::$app->session;
$rules_id       = $session->get('Rules')['rules_id']; 
$company        = $session->get('Rules')['comp_id'];
$fade           = ($model->customer_id)?: 'in';

?>
 
<?php $this->registerCssFile('css/sale_orders.css?v=4.11.13');?>
<div class="warper" style="position: relative;">
    <?php $form = ActiveForm::begin([
        'id' => 'Form-SaleReturnOrder',
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
                <div class="col-sm-5">
                    <h3>
                    <?=$form->field($model, 'return_type')
                                ->dropDownList([
                                    '1' => Yii::t('common', 'Return to customer'),
                                    '2' => Yii::t('common', 'Return from customer')
                                ],
                                [
                                    'class' => 'return_type', 
                                    'style' => 'font-size: 16px; color:red; font-family: saraban, roboto;'
                                    //'prompt'=>'- เลือก Sales -'
                                ]
                            )->label(false);
                    ?>
                    </h3>
                    <div class="row">
                        <div class="col-xs-12 mt-10">
                            <div class="row">
                                <div class="col-xs-12 img-return" style="<?=$model->return_type==1 ? ' ' : 'display: none;';?>">
                                    <div class="col-xs-4"><i class="fas fa-store-alt fa-4x text-green"></i>  </div>
                                    <div class="col-xs-4 text-center"><i class="fas fa-arrow-left fa-4x text-yellow"></i></div>
                                    <div class="col-xs-4 text-right"><i class="fas fa-cube fa-4x "></i></div> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 img-receive" style="<?=$model->return_type==2 ? ' ' : 'display: none;';?>">
                                    <div class="col-xs-4"><i class="fas fa-cube fa-4x  "></i></div>
                                    <div class="col-xs-4 text-center"><i class="fas fa-arrow-right fa-4x text-yellow"></i></div>
                                    <div class="col-xs-4 text-right"><i class="fas fa-industry fa-4x text-green"></i></div> 
                                </div>
                            </div>                            
                        </div>
                    </div>
                </div>
                <div class="col-sm-offset-6  text-right ">
                    <div class="col-xs-12 text-info ">
                        <a role="button" data-toggle="collapse" href="#head-doc" >                             
                            <div>[<?=$model->sales_people?>]  <?=$model->sales ? $model->sales->name : ''; ?></div>
                        </a>
                    </div>
                    <div class="collapse" id="head-doc">                                               
                        <div class="col-md-6 col-xs-8 pull-right <?=in_array($rules_id,SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','search')) ? ' ' : 'hidden'; ?>">
                            <?php echo $form->field($model, 'order_date')->widget(DatePicker::classname(), [
                                'options' => ['placeholder' => 'Enter Order date ...'],
                                'value' => $model->order_date,
                                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'autoclose'=>true,
                                    'remove' => false
                                ]
                            ]); ?>
                        </div>
                    </div>                     
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3 pull-right">
                    <?= $form->field($model, 'ext_document')->textInput()->label(Yii::t('common','Ref No')) ?>
                </div>
            </div> 
            <div class="row">
                <div class="col-xs-12">
                    <a role="button" data-toggle="collapse"  href="#collapseOne" id="SaleOrder" ew-so-id="<?=Yii::$app->request->get('id')?>">                        
                        <?php
                        if($model->customers){
                            echo '<i class="far fa-address-card fa-2x text-green"></i> '.$model->customers->name.' ('.$model->customers->locations->province.')';
                        }else{
                            echo '<i class="far fa-address-card"></i> '.Yii::t('common','Customer'); 
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
                        
                    </ul>
                    
                    <?= $form->field($model, 'no')->hiddenInput(['ng-model'=> 'noseries','readonly' => true])->label(false) ?>
                    <div class="tab-content">
                    <div class="tab-pane  active" id="General">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <?php
                                    if($model->customers){
                                    
                                        $Customer = $model->customers->name .' ('. $model->customers->locations->province.')';
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
                            <div class="col-sm-6 col-xs-12">
                           
                                <div class="<?=($rules_id == 3)? 'hidden' : '' ?>">                                    
                                    <?php                                        

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
                                     
                                <?PHP $model->customer_code = $model->customer_id; ?>
                                    <?php
                                    if($rules_id == 3){                                     
                                        
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
                                                        ->orWhere(['id' => 909])
                                                        ->orderBy(['code' => SORT_ASC])
                                                        ->all();

                                            }

                                            //$List = arrayHelper::map($data,'id','name');
                                            $List = arrayHelper::map($data,'id', function ($element) {
                                                        return '['.$element['code'] .'] :  ' .$element['name'].' ('. $element->locations->province.')';

                                                    });

                                        
                                        echo $form->field($model, 'customer_id')
                                                        ->dropDownList($List,[
                                                            'class' => 'customer_id',
                                                            'prompt'=>'เลือกลูกค้า',
                                                            //'data-toggle'=>"modal" ,
                                                            //'data-target'=>"#ewPickCustomer",
                                                            //'id'=>"ew-modal-pick-cust",
                                                        ]
                                                    )->label(Yii::t('common','Customer'));
                                    }else{
                                        echo $form->field($model, 'customer_id')->hiddenInput(['readonly' => true])->label(false);
                                    }

                                    ?>
                                     
                        
                              
                                
                                
                            
                               
                            
                        </div>
                         
                        </div>
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane fade" id="Invoicing">
                        <!-- The timeline -->
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        
                                        <?= $form->field($model, 'sale_address')->hiddenInput()->label(false) ?>
                                    </div>                                    
                                </div>
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
             
                  
                        
            <div class="SaleLine"> </div>            
            <div class="row"><hr /></div>
            <div class="render-search-item" style="border:0px !important;"></div>
            <div class="row hidden">
                <div class="col-md-6 col-sm-4">
                    <div class="row">

                        <div class="col-xs-12" style="margin: 10px 0px 10px 0px;">
                            <a href="#payment"  data-toggle="collapse" data-target="#customer-payment">
                                <i class="fa fa-credit-card" aria-hidden="true"></i> <?=Yii::t('common','Payment')?>
                            </a>
                        </div>
                        <div id="customer-payment" class="collapse fade">
                            <div class="col-md-5 col-sm-12 col-xs-5">
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

                            <div class="col-md-5 col-sm-12 col-xs-7">
                            <?php
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
                            ?>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-8" style=" ">
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
                            <div class="col-sm-7 col-xs-7">
                                <span id="Exc-vat" >
                                    <?=$form->field($model, 'include_vat')->dropDownList(['0' => Yii::t('common','Include Vat'),'1' => Yii::t('common','Exclude Vat')]);?>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="ew-sum-line"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                
                <div class="col-sm-8 col-xs-12">
                     
                    <?php echo $form->field($model, 'remark')->widget(\yii\redactor\widgets\Redactor::className(), [
                            'clientOptions' => [
                                'imageManagerJson' => ['/redactor/upload/image-json'],
                                'imageUpload' => ['/redactor/upload/image'],
                                'fileUpload' => ['/redactor/upload/file'],
                                'lang' => 'th',
                                'plugins' => ['fontcolor']
                            ]
                        ])?>
                </div>
                    
            </div>
            <div class="row"><hr/></div>
            <div class="row hidden-sm hidden-md hidden-lg submit-btn-zone">
                <div class="col-xs-12">  
                    
                

                <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o" ></i> '.Yii::t('common', 'Create') : '<i class="fa fa-floppy-o" ></i> '.Yii::t('common', 'Save'),
                    [
                        'class' => 'btn btn-success   text-center',
                        'style' => 'width:100% !important; height:40px;',
                        'data-rippleria' => true,
                ]) ?>   

                </div>               
            </div>
            
            <div class="sale-header-form" style="
                                            position: fixed;
                                            bottom: -20px;
                                            border-top: 1px solid #ccc;
                                            width: 100%;
                                            background-color: rgba(239, 239, 239, 0.9);
                                            padding: 10px 10px 15px 10px;
                                            right: 0px;
                                            text-align: right;
                                            z-index:100;
                                            " >                            
                <div class="row  hidden-xs">                         
                    <div class="col-sm-12">    
                        <?= Html::a('<i class="fa fa-print" ></i> '.Yii::t('common', 'Print'),['print', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-info',
                                    'style' => 'margin-right:10px;',
                                    'target' => '_blank',
                            ]) ?>                       
                        <div class="form-group pull-right">

                            
                            <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o" ></i> '.Yii::t('common', 'Create') : '<i class="fa fa-floppy-o" ></i> '.Yii::t('common', 'Save'),
                                [
                                    'class' => 'btn btn-success ',
                                    'data-rippleria' => true,
                            ]) ?>
                        </div>                        
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>    
    <?php ActiveForm::end(); ?>  
    <?=$this->render('../modal/__modal_pick_customer',['model' => $model]) ?> 
</div>
 



<?php
$Yii = 'Yii';
$js =<<<JS

    const loadingDiv = `
        <div class="loading-absolute" style="display:none;">
            <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>           
        </div>`;

   

    $('body').on('click','#clear-line',function(){
        if (confirm('{$Yii::t("common","Do you want to clear all the data?")}')) {
            $.ajax({
                url:'index.php?r=SaleOrders/return/clear-sale-line',
                async:true,
                type: 'POST',
                data:{id:$('#Form-SaleReturnOrder').attr('data-key')},
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
        if($('#salereturnheader-customer_id').val() === ''){
            if(t-n!=0){
                swal(
                        'ดูเหมือนว่า "ยังไม่ได้เลือกลูกค้า"',
                        'กรุณาเลือกลูกค้า',
                        'warning'
                        );              
                route("index.php?r=customers/customer/pick-customer",'GET',{search:'',id:$('#Form-SaleReturnOrder').attr('data-key')},'ew-Pick-Customer');
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
            //return true;                    
        }        
       
    }

    $(document).ready(function() {
        setTimeout(() => {
            $('body').find('a.ew-delete-common').fadeIn();
            $("body").addClass("sidebar-collapse").find(".user-panel").hide();
            $('body').prepend(loadingDiv);           
        }, 50);
        $('.ew-save-common').show();
        setTimeout(() => { 
          $('body').find('.user-panel').hide(); 
        }, 1500);
    });


    $(document).click(function(e) {

        // check that your clicked
        // element has no id=info
         
        if((e.target.type == 'number') || (e.target.type == 'text')) {
            $('.submit-btn-zone').slideUp();        
        }else{            
            $('.submit-btn-zone').slideDown();    
        }
    });

    $('body').on('click','.go-detail',function(){     
        $('body').find('.loading-absolute').fadeIn(); 
        $('body').find('.item-detail').toggle("slide", { direction: "right" }, 500);  
        var tr = $(this).closest('tr');
        $("body").css({ overflow: 'hidden' });
        $('.item-box').slideUp();
        $.ajax({
            url:'index.php?r=SaleOrders%2Fajax%2Fget-saleline&id='+btoa(tr.data('key')),
            type:'GET',
            data:'',
            dataType:'JSON',
            success:function(response){                                
                setTimeout(() => {                    
                    $('body').find('input[name="quantity"]').focus();
                    setTimeout(() => {
                        $('body').find('input[name="quantity"]').select();
                        $('body').find('.loading-absolute').fadeOut('slow');
                    }, 500); 
                }, 800);
            
                $('.item-code').attr('data-key',response.value.item).html(response.value.code);
                $('.item-name').html(response.value.name);
                $('.item-desc').html(response.value.detail);
                $('.item-price').val(response.value.price).attr('data-key',response.value.id);
                $('.item-qty').val(response.value.qty).attr('data-key',response.value.id);
                $('.item-line-amount').val(number_format(response.value.sumline.toFixed(2)));                
                $('a.delete-btn').attr('href','#'+response.value.id).attr('alt',response.value.name).attr('qty',response.value.qty).attr('price',response.value.price);
            }
        })
       
    })
    
    $('body').on('click','#back-btn',function(){
        $('.item-detail').toggle("slide", { direction: "right" }, 500);
        $("body").css({ overflow: 'inherit' });
    })
    //---ITEMBOX CHANGE---
    $('body').on('keyup','input.update-field',function(){
        if($(this).val() > 0){
            var sum = $('input.item-price').val() * $('input.item-qty').val();
            $('.item-line-amount').val(number_format(sum.toFixed(2))); 
        }            
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
            url:'index.php?r=SaleOrders%2Fsaleorder%2Fupdate-line-box&id='+btoa($('#Form-SaleReturnOrder').attr('data-key')),
            type:'POST',
            data: {data:dataArr},
            dataType:'JSON',
            success:function(response){

                if(response.data[0].status===200){

                    $.notify({
                        // options
                        icon: "fas fa-shopping-basket",
                        message: response.data[0].message
                    },{
                        // settings
                        placement: {
                        from: "top",
                        align: "center"
                        },
                        type: "success",
                        delay: 3000,
                        z_index: 3000
                    });  

                    $("body").css({ overflow: 'inherit' });
                    $('tr[data-key="'+response.value.id+'"]').find('input').eq(0).val(response.value.qty);
                    $('tr[data-key="'+response.value.id+'"]').find('input').eq(1).val(response.value.price);
                    $('tr[data-key="'+response.value.id+'"]').find('.line-amount').html(number_format(response.value.sumline.toFixed(2)));
                    
                    $('tr[data-key="'+response.value.id+'"]').find('.text-show-calulate').html(number_format(response.value.qty) + '<span class="text-yellow"> x </span>'+response.value.price);
                    $('tr[data-key="'+response.value.id+'"]').find('.text-show-total').html(number_format(response.value.sumline.toFixed(2)));
                    
                    getSumLine($('#ew-discount-amount').val(),'discount');
                    $('.item-detail').toggle("slide", { direction: "right" }, 500);


                }else if(response.data[0].status===403){
                    $.notify({
                    // options
                    icon: "fas fa-box-open",
                    message: response.data[0].message
                    },{
                        // settings
                        placement: {
                        from: "top",
                        align: "center"
                        },
                        type: "warning",
                        delay: 3000,
                        z_index: 3000
                    });    
                    $('body').find('input.item-qty').focus().select();
                }else{

                    $.notify({
                    // options
                    icon: "fas fa-box-open",
                    message: response.data[0].message
                    },{
                        // settings
                        placement: {
                        from: "top",
                        align: "center"
                        },
                        type: "error",
                        delay: 4000,
                        z_index: 3000
                    }); 

                    if(response.data[0].status===404){

                        $.notify({
                            // options
                            icon: "fas fa-luggage-cart",
                            message: response.data[0].suggestion + ' : ' + number_format(response.data[0].reserve)
                        },{
                            // settings
                            placement: {
                                from: "top",
                                align: "center"
                            },
                            type: "warning",
                            delay: 8000,
                            z_index: 3000
                        });    

                    }

                    $('body').find('input.item-qty').focus().select();
                }
                
                
            }
        })
        

    });
    
    //--- /.ITEMBOX CHANGE---

    //---PICK CUSTOMER---
    $('body').on('change','#salereturnheader-customer_id',function(){
        //if($(this).val()!=''){
            $('#Form-SaleReturnOrder').html('<i class="far fa-address-card  fa-2x text-green"></i> '+$('#salereturnheader-customer_id option:selected').text());
            $('#collapseOne').collapse();
            //$('#collapseOne').removeClass('in')
        //}
    })

    //--- /.PICK CUSTOMER---


    $('body').on('click','a.delete-btn',function(){

        var itemno  = $(this).attr('href');
        var id      = itemno.substring(1);
        var orderno = $('#Form-SaleReturnOrder').attr('data-key');
        var alt     = $(this).attr('alt');

        var data    = { param:{
            lineno:id,
            orderno:orderno
        }};

        var tr = $('tr[data-key="'+itemno.substring(1)+'"]');
   
        // ----- Do confirm delete.-----
        if (confirm('ต้องการลบรายการ "' + alt + '" ?')) {
            $.ajax({
                url:'index.php?r=SaleOrders/return/delete_line&id='+orderno,
                type:'POST',
                data:data,
                async:true,
                success:function(getData){
                    $("body").css({ overflow: 'inherit' });
                    $('.item-detail').toggle("slide", { direction: "right" }, 500);
               

                    tr.css("background-color","#aaf7ff");
                    tr.fadeOut(500, function(){
                        tr.remove();
                    });

                    LoadAjax();
                    getSumLine($('#ew-discount-amount').val(),'discount');

                    if(Number($('#ew-line-total').attr('data'))<=0){
                        $('.ew-sum-line').hide();
                    }else {
                        $('.ew-sum-line').show();
                    }

                }
            });
        }
        // ----- /. Do confirm delete.-----
    }); 


    $('body').on('click','a.ew-delete-common',function(){
        var key = $('#Form-SaleReturnOrder').attr('data-key');
        setTimeout(function(){ 
            if (confirm('{$Yii::t("common","Do you want to confirm ?")}')) {                    
                $.ajax({
                    url:"index.php?r=SaleOrders/return/delete&id=" + key,
                    type: 'POST',
                    data:{id:key},
                    success:function(respond){
                        var obj = jQuery.parseJSON(respond);
                        if(obj.status == 200){
                            // When delete
                            $.notify({
                                // options
                                message: '{$Yii::t("common","Success")}'
                            },{
                                // settings
                                type: 'success',
                                delay: 5000,
                            }); 
                            setTimeout(() => {
                                window.location = "?r=SaleOrders%2Fsaleorder"; 
                            }, 500);
                            
                        }else {
                            //window.location.reload();
                            $.notify({
                                // options
                                message: '{$Yii::t("common","Not allowed to delete documents with status")} = '+ obj.value.status 
                            },{
                                // settings
                                type: 'error',
                                delay: 5000,
                            });     
                        }
                    }
                });            
            }else{
               return false; 
            }
        }, 200);
    })

    $('body').on('change', '.return_type', function(){
        let return_type = parseInt($(this).val());
        if(return_type===1){
            $('.img-return').show();
            $('.img-receive').hide();
        }else{
            $('.img-receive').show();
            $('.img-return').hide();
        } 
        
    })
    
JS;

$this->registerJS($js,\Yii\web\View::POS_END);

?>

<?php $this->registerJsFile('js/item-picker.js?v=4.11.26',[ 'type' => "text/javascript" ]);?>
<?php $this->registerJsFile('js/saleorders/return-order-form.js?v=5.01.02',[ 'type' => "text/javascript" ]);?>
 
<?php  $this->registerJsFile('https://code.jquery.com/ui/1.12.1/jquery-ui.js',['depends' => [\yii\web\JqueryAsset::className()]]);?>