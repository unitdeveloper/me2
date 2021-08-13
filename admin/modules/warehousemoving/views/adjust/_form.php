<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use admin\models\Generater;
use kartik\widgets\DatePicker;

 
use common\models\Itemgroup;
/* @var $this yii\web\View */
/* @var $model common\models\WarehouseHeader */
/* @var $form yii\widgets\ActiveForm */
 

?>


<style type="text/css">
    @media print {
        .adjust-btnGroup{
            display: none;
        }

        tfoot{
            display: none;
        }
        .ew-delete-adj-line{
            display: none;
        }
        .adjust-deleteBtn,
        .navbar,
        .FilterResource,
        .main-footer{
            display: none;
        }
        input[type='text']{
            border:0px;
        }
        .panel{
            border:0px;
        }
      }


    @media (max-width: 1440px) {
        .FilterResource h3{
            font-size:20px !important;
        }
        .FilterResource h5{
            font-size:14px !important;
        }
    } 
    @media (max-width: 1024px) {
        .FilterResource h3{
            font-size:16px !important;
        }
        .FilterResource h5{
            font-size:13px !important;
        }
    }
    .ew-ul-sub li
    {
      list-style-type: none;
      font-size: 18px;
      margin-bottom: 15px;
      margin-top: 15px;
      color:#337ab7 !important;
    }
    #ewSelect,
    #PickToSaleLine{
        display:none;
    }

    .widget-user .widget-user-image>img {
        width: 90px;
        height: auto;
        border: none;
    }
    
    .ew-getItem-Set .box-header{
        font-size:16px;
        margin-left: -33px;
    }
 
</style>





<div class="warehouse-header-form" ng-controller="adjustController">

    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'ew-Item-Adjust',
            'data-key' => $model->id,
            'modules' => 'Adjust'
        ]
    ]); ?>

<div class="panel panel-default  " style="padding: 20px;">

    <div class="row">
        <div class="col-sm-offset-6" >

            <div class=" " >
                <div class="col-sm-6 text-right"> </div>
                <div class="col-sm-6 text-right">
                        <h3><?=Yii::t('common','Item Adjust')?></h3>
                </div>
            </div>

            <div class=" " >
                <div class="col-sm-6 text-right"> </div>
                <div class="col-sm-6 text-right">

                    <?php
                        if($model->PostingDate=='')
                    {
                        $model->PostingDate = date('Y-m-d');
                    }else {

                    $model->PostingDate = date('Y-m-d',strtotime($model->PostingDate));

                    }
                    echo $form->field($model, 'PostingDate')->widget(DatePicker::classname(), [
                                'options' => ['placeholder' => 'Posting date ...','style' => 'background-color: rgba(0, 128, 0, 0.3);'],
                                'value' => date('Y-m-d',strtotime($model->PostingDate)),
                                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'autoclose'=>true
                                ]
                            ]);

                    ?>
                </div>
            </div>

            <div>

                <?php if($model->isNewRecord) $model->id = 0; ?>
                <div class="col-sm-6 text-right"><?= $form->field($model, 'DocumentNo')->textInput(['maxlength' => true,'data' => $model->id]) ?> </div>
                <div class="col-sm-6 text-right">
                        <?php
                        if($model->DocumentDate=='')$model->DocumentDate = date('Y-m-d');
                        echo $form->field($model, 'DocumentDate')->widget(DatePicker::classname(), [
                                            'options' => ['placeholder' => 'Document date ...'],
                                            'value' => $model->DocumentDate,
                                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                            'pluginOptions' => [
                                                'format' => 'yyyy-mm-dd',
                                                'autoclose'=>true
                                            ]
                                        ]);

                                ?>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-9">

            <div class="row">
                <div class="col-sm-4"></div>
                <div class="col-sm-4 col-xs-6">

                    <?= $form->field($model, 'AdjustType')->dropDownlist(
                    [
                        '+' => Yii::t('common','Positive Adjusted').' (+)',
                        '-' => Yii::t('common','Negative Adjusted').' (-)',
                    ],['style' => 'background-color:rgb(246, 216, 176);']) ?>
                </div>
                <div class="col-sm-4 col-xs-6">

                    <?= $form->field($model, 'TypeOfDocument')->dropDownlist(
                    [
                        'Purchase' => Yii::t('common','Product Received').' (+)',
                        'Sale' => Yii::t('common','Product Shipment').' (-)',
                        'Adjust' => Yii::t('common','Item Adjust').' (+,-)',
                        'Consumption' => Yii::t('common','Consumption').' (-)',
                        'Output' => Yii::t('common','Output Product').' (+)',
                    ],[
                      'options' => ['Adjust' => ['Selected'=>'selected']],
                      'style' => 'background-color:rgb(234, 246, 176);'
                    ]) ?>

                </div>
            </div>
        </div>
        <div class="col-sm-3 text-right">


            <?= $form->field($model, 'SourceDoc')->textInput(['maxlength' => true,'placeholder' => 'No. : OP , SO , Invoice']) ?>
        </div>
    </div>
