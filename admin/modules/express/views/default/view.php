<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\models\Isvat */

$this->title = $model->DOCNUM;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Isvats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="isvat-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?= Html::a(Yii::t('common', 'Home'), ['index'], ['class' => 'btn btn-primary']) ?>
         
    </p>
    <div><?= $model->DESCRP?></div>
    <div class="text-right"><span style="border:1px solid #ccc; padding:0px 10px 0px 10px; font-size: 15px;"><?= number_format($model->AMT01,2)?></span></div>


<?php 

$dataProvider = new ActiveDataProvider([
    'query' => \common\models\Stcrd::find()->where(['DOCNUM' => $model->DOCNUM]),
    'pagination' => false,

]);

?>
<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

             'DOCDAT',
             'KCOD',
             'STKDES',
            'PEOPLE',
            'TRNQTY',
            'UNITPR',
            'TRNVAL'
           
        ],
    ]); ?>

<?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'TREC',
            'VATTYP',
            'RECTYP',
            'VATPRD',
            'LATE',
            'VATDAT',
            'DOCDAT',
            'DOCNUM',
            'REFNUM',
            'NEWNUM',
            'DEPCOD',
            'DESCRP',
            'AMT01',
            'VAT01',
            'AMT02',
            'VAT02',
            'AMTRAT0',
            'REMARK',
            'SELF_ADDED',
            'HAD_MODIFY',
            'DOCSTAT',
            'TAXID',
            'ORGNUM',
            'PRENAM',
        ],
    ]) ?>
</div>
