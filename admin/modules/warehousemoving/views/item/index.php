<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
//use kartik\grid\GridView;
use kartik\export\ExportMenu;

use yii\helpers\ArrayHelper;
use common\models\Itemgroup;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\items\models\SearchItems */
/* @var $dataProvider yii\data\ActiveDataProvider */
use yii\widgets\Pjax;

use common\models\WarehouseMoving;

$this->title = Yii::t('common', 'Items RM');
$this->params['breadcrumbs'][] = $this->title;


function favolite($model)
{   
    if($model->interesting=='Enable'){
        $star = '<div class="star-div"><i class="fa fa-star" aria-hidden="true" style="color: #f4d341;  "></i></div>';
    }else {
        $star = NULL;
    }
    return $star;

}
?>
<style type="text/css">
    .star-div{
        position: absolute;
        top: 2%; 
        right: 2px; 
    }

    .relative{
        position: relative;
    }
</style>


<?php 
        $gridColumns = [
            [
                'class' => 'yii\grid\Column'
            ],

            [
                'attribute' => 'Photo',
                'format' => 'html',
                'contentOptions' => ['class' => 'relative'],
                'label' => Yii::t('common', 'Image'),
                'value' => function($model){

                    return Html::a(Html::img($model->getPicture(),
                        ['style'=>'width:50px;']) .favolite($model), ['items/view','id'=>$model->id]);
                     

                }
            ],
            //'barcode',
            //'No',
            'master_code',
            // [
            //     'attribute' => 'master_code',
            //     'format' => 'raw',
                
            //     'label' => Yii::t('common','Master Code'),
            //     'value' => function($model){

                   
            //         return Html::a($model->master_code, ['item/view','id'=>$model->No]);
            //     },

            // ],
             
            [
                'attribute' => 'description_th',
                'format' => 'raw',
                
                'label' => Yii::t('common','Product Name (en)'),
                'value' => function($model){

                   
                    return $model->Description;
                },

            ],
             
            [
                //'attribute' => 'description_th',
                'format' => 'html',
                
                'label' => Yii::t('common','Product Name (th)'),
                'value' => function($model){

                   
                    return $model->description_th;
                },

            ],
            'UnitOfMeasure',
            //'inven:decimal',
            // [
            //     'label' => Yii::t('common','Unit Of Measure'),
            //     'value' => function($model){
            //         return $model->UnitOfMeasure;
            //     }
            // ],
            //'Inventory',
            [
                //'attribute' => 'Inventory',
                'label' => Yii::t('common','Inventory'),
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){

                        // $Query = WarehouseMoving::find()->where(['ItemNo' => $model->No]);
                        // $RealInven = $Query->sum('Quantity');


                        // $Remaining = $model->Inventory + $RealInven;

                        return number_format($model->inven,2);
                }
            ],

        ];
?>




<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="items-index" ng-init="Title='<?=$this->title;?>'">
<div class="text-right" style="margin-bottom: 5px;">
    <?php
     echo ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                       
                    
                    //'barcode:text',
                     [
                        'attribute' => 'description_th',
                        'format' => 'raw',                        
                        'label' => Yii::t('common','Barcode'),
                        'value' => function($model){

                            if($model->barcode != '' ){
                                return $model->barcode;
                            } else {
                                return ' ';
                            }
                        },

                    ],
                  
                    'master_code',
                    
                    'Description',
                   
                    'description_th',
                    
                    'UnitOfMeasure',
                   
                    'inven',
                    
        
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
                    ExportMenu::FORMAT_PDF => false,               
                ],
                // 'styleOptions' => [
                //     ExportMenu::FORMAT_PDF => [
                //         'font' => [
                //              'family' => ['garuda'],
                //                 //'bold' => true,
                //                 'color' => [
                //                      'argb' => 'FFFFFFFF',
                //              ],
                             
                //         ],
                //     ],
                // ],
                'filename' => 'Items-'.date('Ymd-His'),
                //'encoding' => 'utf8',
            ]); 
    ?> 
</div>


<div class="panel panel-default">
    
    <div class="panel-body">

     
  
    <?php yii\widgets\Pjax::begin(['id' => 'grid-item-pjax','timeout'=>5000]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'pjax' => true,
        'rowOptions' => function($model){
            return ['class' => 'pointer editItem'];
        },
        'columns' => $gridColumns,
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=>Yii::t('common','First'),   // Set the label for the "first" page button
            'lastPageLabel'=>Yii::t('common','Last'),    // Set the label for the "last" page button
            'nextPageCssClass'=>Yii::t('common','next'),    // Set CSS class for the "next" page button
            'prevPageCssClass'=>Yii::t('common','prev'),    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>Yii::t('common','first'),    // Set CSS class for the "first" page button
            'lastPageCssClass'=>Yii::t('common','last'),    // Set CSS class for the "last" page button
            'maxButtonCount'=>6,    // Set maximum number of page buttons that can be displayed
            ],
    ]); ?>

    <?php yii\widgets\Pjax::end() ?>
    </div>
</div>
</div>
<?php
$this->registerJs("
    $('body').on('click','tr.editItem',function (e) {
        var id = $(this).data('key');
         
        location.href = '" . Url::to(['/warehousemoving/item/view']) . "&id=' + id;
    });
");