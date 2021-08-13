<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\warehousemoving\models\InventorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'View Inventories';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'yii\grid\SerialColumn'],
[
        'attribute' => 'PostingDate',
        'format' => 'raw',
        'label' => Yii::t('common','Date'),
        'value' => function($model){   
            // $html = '<div>'.date('d-m-Y',strtotime($model->PostingDate)).'</div>';
            // $html.= '<div>:: '.date('H:i:s',strtotime($model->PostingDate)).'</div>';  
            return date('d-m-Y',strtotime($model->PostingDate));
        }
    ], 
    //'id',
    // [
    //     'label' => Yii::t('common','Vat '),
    //     'value' => function($model){
    //         if($model['vat_type']==1){
    //             $vat = 'Vat';
    //         }else{
    //             $vat = 'No Vat';
    //         }
    //         return $vat;
    //     }
    // ],
    'TypeOfDocument',
    'SourceDocNo',
    
    //'DocumentNo',
    //'description_th',
    
    
    //'Quantity',
    //'item_no',
    [
        'attribute' => 'master_code',
        'label' => Yii::t('common','Product Name'),
        'value' => function($model){    
            if($model->item==1414){
                return $model->item_no;                
            }else{
                return $model->master_code;
            }              
            
        }
    ], 
    [
        'attribute' => 'description_th',
        'label' => Yii::t('common','Product Name'),
        'value' => function($model){                     
            return $model->description_th;
        }
    ], 
    [
        'attribute' => 'Quantity',
        'contentOptions' => ['class' => 'text-right'],
        'label' => Yii::t('common','Quantity'),
        'value' => function($model){                     
            return $model->Quantity;
        }
    ], 
    //'unit_price',
    //'vat_type',
    //'PostingDate',
    

    //['class' => 'yii\grid\ActionColumn'],
];
?>
<div class="view-inventory-index">

    <?= Html::a('<i class="fas fa-refresh"></i> '. Yii::t('common','Clear Filter'), ['index'], ['class' => 'btn btn-success btn-flat']) ?> <span class="btn btn-flat btn-primary"><i class="fas fa-table"></i> <?= Html::encode($this->title) ?></span> 
    <div class="box box-success margin-top">
        <?php  echo $this->render('_search', ['model' => $searchModel,'dataProvider' => $dataProvider]); ?> 
    </div>
    <div class="row">
        <div class="col-sm-12 text-right">
            <?=ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' =>  [
                    ['class' => 'yii\grid\SerialColumn'],
                    'PostingDate',
                    [
                        'label' => Yii::t('common','Vat '),
                        'value' => function($model){
                            if($model['vat_type']==1){
                                $vat = 'Vat';
                            }else{
                                $vat = 'No Vat';
                            }
                            return $vat;
                        }
                    ],
                    'TypeOfDocument',
                    'SourceDocNo',
                    'master_code',
                    'description_th',
                    'Quantity',
                    
                ],
                'columnSelectorOptions'=>[
                    'label' => 'Columns',
                    'class' => 'btn btn-success-ew'
                ],
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => 'Export All',
                    'class' => 'btn btn-primary-ew'
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,                                
                ],
                'styleOptions' => [
                    ExportMenu::FORMAT_PDF => [
                        'font' => [
                            'family' => ['THSarabunNew','garuda'],
                                'bold' => true,
                                'color' => [
                                    'argb' => 'FFFFFFFF',
                            ],
                        ],
                    ],
                ],
                'target' => ExportMenu::TARGET_BLANK,
            ]); 
        ?>   
        </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=> Yii::t('common','First'),   // Set the label for the "first" page button
            'lastPageLabel'=> Yii::t('common','Last'),    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>10,    // Set maximum number of page buttons that can be displayed
            ],
          'options' => ['class' => 'table-responsive'],
           
          'pjax'=>true,    
          'responsiveWrap' => false, // Disable Mobile responsive  
    ]); ?>
</div>
