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
<?=$this->registerCssFile('@web/css/warehouse/journal_form.css?v=3.06.01.01');?> 
<div class="warehouse-header-form" ng-controller="adjustController">

    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'item-reclass-journal',
            'data-key' => $model->id,
            'modules' => 'Adjust'
        ]
    ]); ?>

    <div class="panel panel-default  " style="padding: 20px;">

        <div class="row">        
            <div class="col-sm-12" >

                <div class=" " >
                    <div class="col-sm-6 text-right"> </div>
                    <div class="col-sm-6 text-right">
                            <h3><?=Yii::t('common','Item Journal')?></h3>
                    </div>
                </div>

                <div class=" " >
                    <div class="col-sm-9 text-right"> </div>
                    <div class="col-sm-3 text-right">
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
                                'removeButton' => false,
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'autoclose'=>true
                                ]
                            ]);
                        ?>
                    </div>
                </div>

                <div>
                    <div class="col-sm-3 text-right"></div>    
                    <div class="col-sm-3 text-right">
                        <?=$form->field($model,'DocumentDate')->hiddenInput()->label(false)?>                              
                    </div>        
                    <div class="col-sm-3 text-right"><?= $form->field($model, 'DocumentNo')->textInput(['maxlength' => true,'data' => $model->id]) ?> </div>
                    <div class="col-sm-3 text-right"><?= $form->field($model, 'SourceDoc')->textInput(['maxlength' => true,'placeholder' => 'No. : OP , SO , Invoice']) ?></div>                    
                </div>

            </div>
        </div>


        <div class="row">

            <div class="col-sm-9">

                <div class="row">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4 col-xs-6">                                                
                        <?=$form->field($model,'AdjustType')->hiddenInput()->label(false)?>                        
                    </div>
                    <div class="col-sm-4 col-xs-6">
                        <?=$form->field($model,'TypeOfDocument')->hiddenInput()->label(false)?>      
                    </div>
                </div>

            </div>

            <div class="col-sm-3 text-right"></div>

        </div>
    </div>




 

    <div class="row">
        <div class="col-sm-12 ew-adj-row">
            <div class="table-responsive ew-adj-line">
                <span class="blink text-gray">Rendering... </span>
            </div>

            <div class="ewTimeout text-center" style="position: absolute; top: 0px; left: 45%;  ">
                <i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
                <div class="blink"> Loading .... </div>
                <script type="text/javascript">
                    setTimeout(function(){ $('.ewTimeout').html('<span style="color:red"><?=Yii::t('common','Server not responding.')?>...</span>');}, 50000);
                </script>
            </div>

            <div class="render-search-item"></div>

        </div>
    </div>

    

    <div class="content-footer mt-10"  >
 
        <div class="row">
            <div class="col-sm-12 text-right">
                 
                <?= Html::Button('<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save') , ['class' => 'btn btn-success-ew submit-form']) ?>
                <?php if($model->id!=''): ?>
                <button id="ew-Post-Adjust" type="button" class="btn btn-danger-ew"><i class="fa fa-server" aria-hidden="true"></i> <?=Yii::t('common','POST')?></button>
                <?php endif;?>
                 
            </div>
        </div>  
         
    </div>



    <div id="menuFilter" class="panel-collapse collapse in hidden" role="tabpanel" aria-labelledby="headingOne">
        <!-- Menu Filter -->
        <?php // $this->render('../adjust/_FilterProduct'); ?>
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





 

<?php $this->registerJsFile('js/warehouse/reclass-journal.js?v=3.06.21.4');?>
<?php $this->registerJsFile('js/no.series.js?v=3.03.28');?>
<?php $this->registerJsFile('js/manufacturing/item_set.js?v=3.03.28');?>
<?php $this->registerJsFile('js/warehouse/adjustController.js?v=3.03.20.1');?>

<?php $this->registerJsFile('js/item-picker.js?v=3.03.28');?>

<?=$this->render('@admin/views/setupnos/__modal'); ?>
<?php //$this->render('@admin/modules/SaleOrders/views/modal/_pickitem',['model' => $model]) ?>
<?=$this->render('@admin/modules/items/views/ajax/__modal_get_item'); ?>



<?php
$js=<<<JS

    
    $(document).ready(function(){
        $('.ew-save-common').show();
        // ดึงรายการสินค้ามาแสดง โดนสุ่มออกมา 8 แถว
        //route('index.php?r=Itemset/ajax/menu-random','GET',{param:{data:0}},'ResourceItemSearch');

        var footer = $('div.content-footer').html();
        $('footer').html(footer).find('div.content-footer').fadeIn('slow');
        
    })
    
   
   
    $('body').on('change','input',function(){
        $('.btn-danger-ew').attr('disabled',true);
    });







JS;
$this->registerJs($js, yii\web\View::POS_END, 'js-options');
