<?php
use yii\helpers\Html;
 
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use yii\grid\GridView;



function filter($item) {
    $item_group = Yii::$app->request->getQueryParam('filter_item_group', '');
    if (strlen($item_group) > 0) {
        if (strpos($item['item_group'], $item_group) != false) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

$filteredresultData = array_filter($resultData, 'filter');



$item_group = Yii::$app->request->getQueryParam('filter_item_group', '');
$sale_amount = Yii::$app->request->getQueryParam('filtersale_amount', '');

$searchModel = ['id' => null, 'item_group' => $item_group, 'sale_amount' => $sale_amount];

?>
 
<div class="row">
    <div class=" col-xs-12">
        
 

        
         <?= GridView::widget([
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'key'=>'id',
                    'allModels' => $filteredresultData,
                    'sort' => [
                        'attributes' => ['id', 'item_group', 'sale_amount','discount','status'],
                    ],
                ]),
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    //'id',                    
                    [
                        'attribute' => 'item_group', 
                        'value' => function($model){
                             
                            $html = $model['item_group'];   
                                                   
                            return $html;
                        },
                        'filter' => '<input class="form-control" name="filter_item_group" value="'. $searchModel['item_group'] .'" type="text">',
                    ],
                    [
                        "attribute" => "sale_amount",
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => function($model){
                            return number_format($model['sale_amount'],2);
                        },
                    ],
                    [
                        "attribute" => "discount",
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => function($model){
                            return number_format($model['discount'],2);
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'buttonOptions'=>['class'=>'btn btn-default'],
                        'contentOptions' => ['class' => 'text-right','style'=>'min-width:200px;'],
                        'template'=>'<div class="btn-group btn-group text-center" role="group"> {reject} {approve}  </div>',
                        'buttons'=>[
                            'reject' => function($url,$model,$key){  
                                return Html::a('<i class="fas fa-ban"></i> ' .Yii::t('common','Reject'),$url,[
                                    'class' => 'btn btn-warning',
                                    'data' => [
                                        'confirm' => Yii::t('common', 'Are you sure you want to Reject this?'),
                                        'method' => 'post',
                                    ],
                                ]);                                                   
                            },
                            'approve' => function($url,$model,$key){  
                                return Html::a('<i class="fas fa-check"></i> ' .Yii::t('common','Approve'),$url,[
                                    'class' => 'btn btn-primary',
                                    'data' => [
                                        'confirm' => Yii::t('common', 'Are you sure you want to Approve this?'),
                                        'method' => 'post',
                                    ],
                                ]);                                                   
                            },
                            'view' => function($url,$model,$key){
                                return Html::a('<i class="fas fa-eye"></i> ',$url,['class'=>'btn btn-success']);
                            },
                            'delete' => function($url,$model,$key){
                                return Html::a('<i class="far fa-trash-alt"></i> ',$url,[
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ]);
                            },
                            'update' => function($url,$model,$key){
                                return Html::a('<i class="far fa-edit"></i> ',$url,['class'=>'btn btn-primary']);
                            }
                
                          ]
                      ],             
                ],
                
                'pager' => [
                    'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
                    'prevPageLabel' => '«',   // Set the label for the "previous" page button
                    'nextPageLabel' => '»',   // Set the label for the "next" page button
                    'firstPageLabel' => '<i class="fa fa-fast-backward" aria-hidden="true"></i>',   // Set the label for the "first" page button
                    'lastPageLabel' => '<i class="fa fa-fast-forward" aria-hidden="true"></i>',    // Set the label for the "last" page button
                    'nextPageCssClass' => 'next',    // Set CSS class for the "next" page button
                    'prevPageCssClass' => 'prev',    // Set CSS class for the "previous" page button
                    'firstPageCssClass' => 'first',    // Set CSS class for the "first" page button
                    'lastPageCssClass' => 'last',    // Set CSS class for the "last" page button
                    'maxButtonCount' => 15,    // Set maximum number of page buttons that can be displayed
                    ],
                 
            ]); ?>

    </div>

</div>

 
<?php
$Yii = 'Yii';
$js =<<<JS
    
     
JS;

$this->registerJs($js,\yii\web\View::POS_END);
?>