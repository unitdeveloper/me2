<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
 
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\warehousemoving\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Stock Report');
$this->params['breadcrumbs'][] = $this->title;
  
 
?>
 
<div class="content">
    <div class="row">
        <div class="col-12">
            <h3>ใบรายงาน การตัดสต๊อก</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['style' => 'width:50px;'],
                ],
                 
                [
                    'label' => Yii::t('common','Posting date'),
                    'attribute' => 'PostingDate',
                    'headerOptions' => ['style' => 'width:150px;'],
                    'contentOptions' => ['style' => 'font-family:roboto;'],
                    'value' => function($model){
                        return date('Y-m-d', strtotime($model->PostingDate));
                    }
                ],
                [
                    'label' => Yii::t('common','Posting date'),
                    'attribute' => 'DocumentNo',
                    'headerOptions' => ['style' => 'width:150px;'],
                    'contentOptions' => ['style' => 'font-family:roboto;'],
                    'format' => 'raw',
                    'value' => function($model){
                        return Html::a($model->DocumentNo,['/warehousemoving/stock/print-report','id' => $model->id,'group' => $model->SourceDocNo],['target' => '_blank']);
                    }
                ],
               

                [
                    'label' => Yii::t('common','Inspector'),
                    'headerOptions' => ['style' => 'width:150px;'],
                    'value' => 'contact'
                ],
                [
                    'label' => Yii::t('common','Group'),
                    'contentOptions' => ['style' => 'font-family:roboto;'],
                    'value' => 'Description'
                ],

                [
                    'label' => Yii::t('common','Remark'),
                    'headerOptions'     => ['style' => 'width:250px;'],
                    'contentOptions' => ['style' => 'font-family:roboto; white-space: initial;'],
                    'value' => 'remark'
                ],

                 
                 
                [
                    'label' => Yii::t('common','Items count'),
                    'headerOptions' => ['class' => 'text-right','style' => 'width:150px;'],
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function($model){
                        return $model->lineInfo->count;
                    }
                ],
                [
                    'label' => Yii::t('common','Total Qty'),
                    'headerOptions' => ['class' => 'text-right','style' => 'width:150px;'],
                    'contentOptions' => ['class' => 'text-right font-roboto'],
                    'value' => function($model){
                        return number_format($model->lineInfo->sum_qty);
                    }
                ],
                
                 

                //['class' => 'yii\grid\ActionColumn'],
                ],
        ]); ?>
        </div>
    </div>
</div>