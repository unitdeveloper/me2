<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel admin\models\TransportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Transport List');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-list-index">
<div class="">
    <?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
</div>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <?php
         echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
        
                        //'id',
                        'name',
                        'nick_name',
                        'address',
                        'contact',
                        'phone',
                        //'comp_id',
        
                        //['class' => 'yii\grid\ActionColumn'],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'buttonOptions'=>['class'=>'btn btn-default'],
                            'contentOptions' => ['class' => 'text-right','style'=>'min-width:150px;'],
                            'headerOptions' => ['class' => 'hidden-xs'],
                            'filterOptions' => ['class' => 'hidden-xs'],
                            'template'=>'<div class="btn-group btn-group text-center" role="group">{view}  {update} {delete}   </div>',
                            'options'=> ['style'=>'width:150px;'],
                            'buttons'=>[
             
                                
            
                                'view' => function($url,$model,$key){
                                    return Html::a('<i class="fas fa-eye"></i> ',$url,['class'=>'btn btn-default']);
                                },
                                'delete' => function($url,$model,$key){
                                    return Html::a('<i class="far fa-trash-alt"></i> ',$url,[
                                        'class' => 'btn btn-danger',
                                        'data' => [
                                            'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                            'method' => 'post',
                                        ],
                                    ]);
                                },
                                'update' => function($url,$model,$key){
                                    return Html::a('<i class="far fa-edit"></i> ',$url,['class'=>'btn btn-success']);
                                }
                            ]
                        ]
                    ],
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
                    ],
                    'styleOptions' => [
                        ExportMenu::FORMAT_PDF => [
                            'font' => [
                                 'family' => ['garuda'],
                                    //'bold' => true,
                                    'color' => [
                                         'argb' => 'FFFFFFFF',
                                 ],
                            ],
                        ],
                    ],
                    'filename' => 'Items',
                    'target' => ExportMenu::TARGET_BLANK,
                    //'encoding' => 'utf8',
                ]);
        ?>
       
    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                'name',
                'nick_name',
                'address',
                'contact',
                'phone',
                //'comp_id',

                //['class' => 'yii\grid\ActionColumn'],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'buttonOptions'=>['class'=>'btn btn-default'],
                    'contentOptions' => ['class' => 'text-right','style'=>'min-width:150px;'],
                    'headerOptions' => ['class' => 'hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs'],
                    'template'=>'<div class="btn-group btn-group text-center" role="group">{view}  {update} {delete}   </div>',
                    'options'=> ['style'=>'width:150px;'],
                    'buttons'=>[
     
                        
    
                        'view' => function($url,$model,$key){
                            return Html::a('<i class="fas fa-eye"></i> ',$url,['class'=>'btn btn-default']);
                        },
                        'delete' => function($url,$model,$key){
                            return Html::a('<i class="far fa-trash-alt"></i> ',$url,[
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ]);
                        },
                        'update' => function($url,$model,$key){
                            return Html::a('<i class="far fa-edit"></i> ',$url,['class'=>'btn btn-success']);
                        }
                    ]
                ]
            ],
        ]); ?>
    </div>
</div>
