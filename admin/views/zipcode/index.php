<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\models\ZipcodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Zipcodes');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="zipcode-index" ng-init="Title='<?=$this->title;?>'">
 
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'ZIPCODE_ID',
            //'DISTRICT_CODE',
            //'province.PROVINCE_NAME',
            [
                'attribute' => 'PROVINCE_ID',
                'format' => 'raw',
                'value' => function($model)
                {

                    $province = '';
                    if($model->PROVINCE_ID!='') $province = $model->province->PROVINCE_NAME;

                    return $province;
                     

                }
            ],
            [
                'attribute' => 'AMPHUR_ID',
                'value' => function($model)
                {
                    $city     = '';
                    if($model->AMPHUR_ID!='')      $city       = $model->amphur->AMPHUR_NAME;

                    return $city;



                }
            ],
            [
                'attribute' => 'DISTRICT_ID',
                'value' => function($model)
                {
                    $district = '';
                    if($model->DISTRICT_ID!='') $district = $model->district->DISTRICT_NAME;

  
                    return $district;
                }
            ],
            //'amphur.AMPHUR_NAME',
            //'district.DISTRICT_NAME',
            'ZIPCODE',
             'latitude',
             'longitude',

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=> '<i class="fa fa-fast-backward" aria-hidden="true"></i>',   // Set the label for the "first" page button
            'lastPageLabel'=>'<i class="fa fa-fast-forward" aria-hidden="true"></i>',    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>15,    // Set maximum number of page buttons that can be displayed
            ],
    ]); ?>
<?php Pjax::end(); ?></div>
