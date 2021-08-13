<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\daterange\DateRangePicker;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\warehousemoving\models\WarehouseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Warehouse Movings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-moving-index" ng-init="Title='<?=$this->title?>'">

<?= Html::a('<i class="fas fa-table"></i> '.$this->title,['index'],['class' => "text-primary"]) ?>
<div class="  margin-top">
    <div class=" ">
        <!-- <p>Filter</p> -->
    
        <div class="row">
            <div class="col-sm-3"></div>                      
            <div class="col-sm-3"><!--<button class=" btn btn-primary"><i class="fas fa-search"></i> Filter</button>--></div>
            <div class="col-sm-6">
                <div class="text-right">
                    <?php
                    echo ExportMenu::widget([
                                'dataProvider' => $dataProvider,
                                'columns' => [                    
                                    'PostingDate',                    
                                    'SourceDocNo', 
                                    [
                                        'label' => Yii::t('common','DocumentNo'),
                                        'format' => 'raw',
                                        //'contentOptions' => ['class' => 'text-right'],
                                        'value' => function($model){                               
                                            return $model->DocumentNo;        
                                        }
                                    ],                   
                                    [
                                        'attribute' => 'ItemNo',
                                        'format' => 'raw',
                                        'contentOptions' => ['class' => 'text-right'],
                                        'value' => function($model)
                                        {
                                            if($model->item!=''){
                                                return $model->itemstb->master_code;
                                            }
                                            
                                        }
                                    ],
                                    'itemstb.description_th',
                                    // 'Quantity',
                                    [
                                        'attribute' => 'Quantity',
                                        'format' => 'raw',
                                        'contentOptions' => ['class' => 'text-right'],
                                        'value' => function($model)
                                        {
                                            //return $model->Quantity * (int)$model->qty_per_unit;
                                            $qty_per = ($model->qty_per_unit)? $model->qty_per_unit : 1;
                                            return ($model->Quantity * $qty_per) * 1;
                                        }
                                    ],
                                    'locations.name',
                                    [
                                        'attribute' => 'unit_price',
                                        'format' => 'raw',
                                        'contentOptions' => ['class' => 'text-right'],
                                        'value' => function($model)
                                        {
                                            return $model->unit_price;
                                        }
                                    ]
                                ],
                                'columnSelectorOptions'=>[
                                    'label' => 'Columns',
                                    'class' => 'btn btn-success-ew'
                                ],
                                'fontAwesome' => true,
                                'dropdownOptions' => [
                                    'label' => 'Export All',
                                    'class' => 'btn btn-primary-ew'
                                ],
                                'exportConfig' => [
                                    ExportMenu::FORMAT_HTML => false,
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
        </div>
        </div>
</div>
 <div style="font-family: saraban;">
<?= GridView::widget([
        'dataProvider'  => $dataProvider,
        'filterModel'   => $searchModel,
        'rowOptions'    => function($model){
            return [
                'class' => @$_GET['WarehouseSearch']['rowid'] == $model->id ? 'bg-yellow' : ' ',
            ];
        },
        'tableOptions' => [
            'class' => 'table table-bordered table-hover',
        ],
        'pjax' => false,
        'responsiveWrap' => false,
        'pager' => [
            'options'   => ['class'=>'pagination'],   // set clas name used in ui list of pagination
                'prevPageLabel'     => '«',   // Set the label for the "previous" page button
                'nextPageLabel'     => '»',   // Set the label for the "next" page button
                'firstPageLabel'    => Yii::t('common','First'),   // Set the label for the "first" page button
                'lastPageLabel'     => Yii::t('common','Last'),    // Set the label for the "last" page button
                'nextPageCssClass'  => Yii::t('common','next'),    // Set CSS class for the "next" page button
                'prevPageCssClass'  => Yii::t('common','prev'),    // Set CSS class for the "previous" page button
                'firstPageCssClass' => Yii::t('common','first'),    // Set CSS class for the "first" page button
                'lastPageCssClass'  => Yii::t('common','last'),    // Set CSS class for the "last" page button
                'maxButtonCount'    => 6,    // Set maximum number of page buttons that can be displayed
            ],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['class' => 'bg-gray'],
                    'contentOptions' => ['class' => 'font-roboto']
                ],
                //'id',
                
                [
                    'attribute' => 'PostingDate',
                    'headerOptions' => ['class' => 'bg-gray',],
                    'contentOptions' => ['class' => 'font-roboto'],
                    'format' => 'html',
                    'value' => function($model){                               
                        return date('Y-m-d',strtotime($model->PostingDate)). ' <span class="text-gray">'.date('H:i:s',strtotime($model->PostingDate)).'</span>';
                    },
                    'filter' => DateRangePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'PostingDate',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'Y-m-d',
                            ],                                
                        ],
                        
                    ]),
                ],   
                [
                    'label' => Yii::t('common','Ship'),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'bg-gray'],
                    'contentOptions' => ['class' => 'font-roboto text-center'],
                    'value' => function($model){
                        return $model->header->saleOrder != null
                                    ? ($model->header->saleOrder->status == 'Shiped'
                                        ? '<i class="far fa-check-square"></i>'
                                        : '')
                                    : '';
                    }
                ],                  
                [
                    'label' => Yii::t('common','Sale Order'),
                    'headerOptions' => ['class' => 'bg-gray'],
                    'contentOptions' => ['class' => 'font-roboto'],
                    'format' => 'raw',
                    'value' => function($model){   
                        
                        if($model->TypeOfDocument =='Sale-Return'){

                            $html =  $model->header->saleReturn != null
                                        ? Html::a($model->header->saleReturn->no,['/SaleOrders/return/view', 'id' => $model->header->saleReturn->id],['target' => '_blank'])
                                        : '';

                        }else{
                       
                            $html =  $model->header->saleOrder != null
                                        ?   Html::a($model->header->saleOrder->no,['/SaleOrders/saleorder/view', 'id' => $model->header->saleOrder->id],['target' => '_blank'])                                            
                                        : '';

                        }

                        return $html;

                            
                    }
                ],                 
                [
                    'label' => Yii::t('common','Customer'),
                    'headerOptions' => ['class' => 'bg-gray'],
                    'format' => 'raw',
                    'value' => function($model){
                        return  $model->header->saleOrder != null
                                    ? $model->header->customer != null
                                        ?  Html::a($model->header->customer->code,['/customers/customer/view-only', 'id' => $model->header->customer->id],['target' => '_blank', 'data-toggle' => "tooltip", 'title' => $model->header->customer->name])
                                        : ''
                                    : '';
                    }
                ],

                [
                    'attribute' => Yii::t('common','DocumentNo'),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'bg-gray'],
                    'contentOptions' => ['class' => 'font-roboto'],
                    'value' => function($model){
                        $Type = '';                        
                        if(in_array($model->TypeOfDocument, ['Sale'])){
                            $Type = $model->TypeOfDocument.' -> ' ;
                        }else if(in_array($model->TypeOfDocument, ['Adjust'])){   
                            $Type = $model->TypeOfDocument.' '.($model->Quantity > 0 ? '( + )' : '( - )'). ' '.$model->header->remark;
                        }else if(in_array($model->TypeOfDocument, ['Sale-Return'])){   
                            $Type = $model->TypeOfDocument.' '.($model->Quantity > 0 ? '( + )' : '( - )'). ' '.$model->header->remark;
                        }else if($model->header->TypeOfDocument == 'Craft'){
                            $Type = Yii::t('common','Production').' '.($model->Quantity > 0 ? '( + )' : '( - )');
                        }else {
                            $Type = $model->TypeOfDocument.' '.($model->Quantity > 0 ? '( + )' : '( - )');
                        }                  

                        if($model->urlLink != '#'){
                            $html = '<a href="'.$model->urlLink.'" target="_blank" data-toggle="tooltip" title="'.$Type.'">';
                            $html.=     $model->DocumentNo;
                            $html.= '</a>';
                        }else {
                            $html = '<span class="pointer" data-toggle="tooltip" title="'.$Type.'">';
                            $html.=     $model->DocumentNo;
                            $html.= '</span>';                            
                        }
                        return $html;

                    }
                ],
                
                [
                    'attribute' => 'ItemNo',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'bg-gray'],
                    'contentOptions' => ['class' => 'font-roboto'],
                    'label' => Yii::t('common','Code'),
                    'value' => function($model){    
                        if($model->item==1414){
                            $html = $model->ItemNo;                
                        }else{
                            $html = $model->itemstb->master_code;
                        }              
                        return Html::a($html,['/items/items/view', 'id' => $model->item],['target' => '_blank']);
                    }
                ], 

                [
                    'attribute' => 'items.description_th',
                    'headerOptions' => ['class' => 'bg-gray'],
                    'contentOptions' => ['class' => 'font-roboto'],
                    'label' => Yii::t('common','Product Name'),
                    'value' => function($model){                            
                        return $model->Description;
                    }
                ], 
                //'itemstb.description_th',
                //'locations.name',
                [
                    //'attribute' => 'TypeOfDocument',
                    'label' => Yii::t('common','Type of documents'),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-right  bg-gray','style' => 'min-width:120px;'],
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function($model){
                        // ถ้า type = 'Sale' และมี apply_to แสดงว่า undu
                        if($model->apply_to && $model->TypeOfDocument == 'Sale'){
                            return Yii::t('common','Undo');
                        }else{
                            if($model->TypeOfDocument == 'Journal'){
                                return Yii::t('common','Transfer Location') ;
                            }else if($model->TypeOfDocument == 'Invoice'){
                                return Yii::t('common','Direct Invoice');   
                            }else if($model->TypeOfDocument == 'Sale-Return'){
                                return $model->Quantity > 0 
                                            ? Yii::t('common','Return company')
                                            : Yii::t('common','Return customer');
                            }else if($model->TypeOfDocument == 'Purchase'){
                                return Yii::t('common','Buy');   
                            }else{
                                return Yii::t('common',$model->TypeOfDocument);                               
                            }
                        }
                                   
                        // return '<span title="'.$model->users->username.'">'.Yii::t('common', 
                        //     ($model->apply_to && $model->TypeOfDocument == 'Sale') 
                        //             ? Yii::t('common','Undo') 
                        //             : $model->TypeOfDocument == 'Journal' 
                        //                 ? Yii::t('common','Transfer Location') 
                        //                 : Yii::t('common',$model->TypeOfDocument)
                        //     ).'</span>';
                    }
                ],
                
                [
                    //'attribute' => 'Quantity',
                    'label' => Yii::t('common','Before adjust'),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-right  bg-gray'],
                    'contentOptions' => ['class' => 'text-right','style' => 'font-family: roboto;'],
                    'value' => function($model){
                        //return number_format($model->qty_before);
                        $digit_stock    = Yii::$app->session->get('digit') ? Yii::$app->session->get('digit')->stock : 0;
                        if(in_array($model->TypeOfDocument,['Adjust','Output'])){
                            return number_format($model->QtyMoved,$digit_stock);
                        }else {
                            return number_format($model->qty_before,$digit_stock);
                        }
                    }
                ],

                [
                    'attribute' => 'Quantity',
                    'label' => Yii::t('common','Quantity'),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-right bg-gray'],
                    'contentOptions' => ['class' => 'text-right bg-gray font-roboto'],
                    'value' => function($model){
                        $digit_stock    = Yii::$app->session->get('digit') ? Yii::$app->session->get('digit')->stock : 0;
                        $color          = "#00b703"; // green                       
                        if(($model->Quantity * $model->qty_per_unit) < 0) $color = "#ff2e00";       // red                                           
                        $qty_per = ($model->qty_per_unit)? $model->qty_per_unit : 1;
                        return '<span style="color :'.$color.';">'.number_format($model->Quantity * $qty_per,$digit_stock).'</span>';
                    }
                ],

                [
                    //'attribute' => 'Quantity',
                    'label' => Yii::t('common','After adjust'),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-right bg-gray'],
                    'contentOptions' => ['class' => 'text-right','style' => 'font-family: roboto;'],
                    'value' => function($model){
                        //return number_format($model->qty_after);
                        $digit_stock    = Yii::$app->session->get('digit') ? Yii::$app->session->get('digit')->stock : 0;
                        if(in_array($model->TypeOfDocument,['Adjust','Output'])){
                            return number_format($model->QtyToMove,$digit_stock);
                        }else {
                            return number_format($model->qty_after,$digit_stock);
                        }
                    }
                ],
               
                // [
                //     'attribute' => 'unit_price',
                //     'format' => 'raw',
                //     'headerOptions' => ['class' => 'text-right','style' => 'min-width:100px;'],
                //     'contentOptions' => ['class' => 'text-right'],
                //     'value' => function($model)
                //     {
                //         return number_format($model->unit_price,2);
                //     }
                // ]
            ],
        
    ]); ?>
</div>





<?php
$js =<<<JS
    $(document).ready(function(){
        $('[data-toggle=\"tooltip\"]').tooltip(); 
    });            
    
    $('body').on('change','#wh-filter-change',function(){
        window.location.href = 'index.php?r=warehousemoving%2Fwarehouse%2Findex&method='+$(this).val();
    })
JS;
$this->registerJs($js,\yii\web\View::POS_END);
?>
