<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\SaleOrders\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Sale Line');
$this->params['breadcrumbs'][] = $this->title;


$gridColumns = [
          [
              'class' => 'yii\grid\SerialColumn',
          ],
          [
            'contentOptions' => function($model){
              return ['id' => 'showBarcode'.$model->id,'style' => 'min-width:100px;'];
            },
            'label' => Yii::t('common','Barcode'),
            'value' => function($model){
              $code = [
                      'elementId'=> 'showBarcode'.$model->id,
                      'value'=> $model->items->barcode,
                      'type'=>'ean13',
                      ];
              return \barcode\barcode\BarcodeGenerator::widget($code);
            }
          ],

          [
              'attribute' => 'order_date',
              'label' => Yii::t('common','Order Date'),
              'value' => function($model){
                return date('d/m/Y',strtotime($model->order_date));
              }
          ],
          'header.no',
        //   [
        //       'label' => Yii::t('common','Barcode'),
        //       'value' => function($model){
        //         return $model->items->barcode;
        //       }
        //   ],

          'items.description_th',

          [
              'label' => Yii::t('common','Quantity'),
              'headerOptions' => ['class' => 'text-right'],
              'contentOptions' => ['class' => 'text-right'],
              'value' => function($model){
                return $model->quantity;
              }
          ],

          [
              'label' => Yii::t('common','Price'),
              'headerOptions' => ['class' => 'text-right'],
              'contentOptions' => ['class' => 'text-right'],
              'value' => function($model){
                return $model->unit_price;
              }
          ],

          [
              'label' => Yii::t('common','Discount'),
              'headerOptions' => ['class' => 'text-right'],
              'contentOptions' => ['class' => 'text-right'],
              'value' => function($model){
                return $model->line_discount;
              }
          ],

          [
              'label' => Yii::t('common','Amount'),
              'headerOptions' => ['class' => 'text-right'],
              'contentOptions' => ['class' => 'text-right'],
              'value' => function($model){
                return $model->quantity * ($model->unit_price - $model->line_discount);
              }
          ],

        [
            'label' => Yii::t('common','Brand'), 
            'value' => function($model){
            return $model->items->brand;
            }
        ],
        [
            'label' => Yii::t('common','Group'), 
            'value' => function($model){
            return $model->items->group_chart;
            }
        ],
        'header.point',
        //'items.group_chart'
        //  'items.brand'
];


?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-event-sale-line" ng-init="Title='<?=$this->title?>'" >


    <div class="col-sm-offset-8">

        <div class="col-xs-12 text-right">
          <?php
            echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                                     
                                    [
                                        'attribute' => 'order_date',
                                        'label' => Yii::t('common','Order Date'),
                                        'value' => function($model){
                                            return date('d/m/Y',strtotime($model->order_date));
                                        }
                                    ],
                                    'header.no',
                                //   [
                                //       'label' => Yii::t('common','Barcode'),
                                //       'value' => function($model){
                                //         return $model->items->barcode;
                                //       }
                                //   ],
                        
                                    'items.description_th',
                        
                                    [
                                        'label' => Yii::t('common','Quantity'),
                                        'headerOptions' => ['class' => 'text-right'],
                                        'contentOptions' => ['class' => 'text-right'],
                                        'value' => function($model){
                                        return $model->quantity;
                                        }
                                    ],
                        
                                    [
                                        'label' => Yii::t('common','Price'),
                                        'headerOptions' => ['class' => 'text-right'],
                                        'contentOptions' => ['class' => 'text-right'],
                                        'value' => function($model){
                                        return $model->unit_price;
                                        }
                                    ],
                        
                                    [
                                        'label' => Yii::t('common','Discount'),
                                        'headerOptions' => ['class' => 'text-right'],
                                        'contentOptions' => ['class' => 'text-right'],
                                        'value' => function($model){
                                        return $model->line_discount;
                                        }
                                    ],
                        
                                    [
                                        'label' => Yii::t('common','Amount'),
                                        'headerOptions' => ['class' => 'text-right'],
                                        'contentOptions' => ['class' => 'text-right'],
                                        'value' => function($model){
                                        return $model->quantity * ($model->unit_price - $model->line_discount);
                                        }
                                    ],
                        
                                [
                                    'label' => Yii::t('common','Brand'), 
                                    'value' => function($model){
                                    return $model->items->brand;
                                    }
                                ],
                                [
                                    'label' => Yii::t('common','Group'), 
                                    'value' => function($model){
                                    return $model->items->group_chart;
                                    }
                                ],
                                'header.point',
                                //'items.group_chart'
                                //  'items.brand'
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
                     // ExportMenu::FORMAT_PDF => [
                     //                 'label' => Yii::t('common', 'PDF'),
                     //                 'icon' =>  'file-pdf-o',
                     //                 'iconOptions' => ['class' => 'text-danger'],
                     //                 //'linkOptions' => [],
                     //                 'options' => ['title' => Yii::t('common', 'Portable Document Format')],
                     //                 'alertMsg' => Yii::t('common', 'The PDF export file will be generated for download.'),
                     //                 'mime' => 'application/pdf',
                     //                 'extension' => 'pdf',
                     //                 'writer' => 'PDF',
                     //             ],
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
                ]);
            ?>
        </div>

    </div>


    <div class="row">
      <div class="col-md-12 table-responsive">
 

        <?php //Pjax::begin(['id' => 'pjax-search']); ?>

            <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            // 'tableOptions' => ['class' => 'table   table-bordered table-hover'],
            // 'rowOptions' => function($model){
            //     return ['class' => $model->status=='closed' ? 'bg-success pointer editBill' : 'bg-warning pointer editBill'];
            // },
            'columns' => $gridColumns,

        ]); ?>

        <?php //Pjax::end(); ?>
      </div>
    </div>

</div>
<?php $this->registerJS("
    $(document).ready(function(){  

        var element = $('input[name=\"EventLineSearch[order_date]\"]');

        var template = '<div class=\"input-group date\" data-provide=\"datepicker\">'+
                            '<input type=\"text\" class=\"form-control \" name=\"EventLineSearch[order_date]\">'+
                            '<div class=\"input-group-addon\">'+
                                '<span class=\"glyphicon glyphicon-th\"></span>'+
                            '</div>'+
                        '</div>';

        element.parent('td').html(template);

         

       
       
       
    })
    
 
    
    
", yii\web\View::POS_END, 'js-options');
?>