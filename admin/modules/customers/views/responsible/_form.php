<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model common\models\SalesPeople */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-people-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-4">

            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                </div>                
                <div class="col-sm-8">
                    
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'prefix')->textInput(['maxlength' => true, 'placeholder' => Yii::t('common','นาย/นางสาว')]) ?>
                </div>                
                <div class="col-md-4">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('common','ธนาธร')]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'surname')->textInput(['maxlength' => true, 'placeholder' => Yii::t('common',' ')]) ?>
                </div>
            </div>

            
            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>
                </div>                
                <div class="col-sm-8">
                    <?= $form->field($model, 'mobile_phone')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'tax_id')->textInput(['maxlength' => true]) ?>
                </div>                
                 
            </div>
            

            <hr />


            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'address2')->textInput(['maxlength' => true]) ?>

            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($model, 'postcode')->textInput(['maxlength' => true]) ?>
                </div>                
                <div class="col-sm-8">
                     
                </div>
            </div>
            <div class="form-group text-right">
                <?= Html::submitButton('<i class="far fa-save"></i> '.Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>

        </div>
        <div class="col-sm-8">
            <div class="<?=$model->isNewRecord ? 'hidden' : ''?>" >
                <h3><?=Yii::t('common','Customer');?></h3>
                <?php Pjax::begin(); ?>   
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'showFooter' => true,
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                        ],
                        [
                            'headerOptions' => ['class' => 'customer-head-code'],
                            'attribute' => 'customer.code',
                            'value' => function($model){
                                if ($model->type_of=='group'){
                                    return Yii::t('common','Customer Group');
                                }else{
                                    return $model->customer->code;
                                }
                            }
                        ],
                        [
                            'headerOptions' => ['class' => 'customer-head-name'],
                            'attribute' => 'customer.name',                         
                            'value' => function($model){
                                if ($model->type_of=='group'){
                                    return $model->custGroup->group->name;
                                }else{
                                    return $model->customer->name;
                                }
                            }
                        ],
                        // [
                        //     'attribute' => 'customer.locations.province',
                        //     'value' => 'customer.locations.province'
                        // ],
                        [
                            'label' => '',
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-right'],
                            'footerOptions' => ['class' => 'text-right'],
                            'value' => function($model){
                                return '<button type="button" data-key="'.$model->id.'" class="btn btn-danger add-delete-line"><i class="far fa-trash-alt"></i></button>';
                            },
                            'footer' => '<button type="button" class="btn btn-default add-new-line"><i class="fas fa-plus"></i> '.Yii::t('common','Add').'</button>'
                        ],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>



<?php 
$Yii = 'Yii';
$js =<<<JS

