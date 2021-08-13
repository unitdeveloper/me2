<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


use admin\modules\items\models\MultipleUploadForm;
$MultiUpload = new MultipleUploadForm();
/* @var $this yii\web\View */
/* @var $model common\models\Items */
use common\models\WarehouseMoving;

$this->title = $model->description_th;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="box box-default" ng-init="Title='<?=$this->title;?>'">
<div class="row">
    <div class="col-sm-12">
        <div class="items-view">

            <p>
            <div class="row">
            <?php if(Yii::$app->session->get('Rules')['rules_id']==1): ?>
                <div class="col-sm-12">
                    <div class="col-sm-12">

                   <!--  <div class="text-right">
                        <?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->No], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]) ?>
                    </div> -->
                    </div>
                </div>
            <?php endif; ?>
            </div>
            </p>

            <div class="col-lg-4">
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
            <div class="col-lg-8">



                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        //'No',
                        'master_code',
                        'Description',
                        'description_th',
                        'alias',
                        //'UnitOfMeasure',
                        //'Inventory',
                        [
                            'label' => Yii::t('common','Remaining'),
                            'format' => 'raw',
                            'value' => function($model){
                                // $Query = WarehouseMoving::find()->where(['ItemNo' => $model->No]);
                                // $RealInven = $Query->sum('Quantity');

                                // $Remaining = $model->Inventory + $RealInven;
                                // return number_format($Remaining,2).' '.$model->UnitOfMeasure;
                                $html = '<a href="index.php?WarehouseSearch[ItemId]='.$model->No.'&r=warehousemoving%2Fwarehouse" target="_blank">';
                                $html.=     number_format($model->getInven(),2).' '.$model->UnitOfMeasure;
                                $html.= '</a>';

                                return $html;
                            }
                        ],
                        'StandardCost',
                        'UnitCost',
                        'CostGP',
                        'itemGroup.Description',
                        //'itemSet.name',
                        [
                            'label' => Yii::t('common','Item Set'),
                            'value' => function($model){
                                return $model->itemSet['name'];
                            }
                        ],
                        //'TypeOfProduct',
                        [
                            'label' => Yii::t('common','Vat Type'),
                            'value' => function($model){
                                if($model->TypeOfProduct=='0'){
                                    return 'Vat';
                                }else {
                                    return 'Novat';
                                }
                            }
                        ],
                        //'CostingMethod',
                        [
                            'label' => Yii::t('common','Costing method'),
                            'value' => function($model){
                                if($model->CostingMethod=='0'){
                                    return 'FIFO';
                                }else {
                                    return 'Standard';
                                }
                            }
                        ],
                        [
                            //'attributes' => 'ProductionBom',
                            'format' => 'raw',
                            'label' => Yii::t('common','Sales Summary'),
                            'value' => function($model){

                                    return Html::a('<i class="fa fa-line-chart" aria-hidden="true"></i> '.Yii::t('common','Sales Summary'),
                                        ['/SaleOrders/order',
                                        'OrderSearch[item_no]' => $model->No,

                                        ],['class' => 'link']);

                            }
                        ],
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
                        [
                            'label' => Yii::t('common','Product Group'),
                            'format' => 'html',
                            'value' => function($model){

                                return $model->getGroup();

                            }

                        ],

                        [
                            'label' => Yii::t('common','-'),
                            'format' => 'raw',
                            'value' => function($model){
                                if($model->detail == 'Automatic created')
                                {
                                    return '<i class="fa fa-android  text-success" aria-hidden="true"></i>';
                                }else {
                                    return '<i class="fa fa-male  text-danger" aria-hidden="true"></i>';
                                }
                            }
                        ],
                        //index.php?r=SaleOrders%2Forder&textSearch=01-CT02-071006
                        //'PriceStructure_ID',
                        //'priceStructure.Name',


                    ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
</div>
