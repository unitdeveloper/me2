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
 
$isAdmin = Yii::$app->session->get('Rules')['name'] == 'Administrator' || Yii::$app->session->get('Rules')['name'] == 'Accounting';

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
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'         => 'Photo',
                'format'            => 'raw',
                'headerOptions'     => ['class' => 'bg-gray'],
                'contentOptions'    => ['class' => 'relative'],
                'label' => Yii::t('common', 'Image'),
                'value' => function($model){
                    return Html::a(Html::img($model->picture,
                        ['style'=>'width:50px;']) .favolite($model), 
                        ['items/view','id'=>$model->id],
                        ['target' => '_blank']
                    );
                }
            ],

            [
                'attribute'         => 'ProductionBom',
                'visible'           => (Yii::$app->session->get('Rules')['name'] == 'Administrator'),
                'format'            => 'raw',
                'headerOptions'     => ['class' => 'bg-gray'],
                'contentOptions'    => ['class' => 'text-center'],
                'value' => function($model){
                    if($model->ProductionBom > 0 ){
                        return '<i class="fas fa-check-square text-aqua show-bom-detail pointer" data-id="'.$model->ProductionBom.'"></i>';
                    }else{
                        return '<i class="far fa-square"></i>';
                    }
                }
            ],

            [
                'attribute' => 'master_code',
                'format' => 'raw',
                'contentOptions' => ['class' => 'font-roboto'],
                'headerOptions' => ['class' => 'bg-gray'],
                'filterOptions' => ['style' => 'max-width:100px'],
                'label' => Yii::t('common','Master Code'),
                'value' => function($model){
                    return Html::a($model->iteminfo->code, ['items/view','id'=>$model->id],['target' => '_blank']); 
                },

            ],
            
            [
                'attribute'     => 'barcode',
                'format'        => 'raw',
                'contentOptions'=> ['class' => 'font-roboto'],
                'headerOptions' => ['class' => 'bg-gray'],
                'filterOptions' => ['style' => 'max-width:100px'],
                'label'         => Yii::t('common','Barcode'),
                'value'         => function($model){
                    $html = '<div>'.Html::a($model->iteminfo->barcode, ['items/view','id'=>$model->id],['target' => '_blank']).'</div>';
                    if($model->allBarcode != null){
                        foreach ($model->allBarcode as $key => $ref) {
                            $html.= $model->iteminfo->barcode == $ref->barcode ? '' : '<div>'.Html::a($ref->barcode,['/items/cross/index', 'SearchCross[item]' => $model->id],['class' => "text-yellow"]).'</div>';
                        }
                    }
                    return $html;
                },

            ],
            // [
            //     'format' => 'html',
            //     'value' => function($model){
            //         $html = '';
            //         if (strpos($model->detail,'Automatic created')) {
            //             $html.= ' <i class="fab fa-android text-success"></i>';
            //         } else{
            //             $html.= ' <i class="fab fa-creative-commons-by text-info"></i>';
            //         }
            //         return $html;
            //     }
            // ],
            // [
                
            //     'format' => 'html',
            //     'label' => Yii::t('common','tt'),
            //     'value' => function($model){

            //         return $model->transection->table;
            //     },

            // ],            

            [
                'attribute'     => 'description_th',
                'format'        => 'html',
                'label'         => Yii::t('common','Product Name'),
                'headerOptions' => ['class' => 'bg-gray'],
                'value' => function($model){
                    //return $model->Description;
                    $html = '<div>'.$model->iteminfo->name. ' '.$model->detail.' ' . $model->size.'</div>';
                    if($model->iteminfo->name != $model->iteminfo->name_en){
                        $html.= '<div>'. $model->iteminfo->name_en . '</div>';
                    }
                    return $html;
                },

            ],

            [
                'attribute' => 'ItemGroup',
                'label' => Yii::t('common','Items Group'),
                'headerOptions' => ['class' => 'bg-gray'],
                'value' => function($model){
                    //return $model->UnitOfMeasure;
                    return ($model->itemGroups)
                                ? ($model->itemGroups->Description != ''
                                    ? $model->itemGroups->Description
                                    : $model->itemGroups->Description_th
                                    ) 
                                : '';
                }
            ],
            
            $isAdmin ? [
                'attribute' => 'StandardCost',
                'label' => Yii::t('common','Standard Cost'),
                'headerOptions' => ['class' => 'text-right bg-gray','style' => 'min-width:106px'],
                'contentOptions' => ['class' => 'text-right font-roboto'],
                'value' => function($model){
                    return number_format($model->pricing->stdcost,2);
                }
            ] : [
                'label' => Yii::t('common','Price'),
                'headerOptions' => ['class' => 'hidden'],
                'contentOptions' => ['class' => 'hidden'],
                'filterOptions' => ['class' => 'hidden'],
                'value' => function($model){
                    //return $model->pricing->price ? number_format($model->pricing->price,2) : 0;
                    return '';
                }
            ] ,

            $isAdmin ? [
                'label' => Yii::t('common','Default Sale Price'),
                'headerOptions' => ['class' => 'text-right bg-gray','style' => 'min-width:106px'],
                'contentOptions' => ['class' => 'text-right font-roboto'],
                'value' => function($model){
                    return $model->CostGP;
                }
            ] : [
                'label' => Yii::t('common','Sale Price'),
                'headerOptions' => ['class' => 'text-right bg-gray','style' => 'min-width:106px'],
                'contentOptions' => ['class' => 'text-right font-roboto'],
                'value' => function($model){
                    
                    return $model->pricing->price ? number_format($model->pricing->price,2) : 0;
                }
            ],


            $isAdmin ? [
                'label' => Yii::t('common','Special price'),
                'headerOptions' => ['class' => 'text-right bg-gray','style' => 'min-width:106px'],
                'contentOptions' => ['class' => 'text-right font-roboto'],
                'value' => function($model){
                    return number_format($model->sale_price,2);
                }
            ] : [
                'label' => Yii::t('common','Special price'),
                'headerOptions' => ['class' => 'hidden'],
                'contentOptions' => ['class' => 'hidden'],
                'filterOptions' => ['class' => 'hidden'],
                'value' => function($model){                    
                    return '';
                }
            ] ,

           
            [
                //'attribute' => 'UnitOfMeasure',
                'label' => Yii::t('common','Unit Of Measure'),
                'headerOptions' => ['class' => 'bg-gray'],
                'value' => function($model){
                    //return $model->UnitOfMeasure;
                    return $model->iteminfo->measure;
                }
            ],
            
            [
                //'attribute' => 'Inventory',
                'label' => Yii::t('common','Stock'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right bg-gray'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return number_format($model->myItems->last_stock,2);
                }
            ],
            

            [
                
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'     => ['class'=>'btn btn-default'],
                'headerOptions'     => ['class' => 'bg-gray'],
                'contentOptions'    => ['class' => 'text-right','style'=>'min-width:100px;'],
                'template'          => '<div class="btn-group btn-group text-center" role="group"> {update} {delete} </div>',
                'options'           => ['style'=>'width:100px;'],
                'buttons'           => [
                    'view'  => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> ',$url,['class'=>'btn btn-info']);
                    },
                    'delete' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> ',$url,[
                            'class' => 'btn btn-warning',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                    'update' => function($url,$model,$key){
                        return Html::a('<i class="far fa-edit"></i> ',$url,['class'=>'btn btn-success']);
                    }
                  ]
              ],

        ];
