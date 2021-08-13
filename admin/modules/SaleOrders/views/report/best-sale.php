<?php

use yii\helpers\Html;
use yii\helpers\Url;
//use yii\grid\GridView;
use yii\widgets\Pjax;

use kartik\export\ExportMenu;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\warehousemoving\models\ShipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Best Sale');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-moving-index" ng-init="Title='<?= Html::encode($this->title) ?>'">
<!-- Styles -->
<style>
#chartdiv {
  width: 100%;
  height: 400px;
  font-size: 11px;
}

.amcharts-pie-slice {
  transform: scale(1);
  transform-origin: 50% 50%;
  transition-duration: 0.3s;
  transition: all .3s ease-out;
  -webkit-transition: all .3s ease-out;
  -moz-transition: all .3s ease-out;
  -o-transition: all .3s ease-out;
  cursor: pointer;
  box-shadow: 0 0 30px 0 #000;
}

.amcharts-pie-slice:hover {
  transform: scale(1.1);
  filter: url(#shadow);
}	
label.line{
    margin:0px 10px -7px 5px;
    border-left:1px solid #ccc;
    width:1px;
    height:25px;

}						
</style>
<h3><?= Html::encode($this->title) ?></h3>

<?=$this->render('_filter_bar',['model' => $searchModel])?>   
<div class="row">
    <div class="col-md-12" style="height: 400px;">
        <my-chart ></my-chart>
    </div>
</div>
 
<div class="row">
    <div class="col-md-12 text-right">

        <!-- <a href="#" ><i class="fas fa-eye"></i> Default View <i class="fas fa-sort-down"></i></a>
        <label class="line"></label>
        <a href="#" ><i class="fas fa-cog"></i> Colomn <i class="fas fa-sort-down"></i></a>
        <label class="line"></label>
        <a href="#" ><i class="fas fa-download"></i> Export <i class="fas fa-sort-down"></i></a> -->
        <?php
           echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'item',
                    'label' => Yii::t('common','Items ID'),    
                    'format' => 'raw',          
                    'value' => function($model){
                        return $model->item;
                    }
                ],
               
                'itemstb.master_code',
                [
                    'attribute' => 'name',
                    'label' => Yii::t('common','Product Name'),    
                    'format' => 'raw',          
                    'value' => function($model){
                        return $model->name;
                    }
                ],
                'itemstb.inven',
                [
                    'attribute' => 'qty',
                    'label' => Yii::t('common','Quantity'),
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['style' => 'font-family:tahoma','class' => 'text-right'],
                    'value' => function($model){
                        return $model->qty;
                    }
                ]
            ],
            'columnSelectorOptions'=>[
                'label' => 'Columns',
                'class' => 'btn btn-success-ew'
            ],
            'selectedColumns'=>[0, 2,3,5], // ID & Name
            'fontAwesome'       => true,
            'dropdownOptions'   => [
                'label' => 'Export',
                'class' => 'btn btn-primary-ew'
            ],
            'exportConfig' => [
                ExportMenu::FORMAT_HTML => false,
                ExportMenu::FORMAT_PDF => false,
            ],             
        ]);
    ?>
         
       
    </div>
</div>


<div class="table-responsive margin-top">   
<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table  table-bordered table-hover'],
        'headerRowOptions' => ['class' => 'bg-dark'],
        'responsiveWrap' => false,
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

             
            //'item',
           //'itemstb.Description',
           [
                
                'label' => Yii::t('common','Image'),      
                'format' => 'raw',        
                'contentOptions' => ['style' => 'max-width:50px;'],
                'value' => function($model){
                    return "<img src='{$model->itemstb->picture}' class='img-responsive'>";
                }
            ],
            [
                'attribute' => 'name',
                'label' => Yii::t('common','Product Name'),    
                'format' => 'raw',          
                'value' => function($model){
                    $html = "<div>{$model->name}</div>";
                    if($model->itemstb->description_th != $model->itemstb->Description){
                        $html.= "<div>{$model->itemstb->description_th}</div>";
                    }
                    $html.= "<br><div>".Yii::t('common','Remain')." : ".number_format($model->itemstb->inven)."</div>";
                    
                    return Html::a($html,'javascript:void(0);',[
                        'onclick' => "window.open ('".Url::toRoute(['/items/items/view', 'id' => $model->itemstb->id])."'); return false"]);
                }
            ],
            [
                'attribute' => 'qty',
                'label' => Yii::t('common','Quantity'),
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['style' => 'font-family:tahoma','class' => 'text-right'],
                'value' => function($model){
                    return number_format(abs($model->qty));
                }
            ],
             
            
        ],
        'pager' => [
            'options'=>['class' => 'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel'     => '«',         // Set the label for the "previous" page button
            'nextPageLabel'     => '»',         // Set the label for the "next" page button
            'firstPageLabel'    => Yii::t('common','page-first'),     // Set the label for the "first" page button
            'lastPageLabel'     => Yii::t('common','page-last'),      // Set the label for the "last" page button
            'nextPageCssClass'  => 'next',      // Set CSS class for the "next" page button
            'prevPageCssClass'  => 'prev',      // Set CSS class for the "previous" page button
            'firstPageCssClass' => 'first',     // Set CSS class for the "first" page button
            'lastPageCssClass'  => 'last',      // Set CSS class for the "last" page button
            'maxButtonCount'    => 5,           // Set maximum number of page buttons that can be displayed
            ],
    ]); ?>
</div>

</div>


<?php $Options = ['depends' => [\yii\web\JqueryAsset::className()],'type'=>'text/javascript'];?>
<!-- Resources amcharts-->
<?php $this->registerJsFile('//www.amcharts.com/lib/3/amcharts.js',['depends' => [\yii\web\JqueryAsset::className()]]);?>
<?php $this->registerJsFile('//www.amcharts.com/lib/3/serial.js',$Options);?>
<?php $this->registerJsFile('//www.amcharts.com/lib/3/themes/black.js',$Options);?>
<?php $this->registerJsFile('//www.amcharts.com/lib/3/pie.js',$Options);?>
<?php $this->registerJsFile('//www.amcharts.com/lib/3/themes/light.js',$Options);?>
<?php $this->registerJsFile('//code.jquery.com/ui/1.12.1/jquery-ui.js',$Options);?>
<?php $this->registerJsFile('@web/js/saleorders/bestsaleController.js?v=3.04.21.1',$Options);?>
<?php
function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}
$dataList = [];        
foreach ($dataProvider->models as $key => $model) {
    $dataList[]  = [
        "name"  => ($model->itemstb->alias)? $model->itemstb->alias : mb_substr($model->name,0,20) ,
        "qty"   => $model->qty,
        'color' => ($model->itemstb->color)? $model->itemstb->color : '#'.random_color()
    ];
}
$dataList = json_encode($dataList);

$js =<<<JS
var chartData = generatechartData();
function generatechartData() {
    var chartData = {$dataList};    
    return {
      data  : chartData,
    };
}


JS;

$this->registerJs($js,\yii\web\View::POS_HEAD);
?>
