<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\models\SaleInvoiceLine;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\SaleinvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Sale Invoice Headers');
$this->params['breadcrumbs'][] = $this->title;
?>
 
<div class="sale-invoice-header-index">

<?php #Pjax::begin(); ?>    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'no_',
            [
                'attribute' => 'no_',
                'label' => Yii::t('common','Document No.'),
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->no_,['update','id' => $model->id],['target' => '_blank']);

                }

            ],
            //'cust_no_',
            [
                'attribute' => 'cust_no_',
                //'label' => Yii::t('common','Document No.'),
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->cust_name_,['update','id' => $model->id],['target' => '_blank']);

                }

            ],
            //'cust_name_',
            'cust_address:ntext',
            // 'cust_address2:ntext',
            // 'posting_date',
            // 'order_date',
            // 'ship_date',
            // 'cust_code',
            // 'sales_people',
            // 'document_no_',
            [
                
                'label' => Yii::t('common','Balance'),
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    $invLine = SaleInvoiceLine::find()->where(['source_id' => $model->id]);
                    $sumLine = $invLine->sum('quantity * unit_price');

                    return number_format($sumLine,2);

                }

            ],
            

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php #Pjax::end(); ?></div>

 