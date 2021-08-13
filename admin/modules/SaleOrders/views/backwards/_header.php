<?php

 
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
 
use kartik\export\ExportMenu;
 
use kartik\field\FieldRange;
use kartik\widgets\DatePicker;
 

use kartik\widgets\Select2;
use common\models\CommonBusinessType;

?>
<div class="col-xs-12">
    <h3>โปรดทราบ</h3>
    <p class="ml-10">รายการที่สร้างขึ้น จะข้ามระบบการตัดสต๊อก</p>
    <p class="ml-10">เมื่อทำรายการจากหน้านี้ <span class="text-red">(ห้าม)</span> ไปทำ shipment (แพคสินค้าในใบสั่งขาย)</p>
</div>

 
<?php $form = ActiveForm::begin(['id' => 'order-search','method' => 'GET']); ?>
    <div   style="margin-bottom: 10px;">
        <div class="col-sm-5 col-md-6"> 
            <div class="row">
                <div class="col-sm-6" >
                    <?= $form->field($model, 'business_type')->widget(Select2::classname(),[
                        'name' => 'customer',
                        'data' => ArrayHelper::map(CommonBusinessType::find()->orderBy(['name' => SORT_ASC ])->all(),
                                'id',function($model){
                                    return Yii::t('common',$model->name);
                                }
                        ),
                        'options' => [
                            'placeholder' => Yii::t('common','Customer Group'),
                            'multiple' => false,
                            'class'=>'form-control ',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],

                    ])->label(false) ?>
                </div>
                <div class="col-sm-6">
                    <?php
 

                    echo DatePicker::widget([
                        'name' => 'SaleorderSearch[fdate]',
                        'value' => Yii::$app->request->get('SaleorderSearch')['fdate'] == '' ? '01-'.date('m-Y') : date('d-m-Y',strtotime(Yii::$app->request->get('SaleorderSearch')['fdate'])),
                        'type' => DatePicker::TYPE_RANGE,
                        'name2' => 'SaleorderSearch[tdate]',
                        'value2' => Yii::$app->request->get('SaleorderSearch')['tdate'] == '' ? date('t-m-Y') : date('d-m-Y',strtotime(Yii::$app->request->get('SaleorderSearch')['tdate'])),
                        'pluginOptions' => [
                            'autoclose'=>true,
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
        <div class="col-sm-3 col-md-2 ">
            <?= $form->field($model,'vat')->dropDownList(['' => Yii::t('common','All'), '1' => 'Vat','0' => 'No Vat'])->label(false)?>             
        </div>
        <div class="col-sm-4 col-md-4"> 
            <div class="box-tools">
                <div class="input-group  "  >
                    <?= $form->field($model,'search')->textInput(['class' => 'form-control','style' => 'margin-top:-10px;','placeholder' => Yii::t('common','Search')])->label(false)?>                    
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-default s-click"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.row -->
<?php ActiveForm::end(); ?>
 
 