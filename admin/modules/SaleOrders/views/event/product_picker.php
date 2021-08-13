<?php

use \admin\modules\SaleOrders\models\PosFilter;

$filter = new PosFilter();
$product = $filter->showItems(16);




?>

<style>
    .widget-user .widget-user-username{
        font-size:15px;
    }

    .widget-user  .box-footer{
        /* font-size:10px; */
    }

    .description-block>.description-header{
        font-size:18px;
    }

    .widget-user .widget-user-image>img {
        border: 0;
    }
    .widget-user .widget-user-image {
      z-index: 2 !important;
    }
 
    
</style>

<div class="row">
  <div class="col-sm-offset-6">
    <div class=" ">
      <div class="col-xs-12">
        <div class="form-group field-item-search">
          <label class="control-label" for="item-search">ค้นหา</label>
          <div class="input-group">
            <input type="text" id="item-search" class="form-control" name="search" ng-keyup="$event.keyCode == 13 ? productSearch($event) : null">
            <span class="input-group-btn">
              <button type="button" name="search" ng-click="productSearch($event)" class="btn btn-flat btn-default-ew"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
      </div>                
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <div class="render-item-list">
      <?=$filter->renderItemList($product);?>
    </div>    
  </div>
</div>
<?php


// $html = '<div class="row">';
// foreach ($product as $key => $item) {

// //-----Background Color---
// $bg_style   = ($item->color=='')? ' ': 'background-color:'.$item->color.';';
// $bg_class   = ($item->color!='')? ' ': 'bg-aqua-active';
// //---/.Background Color---
 

// $html.=<<<HTML


//     <div class="col-lg-3 col-md-4">
//       <!-- Widget: user widget style 1 -->
//       <a href="#" class="product" >
//       <div class="box box-widget widget-user item-picker" data-rippleria data-key="{$item->No}" data-code="{$item->barcode}" ng-click="pickProduct(\$event)">
//         <!-- Add the bg color to the header using any of the bg-* classes -->
//         <div class="widget-user-header {$bg_class} " style="{$bg_style}">
//           <h3 class="widget-user-username">{$item->description_th}</h3>
//           <h5 class="widget-user-desc">{$item->brand}</h5>
//         </div>
//         <div class="widget-user-image">
//           <img class="img-responsive" src="{$item->getPicture()}" alt="User Avatar">
//         </div>
//         <div class="box-footer">
//           <div class="row" style="height:45px;">
//             <div class="col-xs-4 border-right">
               
//               <!-- /.description-block -->
//             </div>
//             <!-- /.col -->
//             <div class="col-xs-4 border-right">
               
//               <!-- /.description-block -->
//             </div>
//             <!-- /.col -->
//             <div class="col-xs-4">
               
//               <!-- /.description-block -->
//             </div>
//             <!-- /.col -->
//           </div>
//           <!-- /.row -->
//         </div>
//       </div>
//       </a>
//       <!-- /.widget-user -->
//     </div>

// HTML;



// }
// $html.= '</div>';
// echo $html;


?>

<?php
$js=<<<JS

    $('body').on('click','.item-picker',function(){
        //console.log($(this).data('key'));

    });

JS;

$this->registerJS($js);