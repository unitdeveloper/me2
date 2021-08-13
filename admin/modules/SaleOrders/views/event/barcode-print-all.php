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
              return ['id' => $model->barcode,'style' => 'min-width:100px;'];
            },
            'label' => Yii::t('common','Barcode'),
            'value' => function($model){
              $code = [
                      'elementId'=> $model->barcode,
                      'value'=> $model->barcode,
                      'type'=>'ean13',
                      ];
              return \barcode\barcode\BarcodeGenerator::widget($code);
            }
          ],



          [
              'label' => Yii::t('common','Master Code'),
              'value' => function($model){
                return $model->master_code;
              }
          ],
          [
              'label' => Yii::t('common','Image'),
              'format' => 'html',
              'value' => function($model){
                return Html::img($model->getPicture(),['style' => 'width:50px;']);
              }
          ],

          'description_th',

          'UnitOfMeasure',


];


?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-event-sale-line" ng-init="Title='<?=$this->title?>'" >


    <div class="col-sm-offset-8">

        <div class="col-xs-12 text-right">
          <?php
            echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
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
      <div class="col-md-12">


        <?php Pjax::begin(); ?>
            <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            // 'tableOptions' => ['class' => 'table   table-bordered table-hover'],
            // 'rowOptions' => function($model){
            //     return ['class' => $model->status=='closed' ? 'bg-success pointer editBill' : 'bg-warning pointer editBill'];
            // },
            'columns' => $gridColumns,

        ]); ?>
        <?php Pjax::end(); ?>
      </div>
    </div>

</div>
