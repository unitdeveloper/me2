<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\apps_rules\models\SetupSysSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Setup Sys Menus');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="setup-sys-menu-index">
    
    <div class="box-body table-responsive no-padding" >
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'tableOptions' => ['class' => 'table table-hover table-responsive'],
            'rowOptions'=>function($model){
                            if($model->function_group_type == 'Data Access') return ['class' => 'info']; 
                            if($model->function_group_type == 'Main Function') return ['class' => 'danger'];
                    },
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                'id',
                //'function_group_type',
                [
                    'attribute' => 'function_group_type',
                    'format' => 'raw',
                    'value' => function($model)
                    {
                        $icon = '<i class="fa fa-code-fork" aria-hidden="true"></i>';

                        if($model->function_group_type == 'Main Function') $icon = '<i class="fa fa-expeditedssl text-red" aria-hidden="true"></i>';
                        if($model->function_group_type == 'Data Access') $icon = '<i class="fa fa-codepen text-info" aria-hidden="true"></i>';

                        return $icon.' '.$model->function_group_type;
                    },
                    'filter' => Html::activeDropDownList($searchModel,'function_group_type',
                        [
                            'Main Function'          => Yii::t('common','Main Function'),
                            'Data Access'       => Yii::t('common','Data Access'),
                        ],
                        [
                            'class'         => 'form-control hidden-xs',
                            'prompt'        => Yii::t('common','Show All'),
                        ]),
                ],
                'function_modules',
                'function_controllers',
                'function_models',
                //'description',
                'function_name',
                'rules_id:ntext',
                //'company.name',
                //'detail',
                [
                    'attribute' => 'detail',
                    'contentOptions' => ['style' => 'max-width:200px; overflow:auto;'],
                    'label' => Yii::t('common','Comment'),
                    'value' => function($model){
                        return $model->detail;
                    }
                ],
                //['class' => 'yii\grid\ActionColumn'],
                [
                    'label' => Yii::t('common','Edit'),
                    'format' => 'raw',
                    'value' => function($model){
                        $bt = Html::a('<i class="fa fa-search-plus" aria-hidden="true"></i> ',['/apps_rules/setupsys/view','id' => $model->id],['class' => 'btn btn-info']);
                        $bt.= ' ';
                        $bt.= Html::a('<i class="fa fa-pencil-square-o" aria-hidden="true"></i> ',['/apps_rules/setupsys/update','id' => $model->id],['class' => 'btn btn-warning']);

                        return $bt;
                    }
                ],
            ],
        ]); ?>
    </div>
</div>
