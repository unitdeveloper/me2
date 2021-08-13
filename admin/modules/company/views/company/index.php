<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\company\models\SearchCompany */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Companies');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="company-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'format' => 'html',
                'value' => function($model){
                    return Html::img($model->logoViewer,['class' => 'img-responsive']);
                }
            ],
            'id',            
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->name,['view', 'id' => $model->id]);     
                },
            ],
            //'name',
            'address',
            //'address2',
            'city',
            'location',
            // 'postcode',
            // 'country',
            // 'phone',
            // 'fax',
            // 'vat_register',
            // 'vat_address',
            // 'vat_city',
            // 'vat_location',
            // 'headoffice',
            // 'create_time',
            // 'update_time',

            //['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'options'=>['style'=>'width:150px;'],
                'buttonOptions'=>['class'=>'btn btn-default'],
                'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view} {update}  </div>'
             ]
        ],
    ]); ?>
</div>
