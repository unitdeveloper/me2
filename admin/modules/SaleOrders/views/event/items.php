<?php

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
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'Photo',
                'format' => 'html',
                'contentOptions' => ['class' => 'relative'],
                'label' => Yii::t('common', 'Image'),
                'value' => function($model){

                    return Html::a(Html::img($model->getPicture(),
                        ['style'=>'width:50px;']) .favolite($model), ['/items/items/view','id'=>$model->No]);
                    //return Html::img($model->getPicture());

                }
            ],

            [
                'attribute' => 'master_code',
                'format' => 'html',

                'label' => Yii::t('common','Master Code'),
                'value' => function($model){

                    $html = Html::a($model->master_code, ['/items/items/view','id'=>$model->No]);
                    //$html = $model->master_code;
                    //$html.= '<div />Barcode :'. $model->barcode.'</div>';

                    return $html;
                },

            ],
         
            [
                'attribute' => 'description_th',
                'format' => 'html',

                'label' => Yii::t('common','Product Name (en)'),
                'value' => function($model){

                    return $model->Description;
                    //return Html::a($model->Description, ['items/view','id'=>$model->No]);
                },

            ],
            [
                //'attribute' => 'description_th',
                'format' => 'html',

                'label' => Yii::t('common','Product Name (th)'),
                'value' => function($model){

                    return $model->description_th;
                    //return Html::a($model->description_th, ['items/view','id'=>$model->No]);
                },

            ],
          
            [
                'label' => Yii::t('common','Unit Of Measure'),
                'value' => function($model){
                    return $model->UnitOfMeasure;
                }
            ],
            
            [
                //'attribute' => 'Inventory',
                'label' => Yii::t('common','Inventory'),
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){

                        return number_format($model->getInven(),2);
                }
            ],

        ];
?>




<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="items-index" ng-init="Title='<?=$this->title;?>'">
<div class="panel panel-default">
    <div class="panel-heading" style="position: relative; height:55px;">
 
        <div style="position: absolute;right: 10px; top:10px;">
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
                    //'encoding' => 'utf8',
                ]);
        ?>
        </div>

    </div>
    <div class="panel-body">
        <?php yii\widgets\Pjax::begin(['id' => 'grid-item-pjax','timeout'=>5000]) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table  table-bordered table-hover'],
            'rowOptions' => function($model){
                return ['class' => 'pointer editItem'];
            },
            'pjax' => true,
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

        <?php yii\widgets\Pjax::end() ?>
    </div>
</div>
</div>


<?php
$this->registerJs("
    $(document).ready(function(){
        $('.ew-bt-app-home').attr('href','index.php?r=items%2Fitems%2Findex');
        $('.ew-bt-app-new').attr('href','index.php?r=items%2Fitems%2Fcreate');
    });

    $('body').on('click','tr.editItem',function (e) {
        var id = $(this).data('key');
        console.log($(this).data('key'));
        location.href = '" . Url::to(['/items/items/view']) . "&id=' + id;
    });
");
