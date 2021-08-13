<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use kartik\cmenu\ContextMenu;
use kartik\widgets\DatePicker;
/* @var $this yii\web\View */
/* @var $model common\models\Promotions */
/* @var $form yii\widgets\ActiveForm */

//init
/* เพื่อป้องกันการแก้ไข */
/* เมื่อมีการแก้ไขไดๆ สถานะต้องถูกเริ่มใหม่(เสมอ) */
 

$model->sale_amount = $model->sale_amount * 1;
$model->discount    = $model->discount * 1;
?>

<div class="promotions-form">

    <div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'promotions',
            'options' => [
                'data-key' => $model->id
            ]
            
            ]); ?>
            <div class="col-sm-8">  
                <div class="row">
                    <div class="col-sm-6 col-xs-8">
                        <?php
                            ContextMenu::begin([
                                'items' => [
                                    ['label' => '➕ '.Yii::t('common','ADD'), 'url' => ['/SaleOrders/promotions-item-group/create']],
                                    ['label' => '⌥ '.Yii::t('common','Manage'), 'url' => ['/SaleOrders/promotions-item-group/index']],
                                ],
                            ]); 
                            // fill in any content within your target container

                        ?>
                            <?= $form->field($model, 'item_group')->dropDownList(
                                                ArrayHelper::map(\common\models\PromotionsItemGroup::find()
                                                ->select('name,description')
                                                ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                                ->groupBy('name,description')
                                                ->all(),'name','description'),
                                                [
                                                    'data-live-search'=> "true",
                                                    'class' => 'selectpicker form-control ',
                                                    'prompt' => Yii::t('common','Item Group'),                                                    
                                                ]
                            ) 
                            ?>

                        <?php ContextMenu::end(); ?> 
                    </div>
                    <div class="col-sm-6 col-xs-4" style="padding-top:30px;">
                        <?=Html::a('➕ '.Yii::t('common','ADD'),['/SaleOrders/promotions-item-group/create'])?>
                    </div>
                </div>
                
                
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                    
                    <li class="active"><a href="#Promotion" data-toggle="tab"><?=Yii::t('common','Promotion')?></a></li>   
                    <li ><a href="#items" data-toggle="tab"><?=Yii::t('common','Items')?></a></li>                
                     
                    <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
                    </ul>
                    <div class="tab-content">
                    
                    <!-- /.tab-pane -->
                    <div class="tab-pane active" id="Promotion">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12"><?= $form->field($model, 'sale_amount')->textInput(['maxlength' => true,'style' => 'text-align:right;','autocomplete' => 'off']) ?></div>
                            <div class="col-sm-6 col-xs-12"><?= $form->field($model, 'discount')->textInput(['maxlength' => true,'style' => 'text-align:right;','autocomplete' => 'off']) ?></div>
                        </div>
                        <div class="row">
                        <div class="col-sm-6">
                        <?php echo $form->field($model, 'start_date')->widget(DatePicker::classname(), [
                                        'options' => ['placeholder' => 'Start Date ...'],
                                        'value' => $model->start_date,
                                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                        'pluginOptions' => [
                                            'format' => 'yyyy-mm-dd',
                                            //'format' => 'dd/mm/yyyy',
                                            'autoclose'=>true
                                        ]
                                    ]); ?>
                       </div>
                        <div class="col-sm-6">
                        <?php echo $form->field($model, 'end_date')->widget(DatePicker::classname(), [
                                        'options' => ['placeholder' => 'End Date ...'],
                                        'value' => $model->end_date,
                                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                        'pluginOptions' => [
                                            'format' => 'yyyy-mm-dd',
                                            //'format' => 'dd/mm/yyyy',
                                            'autoclose'=>true
                                        ]
                                    ]); ?>
                        </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane " id="items">
                        <div class=" " id="itemTable">
                            <?= $form->field($model, 'item')->textInput([ 'readonly' => true]) ?>
                        </div>
                    </div>
                   
                    </div>
                    <!-- /.tab-content -->
                </div>

                 
                
                
            </div>
             

            <div class="col-xs-12 ">         
                
                <div class="form-group">
                    <?= Html::button('<i class="far fa-save"></i> ' .Yii::t('common', 'Save'), ['class' => 'btn btn-success btn-flat save-btn']) ?>      
                    <?= Html::button('<i class="far fa-calendar-check"></i> ' .Yii::t('common', 'Send Approve'), ['class' => 'btn btn-primary btn-flat save-send-approve']) ?>                 
                </div>
                <?= $form->field($model, 'status')->hiddeninput()->label(false) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>


<?PHP 
$Yii = 'Yii';
$js=<<<JS

     

    // $( document ).ready(function() {
    //     //itemList($('form#promotions').attr('data-key'));

    // });

    

    $('body').on('click','button.save-btn',function(){
        $('input#promotions-status').val(0);
        $('form#promotions').submit();
    });

    $('body').on('click','button.save-send-approve',function(){
        if (confirm('{$Yii::t("common","Confirm ?")}')){
            $('input#promotions-status').val(1);
            $('form#promotions').submit();
        }else{
            return false;
        }
        
    });


    $('body').on('change','select#promotions-item_group',function(){
        //console.log($(this).val());
        $('#itemTable').html('');
         
    })

JS;
$this->registerJs($js);


 

$api = Yii::$app->params['api'];

$jsx=<<<JS
    const url = '{$api}';
    const t_code = '{$Yii::t("common","Code")}';
    const t_name = '{$Yii::t("common","Name")}';
    const t_product = '{$Yii::t("common","Product Name")}';
    const t_manage = '{$Yii::t("common","Manage")}';
    const t_edit = '{$Yii::t("common","Edit")}';
    
JS;

$this->registerJs($jsx,\yii\web\view::POS_HEAD);


$Option = ['depends' => [admin\assets\ReactAsset::className()]];

$Options =  ['depends' => [\admin\assets\ReactAsset::className()],'type'=>'text/jsx'];

$this->registerJsFile('//npmcdn.com/react-bootstrap-table/dist/react-bootstrap-table.min.js',$Option);
$this->registerCssFile('//npmcdn.com/react-bootstrap-table/dist/react-bootstrap-table-all.min.css',$Option); 
$this->registerJsFile('@web/js/saleorders/promotion.jsx?v=3.08.29',$Options); 	

?>


