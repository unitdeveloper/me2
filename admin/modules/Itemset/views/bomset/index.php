<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\SwitchInput;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Itemset\models\ItemsetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Item Set');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="itemset-index" ng-init="Title='<?=$this->title;?>'">

<h4>กลุ่มที่ถูกผูกกับ ชุด Kit แล้ว</h4>
   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'name',
            //'detail',             
            [
                'attribute' => 'name',
                'format' => 'raw',                
                'label' => Yii::t('common','Name'),
                'value' => function($model){
                    return Html::a($model->name ? $model->name : '--', ['/Itemset/bomset/update', 'id' => $model->id]);
                },
            ],
            'detail',
            [
                'format' => 'raw',
                'label' => Yii::t('common','TEST'),
                'value' => function($model){
                    return Html::a('<i class="fa fa-power-off"></i> '.Yii::t('common','TEST'), ['/Itemset/bomset/view', 'id' => $model->id ],['class' => 'btn btn-default-ew']);
                },

            ],

            [               
                'attribute' => 'priority',
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($model){
                    return $model->priority;
                },

            ],
           
            [
                'format' => 'raw',
                'label' => Yii::t('common','Status'),
                'value' => function($model){
                    $data = SwitchInput::widget([
                        'name'          => 'status',
                        'value'         => $model->status,
                        'pluginOptions' => [
                            'onColor'   => 'success',
                            'offColor'  => 'danger',
                            'size'      => 'mini',
                            'onText'    => 'Show',
                            'offText'   => 'hide'
                         
                        ]
                    ]);
                    return $data;
                },

            ],
            //'user_id',
            //'comp_id',

            //['class' => 'yii\grid\ActionColumn'],
            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'options'=>['style'=>'width:150px;'],
            //     'buttonOptions'=>['class'=>'btn btn-default','title' => ''],
            //     'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view}  {update}</div>'
            //   ],
        ],
    ]); ?>
</div>


<?php 
$js=<<<JS


  $('input[name="status"]').on('switchChange.bootstrapSwitch', function (event, state) {
    let id      = $(this).closest('tr').attr('data-key');
    let value   = state;
    $.ajax({
        url:'index.php?r=Itemset/bomset/update-status&id='+id,
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
                        align: "right"
                    }
                });
            }else{
                $.notify({
                    // options
                    icon: 'far fa-save',
                    message: response.message,                         
                },{
                    // settings
                    type: 'success',
                    delay: 1500,
                    z_index:3000,
                    placement: {
                        from: "top",
                        align: "right"
                    }
                });
            }
             
        }
    })
  });
   
JS;
$this->registerJs($js,\yii\web\View::POS_END,'JS');
?>