<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\SaleinvlineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Sale Invoice Lines');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-invoice-line-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
             
            'id',
            'doc_no_',  
            'customers.code',           
            'customers.name', 
            'items.master_code',
            'items.description_th',
            // 'code_desc_',
            'quantity:decimal',
            'unit_price:decimal',
            // 'vat_percent',
            // 'line_discount',
 
        ],
    ]); ?>
</div>
