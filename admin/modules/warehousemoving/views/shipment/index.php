<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\warehousemoving\models\ShipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Warehouse Movings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-moving-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('common', 'Create Warehouse Moving'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'line_no',
            'DocumentNo',
            'PostingDate',
            'TypeOfDocument',
            // 'SourceDoc',
            // 'SourceDocNo',
            // 'ItemNo',
            // 'Description',
            // 'Quantity',
            // 'QtyToMove',
            // 'QtyMoved',
            // 'QtyOutstanding',
            // 'DocumentDate',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
