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

$this->title = Yii::t('common', 'Items');
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
                'attribute' => 'Photo',
                'format' => 'html',
                'contentOptions' => ['class' => 'relative'],
                'label' => Yii::t('common', 'Image'),
                'value' => function($model){
                    return Html::a(Html::img($model->picture,
                        ['style'=>'width:50px;']) .favolite($model), 
                        ['items/view-only','id'=>$model->id]
                    );
                }
            ],

            [
                'attribute' => 'master_code',
                'format' => 'raw',
                'filterOptions' => ['style' => 'max-width:100px'],
                'contentOptions' => ['class' => 'font-roboto'],
                'label' => Yii::t('common','Master Code'),
                'value' => function($model){
                    if(Yii::$app->session->get('Rules')['name'] == 'Administrator' || Yii::$app->session->get('Rules')['name'] == 'Accounting'){
                        return Html::a($model->iteminfo->code, ['items/view','id'=>$model->id],['target' => '_blank']); 
                    }else{
                        return Html::a($model->iteminfo->code, ['items/view-only','id'=>$model->id],['target' => '_blank']); 
                    }
                    
                },

            ],

            [
                'attribute' => 'barcode',
                'format' => 'html',
                'filterOptions' => ['style' => 'max-width:100px'],
                'contentOptions' => ['class' => 'font-roboto'],
                'label' => Yii::t('common','Barcode'),
                'value' => function($model){
                    return $model->iteminfo->barcode;
                },
            ],

            [
                'attribute' => 'description_th',
                'format' => 'html',
                'label' => Yii::t('common','Product Name'),
                'value' => function($model){
                    $html = '<div>'.$model->iteminfo->name.'</div>';
                    if($model->iteminfo->name != $model->iteminfo->name_en){
                        $html.= '<div>'. $model->iteminfo->name_en.'</div>';
                    }
                    return $html;
                },

            ],

            [
                'attribute' => 'ItemGroup',
                'label' => Yii::t('common','Items Group'),
                'value' => function($model){
                    return ($model->itemGroups)? $model->itemGroups->Description : '';
                }
            ],

            [
                'label' => Yii::t('common','Sold'),
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right font-roboto'],
                'format' => 'raw',
                'value' => function($model){
                    if($model->itemOfSold > 0){
                        return Html::a(number_format($model->itemOfSold),['/accounting/inv-line','InvLineSearch[item]' => $model->id,],['class' => 'link', 'target' => '_blink']);
                    }else{
                        return number_format($model->itemOfSold);
                    }
                   
                }
            ],
            // [
            //     'label' => Yii::t('common','Sale Price'),
            //     'headerOptions' => ['class' => 'text-right','style' => 'min-width:106px'],
            //     'contentOptions' => ['class' => 'text-right'],
            //     'value' => function($model){
            //         return $model->pricing->price ? number_format($model->pricing->price,2) : 0;
            //     }
            // ] ,
            [
                //'attribute' => 'UnitOfMeasure',
                'label' => Yii::t('common','Unit Of Measure'),
                'value' => function($model){
                    return $model->iteminfo->measure;
                }
            ],

        ];
?>



 
<div class="items-index" ng-init="Title='<?=$this->title;?>'">
<div class="panel panel-default">
    <div class="panel-heading" style="position: relative; height:55px;">
 
        <div style="position: absolute;right: 10px; top:10px;">
        <?php
         echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        //['class' => 'yii\grid\SerialColumn'],
            
                        [
                            'attribute' => 'master_code',
                            'format' => 'html',                           
                            'label' => Yii::t('common','Master Code'),
                            'value' => function($model){
                                return $model->iteminfo->code;
                            },
            
                        ],
                        [
                            'attribute' => 'barcode',
                            'format' => 'html',                             
                            'label' => Yii::t('common','Barcode'),
                            'value' => function($model){
                                return $model->iteminfo->barcode;
                            },
            
                        ],
                        [
                            'attribute' => 'description_th',
                            'format' => 'html',            
                            'label' => Yii::t('common','Product Name'),
                            'value' => function($model){
                                return $model->iteminfo->name;
                            },
                        ],
                        [
                            'attribute' => 'Description',
                            'format' => 'html',            
                            'label' => Yii::t('common','Product Name'),
                            'value' => function($model){
        
                                return $model->iteminfo->name_en;
                                 
                            },
            
                        ],

                        [
                            'label' => Yii::t('common','Items Group'),
                            'value' => function($model){
                                //return $model->UnitOfMeasure;
                                return ($model->itemGroups)? $model->itemGroups->Description : '';
                            }
                        ],
                        Yii::$app->session->get('Rules')['name'] == 'Administrator' || Yii::$app->session->get('Rules')['name'] == 'Accounting' ? [
                            'attribute' => 'StandardCost',
                            'label' => Yii::t('common','Standard Cost'),
                            'contentOptions' => ['class' => 'text-right'],
                            'value' => function($model){
                                return number_format($model->pricing->stdcost,2);
                            }
                        ] : [
                            'label' => Yii::t('common','Sale Price'),
                            'value' => function($model){
                                return $model->pricing->price ? number_format($model->pricing->price,2) : 0;
                            }
                        ] ,
                        
                        
                        [
                            //'attribute' => 'Inventory',
                            'label' => Yii::t('common','Inventory'),
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-right'],
                            'value' => function($model){
            
                                    return $model->inven;
                            }
                        ],

                        [
                            //'attribute' => 'UnitOfMeasure',
                            'label' => Yii::t('common','Unit Of Measure'),
                            'value' => function($model){
                                //return $model->UnitOfMeasure;
                                return $model->iteminfo->measure;
                            }
                        ],
                        
                    ],
                    'columnSelectorOptions'=>[
                        'label' => Yii::t('common','Columns'),
                        'class' => 'btn btn-success-ew'
                    ],
                    'fontAwesome' => true,
                    'dropdownOptions' => [
                        'label' => Yii::t('common','Export All'),
                        'class' => 'btn btn-primary-ew'
                    ],
                    'exportConfig' => [
                        ExportMenu::FORMAT_HTML => false,
                    ],
                    'styleOptions' => [
                        ExportMenu::FORMAT_PDF => [
                            'font' => [
                                 'family' => ['garuda'],
                                    //'bold' => true,
                                    'color' => [
                                         'argb' => 'FFFFFFFF',
                                 ],
                            ],
                        ],
                    ],
                    'filename' => 'Items',
                    'target' => ExportMenu::TARGET_BLANK,
                    //'encoding' => 'utf8',
                ]);
        ?>
        </div>

    </div>
    <div class="panel-body">
        <?php //yii\widgets\Pjax::begin(['id' => 'grid-item-pjax','timeout'=>5000]) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table  table-bordered table-hover'],
            'rowOptions' => function($model){
                return ['class' => '  editItem'];
            },
            'pjax' => false,
            'responsiveWrap' => false,
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

        <?php // yii\widgets\Pjax::end() ?>
    </div>
</div>
</div>


<?php
// $js=<<<JS
 
// if($('input[name="export_columns_selector[]"]').attr('checked', true)){
//     console.log('true');
// }
// if($('input[name="export_columns_selector[]"]').attr('checked', false)){
//     console.log('false');
// }
// JS;
// $this->registerJs($js);

// $this->registerJs("
//     $('body').on('click','tr.editItem',function (e) {
//         var id = $(this).data('key');
//         location.href = '" . Url::to(['/items/items/view']) . "&id=' + id;
//     });
// ");
?>