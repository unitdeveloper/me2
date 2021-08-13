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

     
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],            
            'name',
            'detail',
            // 'user_id',
            // 'comp_id',
            'priority',
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'template'=>'<div class="btn-group btn-group-sm text-center" role="group">{view} {update} {delete} </div>',
                'options'=> ['style'=>'width:150px;'],
                // 'buttons'=>[
                //   'copy' => function($url,$model,$key){
                //       return Html::a('<i class="glyphicon glyphicon-duplicate"></i>',$url,['class'=>'btn btn-default']);
                //     }
                //   ]
              ],
              [
                'format' => 'raw',
                'label' => Yii::t('common','status'),
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
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

<?php 
$js=<<<JS


  $('input[name="status"]').on('switchChange.bootstrapSwitch', function (event, state) {
    let id      = $(this).closest('tr').attr('data-key');
    let value   = state;
    $.ajax({
        url:'index.php?r=Itemset/itemset/update-status&id='+id,
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