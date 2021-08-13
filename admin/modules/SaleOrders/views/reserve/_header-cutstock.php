<?php
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use common\models\CommonBusinessType;
?>

<div class="row" style="margin-bottom: 10px; padding-top: 20px; margin-top: -15px; border-bottom:1px solid #ededed;">
<?php $form = ActiveForm::begin(['id' => 'order-search','method' => 'GET']); ?>   
    <div class="col-sm-5 col-md-6"> 
        <div class="row">
            <div class="col-xs-6 col-sm-4" >
                 
                 <?= $form->field($model, 'status')->dropDownList([
                        ''                      => Yii::t('common','Status'), 
                        "Checking"  => Yii::t('common','Checking..'),
                        "Shiped"  => Yii::t('common','Shiped..'),
                        "Invoiced"  => Yii::t('common','Invoice created')
                     ])->label(false)?>  
            </div>
            <div class="col-sm-8 mb-10">
                <?php
                    echo DatePicker::widget([
                    'name'  => 'ReserveSearch[fdate]',
                    'value' => Yii::$app->request->get('ReserveSearch')['fdate'] == '' ? '01-'.date('m-Y') : date('d-m-Y',strtotime(Yii::$app->request->get('ReserveSearch')['fdate'])),
                    'type'  => DatePicker::TYPE_RANGE,
                    'name2' => 'ReserveSearch[tdate]',
                    'value2'=> Yii::$app->request->get('ReserveSearch')['tdate'] == '' ? '31-12-'.date('Y') : date('d-m-Y',strtotime(Yii::$app->request->get('ReserveSearch')['tdate'])),
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-mm-yyyy'
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ],
                    'options2' => [
                        'autocomplete' => 'off'
                    ],
                    'pluginEvents' => [
                    //"changeDate" => "function(e) { ReloadSearch(); }",
                    ],
                ]);

                ?>
            </div>
        </div> 
    </div>
    <div class="col-xs-3 col-sm-3 col-md-2">
               
    </div>
    <div class="col-xs-9 col-sm-4 col-md-4"> 
        <div class="box-tools">
            <div class="input-group  "  style="margin-bottom: 20px;" >
                <?= $form->field($model,'search')->textInput(['class' => 'form-control','style' => 'margin-top:-10px;','placeholder' => Yii::t('common','Search')])->label(false)?>                    
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-default s-click"><i class="fa fa-search"></i></button>
                     
                </div>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>
</div><!-- /.row -->
 
 