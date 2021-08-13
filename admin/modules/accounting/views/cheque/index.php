<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;
 
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\ChequeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Cheques');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="cheque-index" ng-init="Title='<?=$this->title?>'">

 

<?php 
 $gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],

    //'id',
    //'posting_date',
    [
        'attribute' => 'create_date',
        'contentOptions' => ['class' => 'font-roboto'], 
        'value' => 'create_date',
        'filter' => DateRangePicker::widget([
            'model' => $searchModel,
            'attribute' => 'create_date',
            'convertFormat' => true,
            'options'   => [
                'autocompleate' => 'off',
                'class' => 'form-control'
            ],
            'pluginOptions' => [
                'locale' => [
                    'format' => 'Y-m-d',
                ],                                
            ],
            
        ]),
    ],
    [
        'attribute' => 'post_date_cheque',
        'contentOptions' => ['class' => 'font-roboto'], 
        'value' => function($model){
            return $model->know_date == 1 ? $model->post_date_cheque : null;
        },
        'filter' => DateRangePicker::widget([
            'model' => $searchModel,
            'attribute' => 'post_date_cheque',
            'convertFormat' => true,
            'options'   => [
                'autocompleate' => 'off',
                'class' => 'form-control'
            ],
            'pluginOptions' => [
                'locale' => [
                    'format' => 'Y-m-d',
                ],                                
            ],
            
        ]),
    ],
    [
        'attribute' => 'invoice.no_',
        'contentOptions' => ['class' => 'font-roboto'],   
        'format' => 'raw',
        'value' => function($model){
            //return $model->invoice ? $model->invoice->no_ : '' ;
            return Html::a($model->apply_to_no ? $model->apply_to_no : 'RC', ['view','id' => $model->id],['target' => '_blank']);
        }
    ],
    [
        'attribute' => 'cust_no_',
        'contentOptions' => ['class' => 'font-roboto'],   
        'format' => 'raw',
        'value' => function($model){
            return $model->customer ? Html::a($model->customer->code,['/customers/customer/view', 'id' => $model->customer->id],['target' => '_blank']) : '';
        }
    ],
    [
        'attribute' => 'cust_name_',
        'contentOptions' => ['class' => 'font-roboto'], 
        'value' => 'customer.name'
    ],
    [
        'attribute' => 'bank',
        'contentOptions' => ['class' => 'font-roboto'], 
        'value' => 'banklist.name'
    ],
    [
        'label' => Yii::t('common','Balance'),
        'contentOptions' => ['class' => 'text-right font-roboto'],   
        'headerOptions' => ['class' => 'text-right'],   
        'value' => function($model){
            return number_format($model->balance,2);
        }
    ],
    //'bank_account',
    //'bank_branch',
    //'bank_id',
    // 'create_date',
    // 'posting_date',
    // 'tranfer_to',
    // 'balance',
    //  [
    //     'label' => Yii::t('common','Approved'),
    //     'value' => function($model){
    //         return $model->getComplete();
    //     }
    //  ],
    // 'post_date_cheque',
    [
        'label' => 'Print',
        'format' => 'raw',
        'contentOptions' => ['class' => 'font-roboto'], 
        'value' => function($model){
            return Html::a('Print',['print', 'id' => $model->id],['target' => '_blank']);
        }
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'options'=>['style'=>'width:150px;'],
        'contentOptions' => ['class' => 'text-right'],   
        'headerOptions' => ['class' => 'text-right'],   
        'buttonOptions'=>['class'=>'btn btn-default'],
        'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {print} {view} {update} {delete}   </div>'
    ],

    //['class' => 'yii\grid\ActionColumn'],
];

?>
    <?php
        echo ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns,
                'columnSelectorOptions'=>[
                    'label' => ' ',
                    'class' => 'btn btn-warning'
                ],
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => 'Export All',
                    'class' => 'btn btn-primary'
                ],
                
            ]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        
        // 'dataProvider'=>new ActiveDataProvider([
        //     'query' => Adanalytics::find()->where(['advertiser_id' =>  Yii::$app->user->identity->id ])
        // ]),
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
    ]); ?>
</div>