</div>





<style>
 
    .ew-adj-line{
        min-height:150px;
        background-color:#f4f4f4;
    }
</style>

    <div class="row">
        <div class="col-sm-12 ew-adj-row">
            <div class="table-responsive ew-adj-line">
                <span class="blink text-gray">Rendering... </span>
            </div>

            <div class="ewTimeout text-center" style="position: absolute; top: 0px; left: 45%;  "><i class="fa fa-refresh fa-spin fa-1x fa-fw" aria-hidden="true"></i><div class="blink"> Loading .... </div>
              <script type="text/javascript">
                setTimeout(function(){ $('.ewTimeout').html('<span style="color:red"><?=Yii::t('common','Server not responding.')?>...</span>');}, 50000);
              </script>
            </div>

            <div class="render-search-item"></div>

        </div>
    </div>

    


    <div class="row adjust-btnGroup margin-top">
        <div class="col-sm-offset-8">
            <div class="col-sm-12 text-right">
                 <div class="form-group" style="padding-right: 15px;">
                    <?= Html::Button('<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save') , ['class' => 'btn btn-success-ew submit-form']) ?>

                    <!-- <button   id="ew-Save-Adjust" class="btn btn-success-ew"><i class="fa fa-server" aria-hidden="true"></i> Save</button> -->

                    <?php if($model->id!=''): ?>
                    <button id="ew-Post-Adjust" type="button" class="btn btn-danger-ew"><i class="fa fa-server" aria-hidden="true"></i> <?=Yii::t('common','POST')?></button>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>




<div id="menuFilter" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
    <!-- Menu Filter -->
    <?= $this->render('_FilterProduct'); ?>
</div>
<!-- ew-filter-onclick -->
<br>

<div class="FilterResource">    
    <div class="ResourceItemSearch">
        <span class="text-gray blink">Loading...</span>
    </div>
</div>
<hr>

    <?php ActiveForm::end(); ?>

</div>





 
 
<?php if(!Yii::$app->request->isAjax)  $this->registerJsFile('js/warehouse/adjust.js?v=3.03.28.1');?>
<?php if(!Yii::$app->request->isAjax)  $this->registerJsFile('js/no.series.js?v=3.03.28');?>
<?php $this->registerJsFile('js/manufacturing/item_set.js?v=3.03.28');?>

<?=$this->registerJsFile('js/warehouse/adjustController.js?v=3.03.20.1');?>

<?=$this->registerJsFile('js/item-picker.js?v=3.03.28');?>
<?=$this->render('../../../../views/setupnos/__modal'); ?>
<?=$this->render('../../../../modules/SaleOrders/views/modal/_pickitem',['model' => $model]) ?>
<?php /*$this->render('../../../../modules/SaleOrders/views/modal/__modal_pick_customer',['model' => $model])*/ ?>
 
<?=$this->render('../../../../modules/items/views/ajax/__modal_get_item'); ?>



