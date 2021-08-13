<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


use admin\modules\items\models\MultipleUploadForm;
$MultiUpload = new MultipleUploadForm();
/* @var $this yii\web\View */
/* @var $model common\models\Items */
use common\models\WarehouseMoving;

$this->title = $model->master_code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//var_dump(Yii::$app->session->get('Rules')['name']);
?>
<style>
table.detail-view th {
        width: 150px;
}
</style>
<div class=" " ng-init="Title='<?=$this->title;?>'">
<div class="row">
    <div class="col-sm-12">
        <div class="items-view">
            <p>
              <div class="row">
              <?php if(Yii::$app->session->get('Rules')['rules_id']==1): ?>
                  <div class="col-sm-12">
                      <div class="col-sm-12">
                      <div class="text-right">
                          <?= Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                              'class' => 'btn btn-danger-ew',
                              'data' => [
                                  'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                  'method' => 'post',
                              ],
                          ]) ?>
                      </div>
                      </div>
                  </div>
              <?php endif; ?>
              </div>
            </p>

            <div class="col-md-3 col-sm-4">
                <div class="col-md-2 hidden-sm hidden-xs" >
                    <div class="row"> 
                        <?= $MultiUpload->ImageRender($model) ?>
                    </div>
                </div>
                <div class="row"> 
                  <div  class="col-md-10 col-sm-12">
                    <?=Html::img($model->getPicture(), ['class'=>'img-thumbnail']); ?>
                    <div class="row margin-top">
                        <div class="col-md-6 col-sm-12 col-xs-6">
                          <i class="fas fa-code-branch" style="font-size:20px;"></i> <?=Yii::t('common','Fork')?>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-6 text-right"> 
                          <span data-toggle="tooltip" title="" class="badge bg-info" data-original-title="3 New Messages"><?=$model->fork;?></span>
                        </div>
                    </div>                   
                  </div>
                </div>
            </div>

            <div class="col-md-9 col-sm-8">                   
              <div class="panel panel-info">
                <div class="panel-heading" >
                  <h3 class="panel-title" ><i class="fas fa-binoculars text-aqua"></i> <?=Yii::t('common','Item information')?></h3>
                </div>                 
                    <?= DetailView::widget([
                      'model' => $model,
                      'attributes' => [
                        'barcode',
                        'barcode_for_box',
                        [
                          
                          'label' => Yii::t('common','Code'),
                          'format' => 'raw',
                          'value' => function($model){
                            return $model->iteminfo->code;
                          }
                        ],   
                        [
                          'label' => Yii::t('common','Product Name (en)'),
                          'format' => 'raw',
                          'value' => function($model){
                            return $model->iteminfo->name_en;
                          }
                        ],  
                        [
                          
                          'label' => Yii::t('common','Product Name (th)'),
                          'format' => 'raw',
                          'value' => function($model){
                            return $model->iteminfo->name;
                          }
                        ],                                        
                        [
                          'label' => Yii::t('common','Remaining'),
                          'format' => 'raw',
                          'value' => function($model){
                            $digit_stock    = Yii::$app->session->get('digit') ? Yii::$app->session->get('digit')->stock : 0;
                            $inven = number_format($model->liveInven,$digit_stock);
                            $html = '<a href="index.php?WarehouseSearch[ItemId]='.base64_encode($model->id).'&r=warehousemoving%2Fwarehouse" target="_blank">';
                            $html.=     $inven.' '.$model->UnitOfMeasure;
                            $html.= '</a>';
                            return $html;
                          }
                        ],

                        [
                          'label' => Yii::t('common','Quantity to reserved'),
                          'format' => 'raw',
                          'contentOptions' => [
                            'class' => $model->reserveInSaleLine? '' : 'hidden',
                          ],
                          'captionOptions' => [
                            'class' => $model->reserveInSaleLine? '' : 'hidden',
                          ],
                          'value' => function($model){           
                            
                            $html = '<a href="?r=SaleOrders/order/reserved&item='.$model->id.'" target="_blank" class="text-yellow" >';
                            $html.=    ' ' . $model->reserveInSaleLine . ' '.$model->UnitOfMeasure;
                            $html.= '</a>';
                            
                            return $html;
                          }
                        ],

                        [
                          'format' => 'raw',
                          'label' => Yii::t('common','Purchase Order Line List'),
                          'value' => function($model){

                            return Html::a('<i class="fas fa-ship"></i> '.Yii::t('common','Purchase Order Line List'),
                            ['/Purchase/purchase-line/list','LineListSearch[item]' => $model->id,],['class' => 'link', 'target' => '_blink']);

                          }
                        ], 

                        [
                          'format' => 'raw',
                          'label' => Yii::t('common','Sale Order Line List'),
                          'value' => function($model){

                            return Html::a('<i class="fas fa-shipping-fast"></i> '.Yii::t('common','Sale Order Line List'),
                            ['/SaleOrders/order/line-list','LineListSearch[item]' => $model->id,],['class' => 'link', 'target' => '_blink']);

                          }
                        ], 

                          
                        [
                          'label' => Yii::t('common','Store-Locations'),
                          'format' => 'raw',
                          'value' => function($model){
                            return '<a class="btn btn-primary-ew btn-flat item-inventory" data-key="'.$model->id.'" href="#item-inventory" data-toggle="modal">
                                      <i class="fab fa-windows"></i> '.Yii::t('common','Store-Locations').'
                                    </a>';
                          }
                        ], 
                        
                        [
                          'label' => Yii::t('common','Sale Price'),
                          'format' => 'raw',
                          'value' => function($model){
                            return number_format($model->pricing->price,2);
                          }
                        ],
                        
                       
                        
                      ],
                    ]) ?>
                   
              </div>

              <div class="panel panel-default">
                <div class="panel-heading" >
                  <h3 class="panel-title" ><i class="fab fa-fort-awesome text-orange"></i> <?=Yii::t('common','Options')?></h3>
                </div>                
                <?= DetailView::widget([
                      'model' => $model,
                      'attributes' => [   
                        'alias',                       
                         
                        [
                          'label' => Yii::t('common','Can sale'),
                          'format' => 'raw',
                          'value' => function($model){
                            return ($model->cansale == 1)? '<i class="fas fa-toggle-on text-success"></i> '.Yii::t('common','YES') : '<i class="fas fa-toggle-off"></i> '.Yii::t('common','NO');

                          }
                        ],  
                       
                        [
                          //'attributes' => 'ProductionBom',
                          'format' => 'raw',
                          'label' => Yii::t('common','Sales Summary'),
                          'value' => function($model){

                            return Html::a('<i class="fa fa-line-chart"></i> '.Yii::t('common','Sales Summary'),
                            ['/accounting/inv-line','InvLineSearch[item]' => $model->id,],['class' => 'link', 'target' => '_blink']);

                          }
                        ],   
                          
                        
                        [
                          'label' => Yii::t('common','Product Group'),
                          'format' => 'html',
                          'value' => function($model){

                            return $model->getGroup();

                          }

                        ],

                        'itemGroup.Description',
                        [
                          'label' => Yii::t('common','Item Set'),
                          'value' => function($model){
                            return $model->itemSet['name'];
                          }
                        ],

                      ],
                    ]) ?>
              </div>
              
              <?php if($model->owner): ?>
                <div class="panel panel-success">
                  <div class="panel-body" style="background:#f9f9f9;">
                    <?= $this->render('_chart_sale',['model' => $model]) ?>
                  </div>
                </div>

                <div class="panel panel-danger hidden-xs hidden-sm">
                  <div class="panel-body">
                    <?= $this->render('_chart',['model' => $model]) ?>
                  </div>
                </div>
              <?php endif; ?>

            </div>
        </div>
    </div>
</div>
</div>


<div class="modal fade" id="item-inventory">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fas fa-home"></i> <?=Yii::t('common','Warehouse')?></h4>
      </div>
      <div class="modal-body">
        <div class="locations"  style="display:none;"></div>
        <div class="summary-count">
              <div class="row">
                <div class="col-sm-12 text-right"><h4><?=Yii::t('common','Inventory')?></h4>  <h1><span class="inven"></span></h1></div>
              </div> 
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fas fa-power-off"></i> <?=Yii::t('common','Close')?></button>         
      </div>
    </div>
  </div>
</div>
<?php
$js =<<<JS
  
  $('body').on('click','a.item-inventory',function(){
      var modal = $('#item-inventory');
      var body  = modal.find('.modal-body').find('.locations');
      var id    = $(this).data('key');           
      $.ajax({
        url:'index.php?r=warehousemoving/inventory/inven-by-location',
        type:'GET',
        data:{id:id},
        dataType:'JSON',
        success:function(response){          
          body.html(response.html);
          body.fadeIn('slow');
          modal.find('.modal-body').find('.inven').html(number_format(response.inven));
        }
      })    
  })

  $('#item-inventory').on('shown.bs.modal', function() {
      
  })             
JS;
$this->registerJS($js);
?>
