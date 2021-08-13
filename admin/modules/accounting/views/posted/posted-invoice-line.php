<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;
use common\models\Company;
use kartik\widgets\DatePicker;

use kartik\grid\GridView;
//use yii\grid\GridView;

 
 
?>
 
<?php 
    $gridColumns = [
        [
            'headerOptions' => ['class' => 'bg-gray'],
            'contentOptions' => ['class' => 'bg-gray'],
            'class' => 'yii\grid\SerialColumn'
        ],
        [
            'label' => Yii::t('common','Item'),
            'headerOptions' => ['class' => 'bg-gray'],
            'contentOptions' => ['class' => ' '],
            'format' => 'raw',
            'value' => function($model){
                if($model->type=='Item')
                {
                    $code = $model->crossreference->no;
                }else {
                    $code = 'G/L Number';
                }                        
                $color = '';
                        if($model->status=='delete') $color = 'text-red';
                        if($model->item=='1414') $color = 'text-orange';
    
                        $html = '<div class="'.$color.'">'.Html::a($code,['/items/items/view', 'id' => $model->item],['target' => '_blank']).'</div>';
    
                        // ถ้าเป็นข้อความ
                        // 
                        if($model->item=='1414'){
                            if($model->code_no_=='1^x'){
                                $html = '<div class="'.$color.'"> </div>';
                            }else if($model->code_no_==' '){
                                $html = '<div class="'.$color.'"> </div>';
                            }else {
                                $html = '<div class="'.$color.'">'.Html::a($model->code_no_,['/items/items/view', 'id' => $model->item],['target' => '_blank']).'</div>';
                            }
                            
                        } 
                        return $html;
            },
            
        ],
        [
            'label' => Yii::t('common','Name'),
            'headerOptions' => ['class' => 'bg-gray'],
            'value' => function($model){
                return $model->code_desc_;
            },                     
        ],
        [
            'label' => Yii::t('common','Quantity'),
            'headerOptions' => ['class' => 'text-right bg-gray', 'style' => 'width:115px'],
            'contentOptions' => ['class' => 'text-right'],
            'format' => 'raw',
            'value' => function($model){
                return '<input type="text" value="'.number_format($model->quantity,2).'" name="quantity" class="form-control text-right quantity-change" style="width:115px;" />';
            },                     
        ],

        [
            'label' => Yii::t('common','Measure'),
            'headerOptions' => ['class' => 'text-right bg-gray','style' => 'width:120px', 'title' => 'หากไม่มีให้เลือก ต้องไปเพิ่มหน่วยในสินค้าตัวนั้นๆ'],
            'contentOptions' => ['class' => ' '],
            'format' => 'raw',
            'value' => function($model){
                $html = '<select class="form-control measure-change">';
                //$html.= '<option value="0">'.Yii::t('common','(not set)').'</option>';
                foreach ($model->itemMeasures as $key => $value) {
                    $html.= '<option value="'.$value->measures->id.'" '.($model->measure == $value->measure ? 'selected' : '').'>'.$value->measures->UnitCode.'</option>';
                }
                $html.= '</select>';

                return $html;
            },                     
        ],

        [
            'label' => Yii::t('common','Unit Price'),
            'headerOptions' => ['class' => 'text-right bg-gray', 'style' => 'width:115px'],
            'contentOptions' => ['class' => 'text-right'],
            'format' => 'raw',
            'value' => function($model){                                 
                return '<input type="text" value="'.number_format($model->unit_price,2).'" name="unit_price" class="form-control text-right price-change" style="width:115px;" />';
            },                     
        ], 

        [
            'label' => Yii::t('common','Discount'),
            'headerOptions' => ['class' => 'text-right bg-gray'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function($model){
                return number_format($model->line_discount,2);
            },                     
        ], 

        [
            'label' => Yii::t('common','Amount'),
            'headerOptions' => ['class' => 'text-right bg-gray'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function($model){
                return number_format(($model->quantity * $model->unit_price)- $model->line_discount,2);
            },
            
        ], 
    ];
?>
<?=  GridView::widget([
    'dataProvider'=> $dataProvider,
    //'filterModel' => $searchModel,
    'summary' => false,
    //'showFooter' => true,
    'rowOptions' => function($model){
        if($model->referCreditNote){
            return ['class' => 'bg-danger'];
        }
    },
    'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
    'columns' => $gridColumns,
    //'responsive'=>true,
    //'hover'=>true,
    //'summary' => false,
    'striped'=>false, 
    'responsiveWrap' => false,
]);
?>                
             