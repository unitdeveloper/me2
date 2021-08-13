<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\SwitchInput;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\salepeople\models\SearchPeople */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Sales Peoples');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sales-people-index" ng-init="Title='<?=$this->title;?>'">

 


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'code',
            [
                'attribute' => 'code',
                'format' => 'raw',
                'value' => function ($model) { 
                    return Html::a($model->code, ['view', 'id' => $model->id]);
                },
            ],
            //'name',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) { 
                    return Html::a($model->name.' '.$model->surname, ['view', 'id' => $model->id]);
                },
            ],
            'nickname',
            //'sale_group',
            // 'user_id',
            // 'comp_id',
             'tax_id',
             //'position',
             'address',

            // [
            //     'attribute' => 'status',
            //     'format' => 'raw',
            //     'value' => function($model){
            //         if($model->status == 1)
            //         {
            //             $status = '<span class="text-success">'.Yii::t('common','Enabled').'</span>';
            //         }else {
            //             $status = '<span class="text-gray">'.Yii::t('common','Disabled').'</span>';
            //         }
                    

            //         return $status;
            //     },
                 
            // ], 
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model){
                    $data = SwitchInput::widget([
                        'name' => 'status',
                        'value' => $model->status,
                        'pluginOptions' => [
                            'onColor' => 'success',
                            'offColor' => 'danger',
                         
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
                    'prompt' => Yii::t('common','Status'),
                ]),
            ],

            // [
            //     'attribute' => 'status',
            //     'format' => 'raw',
            //     'value' => function($model){
            //         if($model->status == 1)
            //         {
            //             $status = 'checked';
            //         }else {
            //             $status = NULL;
            //         }
            //         $data = '<input id="ew-sales-status" name="status" type="checkbox" '.$status.' data-toggle="toggle" data-style="android" data-onstyle="info" value="'.$model->status.'" ew-sid="'.$model->id.'" >';

            //         return $data;
            //     },
            //     'filter' => Html::activeDropDownList($searchModel,'status',
            //         [
            //             '0' => Yii::t('common','Off'),
            //             '1' => Yii::t('common','On'),
            //         ],
            //         [                        
            //             'class' => 'form-control hidden-xs',
            //             'prompt' => Yii::t('common','Status'),
            //         ]),
            // ],

            // 'address2',
            // 'postcode',
            // 'date_added',
            // 'sign_img',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<script>
    // $(function() {     
    //     $('input[id="ew-sales-status"]').change(function() {
    //       route('index.php?r=salepeople/people/ajax-update','POST',{id:$(this).attr('ew-sid'),val:$(this).prop('checked')},'Navi-Title');
    //     })
    // });
</script>


 
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