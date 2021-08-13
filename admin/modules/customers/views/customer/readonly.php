<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
 
//use yii\grid\GridView;
//use yii\widgets\Pjax;
use common\models\Province;
use yii\helpers\ArrayHelper;

use common\models\SalesPeople;

use kartik\export\ExportMenu;
use kartik\widgets\SwitchInput;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\customers\models\SearchCustomer */
/* @var $dataProvider yii\data\ActiveDataProvider */
$Profile  = \common\models\Profile::findOne(Yii::$app->user->identity->id);
$this->title = Yii::t('common', 'Customers');
$this->params['breadcrumbs'][] = $this->title;
 
        $gridColumns = [

                [
                    'attribute' => 'code',
                    'filterOptions' => ['class' => 'hidden-xs'],
                    'headerOptions' => ['class' => 'hidden-xs','style' => 'width:100px;'],
                    'contentOptions' => ['class' => 'hidden-xs'],
                    'format' => 'raw',
                    'value' => function ($model) { 
                        return $model->code;
                    },
                ],
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'max-width:460px; padding: 20px 10px 20px 10px'],
                    'value' => function ($model) { 

                        $html = '<div style="font-size: medium;">'.Html::a($model->name,['view-only','id' => $model->id]).'</div>';
                        $html.= '<small style="max-width:160px; hyphens: auto;">'.$model->locations->address.'</small>';
                        $html.= '<div style="margin-top:15px;">'.($model->contact? '<i class="far fa-user-circle"></i> '.$model->contact : null).'</div>';
                        $model->phone = str_replace(" ",",",$model->phone);
                        $mobiles = explode(",",$model->phone);
                        foreach ($mobiles as $mobile) {
                            if($mobile)
                            $html.= '<div><i class="fas fa-mobile-alt"></i> '.($mobile ? $mobile : '--').'</div>';
                        }
                        return $html;
                    },
                ],

                
                [
                    'attribute' => 'province', 
                    'label' => Yii::t('common','Province'),
                    'filterOptions' => ['class' => ' '],
                    'headerOptions' => ['class' => ' '],
                    'contentOptions' => ['class' => ' '],
                    'value' => function($model){
                        $Province = '';
                        if($model->province!='') $Province = $model->provincetb->PROVINCE_NAME;
                        return $Province;
    
                    }
                ],
                [
                    'attribute' => 'region', 
                    'filterOptions' => ['class' => 'hidden-xs'],
                    'headerOptions' => ['class' => 'hidden-xs'],
                    'contentOptions' => ['class' => 'hidden-xs'],
                    'value' => 'provincetb.zone.name',
                    'filter'=>ArrayHelper::map(\common\models\Zone::find()->asArray()->all(), 'id', 'name'),
                    'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => Yii::t('common','Show All')],
                ],

                [
                    'attribute' => 'owner_sales',
                    'format' => 'raw',
                    'filterOptions' => ['class' => 'hidden-xs'],
                    'headerOptions' => ['class' => 'hidden-xs'],
                    'contentOptions' => ['class' => 'hidden-xs', 'style' => 'max-width:150px; overflow-x:auto;'],
                    'visible' => (Yii::$app->session->get('Rules')['name'] == 'Administrator' || Yii::$app->session->get('Rules')['name'] == 'Accounting'),
                    'value' => function ($model) { 
                        
                        if(SalesPeople::find()->where(['code' => explode(',',$model->owner_sales)])->count()>0)
                        {
                            $sales = SalesPeople::find()
                            ->where(['code' => explode(',',$model->owner_sales)])
                            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->all();
                            $salpeople = '';
                            foreach ($sales as $people) {
                                $salpeople.= '<span>['.$people->code.'] '.$people->name.'</span> <br />'."\r\n"; 
                            }
                            return $salpeople;
    
                        }else {
                            return '-';
                        }
                    },
                    'filter' => ArrayHelper::map(\common\models\SalesPeople::find()
                                                    ->where(['status'=> 1])
                                                    ->andWhere(['comp_id' => \Yii::$app->session->get('Rules')['comp_id']])
                                                    ->orderBy(['code' => SORT_ASC])
                                                    ->all(), 
                                                    'code',
                                                    function($model){ return '['.$model->code.'] '.$model->name. ' '.$model->surname; }
                    ),
                    'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => Yii::t('common','Every one')],
                ],
                [
                    'attribute' => 'payment_due',
                    'format' => 'raw',
                    //'label' => Yii::t('common','Pay Every date'),
                    //'headerOptions' => ['class' => 'text-right'],
                    //'contentOptions' => ['class' => 'text-right'],
                    'value' => function($model){
                        return $model->payment_due ? Yii::t('common','Every date').' '. $model->payment_due : '';
                    }
                ] 
            
        ];

?>
 
<style>
    .pagination {
        margin: 0px;
    }
    .form-group {
        margin-bottom: 0px;
    }
    @media print {
        .no-print, .no-print *{
            display: none !important;
        }
    }
</style>

<div class="customer-index" ng-init="Title='<?=$this->title;?>'">
<div class="panel-heading" style="position: relative; height:55px;">
    <div class="row"><?= $Profile->sales != null ? $Profile->sales->name : Yii::t('common','None') ?></div>     
 
    </div>
  <?php
$layout = <<< HTML
<div class="pull-right">
    {summary}
</div>
{custom}
<div class="clearfix"></div>
{items}
<div class="row">
    <div class="pull-right">
        <div class="col-xs-12">
        {pager}
        </div>
    </div>
</div>
HTML;
?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'options' => ['class' => 'table-responsive'],
         
        'tableOptions' => ['class' => 'table  table-bordered', 'style' => 'font-family: saraban, roboto;'],
        'responsiveWrap' => false,
        'pjax'=>false,
        // 'rowOptions'=>function($model){
        //                     //if($model->genbus_postinggroup == '01') return ['class' => 'success'];
        //                     if($model->genbus_postinggroup == '02'){
        //                         return ['class' => 'info viewCustomer'];
        //                     }else {
        //                         return ['class' => 'viewCustomer'];
        //                     }
        //             },
        'columns' => $gridColumns,
        'resizableColumns'=>true,
        'layout' => $layout,
        'replaceTags' => [
            '{custom}' => function($widget) {
                // you could call other widgets/custom code here
                if ($widget->panel === false) {
                    return '';
                } else {
                    return '';
                }
            }
        ],
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=> Yii::t('common','First'),   // Set the label for the "first" page button
            'lastPageLabel'=> Yii::t('common','Last'),    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>5,    // Set maximum number of page buttons that can be displayed
            ],

    ]); ?>


</div>

  