var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();

    $('#w0').on('keypress', function(e) {
        // Disable form submit on enter.
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    $('body').on('click',function(el){
        if ($($(el.target).closest('div').offsetParent('className')[0]).attr('class') == 'wrapper'){
            $('body').find('.customer-choice').remove();
            $('body').find('.customer-group-choice').remove();
        }

       
        if($(el.target).attr('class')=='form-control customer-code'){
            
            if($(el.target).val()!=''){
                findCustomer($(el.target));
            }
        }
    })

    $('#form-promotions').on('keypress', function(e) {
        // Disable form submit on enter.
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    $('body').on('click','button.add-new-line',function(){

 

        var template = '<tr>';
            template += '<td><select name="type_of[]" class="form-control type-of"><option value="group">{$Yii::t("common","Customer Group")}</option><option value="customer">{$Yii::t("common","Customer")}</option></selection></td>';
            template += '<td class="cust_code">' + selectCustomer() + '</td>';
            template += '<td class="cust_name"><input type="text" class="form-control customer-name" name="customer_name[]" readonly="readonly"></td>';
            //template += '<td><input type="text" class="form-control customer-province" name="customer_province[]" readonly="readonly"></td>';
            template += '<td class="text-right"><input type="hidden" class="form-control customer-id" style="width:50px;" name="customer_id[]"> <button type="button" class="btn btn-default-ew add-delete-empty-line"><i class="fas fa-minus"></i></button></td>';
            template += '</tr>';

        $('.grid-view').find('table>tbody').append(template)
        $('body').find('input.customer-code').focus();
    });

    $('body').on('click','button.add-delete-line',function(){
        var id = $(this).attr('data-key');
        var el = $(this).closest('tr');
        if(confirm("{$Yii::t('common','Do you wont to delete ?')}")){
            $.ajax({
                url:'index.php?r=salepeople/has-customer/delete&id=' + id,
                type:'POST',
                dataType:'JSON',
                data: {ajax:true},
                success:function(response){
                    if (response.status === 200){

                        el.remove();
                        $.notify({
                            // options
                            message: "{$Yii::t('common','Deleted')}"
                        },{
                            // settings
                            type: 'warning',
                            delay: 2000,
                        });

                    }else {
                        swal(
                            "'Error'",
                            response.message,
                            'warning'
                        );
                    }
                }
            })
        }

    });

    $('body').on('click','button.add-delete-empty-line',function(){
        var el = $(this).closest('tr').remove();
    });


    $('body').on('keyup','input.customer-code',function(){
        findCustomer(this);
    })

    


    function findCustomer(obj){
        var el = $(obj).closest('tr');
        var th = $(obj);
        var len = $.trim(th.val()).length;
        var search = th.val().trim();
        var storage = JSON.parse(sessionStorage.getItem('data'));
        var serch_history = sessionStorage.getItem('data') ? storage.w.trim() : '';
        delay(function(){
        
            
    
            if(serch_history != search){
                
                $.ajax({
                    url:"index.php?r=customers/ajax/find-customer",
                    type:'POST',
                    dataType:'JSON',
                    data: {word:search},
                    success:function(model){
                        
                        if (model.length == 1){
                            el.find('input.customer-code').val(model[0].code);
                            el.find('input.customer-name').val(model[0].name);
                            el.find('input.customer-province').val(model[0].province);
                            el.find('input.customer-id').val(model[0].id);
                            //$('body').find('button.add-new-line').click();
                            $('body').find('input.customer-code').focus();
                        }if (model.length > 1) {
                            $('body').find('.customer-choice').remove();
                            renderSearch(th,model);
                            sessionStorage.setItem('data',JSON.stringify({w:search,value:model}));

                        } 
                    }
                })
            }else{
                renderSearch(th,storage.value);
                $('body').find('.customer-choice').slideDown();
            }

        },800);
    }


    function renderSearch(el,model){

        
        var template = '<div class="customer-choice" style="position:absolute;z-index:10;border: 1px solid #ccc;width: 79%;margin-top: 2px;padding: 10px; background-color:#fdfdfd;">';

            $.each(model,function(index,value){
                template += '<div style="margin:10px 0 10px 0;"> <a href=# data-key="'+ value['id'] +'" data-code="'+ value['code'] +'" data-name="'+ value['name'] +'" data-province="'+ value['province'] +'" class="select-customer ">' + value['code'] + ' ' + value['name'] + ' (' + value['province'] + ') </a></div>';
            });
            template += '</div>';

        el.closest('td').find('input').after(template);

    }

    $('body').on('click','a.select-customer',function(){
        
        $(this).closest('tr').find('input.customer-code').val($(this).data('code')).attr('readonly',true);
        $(this).closest('tr').find('input.customer-name').val($(this).data('name'));
        //$(this).closest('tr').find('input.customer-province').val($(this).data('province'));
        $(this).closest('tr').find('input.customer-id').val($(this).data('key'));
        $('body').find('.customer-choice').slideUp('fast');
        $('body').find('button.add-new-line').click();
    });

    function selectCustomer(){
        var template =  '<div class="input-group">';
            template += '   <input type="text"  class="form-control find-group" name="group[name][]" placeholder="{$Yii::t("common","Customer Group")}" >';
            template += '   <span class="btn btn-info input-group-addon getCustomerGroup">';
            template += '       <i class="fa fa-caret-square-o-down" aria-hidden="true" style="cursor:pointer;"></i>';
            template += '   </span>';
            template += '   <input type="hidden"  class="group-id" name="group[id][]" >';
            template += '</div>';

        return template;
    }

    $('body').on('change','.type-of',function(){
        console.log($(this).val());
        var template = '';
        if ($(this).val()=='group'){        
            template += selectCustomer();
        }else{
            template += '<input type="text" class="form-control customer-code" name="customer_code[]" placeholder="{$Yii::t("common","Customer code")}, {$Yii::t("common","Customer Name")}">';
        }
            
        $(this).closest('tr').find('td.cust_code').html(template);
    })


    function getCustomerGroupRender(el,model){
        var template = '<div class="customer-group-choice" style="position:absolute;z-index:10;width:100%; border: 1px solid #ccc;margin-top: 35px;padding: 10px; background-color:#fdfdfd;">';
            $.each(model,function(index,value){
                template += '<div style="margin:10px 0 10px 0;">';
                template += '<a href=# data-key="'+ value['id'] +'" data-name="'+ value['name'] +'"  class="select-customer-group ">';
                template +=  value['name'] + ' (' + value['count'] + ')</a>';
                template += '</div>';
            });
            template += '</div>';
        el.closest('td').find('input[type="text"]').after(template);
    }


    function searchGroup(el){
        let search = el.val().trim();
        let storage = JSON.parse(sessionStorage.getItem('group'));
        let serch_history = sessionStorage.getItem('group') ? storage.w.trim() : '';
        
         
        if((serch_history != search) || !storage){
            $.ajax({
                url : '?r=customers/ajax/find-customer-group',
                type : 'POST',
                dataType:'JSON',
                data : {word:el.val()},
                success:function(res){
                    if(res.length > 0){
                        sessionStorage.setItem('group',JSON.stringify({w:el.val(),value:res}));
                        getCustomerGroupRender(el,res);
                    }                    
                }
            })
        }else{
            getCustomerGroupRender(el,storage.value);
        }
    }

    

    $('body').on('keyup','input.find-group',function(){
        let th = $(this);
        delay(function(){
            $('body').find('.customer-choice').remove();
            $('body').find('.customer-group-choice').remove();
            searchGroup(th);
        }, 800 );
    })

    $('body').on('click','.getCustomerGroup',function(){
 
        $('body').find('.customer-choice').remove();
        $('body').find('.customer-group-choice').remove();
        searchGroup($(this));
    })

    $('body').on('click','a.select-customer-group',function(){
        $(this).closest('tr').find('input.find-group').val('{$Yii::t("common","Customer Group")}').attr('readonly',true);
        $(this).closest('tr').find('input.customer-name').val($(this).data('name'));
        $(this).closest('tr').find('input.group-id').val($(this).data('key'));
        $('body').find('.customer-group-choice').slideUp('fast');
        $('body').find('button.add-new-line').click();
    });
JS;

$this->registerJs($js);

?>
