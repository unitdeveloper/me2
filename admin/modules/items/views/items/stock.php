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
 
<?= $this->render('_header',['model' => $searchModel])?>


<?php
        $gridColumns = [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'Photo',
                'format' => 'html',
                'contentOptions' => ['class' => 'relative'],
                'label' => Yii::t('common', 'Image'),
                'value' => function($model){
                    return  Html::img($model->picture,['style'=>'width:50px;']);
                }
            ],

            [
                'attribute' => 'master_code',
                'format' => 'html',
                'filterOptions' => ['style' => 'max-width:100px'],
                'label' => Yii::t('common','Master Code'),
                'value' => function($model){
                    return $model->iteminfo->code; 
                },

            ],
            [
                'attribute' => 'barcode',
                'format' => 'html',
                'filterOptions' => ['style' => 'max-width:100px'],
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

                    //return $model->Description;
                    $html = '<div>'.$model->iteminfo->name.'</div>';
                    if($model->iteminfo->name != $model->iteminfo->name_en){
                        $html.= '<div>'. $model->iteminfo->name_en.'</div>';
                    }

                    
                    
                    return $html;
                    //return Html::a($model->Description, ['items/view','id'=>$model->No]);
                },

            ],
 
           
        ];
?>


 
<div class="items-index" ng-init="Title='<?=$this->title;?>'">
 
        <?php //yii\widgets\Pjax::begin(['id' => 'grid-item-pjax','timeout'=>5000]) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
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


 