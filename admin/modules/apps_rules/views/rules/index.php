<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\widgets\SwitchInput;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\apps_rules\models\SearchRules */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Apps Rules');
$this->params['breadcrumbs'][] = $this->title;
//$this->registerJsFile('https://code.jquery.com/jquery-1.10.2.js',['depends' => [\yii\web\JqueryAsset::className()]]);
?>
 
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="apps-rules-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

     
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive' => true,
        'responsiveWrap' => true,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions'  => function($model){
                    return ['class' => ($model->sale_id!=NULL) ? 'bg-success' : 'bg-danger'];
                }
            ],
            [
                'attribute' => 'name',
                'label' => Yii::t('common','User Name'),
                'format' => 'raw',
                'value' => function ($model) { 
                    if($model->user_id!='')
                    {
                        return Html::a('['.$model->usertb->username.'] - '.$model->profiletb->name, ['view', 'id' => $model->id]);
                    }else {
                        return Html::a('['.$model->usertb->username.'] - '.'(No Name)', ['view', 'id' => $model->id]);
                    }
                    
                },
            ],
            [
                'attribute' => 'company.name',
                'label' => Yii::t('common','Company'),
                'format' => 'raw',
                'value' => function ($model) { 
                    return Html::a($model->company->name, ['view', 'id' => $model->id]);
                },
            ],
            [
                'label' => Yii::t('common','Name'),
                'value' => 'rulesetup.name'
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model){
                    $data = SwitchInput::widget([
                        'name' => 'status',
                        'value' => $model->status,
                        'pluginOptions' => [
                            // 'onText' => 'Yes',
                            // 'offText' => 'No',
                            'size' => 'mini',
                            'onColor' => 'success',
                            'offColor' => 'danger',
                         
                        ]
                    ]);
                    return $data;
                }
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
        url:'index.php?r=apps_rules/ajax/update-status',
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