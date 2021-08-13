<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
//use kartik\grid\GridView;
use kartik\export\ExportMenu;
 
use kartik\daterange\DateRangePicker;

 

$this->title = Yii::t('common', 'Sale Not Invoice');
$this->params['breadcrumbs'][] = $this->title;
function utf8_strlen($string) {
    $c = strlen($string); $l = 0;
    for ($i = 0; $i < $c; ++$i)
    if ((ord($string[$i]) & 0xC0) != 0x80) ++$l;
    return $l;
}
?>
 

<div style="position: absolute; right: 20px; top: 75px;">
    <div class="hidden-xs hidden-sm text-right" >
    <?php
        echo ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => [

                    [
                        'label' => Yii::t('common','Date'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            return date('d/m/Y',strtotime($model->order_date));
                        },
                    ],
                    'no',
                    'customer.name',
                    'customer.address',
                    'customer.locations.province',
                     
                    'salespeople.code',                    
                    [
                        'attribute' => 'sales_people',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->salespeople['name']. ' '.$model->salespeople['surname'] ;
                        }
                    ],            
                    'balance'    
                ],
                'columnSelectorOptions'=>[
                    'label' => 'Columns',
                    'class' => 'btn btn-success-ew'
                ],

                'fontAwesome'       => true,
                'dropdownOptions'   => [
                    'label' => 'Export All',
                    'class' => 'btn btn-primary-ew'
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_PDF => false,
                ],
                'styleOptions' => [
                    ExportMenu::FORMAT_PDF => [
                        'font' => [
                             'family' => ['THSarabunNew','garuda'],
                                'bold' => true,
                                'color' => [
                                     'argb' => 'FFFFFFFF',
                             ],
                        ],
                    ],
                ],
                'target' => ExportMenu::TARGET_BLANK,
            ]);
        ?>
    </div>    
</div>

<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-header-index" ng-init="Title='<?=$this->title;?>'"> 
    <div class="row-">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table   table-hover'], 

            'pager' => [
                'options'=>['class' => 'pagination'],                   // set clas name used in ui list of pagination
                'prevPageLabel'     => '«',                             // Set the label for the "previous" page button
                'nextPageLabel'     => '»',                             // Set the label for the "next" page button
                'firstPageLabel'    => Yii::t('common','page-first'),   // Set the label for the "first" page button
                'lastPageLabel'     => Yii::t('common','page-last'),    // Set the label for the "last" page button
                'nextPageCssClass'  => 'next',                          // Set CSS class for the "next" page button
                'prevPageCssClass'  => 'prev',                          // Set CSS class for the "previous" page button
                'firstPageCssClass' => 'first',                         // Set CSS class for the "first" page button
                'lastPageCssClass'  => 'last',                          // Set CSS class for the "last" page button
                'maxButtonCount'    => 4,                               // Set maximum number of page buttons that can be displayed
            ],

            'options' => ['class' => 'table-responsive-'],
            'columns' => [
                [
                    'class'             => 'yii\grid\SerialColumn',
                    'options'           => ['style' => 'width:50px;'],
                    'headerOptions'     => ['class' => 'text-center hidden-xs'],
                    'filterOptions'     => ['class' => 'hidden-xs'],
                    'contentOptions'    => ['class' => 'text-center hidden-xs  show-doc']
                ],                       
                [
                    'attribute' => 'order_date',
                    'label' => Yii::t('common','Order Date'),
                    'format' => 'html',
                    'headerOptions' => ['class' => 'hidden-xs'],
                    'contentOptions' => ['class' => 'hidden-xs'],
                    'filterOptions'     => ['class' => 'hidden-xs','style' => 'width:150px;'],
                    'value' => function($model){
                        $html = ($model->order_date)? $model->order_date : ' ';
                        return Html::a($html,['/SaleOrders/saleorder/view','id' => $model->id]);
                    },
                    'filter' => DateRangePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'order_date',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'Y-m-d',
                            ],                                
                        ],                        
                    ]),
                ],                 
                [
                    'attribute'         => 'no',
                    'format'            => 'raw',
                    'contentOptions'    => ['class' => 'text-info doc-no','style' => 'position:relative;'],
                    'value'             => function($model){
    
                        // ตัดตัวอักษร ถ้ามากกว่า 35 ตัว
                        $count_char = utf8_strlen($model->customer['name']);
                        if($count_char >=32 )
                        {
                            $cust_name = iconv_substr($model->customer['name'],0,32,'UTF-8').'...';
                        }else {
                            $cust_name = $model->customer['name'];
                        }
    
    
                        if($model->vat_type==1)
                        {
                            $vat_color =  'text-success';
                        }else {
                            $vat_color =  'text-primary';
                        }
                       
    
                        $cus = '<div class="show-doc">';
                        $cus.= '<div class="text-customer-info">'.Yii::t('common',$cust_name).'</div>';
    
                        $cus.= '<div class="'.$vat_color.' text-order-number">'.$model->no.'</div>';
    
                        $cus.= '<div class="hidden-sm hidden-md hidden-lg text-right" style="position:absolute; right:15px; top:10px; color:#ccc;">
                                  <div class="text-aqua text-balance">
                                    <span  style="background-color:#fff; padding-left:5px;padding-right:5px;">'.number_format($model->balance,2).'</span>
                                  </div>
                                  <small class="hidden-sm hidden-md hidden-lg " style="padding-left:5px;padding-right:5px;"><i class="fas fa-clock"></i> '.$model->order_date.'</small>
                                </div>'."\r"; 
    
                        $cus.= '</div>';
                        $cus.= '<div class="hidden-sm hidden-md hidden-lg">
                                    <a class="actions-menu" href="javascript:void(0);" data-rippleria></a>
                                </div>';
    
                        return Html::a($cus,['/SaleOrders/saleorder/view','id' => $model->id]);
                    },
                ],            
                [
                    'attribute'         => 'sale_id',
                    'label'             => Yii::t('common','Sale Person'),
                    'format'            => 'raw',
                    'contentOptions'    => ['class' => 'hidden-xs show-doc'],
                    'headerOptions'     => ['class' => 'hidden-xs'],
                    'filterOptions'     => ['class' => 'hidden-xs'],
                    'value'             => function($model){
                        $html = '<div id="sale-name">'.$model->salespeople['name'].' '. $model->salespeople['surname'].'</div>';
                        $html.= '<small style="color:#ccc;">['.$model->salespeople['code'].']</small>';
                        return Html::a($html,['/SaleOrders/saleorder/view','id' => $model->id]);
                    }
                ],            
                [
                    'attribute'         => 'balance',
                    'label'             => Yii::t('common','Balance'),
                    'format'            => 'raw',
                    'contentOptions'    => ['class' => 'text-right hidden-xs '],
                    'filterOptions'     => ['class' => 'hidden-xs'],
                    'headerOptions'     => ['class' => 'hidden-xs'],
                    'value'             => function($model){

                        $color = NULL;
    
                        $html = '<div><div class="'.$color.'">'.number_format($model->balance,2).'</div></div>';

                        return Html::a($html,['/SaleOrders/saleorder/view','id' => $model->id]);
                    },
                ], 
                
                     
            ],
            
        ]); ?>

    </div>
</div>



 

