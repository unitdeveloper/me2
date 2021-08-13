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

$this->title = Yii::t('common', 'Item Cross Reference');
$this->params['breadcrumbs'][] = $this->title;
 
 
?>
<div class="text-right">
    <?= Yii::$app->request->get('SearchCross') ? Html::a('<i class="fas fa-brush"></i> ' .Yii::t('common','Clear Filter'), ['index'],['class' => 'btn btn-primary mb-10']) : ''; ?>
</div>
<?php
        $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'Photo',
                'format' => 'html',
                'contentOptions' => ['class' => 'relative'],
                'label' => Yii::t('common', 'Image'),
                'value' => function($model){
                    return Html::img($model->items->picture,['style'=>'width:50px;']);
                }
            ],
            [
                'attribute' => 'item_no',
                'filterOptions' => ['style' => 'max-width:50px'],
                'contentOptions' => ['class' => 'font-roboto'],
                'format' => 'html',
                'value' => function($model){
                    $html = '<div>'. Html::a($model->item_no, ['/items/items/view', 'id' => $model->item],['target' => '_blank']). '</div>';
                    $html.= '<div class="text-gray" title="'.Yii::t('common','Real Code').'">'. $model->items->master_code . '</div>';
                    return $html;
                }
            ],    
            // [
            //     'label' => Yii::t('common','Real Code'),
            //     'format' => 'html',
            //     'contentOptions' => ['class' => 'font-roboto'],
            //     'value' => function($model){
            //         return Html::a($model->items->master_code, ['/items/items/view', 'id' => $model->item],['target' => '_blank']);
            //     }
            // ],        
            // [
            //     'label' => Yii::t('common','Real Barcode'),
            //     'format' => 'html',
            //     'contentOptions' => ['class' => 'font-roboto'],
            //     'value' => function($model){
            //         return Html::a($model->items->barcode, ['/items/items/view', 'id' => $model->item],['target' => '_blank']);
            //     }
            // ],
            [
                'attribute' => 'barcode',
                'contentOptions' => ['class' => 'font-roboto'],
                'format' => 'html',
                'value' => function($model){
                    $html = '<div>'. $model->barcode.' </div>';
                    $html.= '<div class="text-gray" title="'.Yii::t('common','Real Barcode').'">'. $model->items->barcode. '</div>';
                    return $html;
                }
            ],
            
            'description',
            [
                'attribute' => 'customer',
                'format' => 'html',
                'label' => Yii::t('common','Customer'),
                'value' => function($model){
                    return $model->customer ? Html::a($model->customer->name, ['/customers/customer/view', 'id' => $model->reference_no],['title' => $model->reference_no]) : '';
                }
            ],
           /*
            [
                
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'contentOptions' => ['class' => 'text-right','style'=>'min-width:150px;'],
                'template'=>'<div class="btn-group btn-group text-center" role="group">{view} {update} {delete} </div>',
                'options'=> ['style'=>'width:150px;'],
                'buttons'=>[
                    'view' => function($url,$model,$key){
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
*/
        ];
?>
 
<div class="items-index" ng-init="Title='<?=$this->title;?>'">
    <div class="panel panel-default">    
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table  table-bordered table-hover'],            
                'pjax' => false,
                'responsiveWrap' => false,
                'columns' => $gridColumns,
                'pager' => [
                    'options'=>['class' => 'pagination'],   // set clas name used in ui list of pagination
                    'prevPageLabel' => '«',   // Set the label for the "previous" page button
                    'nextPageLabel' => '»',   // Set the label for the "next" page button
                    'firstPageLabel' => Yii::t('common','First'),   // Set the label for the "first" page button
                    'lastPageLabel' => Yii::t('common','Last'),    // Set the label for the "last" page button
                    'nextPageCssClass' => Yii::t('common','next'),    // Set CSS class for the "next" page button
                    'prevPageCssClass' => Yii::t('common','prev'),    // Set CSS class for the "previous" page button
                    'firstPageCssClass' => Yii::t('common','first'),    // Set CSS class for the "first" page button
                    'lastPageCssClass' => Yii::t('common','last'),    // Set CSS class for the "last" page button
                    'maxButtonCount' => 6,    // Set maximum number of page buttons that can be displayed
                    ],
            ]); ?>
        </div>
    </div>
</div>
