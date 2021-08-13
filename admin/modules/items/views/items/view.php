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

// Update Quantity
if(isset($_GET['force'])){
  $model->qtyForce;
}


//var_dump(Yii::$app->session->get('Rules')['name']);
?>
<style>
  table.detail-view th {
          width: 150px;
  }
  .item-a-hover:hover{
    background:#1796ab;
    color: #fff;
  }

  .bom-link-name {
    font-size:10px; 
    padding:10px;
  }

  .bom-image-tag{
    padding:20px 20px 20px 20px;
  }

  .search-control{
    max-width: 300px; 
  }

  @media (max-width: 767px) {
    .bom-image-tag{
      padding:20px 20px 0px 20px;
    }
  }

  @media (max-width: 375){
    #search-item {
      width:100px;
    }

    .search-control{
      max-width:150px !important;
    }
  }
      

  #item-source-render .minus-item{
    min-height: 100px;
    /* min-width: 80px; */
    
    font-family: 'roboto';
    border: 1px solid #ccc;
  }

  #item-source-render .minus-item .qty{
    font-size: 25px;              
    text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;
  }
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="row" ng-init="Title='<?=$this->title;?>'">
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
                
                <div class="row">             
                  <div  class="col-lg-12 col-md-10 col-sm-12">
                    
                    <?= Html::a(Html::img($model->myItems ? $model->myItems->picture : $model->picture, ['class'=>'img-responsive item-img']), "#toggle-member",
                    [
                      'class'         =>"btn ",
                      'data-toggle'   =>"collapse",
                      "aria-expanded" =>"false",
                      "aria-controls" =>"toggle-member"
                    ]); ?>
                    
                    <div class="row margin-top">
                        

                        <div class="col-xs-12">
                        <ul class="list-group list-group-unbordered">
                          <li class="list-group-item">
                            <b><i class="fas fa-code-branch" style="font-size:20px;"></i> <?=Yii::t('common','Fork')?></b> 
                            <a class="pull-right" ><span data-toggle="tooltip" title="" class="badge bg-info" data-original-title=" "><?=$model->fork;?></span></a>
                          </li>
                           
                        </ul>

                        <div class="panel panel-info">
                            <div class="panel-heading">
                              <h3 class="panel-title"><?=Yii::t('common','Real Inventory')?></h3>
                            </div>
                            <div class="panel-body  text-center"><h2 class="remaining-total"></h2></div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                              <h3 class="panel-title"><?=Yii::t('common','Creator')?></h3>
                            </div>
                            <div class="panel-body  text-center">                               
                        
                              <?php 
                                  $creator  = '['.$model->users->username.'] '.$model->users->profile->name;
                                  if (strpos(strtolower($model->create_by), 'created')) {
                                    $html = '<i class="fa fa-android fa-2x text-success"></i> AI By : ('. $creator. ')';
                                  }else{
                                    $html = '<i class="fa fa-male fa-2x text-danger"></i> Human ('. $creator. ')';
                                  }
                                  $html.= '<div>'.date('Y-m-d H:i',strtotime($model->date_added)).'</div>';
                                  echo $html;
                              ?>

                              <div class="btn-group">

                                <?=Html::a(Yii::t('common','Count'), ['view','id' => $model->id,'force' => 'true'],[
                                              'class' => 'btn btn-info-ew btn-xs',
                                              'title' => 'คำนวน จำนวนสินค้า'
                                            ])?>

                                
                                <button type="button" title="เพิ่มในรายงาน วางแผนการผลิต" class="btn btn-default btn-xs add-to-planing">
                                  <?=$model->reportDesign ? '<i class="fas fa-heart" style="color:#ff4263;" data-key="'.$model->reportDesign->id.'"></i>' : '<i class="fas fa-heart" data-key="0"></i>' ?> <?=Yii::t('common','Add to Planing')?>
                                </button>
                              </div>
                              <div>
                                <?=Html::a('<i class="fas fa-chart-bar"></i> '.Yii::t('common','See Report'), ['/Planning/planning/list'],[
                                  'class' => ' ',
                                  'title' => 'ดูรายงาน',
                                  'target' => '_blank'
                                ])?>
                              </div>
                          </div>
                        </div>
                      </div>
                    </div>     
                                  
                  </div>    
                </div>
                <div class="col-lg-12 col-md-5 hidden-xs mt-10" >
                    <div class="row">                            
                        <?= $MultiUpload->ImageRender($model) ?>                                            
                    </div>                      
                </div>               
            </div> 
            <div class="col-md-9 col-sm-8">
              <div class="row">   
                <div class="collapse" id="toggle-member" >                  
                  <div class="col-sm-12 hidden-xs text-right " >
                    <hr class="style14" />
                    <?= Html::a('<i class="fas fa-arrow-up"></i>', "#toggle-member",
                    [
                      'class'         => 'mr-10',
                      'style'         => "font-size:23px;",
                      'data-toggle'   => "collapse",
                      "aria-expanded" => "false",
                      "aria-controls" => "toggle-member"
                    ]); ?>                     
                  </div>
                  <div class="col-sm-3  hidden-xs  text-center">
                    <div class="bg-gray" style="margin-top: -50px; margin-bottom: -18px; padding: 35px 0px 40px 0px;">
                      <?php 
                        $GROUPS = [];
                        foreach ($model->memberOfBom as $key => $bom) {
                          $bomImg   = $bom->header 
                                      ? $bom->header->items 
                                        ? Html::img($bom->header->items->picture, 
                                          [
                                            'class' => 'img-responsive bom-image-tag',
                                            'alt'   => $bom->header->name,
                                            'title' => $bom->header->name
                                          ]) 
                                        : ''
                                      : '';
                          $bomName  = $bom->header 
                                      ? $bom->header->items                                          
                                        ? $bom->header->items->description_th                                      
                                        : null 
                                      : null;  

                          $itemId   = $bom->header 
                                      ? $bom->header->items                                          
                                        ? $bom->header->items->id                                            
                                        : null 
                                      : null; 

                          $GROUPS[] = $bom->header 
                                      ? (Object)[
                                          'img' => $bomImg,
                                          'name' => $bomName,
                                          'id'  => $itemId
                                        ]
                                      : null;                          
                        }    
                      ?>
                      <i class="fas fa-arrow-right fa-2x mt-10"></i>
                      <div class="mb-10"><?= Yii::t('common','Use with')?> <?= count($GROUPS); ?> <?= Yii::t('common','items')?></div>  
                    </div>                  
                  </div>
                  <div class="col-sm-9 col-xs-12 mb-10">       
                    <div class="row">
                      <?php                        
                        $html = '';
                        foreach ($GROUPS as $value) {
                            $html.= $value->id
                                    ? '<div class="col-sm-3 col-xs-6 mt-10">'
                                          .Html::a('<div class="bom-image">'.$value->img.'</div><div class="bom-link-name">'.$value->name.'</div>',
                                            ['view', 'id' => $value->id],
                                            [
                                              'class' => "img-thumbnail item-a-hover", 
                                              'style' => "height:200px;"
                                            ]).                                          
                                        '</div>'
                                    : null;
                        }
                      ?>
                      <?php 
                         if(count($GROUPS) > 0 ) {
                           echo $html;
                         }else {
                          if($model->ProductionBom==''){
                            echo Html::a('<i class="fas fa-cubes text-black"></i> '.Yii::t('common','Create Bom'),['/Manufacturing/prodbom/create',
                            'BomHeader[item]' => $model->id
                            ],['target' => '​_blank']);
                          }else {
                            $Bom = \common\models\BomHeader::findOne($model->ProductionBom);
                            if($Bom)
                              echo Html::a($Bom->code,['/Manufacturing/prodbom/view','id' => $model->ProductionBom]);
                            else
                              echo Html::a('[Error] Ai'.$model->ProductionBom,['/Manufacturing/prodbom/index']);
                          } 
                         }?>
                    </div>                    
                  </div> 
                  <div class="col-xs-12"><hr class="style14" /></div>
                </div>
              </div> 
              <?PHP if(Yii::$app->session->get('Rules')['name'] == 'Administrator' || Yii::$app->session->get('Rules')['name'] == 'Accounting' || Yii::$app->session->get('Rules')['name'] == 'Financial') { ?>
                <div class="row">
                  <div class="col-xs-6  col-md-3  text-center">
                  
                  <div class="panel panel-danger">
                      <div class="panel-heading">
                        <h3 class="panel-title">ต้นทุนซื้อ</h3>
                      </div>
                      <div class="panel-body">
                          <div style="position: absolute; color: rgba(222, 220, 220, 0.31); font-size:2vh;">AVG</div>   
                          <h3 
                            alt="<?=number_format($model->pricing->purprice,2); ?>" 
                            title="<?=number_format($model->pricing->purprice,2); ?>">
                            <?=number_format($model->pricing->purprice); ?>
                          </h3>
                      </div>
                      <div class="panel-footer bg-white">
                          <div class="row" style="margin-bottom: -10px; margin-top: -10px;">
                            <div class="col-xs-4 text-center"  style="height: 35px;">
                              <div style="position: absolute; color: rgba(222, 220, 220, 0.31); font-size: 11px; right: 2px;">MIN</div>   
                              <div style="margin-top: 7px; font-size: 16px;"><?=number_format($model->pricing->pminprice); ?></div>          
                            </div>
                            <div class="col-xs-4 text-center" style="border-left: 1px solid #ccc; height: 35px;">
                              <div style="position: absolute;  color: rgba(222, 220, 220, 0.31); font-size: 11px; right: 2px;">MAX</div>   
                              <div  style="margin-top: 7px; font-size: 16px;"><?=number_format($model->pricing->pmaxprice); ?></div>             
                            </div>
                            <div class="col-xs-4 text-center" style="border-left: 1px solid #ccc; height: 35px;">
                              <div style="position: absolute;  color: rgba(222, 220, 220, 0.31); font-size: 11px; right: 2px;">LAST</div>   
                              <div  style="margin-top: 7px; font-size: 16px;"><?=number_format($model->pricing->plastprice); ?></div>             
                            </div>
                          </div>
                        </div>
                  </div>
                  
                     
                  </div>
                  <div class="col-xs-6 col-md-3 text-center">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                          <h3 class="panel-title">ต้นทุนผลิต</h3>
                        </div>
                        <div class="panel-body">
                          <div style="position: absolute; color: rgba(222, 220, 220, 0.31); font-size:2vh;">Consumption</div>   
                          <h3 
                            title="<?=number_format($model->pricing->conCost,2); ?>" 
                            alt="<?=number_format($model->pricing->conCost,2); ?>">
                              <?=number_format($model->pricing->conCost); ?>
                          </h3>                                                                     
                        </div>
                        <div class="panel-footer bg-white">
                          <div class="row" style="margin-bottom: -10px; margin-top: -10px;">
                            <div class="col-xs-6 text-center"  style="height: 35px;">
                              <div style="position: absolute; color: rgba(222, 220, 220, 0.31); font-size: 11px; right: 4px;">PURCHASE</div>   
                              <div style="margin-top: 6px; font-size: 16px;"><?=number_format($model->pricing->purprice); ?></div>          
                            </div>
                            <div class="col-xs-6 text-center" style="border-left: 1px solid #ccc; height: 35px;">
                              <div style="position: absolute;  color: rgba(222, 220, 220, 0.31); font-size: 11px; right: 4px;">STANDARD</div>   
                              <div  style="margin-top: 6px; font-size: 16px;"><?=number_format($model->pricing->stdcost,2); ?></div>             
                            </div>
                             
                          </div>
                        </div>
                    </div>
                     
                  </div>
                  <div class="col-xs-6 col-md-3 text-center">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                          <h3 class="panel-title">ต้นทุนขาย</h3>
                        </div>
                        <div class="panel-body">
                          <div style="position: absolute; color: rgba(222, 220, 220, 0.31); font-size:2vh;">Fixed</div>   
                          <h3 
                            title="<?=number_format($model->pricing->saleCost,2); ?>" 
                            alt="<?=number_format($model->pricing->saleCost,2); ?>">
                              <?=number_format($model->pricing->saleCost); ?>
                          </h3> 
                        </div>
                        <div class="panel-footer bg-white">
                          <div class="row" style="margin-bottom: -10px; margin-top: -10px;">
                            <div class="col-xs-6 text-center"  style="height: 35px;">
                              <div style="position: absolute; color: rgba(222, 220, 220, 0.31); font-size: 11px; right: 4px;">Price</div>   
                              <div style="margin-top: 6px; font-size: 16px;"><?=number_format($model->CostGP,2); ?></div>          
                            </div>
                            <div class="col-xs-6 text-center" style="border-left: 1px solid #ccc; height: 35px;">
                              <div style="position: absolute;  color: rgba(222, 220, 220, 0.31); font-size: 11px; right: 4px;">Sale</div>   
                              <div  style="margin-top: 6px; font-size: 16px;"><?=number_format($model->sale_price,2); ?></div>             
                            </div>
                             
                          </div>
                        </div>
                    </div>
                     
                  </div>
                  <div class="col-xs-6 col-md-3 text-center">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                          <h3 class="panel-title">ราคาขาย</h3>
                        </div>
                        <div class="panel-body">
                          <div style="position: absolute; color: rgba(222, 220, 220, 0.31); font-size:2vh;">AVG</div>   
                          <h3 
                            title="<?=number_format($model->pricing->price,2); ?>" 
                            alt="<?=number_format($model->pricing->price,2); ?>">
                              <?=number_format($model->pricing->price); ?> 
                          </h3> 
                          <?php /* $model->sale_price > 0 
                            ? '<span class="text-red">'.Yii::t('common','Special price').' : '.($model->sale_price *1 ).'</span>' 
                            : null */ ?>
                        </div>
                        <div class="panel-footer bg-white">
                          <div class="row" style="margin-bottom: -10px; margin-top: -10px;">
                            <div class="col-xs-4 text-center"  style="height: 35px;">
                              <div style="position: absolute; color: rgba(222, 220, 220, 0.31); font-size: 11px; right: 4px;">MIN</div>   
                              <div style="margin-top: 6px; font-size: 16px;"><?=number_format($model->pricing->minprice); ?></div>          
                            </div>
                            <div class="col-xs-4 text-center" style="border-left: 1px solid #ccc; height: 35px;">
                              <div style="position: absolute;  color: rgba(222, 220, 220, 0.31); font-size: 11px; right: 4px;">MAX</div>   
                              <div  style="margin-top: 6px; font-size: 16px;"><?=number_format($model->pricing->maxprice); ?></div>             
                            </div>
                            <div class="col-xs-4 text-center" style="border-left: 1px solid #ccc; height: 35px;">
                              <div style="position: absolute;  color: rgba(222, 220, 220, 0.31); font-size: 11px; right: 4px;">LAST</div>   
                              <div  style="margin-top: 6px; font-size: 16px;"><?=number_format($model->pricing->lastprice); ?></div>             
                            </div>
                          </div>
                        </div>
                    </div>  
                </div>
                </div>    
              <?php } ?>                  
              <div class="panel <?=$model->Status == 1 ? 'panel-info' : 'panel-danger' ?>">
                <?php if($model->Status==0) {  ?>                
                <div style="position:absolute; top: 20%; left:35%;"><h1 class="text-red" style="transform: rotate(-20deg);"><?=Yii::t('common','Disable')?><h1></div>
                <?php } ?>   
                <div class="panel-heading" >
                  <h3 class="panel-title" ><i class="fas fa-binoculars text-aqua"></i> <?=Yii::t('common','Item information')?></h3>
                </div>
                 
                    <?= DetailView::widget([
                      'model' => $model,
                      'attributes' => [
                        [
                          'label' => Yii::t('common','Barcode'),
                          'format' => 'html',
                          'value' => function($model){
                              $html = '<div>'.$model->iteminfo->barcode.'</div>';
                              if($model->allBarcode != null){
                                  foreach ($model->allBarcode as $key => $ref) {
                                      $html.= $model->iteminfo->barcode == $ref->barcode ? '' : '<div>'.Html::a($ref->barcode,['/items/cross/index', 'SearchCross[item]' => $model->id],['class' => "text-gray"]).' <span class="pull-right">' .$ref->customer->name . '</span></div>';
                                  }
                              }
                              return $html;
                          }
                        ],
                        'barcode_for_box',
                        [
                          
                          'label' => Yii::t('common','Code'),
                          'format' => 'raw',
                          'value' => function($model){
                              $html = '<div>'.$model->iteminfo->code.'</div>';
                              if($model->allBarcode != null){
                                  foreach ($model->allBarcode as $key => $ref) {
                                      $html.= $model->iteminfo->code == $ref->item_no ? '' : '<div>'.Html::a($ref->item_no,['/items/cross/index', 'SearchCross[item]' => $model->id],['class' => "text-gray"]).' <span class="pull-right">' .$ref->customer->name . '</span></div>';
                                  }
                              }
                              return $html;                             
                          }
                        ],   
                        [
                          'label' => Yii::t('common','Product Name (en)'),
                          'format' => 'raw',
                          'value' => function($model){
                            //return $model->iteminfo->name_en;
                            $html = '<div>'.$model->iteminfo->name_en.'</div>';
                              if($model->allBarcode != null){
                                  foreach ($model->allBarcode as $key => $ref) {
                                      $html.= $model->iteminfo->name_en == $ref->description ? '' : '<div>'.Html::a($ref->description,['/items/cross/index', 'SearchCross[item]' => $model->id],['class' => "text-gray"]).' <span class="pull-right">' .$ref->customer->name . '</span></div>';
                                  }
                              }
                              return $html;  
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
                          'attribute' => 'name',
                          'format' => 'raw',
                          'contentOptions' => ['class' => 'sort-name-source'],
                          'value' => function($model){
                            return $model->name;
                          }
                        ],
                        'size',      
                        'detail',                                
                        [
                          'label'           => Yii::t('common','Remaining'), 
                          'format' => 'raw',
                          'value' => function($model){
                            $digit_stock    = Yii::$app->session->get('digit') ? Yii::$app->session->get('digit')->stock : 0;
                            //$inven          = number_format($model->liveInven,$digit_stock);
                            if(!$model->last_update_stock){    
                                $inven = $model->updateQty->model->last_stock * 1;
                            } 

                            $inven = $model->myItems ? $model->myItems->last_stock * 1 : $model->last_stock * 1;

                            $html = '<div class="row">';
                            $html.= '<div class="col-sm-6 col-xs-12 remaining-qty" data-val="'.$inven.'">
                                        <a href="index.php?WarehouseSearch[ItemId]='.base64_encode($model->id).'&r=warehousemoving%2Fwarehouse" target="_blank" class="mr-10 remaining">
                                        <span>'.number_format($inven,$digit_stock).'</span> '.$model->UnitOfMeasure. '
                                      </a>';

                            $html.= '</div>';   
                            $html.= '<div class="col-sm-6 col-xs-12 text-right">
                                      '.Html::a('<i class="fas fa-gavel"></i> '.Yii::t('common','Produce'),'#',['data-key' => $model->id, 'class' => 'mr-10 btn btn-warning', 'id' => 'craft-item']).'
                                      </div>';                          
                            $html.= '</div>';

                            return $html;

                          }
                        ],

                        [
                          'label' => Yii::t('common','Can Produce'),
                          'format' => 'raw', 
                          'contentOptions' => [
                            'class' => $model->hasbom  != null ? '' : 'hidden'
                          ],
                          'captionOptions' => [
                            'class' => $model->hasbom  != null ? '' : 'hidden',
                          ],                         
                          'value' => function($model){
                            //$inven  = number_format($model->invenByCache);   
                            $digit_stock    = Yii::$app->session->get('digit') ? Yii::$app->session->get('digit')->stock : 0;  

                            if(!$model->last_update_stock){    
                                $inven = $model->updateQty->model->last_possible;
                            }else{
                                $inven = $model->last_possible;
                            }

                            $html = '<div class="row produce-qty" data-val="'.$inven.'">';
                            $html.=   '<div class="col-xs-12">'.Html::a(number_format($inven,$digit_stock).' '.$model->UnitOfMeasure,['/Manufacturing/prodbom/view','id' => $model->hasbom ? $model->hasbom->id : ''],['target' => '_blank', 'class' => 'mr-10']).'</div>';
                            //$html.=   '<div class="col-xs-6 text-right">'.Html::a('<i class="fas fa-gavel"></i> '.Yii::t('common','Produce'),'#',['data-key' => $model->id, 'class' => 'mr-10 btn btn-warning-ew', 'id' => 'craft-item']).'</div>';
                            $html.= '</div>';

                            return $model->hasbom != null ? $html : '' ;
                            
                          }
                        ],

                        [
                          'label' => Yii::t('common','Quantity to reserved'),
                          'format' => 'raw',
                          // 'contentOptions' => [
                          //   'class' => $model->reserveInSaleLine? '' : 'hidden',
                          // ],
                          // 'captionOptions' => [
                          //   'class' => $model->reserveInSaleLine? '' : 'hidden',
                          // ],
                          'value' => function($model){     
                          
                            $reserve = $model->reserveInSaleLine ? $model->reserveInSaleLine : 0;
                            
                            $html = '<a href="?r=SaleOrders/order/reserved&item='.$model->id.'" target="_blank" class="text-yellow reserve-qty" data-val="'.$reserve.'">';
                            $html.=    ' ' . number_format($reserve) . ' '.$model->UnitOfMeasure;
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


                        
                        // [
                        //   'label' => Yii::t('common','Sale Price'),
                        //   'format' => 'raw',
                        //   'value' => function($model){
                        //     return number_format($model->pricing->price * 1, 2);
                        //   }
                        // ],
                         
                        // [
                        //   'label' => Yii::t('common','Cost'),
                        //   'format' => 'raw',
                        //   'value' => function($model){
                        //     return  $model->iteminfo->cost * 1 ;
                        //   }
                        // ] ,
                        // [
                        //   'label' => Yii::t('common','Standard Cost'),
                        //   'format' => 'raw',
                        //   'value' => function($model){
                        //     return Yii::$app->session->get('Rules')['name'] == 'Administrator' || Yii::$app->session->get('Rules')['name'] == 'Accounting' ? number_format($model->iteminfo->stdcost * 1, 2) : 0;
                        //   }
                        // ],
                        
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
                        //'TypeOfProduct',
                        [
                          'label' => Yii::t('common','Type of product'),
                          'value' => function($model){
                            if($model->TypeOfProduct=='0'){
                              return 'Vat';
                            }else {
                              return 'Novat';
                            }
                          }
                        ],
                        //'CostingMethod',
                        
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
                        /*                    
                        [
                          'label' => Yii::t('common','Creator'),
                          'format' => 'raw',
                          'value' => function($model){

                            $creator  = '['.$model->users->username.'] '.$model->users->profile->name;

                            if (strpos(strtolower($model->create_by), 'created')) {
                              $html = '<i class="fa fa-android fa-2x text-success"></i> AI By : ('. $creator. ')';
                            }else{
                              $html = '<i class="fa fa-male fa-2x text-danger"></i> Human ('. $creator. ')';
                            }

                            $html.= '<div>'.date('Y-m-d H:i',strtotime($model->date_added)).'</div>';
                            return $html;

                          }
                        ], */
                        [
                          'label' => Yii::t('common','Can sale online'),
                          'format' => 'raw',
                          'value' => function($model){
                            return ($model->cansale == 1)? '<i class="fas fa-toggle-on text-success"></i> '.Yii::t('common','YES') : '<i class="fas fa-toggle-off"></i> '.Yii::t('common','NO');

                          }
                        ],  
                        [
                          'attribute' => 'color',
                          'label' => Yii::t('common','Color of graph'),
                          'format'  => 'html',
                          //'contentOptions' => ['style' => 'background-color:'.$model->color.''],
                          'value' => function($model){
                            return '<div class="pull-left" style="background-color:'.$model->color.'; height:20px; width:20px; margin-right:10px;"></div> '.$model->color;
                          }
                        ],  
                       
                        

                        [
                          'format' => 'raw',
                          'label' => Yii::t('common','Sales Summary'),
                          'value' => function($model){

                            return Html::a('<i class="fa fa-line-chart  " aria-hidden="true"></i> '.Yii::t('common','Sales Summary'),
                            ['/SaleOrders/order/identify','OrderSearch[item]' => $model->id,],['class' => 'link', 'target' => '_blink']);

                          }
                        ], 

                        

                        [
                          'format' => 'raw',
                          'label' => Yii::t('common','Sales Summary'),
                          'value' => function($model){

                            return Html::a('<i class="fa fa-line-chart"></i> '.Yii::t('common','Sales Summary Invoice'),
                            ['/accounting/inv-line','InvLineSearch[item]' => $model->id,],['class' => 'link', 'target' => '_blink']);

                          }
                        ],   
                        
                        [
                          'attributes' => 'ProductionBom',
                          'format' => 'raw',
                          'label' => Yii::t('common','Production Bom'),
                          'value' => function($model){
                            if($model->ProductionBom==''){
                              //return '<a href="Manufacturing%2Fprodbom%2Fcreate" class="btn btn-info">'.Yii::t('common','Create Bom').'</a>';
                              return Html::a('<i class="fas fa-cubes text-black"></i> '.Yii::t('common','Create Bom'),['/Manufacturing/prodbom/create',
                              'BomHeader[item]' => $model->id
                              ],['target' => '​_blank']);
                            }else {
                              $Bom = \common\models\BomHeader::findOne($model->ProductionBom);
                              if($Bom)
                              return Html::a($Bom->code,['/Manufacturing/prodbom/view','id' => $model->ProductionBom]);
                              else
                              return Html::a('[Error] Ai'.$model->ProductionBom,['/Manufacturing/prodbom/index']);

                            }
                          }
                        ], 
                        [
                          'label' => Yii::t('common','Interesting'),
                          'format' => 'html',
                          'value' => function($model){
                            if($model->interesting=='Enable'){
                              $star = '<i class="fa fa-star" aria-hidden="true" style="color: #f4d341;  "></i>';
                            }else {
                              $star = NULL;
                            }
                            return Yii::t('common',$model->interesting). ' ' .$star;
                          }

                        ],  
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
                          'label' => Yii::t('common','Product Group'),
                          'format' => 'html',
                          'value' => function($model){

                            return $model->getGroup();

                          }

                        ],

                        [
                          'label' => Yii::t('common','Replenishment'),
                          'format' => 'html',
                          'value' => function($model){
                            if($model->replenishment == 'Produce'){
                              return Yii::t('common','Produce.In');
                            }else {
                              return Yii::t('common','Purchase.In');
                            }
                            

                          }

                        ],          

                      ],
                    ]) ?>
              </div>
              <?php if($model->owner): ?>
              <div class="panel panel-success hidden">
                <div class="panel-body" style="background:#f9f9f9;">
                  <?php // $this->render('_chart_sale',['model' => $model]) ?>
                </div>
              </div>
              
              <div class="panel panel-danger hidden-xs hidden-sm hidden">
                <div class="panel-body">
                  <?php // $this->render('_chart',['model' => $model]) ?>
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

 
<div class="modal fade modal-full" id="modal-craft-item">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=Yii::t('common','Produce')?></h4>
      </div>
      <div class="modal-body" style="background-color: rgba(41, 46, 48, 1);margin-top: -5px; padding-bottom:10px; padding-top: 15px; color:#fff;">
         
        <div class="row">
          <div class="col-sm-6 mb-10">
            <div class=" " id="item-source-render"></div>
          </div>
          <div class="col-sm-2  text-center align-middle mt-10 mb-10">    
            <span class="hidden-xs">         
              <a href="#" class="btn btn-default-ew make-craft-item" style="margin-top:200px; color:#fff;"><i class="fas fa-arrow-right fa-4x" ></i></a>
            </span> 

            <span class="hidden-sm hidden-md hidden-lg">         
              <a href="#" class="btn btn-default-ew make-craft-item mt-10" style="color:#fff;"><i class="fas fa-arrow-down fa-4x btn"></i></a>
            </span> 
          </div>
          <div class="col-sm-4 text-center">
            
            <div class="input-group" style="margin: 10px auto 10px auto;">
              <span class="input-group-btn">
                <button type="button" class="btn btn-number btn-default" data-type="minus" data-field="craft-qty" data-rippleria="">
                  <span class="glyphicon glyphicon-minus"></span>
                </button>
              </span>
              <input type="number" step="any" class="form-control input-number text-center" name="craft-qty" value="1"/>
              <span class="input-group-btn">
                <button type="button" class="btn btn-number btn-default" data-type="plus" data-field="craft-qty" data-rippleria="">
                  <span class="glyphicon glyphicon-plus"></span>
                </button>
              </span>
            </div>
            
            <img src="" class="img-item-destination img-responsive img-thumbnail" style="width: 100%;" />
            <div class="sort-name" style="position: absolute; left: 30%;top: 60px; color: #000;"><?=$model->name?: $model->Description;?></div>
            
          </div>
        </div>

        <div class="row" >
          <div class="col-xs-12 mt-10" style="min-height:150px;">

          <div class="panel panel-warning">
              <div class="panel-heading">
                <h3 class="panel-title"><?=Yii::t('common','Items')?></h3>
                <div class="pull-right search-control" style="margin-top: -25px; margin-right: -13px;">
                    
                    <div class="input-group">
                      <input type="text" class="form-control" id="search-item" placeholder="Search">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-default"><i class="fa fa-search"></i></button>
                      </span>
                    </div>
                   
                </div>
              </div>
              <div class="panel-body bg-gray">
                 <div class="row render-item-search"></div>
              </div>
          </div>
          
            
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
        <a href="#" class="btn btn-warning make-craft-item"><i class="fas fa-cogs"></i> <?=Yii::t('common','Produce')?></a>
      </div>
    </div>
  </div>
</div>
<div class="loading-div" style="position:fixed; top:30%; left:50%; z-index:2000;">
  <i class="fas fa-sync fa-spin fa-4x text-white" ></i>
</div>



 
<div class="modal fade" id="modal-showitem" style="z-index:1050">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">ITEM</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-6">
            <div class="name hidden"></div>
              <img src="" class="img-responsive"/>
            <div class="fullname mt-5"></div>
          </div>
          <div class="col-xs-6">
              <label for="item-name" class="mr-5"><?=Yii::t('common','Name')?></label>
              <input type="text" id="item-name" name="alias" class="form-control" />
              <div class="mt-5 item-description"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
      </div>
    </div>
  </div>
</div>

<?php
$Yii = "Yii"; 
$js =<<<JS
  


  const reCalculate = (obj, callback) =>{
        fetch("?r=items/ajax/recalculate", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {          
            callback(res);
        })
        .catch(error => {
            console.log(error);
        });
    }


  const calInven = () => {
 
    let id  = parseInt("{$model->id}");
               
    reCalculate({id:id}, res =>{
      
      let stock = res.raws.stock;

      $('body').find('.remaining-qty').attr('data-val',stock).find('span').html(number_format(stock));

      let inven   = 0;
      let reserve = 0;

            inven+= stock;
            reserve+= parseFloat($('body').find('a.reserve-qty').attr('data-val'));

      let total   = parseFloat(inven - reserve);

      let html    = `<div class="` + (total < 0 ? 'text-red' : 'text-aqua') + `">` + number_format(total) + `</div>`;

      $('body').find('.remaining-total').html(html);

    });
  }

  $(document).ready(function(){
    $('.myclass').mousedown(function(event) {
      switch (event.which) {
          case 1:
              alert('Left mouse button is pressed');
              break;
          case 2:
              alert('Middle mouse button is pressed');
              break;
          case 3:
              alert('Right mouse button is pressed');
              break;
          default:
              alert('Nothing');
        }
    });

    calInven();
  });

  
  const getItemCraft = (obj, callback) => {
    $('.loading-div').show();
    fetch("?r=items/item-craft/get-item-craft", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
      })
      .then(res => res.json())
      .then(response => {            
          callback(response);    
          $('.loading-div').hide();        
      })
      .catch(error => {
          console.log(error);
      });
  }

  const addToTable = (obj, callback) => {
    $('.loading-div').show();
    fetch("?r=items/item-craft/add-item-to-bom-table", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {            
            callback(response); 
            $('.loading-div').hide();           
        })
        .catch(error => {
            console.log(error);
        });
  }

  const minusFoTable = (obj, callback) => {
    $('.loading-div').show();
    fetch("?r=items/item-craft/minus-from-table", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
    })
    .then(res => res.json())
    .then(response => {            
        callback(response);   
        $('.loading-div').hide();         
    })
    .catch(error => {
        console.log(error);
    });
  }

  const craftItem = (obj, callback) => {
    $('.loading-div').show();
    fetch("?r=items/item-craft/carft-item", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
    })
    .then(res => res.json())
    .then(response => {            
        callback(response);      
        $('.loading-div').hide();      
    })
    .catch(error => {
        console.log(error);
    });
  }

  const renderBomTable = (data, callback) => {
    let tbody = ``;

    var i;
    var x;

    data.map((model,i) => {
      tbody+= `
          
        <div class="col-xs-3 ` +( model.id ? 'pointer' : ' ')+ ` minus-item" 
              data-img="` + model.img + `" 
              data-key="` + model.id + `" 
              data-item="` + model.item + `"
              data-name="`+ model.name +`"
              data-nameTh="` + model.nameTh + `"
              data-code="`+ model.code +`"
              data-alias="`+ (model.alias ? model.alias : ' ') + `"
              style="position: relative; background: url('`+ model.img +`'); background-repeat: no-repeat; background-size: auto 100%; background-position: center;">
          <div style="color: #404040; position: absolute; right: 6px;" >`+ (i+1) +`</div>
          <div style="margin: 0px -10px 0px 10px;" class="qty">`+ model.qty + `</div>
          <div style="position: absolute; bottom:6px; left: 0px; padding: 0px 2px 0px 2px;background: rgba(204, 204, 204, 0.47); }" class="name">`+ (model.alias ? model.alias : ' ') + `</div>
          <div class="` + (model.id ? '' : 'hidden') + `" style="position:absolute; bottom:5px; right:5px; color: #696969;"><span class="stock">`+ model.qty + `</span> / <span class="remain">`+ model.stock + `</span></div>
        </div>
          
      `;
    })

    // for (x = 0; x < 5; x++) {
    //   tbody+= `<tr>`;

    //   for (i = 0; i < 4; i++) {
        
    //     let id  = (data[i] != undefined || data[i] != null)  
    //                 ? data[i]['id']
    //                 : '';
    //     let qty = (data[i] != undefined || data[i] != null)  
    //                 ? data[i]['qty']
    //                 : '';
    //     let img = (data[i] != undefined || data[i] != null)  
    //                 ? data[i]['img']
    //                 : '';
        
    //     tbody+= ` 
    //               <td class="text-right pointer minus-item" data-key="` + id + `" style="position: relative; background: url('`+ img +`'); background-repeat: no-repeat; background-size: 100px auto;">
    //                 <div style="margin: 2px 2px 0px 0px;">`+ qty + `</div>
    //               </td>
                  
    //             `;
    //   }

    //   tbody+= `</tr>`;

    // }
   

    callback(tbody);
  }


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
      });    
  });

  // localStorage.setItem('produce',JSON.stringify(produce));
  // localStorage.removeItem("reserve-order");
  // let data = localStorage.getItem("reserve-new-sale-line")
  //         ? JSON.parse(localStorage.getItem("reserve-new-sale-line"))
  //         : [];

  $(document).ready(function(){   
    $('.loading-div').hide();

    setTimeout(() => {
      
      let inven   = 0;
      let reserve = 0;

          inven+= parseFloat($('body').find('.remaining-qty').attr('data-val'));
          reserve+= parseFloat($('body').find('a.reserve-qty').attr('data-val'));

      let total   = parseFloat(inven - reserve);

      let html    = `<div class="` + (total < 0 ? 'text-red' : 'text-aqua') + `">` + number_format(total) + `</div>`;

      $('body').find('.remaining-total').html(html);
      
    }, 1000);
  });

  $('#modal-craft-item').on('shown.bs.modal', function() {
      
  });      
  
  
  $('body').on('click', 'a#craft-item', function(){
 
    let id    = parseInt("{$model->id}");
    $('#modal-craft-item').modal('show');
    $('#modal-craft-item .img-item-destination').attr('src', $('.item-img').attr('src'));


    let data = localStorage.getItem('item:'+id)
          ? JSON.parse(localStorage.getItem('item:'+id))
          : [];

    if(data.length > 0){
      renderBomTable(data, html => {
        $('#item-source-render').html(html);
      });
    }else{
      getItemCraft({id:id}, res => {
        localStorage.setItem('item:'+id,JSON.stringify(res));
        renderBomTable(res.raws, html => {
          $('#item-source-render').html(html);
        });
      });
    }
    
  });


  $('body').on('click', '.add-to-table', function(){
    let id      = parseInt($(this).attr('data-key'));
    let source  = parseInt("{$model->id}");

    
    addToTable({id:id, source:source}, res => {
      if(res.status===200){
        // render table
        renderBomTable(res.raws, html => {
          $('#item-source-render').html(html);

        });

      }else{
        $.notify({
          // options
          icon: "fas fa-box-open",
          message: res.message
        },{
            // settings
            placement: {
              from: "top",
              align: "center"
            },
            type: "error",
            delay: 4000,
            z_index: 3000
        });
      }
    });
     

  });
 

  

  $('body').on('mousedown', '.minus-item', function(event){
    let id      = parseInt($(this).attr('data-key'));
    let img     = $(this).attr('data-img');
    let code    = $(this).attr('data-code');
    let name    = $(this).attr('data-alias');
    let fullname= $(this).attr('data-name');
    let nameTh  = $(this).attr('data-nameTh');
    let itemId  = parseInt($(this).attr('data-item'));
    
    switch (event.which) {
          case 1:
             // alert('Left mouse button is pressed');  
              if(id > 0){      
                $('#modal-showitem').modal('show').attr('data-key', itemId);
                $('body').find('#modal-showitem .modal-title').html(`<a href="?r=items%2Fitems%2Fview&id=`+itemId+`" target="_blank">` +code + `</a>`);
                $('body').find('#modal-showitem div.name').html(name);
                $('body').find('#modal-showitem div.fullname').html(fullname);
                $('body').find('#modal-showitem img').attr('src',img);
                $('body').find('#modal-showitem input[name="alias"]').val(name);
                $('body').find('#modal-showitem div.item-description').html(nameTh);
              }
              break;
          case 2:
             //alert('Middle mouse button is pressed');
              break;
          case 3:
                    
              event.preventDefault();
              
              if(id > 0){

                if (confirm('{$Yii::t("common","Delete")} -1 ?')) {
                  minusFoTable({id:id}, res => {
                    if(res.status===200){
                      // render table
                      renderBomTable(res.raws, html => {
                        $('#item-source-render').html(html);
                      });

                    }else{
                      $.notify({
                        // options
                        icon: "fas fa-box-open",
                        message: res.message
                      },{
                          // settings
                          placement: {
                            from: "top",
                            align: "center"
                          },
                          type: "error",
                          delay: 4000,
                          z_index: 3000
                      }); 

                    }

                  });
                }
              }
            return false;
            break;
          default:
              alert('Nothing');
        }
     

  });

  const updateAlias = (obj, callback) => {
    $('.loading-div').show();
    fetch("?r=items/items/update-alias", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
    })
    .then(res => res.json())
    .then(response => {            
        callback(response);     
    })
    .catch(error => {
        console.log(error);
    });
  }

  $('body').on('change','input[name="alias"]', function(){
      let id  = $(this).closest('#modal-showitem').attr('data-key');
      let val = $(this).val();
      let el  = $('body').find('div[data-item="'+id+'"]');

      updateAlias({id:id, val:val.trim(), field:'name'},res=> {
        if(res.status===200){
          $('#modal-showitem').modal('hide');
          $('.loading-div').hide();  
          
          el.find('div.name').html(val.trim());
          el.attr('data-alias', val.trim());
        }
      })
  })


  $('body').on('click', 'a.make-craft-item', function(){
    let source  = parseInt("{$model->id}");
    let qty     = parseInt($('body').find('input[name="craft-qty"]').val());
 
    let bom     = [];

    $('#item-source-render .minus-item').each((key, el) => {
        bom.push(parseInt($(el).attr('data-key')));
    })

    
    if(bom.length > 0 && qty != 0){
  
      if (confirm('Confirm ?')) {
        
        craftItem({source:source, qty:qty}, res => {

          if(res.status===200){
            $('a.remaining span').html(res.stock)
            $('#modal-craft-item').modal('hide');
          }else{

            $.notify({
              // options
              icon: "fas fa-box-open",
              message: res.message
            },{
                // settings
                placement: {
                  from: "top",
                  align: "center"
                },
                type: "error",
                delay: 4000,
                z_index: 3000
            }); 
            
          }
          
        });
      }

    }else{

      alert('Please add member of item. Or Quantity must not be 0.');
      
    }
      
  });

  const searchItem = (search, callback) => {
    $('#modal-craft-item .render-item-search').html('<i class="fas fa-sync fa-spin text-center"></i>');
    setTimeout(() => {
      
      fetch("?r=items/ajax/search-items-json", {
          method: "POST",
          body: JSON.stringify({search:search}),
          headers: {
              "Content-Type": "application/json",
              "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
          },
      })
      .then(res => res.json())
      .then(response => {            
          callback(response);            
      })
      .catch(error => {
          console.log(error);
      });
    }, 500);
  }

  let renderTableSeach = (data) => {
    var html = ``;

    data.map(model => {
      html+= `<div class="col-lg-1 col-md-2 col-sm-3 col-xs-4 mt-5 font-roboto">
                <img src="` + model.img +`" class="img-responsive img-thumbnail add-to-table pointer" data-key="` + model.id +`" title="` + model.name +`"   />
                <div style="position:absolute; top:5px; right:26px; font-size:18px;">
                  <span style="padding: 0px 5px 0px 5px; background: #d7dad9b8;">` + model.stock + `</span>
                </div>
                <div style="position:absolute; bottom:5px; left:26px; font-size:18px;">
                  <span style="padding: 0px 5px 0px 5px; background: #d7dad9b8;">` + model.alias + `</span>
                </div>
              </div>`;
    });
      
    $('body').find(".render-item-search").html(html);
  }
  
  $('body').on('change', 'input#search-item', function(){
    let search = $(this).val();

    searchItem(search, res => {
      renderTableSeach(res.raws);
    });
    
  });

  $('body').on('keypress', 'input#search-item', function(e){
    let search = $(this).val();
    if (e.which == 13) {
      searchItem(search, res => {
        renderTableSeach(res.raws);
      });
    }

  });


  const calulateQty = (qty) => {
 
    $('body').find('#item-source-render .minus-item').each((key, el) => {
        let myQty   = parseInt($(el).find('.qty').text());
        let remain  = parseInt($(el).find('.remain').text());

        let total   = qty * myQty;
        let img     = $(el).attr('data-img');

        if(total > remain){
          $(el).find('.stock').attr('style', 'color:red;').html(total);
          $(el).attr('style',`position: relative; background: url(` +img + `); background-repeat: no-repeat; background-size: auto 100%; background-color: rgba(204, 204, 204, 0.3); background-position: center;`);
        }else{
          $(el).find('.stock').attr('style', 'color:#50dfff;').html(total);
          $(el).attr('style',`position: relative; background: url(` +img + `); background-repeat: no-repeat; background-size: auto 100%; background-position: center;`);
        }

        
    })
  }

  $('body').on('change', 'input[name="craft-qty"]', function(){
    let qty     = parseInt($(this).val());
    calulateQty(qty);
  })

  $('body').on('click', 'button[data-type="plus"]', function(){
    let qty     = parseInt($('input[name="craft-qty"]').val());
    let newQty  = parseInt(qty) + 1;
    $('input[name="craft-qty"]').val(newQty);
    
    calulateQty(newQty);
    
  })

  $('body').on('click', 'button[data-type="minus"]', function(){
    let qty     = parseInt($('input[name="craft-qty"]').val());
    let newQty  = parseInt(qty) - 1;
    $('input[name="craft-qty"]').val(newQty);
    
    calulateQty(newQty);
    
  })

    
  $('body').on('click', '.add-to-planing', function(){
      let el = $(this);
      let id = parseInt('{$model->id}');
      fetch("?r=Planning/planning/add-to-list", {
          method: "POST",
          body: JSON.stringify({id:id}),
          headers: {
              "Content-Type": "application/json",
              "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
          }
      })
      .then(res => res.json())
      .then(res => {  
        if(res.actions == 0){
          el.find('.fas').attr('style',' ');
        }else{          
          el.find('.fas').attr('style','color:#ff4263;');
        }   
       
      })
      .catch(error => {
          console.log(error);        
      });
    
  })
JS;
$this->registerJS($js);
?>
