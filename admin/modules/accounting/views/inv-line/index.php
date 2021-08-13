<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\InvLineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Invoice Lines');
$this->params['breadcrumbs'][] = $this->title;
?>
 
<div class="view-invoice-line-index">

    
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    
    <?php 
     $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],
        
        [
            'attribute' => 'posting_date',
            'contentOptions' => ['style' => 'font-family: roboto;'],
            'value' => function($model){
                return date('Y-m-d', strtotime($model->header->posting_date));
            }
        ],
        [
            'attribute' => 'header.customer.code',
            'format' => 'raw',
            'contentOptions' => ['style' => 'font-family: roboto;'],
            'value' => function($model){
                return Html::a(mb_substr($model->header->customer->name, 0, 20), ['/customers/customer/view-only', 'id' => $model->header->customer->id],['target' => '_blank', 'title' => $model->header->customer->name]);
            }
        ],
        [
            'attribute' => 'items.master_code',
            'contentOptions' => ['style' => 'font-family: roboto;'],
            'value' => function($model){
                return $model->items->master_code;
            }
        ],
        'items.description_th',
        [
            'attribute' => 'doc_no_',
            'format' => 'raw',
            'contentOptions' => ['style' => 'font-family: roboto;'],
            'value' => function($model){
                if($model->posted =='posted'){
                    $html = Html::a($model->doc_no_,
                    ['/accounting/posted/print-inv','id' => base64_encode($model->source_id), 'footer' => 1],['target' => '_blank', 'class' => 'text-green']);
                }else{
                    $html = Html::a($model->doc_no_,
                    ['/accounting/saleinvoice/print-inv-page','id' => $model->source_id, 'footer' => 1],['target' => '_blank', 'class' => 'text-orange']);
                }
                return $html;
            }
        ],
        [
            'attribute' => 'quantity',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right','style' => 'font-family: roboto;'],
            'value' => function($model){
                return number_format($model->quantity,2);
            }
        ],
        [
            'attribute' => 'unit_price',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right','style' => 'font-family: roboto;'],
            'value' => function($model){
                return number_format($model->unit_price,2);
            }
        ]
    ];

     ?>
    <div class="row">
        <div class="col-sm-12 text-right">
        <?php
            echo ExportMenu::widget([
                'dataProvider' =>  $dataProvider,
                'columns' => $gridColumns,
                'columnSelectorOptions'=>[
                    'label' => Yii::t('common','Columns'),
                    'class' => 'btn btn-success-ew'
                ],
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => Yii::t('common','Export All'),
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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=> Yii::t('common','First'),   // Set the label for the "first" page button
            'lastPageLabel'=> Yii::t('common','Last'),    // Set the label for the "last" page button
            'nextPageCssClass'=> Yii::t('common','next'),    // Set CSS class for the "next" page button
            'prevPageCssClass'=> Yii::t('common','prev'),    // Set CSS class for the "previous" page button
            'firstPageCssClass'=> Yii::t('common','first'),    // Set CSS class for the "first" page button
            'lastPageCssClass'=> Yii::t('common','last'),    // Set CSS class for the "last" page button
            'maxButtonCount'=> 5,    // Set maximum number of page buttons that can be displayed
            ],
    ]); ?>
</div>