?>




<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="items-index" ng-init="Title='<?=$this->title;?>'">
<div class=" ">
    <div class=" " style="position: relative; height:55px;">
 
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
                                return '<div>'.$model->iteminfo->name.'</div>';
                            },
                        ],
                        [
                            'attribute' => 'Description',
                            'format' => 'html',            
                            'label' => Yii::t('common','Product Name'),
                            'value' => function($model){
        
                                return '<div>'.$model->iteminfo->name_en.'</div>';
                                 
                            },
            
                        ],

                        // [
                        //     'label' => Yii::t('common','Items Group'),
                        //     'value' => function($model){
                        //         //return $model->UnitOfMeasure;
                        //         return ($model->itemGroups)? $model->itemGroups->Description : '';
                        //     }
                        // ],
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
            
                                    return $model->ProductionBom > 0 ? $model->myItems->last_possible : $model->myItems->last_stock;
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
            'dataProvider'  => $dataProvider,
            'filterModel'   => $searchModel,
            'tableOptions'  => ['class' => 'table  table-bordered table-hover'],
            'rowOptions'    => function($model){
                return ['class' => '  editItem'];
            },
            'pjax' => false,
            'responsiveWrap' => false,
            'columns' => $gridColumns,
            'pager' => [
                'options'   => ['class' => 'pagination'],   // set clas name used in ui list of pagination
                'prevPageLabel'     => '«',   // Set the label for the "previous" page button
                'nextPageLabel'     => '»',   // Set the label for the "next" page button
                'firstPageLabel'    => Yii::t('common','First'),   // Set the label for the "first" page button
                'lastPageLabel'     => Yii::t('common','Last'),    // Set the label for the "last" page button
                'nextPageCssClass'  => Yii::t('common','next'),    // Set CSS class for the "next" page button
                'prevPageCssClass'  => Yii::t('common','prev'),    // Set CSS class for the "previous" page button
                'firstPageCssClass' => Yii::t('common','first'),    // Set CSS class for the "first" page button
                'lastPageCssClass'  => Yii::t('common','last'),    // Set CSS class for the "last" page button
                'maxButtonCount'    => 6,    // Set maximum number of page buttons that can be displayed
            ],
        ]); ?>

        <?php // yii\widgets\Pjax::end() ?>
    </div>
