<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\company\models\PrintSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Print Pages');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="print-page-index" ng-init="Title='<?=$this->title?>'">


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model){
            return ['class' => 'pointer editOrder'];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            //'logo',
            //'header:ntext',
            'header_height',
             'footer_height',
             'body_height',
            // 'footer:ntext',
            // 'signature:ntext',
             'pagination',
             'paper_size',
            // 'comp_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
<?php
$this->registerJs("
    $('tr.editOrder').click(function (e) {
        var id = $(this).data('key');
        console.log($(this).data('key'));
        location.href = '" . Url::to(['/company/setup-print/update']) . "&id=' + id;
    });
");
