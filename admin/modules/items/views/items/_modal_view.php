<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

use kartik\color\ColorInput;

use admin\modules\items\models\MultipleUploadForm;
use common\models\WarehouseMoving;

$MultiUpload = new MultipleUploadForm();
/* @var $this yii\web\View */
/* @var $model common\models\Items */

$this->title = $model->description_th;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class=" ">
<div class="row">
    <div class="col-sm-12">
        <div class="items-view">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>

            </p>

            <div class="col-lg-6">
                <div class="col-sm-2" >
                    <div class="row">
                        <?= $MultiUpload->ImageRender($model) ?>


                    </div>

                </div>
                <div  class="col-sm-10">
                <?php
                    // if($model->Photo=="")
                    // {
                    //     echo Html::img('images/nopic.png', ['class'=>'img-thumbnail']);
                    // }else {
                    //     echo Html::img('images/product/'.$model->ItemGroup.'/'.$model->Photo, ['class'=>'img-thumbnail']);
                    // }

                    echo Html::img($model->getPicture(), ['class'=>'img-thumbnail']);
                ?>

                </div>
            </div>
            <div class="col-lg-6">



                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        //'No',
                        'master_code',
                        'barcode',
                        'Description',
                        'description_th',

                        [
                            'label' => Yii::t('common','Alias'),
                            'format' => 'raw',
                            'value' => function($model){
                                $html = '<input type="text" name="alias" value="'.$model->alias.'" class="form-control ajax-update" data-key="'.$model->No.'"/>';
                                return $html;
                            }
                        ],
                        [
                            'label' => Yii::t('common','Group'),
                            'format' => 'raw',
                            'value' => function($model){
                                $html = '<input type="text" name="group_chart" value="'.$model->group_chart.'" class="form-control ajax-update" data-key="'.$model->No.'"/>';
                                return $html;
                            }
                        ],
                        [
                            'label' => Yii::t('common','Brand'),
                            'format' => 'raw',
                            'value' => function($model){
                                $html = '<input type="text" name="brand" value="'.$model->brand.'" class="form-control ajax-update" data-key="'.$model->No.'"/>';
                                return $html;
                            }
                        ],
                        
                        
                        //'UnitOfMeasure',
                        //'Inventory',
                        [
                            'label' => Yii::t('common','Remaining'),
                            'value' => function($model){
                                $Query = WarehouseMoving::find()->where(['ItemNo' => $model->No]);
                                $RealInven = $Query->sum('Quantity');

                                $Remaining = $model->Inventory + $RealInven;
                                return number_format($Remaining,2).' '.$model->UnitOfMeasure;
                            }
                        ],
                        //'StandardCost',
                        //'UnitCost',
                        //'CostGP',
                        'itemGroup.Description',
                        //'itemSet.name',
                        [
                            'label' => Yii::t('common','Item Set'),
                            'value' => function($model){
                                return $model->itemSet['name'];
                            }
                        ],
                        [
                            //'attributes' => 'ProductionBom',
                            'format' => 'raw',
                            'label' => Yii::t('common','Sales Summary'),
                            'contentOptions' => ['class' => 'sale-summary'],
                            'value' => function($model){

                                    return Html::a('<i class="fa fa-line-chart" aria-hidden="true"></i> '.Yii::t('common','Sales Summary'),['/SaleOrders/order',
                                        'table_search' => $model->master_code,

                                        ],['class' => 'link']);

                            }
                        ],

                        //'TypeOfProduct',
                        // [
                        //     'label' => 'TypeOfProduct',
                        //     'value' => function($model){
                        //         if($model->TypeOfProduct=='0'){
                        //             return 'Vat';
                        //         }else {
                        //             return 'Novat';
                        //         }
                        //     }
                        // ],
                        //'CostingMethod',
                        // [
                        //     'label' => 'CostingMethod',
                        //     'value' => function($model){
                        //         if($model->CostingMethod=='0'){
                        //             return 'FIFO';
                        //         }else {
                        //             return 'Standard';
                        //         }
                        //     }
                        // ],
                        //'ProductionBom',
                        //'interesting',
                        // [
                        //     'label' => Yii::t('common','Interesting'),
                        //     'format' => 'html',
                        //     'value' => function($model){
                        //         if($model->interesting=='Enable'){
                        //             $star = '<i class="fa fa-star" aria-hidden="true" style="color: #f4d341;  "></i>';
                        //         }else {
                        //             $star = NULL;
                        //         }
                        //         return Yii::t('common',$model->interesting). ' ' .$star;
                        //     }

                        // ],
                        //'PriceStructure_ID',
                        //'priceStructure.Name',


                    ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
</div>
