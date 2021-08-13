<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerGroups */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-groups-form">
    <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-4">           

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'detail')->textarea(['rows' => 6]) ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
                </div>

            </div>
            <div class="col-md-8">
                <h3><?=Yii::t('common','Customer');?></h3>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'showFooter' => true,
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                        ],
                        [
                            'attribute' => 'customer.code',
                            'value' => 'customer.code',
                        ],
                        [
                            'attribute' => 'customer.name',
                            'value' => 'customer.name',
                        ],
                        [
                            'attribute' => 'customer.locations.province',
                            'value' => 'customer.locations.province'
                        ],
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
            
            </div>
        </div>

    

    <?php ActiveForm::end(); ?>

</div>





<?php 
$Yii = 'Yii';
$js =<<<JS

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
            template += '<td></td>';
            template += '<td><input type="text" class="form-control customer-code" name="customer_code[]" placeholder="{$Yii::t("common","Customer code")}, {$Yii::t("common","Customer Name")}"></td>';
            template += '<td><input type="text" class="form-control customer-name" name="customer_name[]" readonly="readonly"></td>';
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
                url:'index.php?r=customers/has-group/delete&id=' + id,
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

    $('body').on('click',function(el){
        if ($($(el.target).closest('div').offsetParent('className')[0]).attr('class') == 'wrapper'){
            $('body').find('.customer-choice').remove();
        }

       
        if($(el.target).attr('class')=='form-control customer-code'){
            
            if($(el.target).val()!=''){
                findCustomer($(el.target));
            }
        }
    })


    function findCustomer(obj){
        var el = $(obj).closest('tr');
        var th = $(obj);
        var len = $.trim(th.val()).length;
        var search = th.val().trim();
        var storage = JSON.parse(sessionStorage.getItem('data'));
        var serch_history = sessionStorage.getItem('data') ? storage.w.trim() : '';
        if(len >= 3){
        
            
    
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

        }
    }


    function renderSearch(el,model){

        
        var template = '<div class="customer-choice" style="position:absolute;z-index:10;border: 1px solid #ccc;width: 89%;margin-top: 2px;padding: 10px; background-color:#fdfdfd;">';

        $.each(model,function(index,value){
            template += '<div style="margin:10px 0 10px 0;"> <a href=# data-key="'+ value['id'] +'" data-code="'+ value['code'] +'" data-name="'+ value['name'] +'" class="select-customer ">' + value['code'] + ' ' + value['name'] + ' (' + value['province'] + ') </a></div>';
        });
        template += '</div>';

        el.closest('td').find('input').after(template);

    }

    $('body').on('click','a.select-customer',function(){
        
        $(this).closest('tr').find('input.customer-code').val($(this).data('code')).attr('readonly',true);
        $(this).closest('tr').find('input.customer-name').val($(this).data('name'));
        $(this).closest('tr').find('input.customer-id').val($(this).data('key'));
        $('body').find('.customer-choice').slideUp('fast');
        $('body').find('button.add-new-line').click();
    });
JS;

$this->registerJs($js);

?>
