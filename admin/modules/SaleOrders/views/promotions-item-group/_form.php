<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PromotionsItemGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="promotions-item-group-form">
<?php $form = ActiveForm::begin(['id' => 'form-promotions']); ?>
<div class="row">
    <div class="col-md-2 col-sm-4">
        <h4><?=Yii::t('common','Information')?></h4>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label(Yii::t('common','Group Name')) ?>

        <?= $form->field($model, 'description')->textInput() ?>

 

        <div class="form-group">
            <?= Html::submitButton('<i class="far fa-save"></i> '.Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <div class="col-md-10 col-sm-8">
        <h4><?=Yii::t('common','Please select items.')?></h4>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'showFooter' => true,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    //'contentOptions' => ['style' => 'width:80px'],
                    //'footer' => '<button type="button" class="btn btn-default add-new-line"><i class="fas fa-plus"></i> '.Yii::t('common','ADD').'</button>'
                ],

                //'id',
                [
                    'attribute' => 'items.master_code',
                    'value' => 'items.master_code',
                    //'footer' => '<button type="button" class="btn btn-default add-new-line"><i class="fas fa-plus"></i> '.Yii::t('common','ADD').'</button>'
                    //'footer' => '<input type="text" class="form-control" name="master_code">'
                ],
                [
                    'attribute' => 'items.description_th',
                    'value' => 'items.description_th',
                    //'footer' => '<input type="text" class="form-control" name="description_th">'
                ],
                [
                    'label' => '',
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-right'],
                    'footerOptions' => ['class' => 'text-right'],
                    'value' => function($model){
                        return '<button type="button" data-key="'.$model->id.'" class="btn btn-danger add-delete-line"><i class="far fa-trash-alt"></i></button>';
                    },
                    'footer' => '<button type="button" class="btn btn-default add-new-line"><i class="fas fa-plus"></i> '.Yii::t('common','ADD').'</button>'
                ],
                
                //'comp_id',

                //['class' => 'yii\grid\ActionColumn'],
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
            template += '<td><input type="text" class="form-control item-code" name="item_code[]" placeholder="{$Yii::t("common","Item Code")}, {$Yii::t("common","Item Name")}"></td>';
            template += '<td><input type="text" class="form-control item-name" name="item_name[]" readonly="readonly"></td>';
            template += '<td class="text-right"><input type="hidden" class="form-control item-id" style="width:50px;" name="item_id[]"> <button type="button" class="btn btn-default-ew add-delete-empty-line"><i class="fas fa-minus"></i></button></td>';
            template += '</tr>';

        $('.grid-view').find('table>tbody').append(template)
    });

    $('body').on('click','button.add-delete-line',function(){
        var id = $(this).attr('data-key');
        var el = $(this).closest('tr');
        if(confirm("{$Yii::t('common','Do you wont to delete ?')}")){
            $.ajax({
                url:'index.php?r=SaleOrders%2Fpromotions-item-group%2Fdelete-line&id=' + id,
                type:'POST',
                dataType:'JSON',
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


    $('body').on('keyup','input.item-code',function(){
        var el = $(this).closest('tr');
        var len = $.trim($(this).val()).length;
        var search = $(this).val();
        if(len >= 3){
            $.ajax({
                url:"index.php?r=items/ajax/find-items-json-limit&word=" + search + '&limit=20',
                type:'GET',
                dataType:'JSON',
                success:function(model){
           
                    if (model.length == 1){
                        el.find('input.item-code').val(model[0].item);
                        el.find('input.item-name').val(model[0].desc_th);
                        el.find('input.item-id').val(model[0].id);
                    }
                }



            })
        }
    })
JS;

$this->registerJs($js);

?>