</div>
</div>


 
<div class="modal fade" id="modal-show-bom">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">BOM DETAIL</h4>
            </div>
            <div class="modal-body">
                <div class="show-bom-item"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 
            </div>
        </div>
    </div>
</div>

<?php
$js=<<<JS
 
// if($('input[name="export_columns_selector[]"]').attr('checked', true)){
//     console.log('true');
// }
// if($('input[name="export_columns_selector[]"]').attr('checked', false)){
//     console.log('false');
// }\
$('body').on('click','.show-bom-detail', function(){
    let id = $(this).attr('data-id');
    $('#modal-show-bom').modal('show');
    $('.show-bom-item').html('<div class="text-center col-xs-12 mt-10 pb-10 "><i class="fa fa-refresh fa-spin"></i> Loading...</div>');
    setTimeout(() => {
        route('index.php?r=Manufacturing%2Fprodbom%2Fview-ajax&id='+id,'GET',{bom:id},'show-bom-item');
        $('body').find('#modal-show-bom .left-menu-widget').hide();
    }, 800);
})



const getCountStock = (obj, callback) => {
    // ถ้านับแล้วไม่ต้องนับอีก
    let countOrNot = localStorage.getItem('count-or-not') ? JSON.parse(localStorage.getItem('count-or-not')) : [];

    if(countOrNot.length <= 0){
        // Increase expiration time after save  
        localStorage.setItem('saved', new Date().getTime())
        localStorage.setItem('count-or-not', JSON.stringify({id:1}))     

        fetch("?r=items/items/count-stock-ajax", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {        
            
        })
        .catch(error => {
            console.log(error);        
        });
    }
}


$(document).ready(function(){
    setTimeout(() => {
        console.log('try-count')
        getCountStock({limit:0})
        // Clear on startup if expired
        if(!localStorage.getItem('hours')){
            localStorage.setItem('hours',2)
        }
        let hours   = localStorage.getItem('hours');
        let saved   = localStorage.getItem('saved')
        let now     = new Date().getTime() - saved;
        let compaire= hours * 60 * 60 * 1000;
        let inTime  = (((compaire - now)  ) ) / 1000 ;
        if (saved && (now > compaire)) {
            localStorage.removeItem('count-or-not')
        }else{
            console.log('Count next in '+ number_format(inTime.toFixed(0)) +' sec')
        }
    }, 3000);


});
JS;
$this->registerJs($js);

// $this->registerJs("
//     $('body').on('click','tr.editItem',function (e) {
//         var id = $(this).data('key');
//         location.href = '" . Url::to(['/items/items/view']) . "&id=' + id;
//     });
// ");

?>