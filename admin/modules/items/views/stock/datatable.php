<?php
ini_set('MAX_EXECUTION_TIME', 3600);
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

use yii\helpers\ArrayHelper;
use common\models\Itemgroup;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\items\models\SearchItems */
/* @var $dataProvider yii\data\ActiveDataProvider */
use yii\widgets\Pjax;

use common\models\WarehouseMoving;

use fedemotta\datatables\DataTables;
?>



<?php

$this->title = Yii::t('common', 'Items');
$this->params['breadcrumbs'][] = $this->title;
 
 
?>
 

 
<div class="items-index" ng-init="Title='<?=$this->title;?>'">
 
    <?= DataTables::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'Photo',
                'format' => 'html',
                'contentOptions' => ['class' => 'relative'],
                'label' => Yii::t('common', 'Image'),
                'value' => function($model){
                    return  Html::img($model->items->picture,['style'=>'width:50px;']);
                }
            ],
            // 'items.master_code',
            // 'items.barcode',
            [
                'attribute' => 'items.master_code',
                'format' => 'html',
                'headerOptions' => ['class' => 'hidden-xs'],
                'contentOptions' => ['class' => 'hidden-xs'],
                'label' => Yii::t('common', 'Code'),
                'value' => function($model){
                    return  $model->items->master_code;
                }
            ],
            [
                'attribute' => 'items.barcode',
                'format' => 'html',
                'headerOptions' => ['class' => 'hidden-xs'],
                'contentOptions' => ['class' => 'hidden-xs'],
                'label' => Yii::t('common', 'Barcode'),
                'value' => function($model){
                    return  $model->items->barcode;
                }
            ],
            'items.description_th',
            //'items.inven'
            [
                'attribute' => 'items.inven',
                'format' => 'html',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'label' => Yii::t('common', 'Inventory'),
                'value' => function($model){
                    return  number_format($model->items->inven);
                }
            ],
            //['class' => 'yii\grid\ActionColumn'],
        ],
        'clientOptions' => [
            "lengthMenu"=> [[20,-1], [20,Yii::t('common',"All")]],
            //"info"=>false,
            "responsive"=>true, 
            //"dom"=> 'lfTrtip',
            "tableTools"=>[
                "aButtons"=> [  
                    [
                    "sExtends"=> "copy",
                    "sButtonText"=> Yii::t('common',"Copy to clipboard")
                    ],[
                    "sExtends"=> "csv",
                    "sButtonText"=> Yii::t('common',"Save to CSV")
                    ],[
                    "sExtends"=> "xls",
                    "oSelectorOpts"=> ["page"=> 'current']
                    ],[
                    "sExtends"=> "pdf",
                    "sButtonText"=> Yii::t('common',"Save to PDF")
                    ],[
                    "sExtends"=> "print",
                    "sButtonText"=> Yii::t('common',"Print")
                    ],
                ]
            ]
        ],
    ]);?>
 
</div>


 