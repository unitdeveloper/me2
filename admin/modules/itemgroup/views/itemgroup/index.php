<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\itemgroup\models\SearchItemGroup */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Itemgroups');
 
?>
<style>
    li.child-group{
        list-style-type: none;
        padding-bottom:30px;
    }
    li.child-group:hover{
        list-style-type: none;
        color:red;
        /* border-bottom:1px solid #ccc; */
    }
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="itemgroup-index">

     
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            [
                'label' => Yii::t('common','Group'),
                'format' => 'raw',
                'value' => function($model){
                    if($model->Child==0){
                        $html = Html::a('<img src="'.$model->picture.'" class="img-responsive" style="max-width:100px;"> '.$model->Description_th,['view','id' => $model->GroupID]);
                    }else{
                        $html = Html::a($model->Description_th,['view','id' => $model->GroupID]);
                    }                  

                    return $html;
                    
                }
            ],
            [
                'label' => Yii::t('common','Name'),
                'format' => 'raw',
                'value' => function($model){
                    $html = "<div class='row'>";
                        $html.= "<div class='col-sm-6'>";
                        $html.= Html::a(' » '.$model->Description_th,['view','id' => $model->GroupID],['class' => 'btn btn-warning-ew']).' ';
                        $html.= "</div>";
                        $html.= "<div class='col-sm-6 text-right'>";
                        $html.= Html::a('<i class="far fa-plus-square text-warning"></i> '.Yii::t('common','Add Child Group'),['create','childof' => $model->GroupID],['class' => 'text-right']).'</p>'; 
                        
                        $html.= "</div>";
                       
                    $html.= "</div>";
                    $html.= "<div class='row'>";
                    $html.= "<div class='col-sm-12'>";
                    $html.= $model->getChildLoop($model->GroupID);
                    $html.= "</div>";
                    $html.= "</div>";
                   return $html;
                    
                }
            ],
            
            //'GroupID',
            //'Description',
            //'Description_th',
            //'Child',
            //'Status',
            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'options'=>['style'=>'width:150px;'],
            //     'buttonOptions'=>['class'=>'btn btn-default'],
            //     'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view} {update} {delete} </div>'
            //   ],

            
        ],
    ]); ?>
</div>

 


<?php $this->registerJsFile('https://code.jquery.com/ui/1.12.1/jquery-ui.js',['depends' => [\yii\web\JqueryAsset::className()]]);?>
<?php 
$js =<<<JS
    
    $( function() {
        $( ".sortable" ).sortable({
            connectWith: '.sortable',
            update: function(e,ui){
            var lis = $('.sortable li');
            var ids = lis.map(function(i,el){   
                var parent = $(this).closest('ul').data('key');   
                console.log(parent);          
                            return {
                                id:el.dataset.key, 
                                child:el.dataset.child,
                                sequent:el.dataset.sequent,
                                parent:parent}                    
                        }).get();
            console.log(JSON.stringify(ids));
            $.ajax({
                url:'index.php?r=itemgroup/itemgroup/update-priority',
                type:'POST',
                data:{ids:ids},
                success:function(response){
                    //console.log(response);
                }
            });


         }
    });
        $( ".sortable" ).disableSelection();
    });

JS;

$this->registerJS($js);