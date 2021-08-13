<?php

use yii\helpers\Html;
//use yii\grid\GridView;

 
use kartik\grid\GridView; 
/* @var $this yii\web\View */
/* @var $searchModel admin\models\SeriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
 

$this->title = Yii::t('app', 'Number Series');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
 
<div class="number-series-index" ng-init="Title='<?=$this->title?>'">

    
    <?php 

        $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],

            //'name',
            [
                
                'attribute'=>'name', 
                'headerOptions' => ['class' => ' ', 'style' => 'width:130px;'],
                'value' => function($model){
                    return $model->name;
                }

            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'description', 
                'headerOptions'     => ['class' => ' '],
                'contentOptions'    => ['class' => 'text-left'],  
                'value' => function($model){
                    return $model->description;
                }

            ],
           
            //'starting_no',
            

            [
                //'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'table_name', 
                'value' => function($model){
                    return $model->table_name;
                }

            ],
             [
                //'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'field_name', 
                'value' => function($model){
                    return $model->field_name;
                }

            ],
             [
                //'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'cond', 
                'value' => function($model){
                    return $model->cond;
                }

            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'last_no', 
                'value' => function($model){
                    return $model->last_no;
                }

            ],
            // 'default_no',
            // 'manual_nos',
            // 'type',
            // 'comp_id',
            [
                //'attribute'         => 'starting_no',
                'label'             => Yii::t('common','Setting'),
                'format'            => 'html', 
                'headerOptions' => ['class' => ' ', 'style' => 'width:50px;'],
                'contentOptions'    => ['style' => ' '],             
                'value'             => function($model){
                    //'. $model->starting_no. ' 
                    $data = '<div><a href="#id='.$model->id.'&code='.$model->starting_char.'" class="a-OpenModal pull-right" ><i class="far fa-caret-square-up btn-success btn" aria-hidden="true"></i></a></di>';

                     
                   

                    return $data;
                },
            ],
            [
              'class' => 'yii\grid\ActionColumn',
              'headerOptions' => ['class' => ' ', 'style' => 'width:130px;'],
              'contentOptions'    => ['class' => 'text-right'],    
              //'options'=>['style'=>'width:200px;'],
              'buttonOptions'=>['class'=>'btn btn-default','title' => ' '],
              'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view} {update} {delete} </div>'
           ],

        ];
    
     ?>
  
    <?= GridView::widget([
        'id' => 'kv-grid-demo',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns'=> $gridColumns,
        'responsive' => true,
        'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        //'pjax'=>true,
        'options' => [
            'class' => 'font-roboto'
        ],
        'toolbar'=> [
            ['content'=>
                Html::button('<i class="glyphicon glyphicon-plus"></i>', ['type'=>'button', 'title'=>Yii::t('common', 'Add Book'), 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['grid-demo'], ['data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>Yii::t('common', 'Reset Grid')])
            ],
            '{export}',
            '{toggleData}',
        ],
        // set export properties
        'export'=>[
            'fontAwesome'=>true
        ],
        'exportConfig'=>'PDF',

    ]); ?>
    
    
</div>

<!-- Modal data-keyboard="false" data-backdrop="static"-->
<div id="RunNoSeries" class="modal  fade" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header bg-green">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Number Series</h4>
      </div>
      <div class="modal-body ">
        <div class="data-body">
            <p>Loading.</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off" aria-hidden="true"></i> Close</button>
      </div>
    </div>

  </div>
</div>



<?php $this->registerJsFile('js/no.series.js?update=111017-1');?>