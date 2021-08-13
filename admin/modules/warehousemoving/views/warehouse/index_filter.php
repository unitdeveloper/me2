<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\widgets\DatePicker;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\warehousemoving\models\WarehouseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Warehouse Movings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-moving-index" ng-init="Title='<?=$this->title?>'">

 
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<span class="btn btn-flat btn-primary"><i class="fas fa-table"></i> <?=$this->title?></span>
<div class="box box-success margin-top">
    <?=$this->render('_search',['dataProvider' => $dataProvider])?>
</div>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],             
            [
                'label' => Yii::t('common','Vat'),
                'value' => function($model){                     
                    return date('d/m/Y',strtotime($model['PostingDate']));
                }
            ], 
            [
                'label' => Yii::t('common','Vat'),
                'value' => function($model){
                    if($model['vat_type']==1){
                        $vat = 'Vat';
                    }else{
                        $vat = 'No Vat';
                    }
                    return $vat;
                }
            ],
            'SourceDocNo',
            'DocumentNo',
            'master_code',                
            'description_th',
            'Quantity',
            'unit_price'
        ],
        'responsiveWrap' => false, // Disable Mobile responsive    
    ]); ?>
<?php Pjax::end(); ?></div>





<?php
$js =<<<JS
    $(document).ready(function(){
        $('[data-toggle=\"tooltip\"]').tooltip(); 
    });            
    
    // $('body').on('change','#wh-filter-change',function(){
    //     window.location.href = 'index.php?r=warehousemoving%2Fwarehouse%2Findex-filter&method='+$(this).val();
    // })
JS;
$this->registerJs($js,\yii\web\View::POS_END);
     ?>
