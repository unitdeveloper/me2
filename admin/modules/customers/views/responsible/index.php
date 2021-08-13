<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\customers\models\ResponsibleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Sales Peoples');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sales-people-index" ng-init="Title='<?= Html::encode($this->title) ?>'">

 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table  table-bordered', 'style' => 'font-family: saraban, roboto;'],
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => [
                    'style' => 'width:50px;'
                ]
            ],
            //'code',
            //'name',
            [
                'attribute' => 'code', 
                'filterOptions' => ['style' => 'width:150px;'],
                'value' => 'code'
            ],
            [
                'attribute' => 'name', 
                'label' => Yii::t('common','Responsible'),
                'format' => 'html',
                'value' => function($model){
                    return Html::a($model->prefix.$model->name. ' ' . $model->surname,['view','id' => $model->id]);
                }
            ],
            [
                'attribute' => 'gender',                
                'headerOptions' => ['class' => 'text-center'],
                'filterOptions' => ['style' => 'visibility:hidden; width:80px;'],
                'contentOptions' => ['class' => 'text-center'],
                'format' => 'html',
                'value' => function($model){
                    if ($model->gender=='man'){
                        return '<i class="fas fa-male"></i>';
                    }else if ($model->gender=='Woman'){
                        return '<i class="fas fa-female"></i>' ;
                    }else{
                        return '<i class="fas fa-transgender"></i>' ;
                    }    
                }
            ],
            //'gender',
            //'surname',
            //'nickname',
            //'sale_group',
            //'user_id',
            //'comp_id',
            //'tax_id',
            //'position',
            //'address',
            //'address2',
            //'postcode',
            //'date_added',
            //'sign_img',
            
            //'photo',
            //'wall_photo',
            //'mobile_phone',
            //'line_id',

            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'buttonOptions'=>['class'=>'btn btn-default'],
            //     'contentOptions' => ['class' => 'text-right','style'=>'min-width:260px;'],
            //     'headerOptions' => ['class' => 'hidden-xs'],
            //     'filterOptions' => ['class' => 'hidden-xs'],
            //     'template'=>'<div class="btn-group btn-group text-center" role="group"> {view} {update} {delete} </div>',
            //     'options'=> ['style'=>'width:300px;'],
            //     'buttons'=>[
            //         'view' => function($url,$model,$key){
            //             return Html::a('<i class="fas fa-eye"></i> ',['/customers/responsible/view','id' => $model->id],['class'=>'btn btn-primary btn-sm']);
            //         },
            //         'delete' => function($url,$model,$key){
            //             return Html::a('<i class="far fa-trash-alt"></i> ',['/customers/responsible/delete','id' => $model->id],[
            //                 'class' => 'btn btn-danger btn-sm',
            //                 'data' => [
            //                     'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
            //                     'method' => 'post',
            //                 ],
            //             ]);
            //         },
            //         'update' => function($url,$model,$key){
            //             return Html::a('<i class="far fa-edit"></i> ',['/customers/responsible/update','id' => $model->id],['class'=>'btn btn-success btn-sm']);
            //         }
            //     ]
            // ],
            [
                'label' => Yii::t('common','Customer'),                
                'headerOptions' => ['class' => 'text-center'],
                'filterOptions' => ['style' => 'width:80px;'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($model){
                    
                    return count($model->myCustomer);
                }
            ],

            [
                'attribute' => 'status',
                'format' => 'raw',
                'filterOptions' => ['style' => 'width:80px;'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($model){
                    $data = SwitchInput::widget([
                        'name' => 'status',
                        'value' => $model->status,
                        'pluginOptions' => [
                            'onColor' => 'success',
                            'offColor' => 'danger',
                            'size' => 'mini'
                        ]
                    ]);
                    return $data;
                },
                'filter' => Html::activeDropDownList($searchModel,'status',
                [
                    '0' => Yii::t('common','Off'),
                    '1' => Yii::t('common','On'),
                ],
                [                        
                    'class' => 'form-control hidden-xs',
                    'prompt' => Yii::t('common','All'),
                ]),
            ],
        ],
    ]); ?>
</div>

 <?php 
$js=<<<JS


  $('input[name="status"]').on('switchChange.bootstrapSwitch', function (event, state) {
    let id      = $(this).closest('tr').attr('data-key');
    let value   = state;
    $.ajax({
        url:'index.php?r=salepeople/people/update-status&id='+id,
        type:'POST',
        data:{param:{id:id,val:value}},
        dataType:'JSON',
        success:function(response){

            if(response.status===200){
                $.notify({
                    // options
                    icon: 'far fa-save',
                    message: 'Saved',                         
                },{
                    // settings
                    type: 'success',
                    delay: 1500,
                    z_index:3000,
                    placement: {
                        from: "top",
                        align: "center"
                    }
                });
            }
            console.log(response);
        }
    })
  });
   
JS;
$this->registerJs($js,\yii\web\View::POS_END,'JS');
?>