<?php
$js=<<<JS

    
    $(document).ready(function(){
        
        // ดึงรายการสินค้ามาแสดง โดนสุ่มออกมา 8 แถว
        route('index.php?r=Itemset/ajax/menu-random','GET',{param:{data:0}},'ResourceItemSearch');
        
    })
    
    $('body').on('click','button.submit-form',function () {
        $('form#ew-Item-Adjust').submit();
    });

    $('body').on('click','.item-selecter #picker',function(){
         
    });

    $('body').on('click','.ew-filter-onclick',function(){
        var href = $(this).attr('href').slice(1);
        var data = { param:
                        {
                            href:href
                        }
                    };
        $('.FilterResource').hide('fast');
        route('index.php?r=Itemset/ajax/items','POST',data,'ResourceItemSearch');
        $('.FilterResource').show('normal');
    });


    $('body').on('click','.ew-PickItem',function(){
        var data = { param:{
                    itemno:$(this).attr("itemno"),
                    docno:$('#itemjournal-documentno').val(),
                    pset:$(this).attr("itemset"),
                    itemset:$(this).attr("itemset")
                }};
        if($(this).attr('ew-bom')==='enabled'){
            $.ajax({
                    url:"index.php?r=Itemset/bomset/view&id="+$(this).attr("itemset"),
                    type: "POST",
                    data: data,
                    async:true,
                    success:function(getData){
                        $('.ew-create-item').html(getData);
                    }
            });           
        }else {
            route("index.php?r=Itemset/bomset/viewitem",'POST',data,'ew-create-item'); // render _modal_pickitem
            loadItem($('#itemno').val());
            
        }     
        
        $("#PickToSaleLine" ).hide();
        $("#ewSelect" ).hide();
        $('.modal-title').html($(this).attr('ew-set-name'));
        $('body').attr('style','overflow:hidden; margin-right:0px;');
    });


    $('body').on('click','.ew-action-my-item',function(){

        //ItemValidate
        var data = { param:{
            pid:$(this).attr('ew-radio-id'),
            pval:$(this).attr('ew-radio-val'),
            pset:$('#itemset').val(),

        }};

        // $.ajax({
        //     url:"index.php?r=Itemset/ajax/item-validate",
        //     type: "POST",
        //     data: data,
        //     dataType:'JSON',
        //     success:function(obj){
        //         console.log(obj);
           
                route("index.php?r=Itemset/ajax/item-validate",'POST',data,'ew-getItem-Set');


                $('#ew-price').val(0);
                $('.ew-render-itemno').html('');
                $('.ew-render-item').html('');
                $('.text-amount').hide('')


                $("#ew-price").prop('disabled', true);
                $("#ew-amount").prop('disabled', true);
                $("#PickToSaleLine" ).hide();
        //     }
        // });

    });

    function loadItem(item){
    // ดึงรายการแรกออกมา เพื่อแสดงภาพ และกำหนดราคา
    $.ajax({
            url:"index.php?r=Itemset/bomset/item-getdata",
            type: "POST",
            data: {param:{item:item}},
            async:true,
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                $('#ew-price').val(0);
                $("#ew-price").prop('disabled', true);
                $("#ew-amount").prop('disabled', true);
                $('.ew-render-item').html(obj.desc);
                // Change Photo
                $('.ew-itemset-pic').attr('src','//assets.ewinl.com/images/product/' +obj.ig +'/' + obj.Photo);
            }
        });


    }


    $('body').on('click','.ew-action-item',function(){
        $('#itemno').val($(this).attr('ew-radio-item'));
        $(this).closest('#selector').find('a').addClass('btn-default').removeClass('btn-info');
        var myBtn = $(this).attr('ew-radio-item');
        $.ajax({
            url:"index.php?r=Itemset/bomset/item-getdata",
            type: "POST",
            data: {param:{item:$(this).attr('ew-radio-item')}},
            async:true,
            success:function(getData){
                var obj = jQuery.parseJSON(getData);

                if(obj.item == myBtn){
                    $('a[ew-radio-item="'+myBtn+'"]').addClass('btn-info');
                }
                 

                $('#ew-render-itemno').attr('data-key',obj.id).attr('data-name',obj.desc_th);
                $('#ew-price').val(obj.std);
                $('.ew-render-itemno').hide().html(obj.code).fadeIn('slow');
                $('.ew-render-item').hide().html(obj.desc).fadeIn('slow');
                $('.text-amount').hide().html(number_format(obj.inven)).fadeIn('slow');
                $("#ew-price").prop('disabled', false);
                $("#ew-amount").prop('disabled', false);
                $("#PickToSaleLine" ).show();
                // Change Photo
                $('.ew-itemset-pic').attr('src','//assets.ewinl.com/images/product/' +obj.ig +'/' + obj.Photo).fadeIn('slow');
            }
        });
     });


    // --------- Insert Item -----------

    $('body').on('click','input[name="ew-InsertAdd"]',function(e){
        if($('.ew-InsertDesc').attr('ew-item-code')!='eWinl'){
            AddItemToLine({
                item:$('.ew-InsertDesc').data('key'),
                desc:$('.ew-InsertDesc').val(),
                qty:$('.ew-direct-qty').val(),
                price:$('.ew-direct-price').val(),
                id:$(form+'documentno').attr('data'),
                no:$('itemjournal[id="documentno"]').attr('ew-no_'),
                type:$('select[name="InsertType"]').val(),
                docNo:$(form+'documentno').val(),
                typeDoc:$(form+'typeofdocument').val(),
                adjType:$(form+'adjusttype').val(),
            },'#Item_Adjust_Line','warehousemoving/adjust');
        }
    });

    $('body').on('keydown','.ew-direct-price,.ew-add-to-adj-line', function(e) {

    var keyCode = e.keyCode || e.which;
    if (keyCode === 13){
        if($('.ew-InsertDesc').attr('ew-item-code')!='eWinl'){
                AddItemToLine({
                    item:$('.ew-InsertDesc').data('key'),
                    desc:$('.ew-InsertDesc').val(),
                    qty:$('.ew-direct-qty').val(),
                    price:$('.ew-direct-price').val(),
                    id:$(form+'documentno').attr('data'),
                    no:$('itemjournal[id="documentno"]').attr('ew-no_'),
                    type:$('select[name="InsertType"]').val(),
                    docNo:$(form+'documentno').val(),
                    typeDoc:$(form+'typeofdocument').val(),
                    adjType:$(form+'adjusttype').val(),
                },'#Item_Adjust_Line','warehousemoving/adjust');
            }
        }
    });


    $('body').on('click','.ew-delete-adj-line',function(){

    if (confirm('Do you want to delete  ?')) {

        var data = {id:Number($(this).attr('data'))};

        var tr = $(this).closest('tr');
        tr.css("background-color","#aaf7ff");
        tr.fadeOut(500, function(){
            tr.remove();
            route('index.php?r=warehousemoving/adjust/delete-adj-line','POST',data,'Navi-Title');



        });

    }

    return false;

    });
    // --------- /.Insert Item -----------

    $('body').on('click','#ewSelect',function(){  
        GenerateBom($('.ew-Code').attr('ew-post-param'));    
        AddItemToLine({
			item:$('#ew-render-itemno').data('key'),
			desc:$('#ew-render-itemno').data('name'),
			qty:$('input[name="Quantity"]').val(),
			price:$('input[name="Price"]').val(),
			id:$('form#ew-Item-Adjust').data('key'),
			no:$('itemjournal[id="documentno"]').attr('ew-no_'),
			type:$('select[name="InsertType"]').val(),
			docNo:$(form+'documentno').val(),
			typeDoc:$(form+'typeofdocument').val(),
			adjType:$(form+'adjusttype').val(),
        },'#Item_Adjust_Line','warehousemoving/adjust');   
        $('#PickItem-Modal').modal('hide');
    }) 

    $('body').on('click','#PickToSaleLine',function(){  
        AddItemToLine({
			item:$('#ew-render-itemno').data('key'),
			desc:$('#ew-render-itemno').data('name'),
			qty:1,
			price:$('#ew-price').val(),
			id:$('form#ew-Item-Adjust').data('key'),
			no:$('itemjournal[id="documentno"]').attr('ew-no_'),
			type:$('select[name="InsertType"]').val(),
			docNo:$(form+'documentno').val(),
			typeDoc:$(form+'typeofdocument').val(),
			adjType:$(form+'adjusttype').val(),
        },'#Item_Adjust_Line','warehousemoving/adjust');     
    })


    $('body').on('click','.pick-item-to-createline',function(){
        AddItemToLine({
			item:$(this).data('key'),
			desc:$(this).attr('desc'),
			qty:1,
			price:$(this).attr('price'),
			id:$('form#ew-Item-Adjust').data('key'),
			no:$('itemjournal[id="documentno"]').attr('ew-no_'),
			type:$('select[name="InsertType"]').val(),
			docNo:$(form+'documentno').val(),
			typeDoc:$(form+'typeofdocument').val(),
			adjType:$(form+'adjusttype').val(),
        },'#Item_Adjust_Line','warehousemoving/adjust');
    });

   


JS;
$this->registerJs($js, yii\web\View::POS_END, 'js-options');
