<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
 
$column = [
    ['class' => 'kartik\grid\SerialColumn'],
    'code',
    'name',
    'address',
    'provincetb.PROVINCE_NAME',
    [
        'format' => 'raw',
        'label' => Yii::t('common','Zip Code'),
        'contentOptions' => ['class' => 'font-roboto text-right'],
        'value' => function($model){
            return (string)$model->postcode;
        }
    ],
    [
        //'attribute' => 'balance',
        'format' => 'raw',
        'label' => Yii::t('common','Amount'),
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'font-roboto text-right'],
        'value' => function($model){
            return number_format($model->totalBanalce);
        }
    ]    
];
?>

<div class="row">
    <div class="col-xs-12 text-right">
        <?php
            echo ExportMenu::widget([
                'dataProvider'          => $dataProvider,
                'showColumnSelector'    => false,
                'target'                => ExportMenu::TARGET_BLANK,
                'columns'               => $column,
                'filename'              => Yii::t('app', 'Customer'),
                'columnSelectorOptions' => [
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
                ]
            ]);
        ?>
    </div>
    <div class="col-xs-12">
        <h4>รายชื่อลูกค้าที่มีการขายในปี <?=date('Y');?></h4>
        <?= GridView::widget([
                'dataProvider'      => $dataProvider,
                'responsiveWrap'    => false,
                'columns'           => [
                    ['class' => 'kartik\grid\SerialColumn'],
                    'code',
                    'name',
                    'address',
                    'provincetb.PROVINCE_NAME',
                    [
                        'format' => 'raw',
                        'label' => Yii::t('common','Zip Code'),
                        'contentOptions' => ['class' => 'font-roboto text-right'],
                        'value' => function($model){
                            return (string)$model->postcode;
                        }
                    ]                               
                ]
            ]); 
        ?>
    </div>
</div>