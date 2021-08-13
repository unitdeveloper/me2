<?php

use yii\helpers\Html;
use yii\helpers\Url;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\daterange\DateRangePicker;
 

$this->title = Yii::t('common', 'Payment');
$this->params['breadcrumbs'][] = $this->title;

$column = [
    ['class' => 'yii\grid\SerialColumn'],
     'id'
    ];
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="purchase-header-index" ng-init="Title='<?=$this->title?>'">
 
    <div class="table-responsive-"  >
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table  table-bordered '],
            // 'rowOptions' => function($model){
            //     if($model->completeReceive){
            //         return ['class' => 'bg-success  editOrder'];
            //     }else{
            //         return ['class' => '  editOrder'];
            //     }
                
            // },
            'columns' => $column,
            'responsiveWrap' => false
        ]); ?>
    </div>
</div>

<div class="content-footer hidden-lg hidden-md hidden-sm" >
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <?= Html::a('<i class="fas fa-list-ul"></i> '.Yii::t('common', 'Purchase Line'), ['/Purchase/purchase-line'], ['class' => 'btn btn-default ']) ?>   
        </div>
        <div class="hidden-xs  col-sm-6 text-right">            
            <?= Html::a('<i class="fas fa-th"></i> '.Yii::t('common', 'แสดงทั้งหมด'), '#', ['class' => 'btn btn-primary ']) ?>   
            <?= Html::a('<i class="far fa-clock"></i> '.Yii::t('common', 'ยังไม่รับสินค้า'), '#', ['class' => 'btn btn-warning ']) ?>   
            <?= Html::a('<i class="fas fa-warehouse"></i> '.Yii::t('common', 'รับสินค้าแล้ว'), ['index','cond' => 'received'], ['class' => 'btn btn-success ']) ?>   
        </div>
    </div>
</div>


<?php 
$js=<<<JS

$(document).ready(function(){
    var footer = $('div.content-footer').html();
    $('footer').html(footer).find('div.content-footer').fadeIn('slow');
 
})

JS;
$this->registerJS($js,\yii\web\View::POS_END,'yiiOptions');