<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\WarehouseHeader */

$this->title = $model->DocumentNo;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Warehouse Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>

<div class="warehouse-header-view" ng-init="Title='<?= Html::encode($this->title) ?>'">
 

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-print"></i> '.Yii::t('common', 'Print'), ['print', 'id' => $model->id], ['class' => 'btn btn-info','target' => '_blank']) ?>
        <?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'line_no',
            'PostingDate',
            'DocumentDate',
            //'TypeOfDocument',
            [
                'label' => Yii::t('common','Type Of Document'),
                'value' => function($model){
                    return $model->TypeOfDocument.' ('.$model->AdjustType.')';
                }
            ],
            'SourceDocNo',
            'DocumentNo',
            'customer_id',
            'SourceDoc',
            'Description',
            'Quantity',
            //'address',
            //'address2',
            //'district',
            //'city',
            //'province',
            //'postcode',
            //'contact',
            //'phone',
            //'gps:ntext',
            'update_date',
            //'status',
            //'user_id',
            //'comp_id',
            //'ship_to',
            //'ship_date',
        ],
    ]) ?>
    <div class="row">
        <div class="col-sm-12 text-right"><button id="ew-Post-Adjust" type="button" class="btn btn-danger-ew"><i class="fa fa-server" aria-hidden="true"></i> <?=Yii::t('common','POST')?></button></div>
    </div>
 
</div>
 
<?php
$js=<<<JS

$('body').on('click','#ew-Post-Adjust',function(){

    if (confirm('ยืนยันการทำรายการ ! ')) {
        var data = {
                        sourceId:'{$model->id}',
                        adjType:'{$model->AdjustType}',
                        postDate:'{$model->PostingDate}',
                    };
 

        $.ajax({

                url:"index.php?r=warehousemoving/journal/post-journal",
                type: "POST",
                data: data,
                async:true,
                dataType:'JSON',
                success:function(response){
                    if(response.status==200){
                        window.location="index.php?WarehouseSearch[DocumentNo]='"+response.value.doc+"'&r=warehousemoving%2Fwarehouse";
                    }
                }

            });
    }
    return false;
});

JS;

$this->registerJs($js);
?>