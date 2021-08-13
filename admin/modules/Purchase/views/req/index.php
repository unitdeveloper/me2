<?php

use yii\helpers\Html;
use yii\helpers\Url;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Purchase\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Purchase Headers');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="purchase-header-index" ng-init="Title='<?=$this->title?>'">


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table  table-bordered table-hover'],
        'rowOptions' => function($model){
            
            return ['class' => '  editOrder'];
        },
        'columns' => [
            [
                'headerOptions' => ['class' => 'hidden-xs'],
                'contentOptions' => ['class' => 'hidden-xs'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'class' => 'yii\grid\SerialColumn'
            ],

            //'id',
            [
                'attribute' => 'order_date',
                'label' => Yii::t('common','Order Date'),
                'format' => 'html',
                'headerOptions' => ['class' => 'hidden-xs','style' => 'min-width:50px; max-width:80;'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'contentOptions' => ['class' => 'font-roboto','style' => 'min-width:50px;max-width:80;'],
                'value' => function($model){
                    return ($model->order_date)? $model->order_date : ' ';
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
            'purchaser',
            [
                'attribute' => 'doc_no',
                'format' => 'html',
                'headerOptions' => ['class' => 'hidden-xs'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'contentOptions' => ['class' => 'font-roboto'],                
                'value' => function($model){
                    return Html::a($model->doc_no,['view', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'projects.name',
                'format' => 'html',
                'headerOptions' => ['class' => 'hidden-xs'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'contentOptions' => ['class' => ' '],                
                'value' => 'projects.name'
            ],
            //'projects.name',
             
            
            [
                'attribute' => 'remark',
                'label' => Yii::t('common','Apply for'),     
                'headerOptions' => ['class' => 'hidden-xs'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'contentOptions' => [
                    'style'=>'max-width:350px; overflow: auto; white-space: normal; word-wrap: break-word; font-family: saraban;'
                ],   
                'value' => function($model){
                    return $model->remark;
                }
            ],
             
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'headerOptions' => ['class' => 'hidden-xs'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'contentOptions' => ['class' => 'text-right','style'=>'min-width:260px;'],
                'template'=>'<div class="btn-group btn-group text-center" role="group">{view} {print}  {update}  {delete} </div>',
                'options'=> ['style'=>'width:300px;'],
                'buttons'=>[
                    'print' => function($url,$model,$key){                      
                        return Html::a('<i class="fas fa-print"></i> ',$url,['class'=>'btn btn-info','target'=>'_blank']);
                    },
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
              ],
            //['class' => 'yii\grid\ActionColumn'],
        ],
        'responsiveWrap' => false
    ]); ?>
</div>


<div class="content-footer" style="display:none;">
    <div class="row">
        <div class="col-xs-12 text-right">
            <button type="button" class="btn btn-primary"><i class="far fa-save"></i> เปิด PO แล้ว</button>
        </div>
    </div>
</div>


<?php 
$js=<<<JS

$(document).ready(function(){
    var footer = $('div.content-footer').html();
    $('footer').html(footer).find('div.content-footer').fadeIn('slow');
 
})

JS;
$this->registerJS($js,\yii\web\View::POS_END,'yiiOptions');