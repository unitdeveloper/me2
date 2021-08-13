<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PrintPage */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Print Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style media="screen">
.page{


    padding-right: 15px;
    font-size: 10px;
    height: 20px;

}

.header{
    position: relative;

    padding-top: <?=$model->margin_top?>px;
    margin: 0 -10mm 0 -10mm;


}
div.header div.body-template{
    position: absolute;
    top:1mm;

    height: <?=$model->body_height?>;

    width: 100%;
    left: 0px;

    margin: 1mm 0 0 0;




}

.body{
    padding-top: <?=$model->header_height?>px;
    height: 405px;
    margin: 0 -10mm 0 -10mm;


}





/*------Body-----*/

.item{
    /*font-size: 0.95em;*/

    font-weight: normal;
    height:27px;
}

.item-count{
    width:45px; padding: 0 15px 0 15px;
}

.item-code{
    width:110px;
}



.item-measure{
    width:90px; padding-right:5px;
}

.item-price{
    width:75px; padding-right:5px;
}

.item-discount{
     width:67px; padding-right:5px;
}

.item-amount{
    width:145px; padding-right:25px;
}


/*------/. Body-----*/




/*------Footer-----*/
.footer{
    margin: 0 -15mm 0 -15mm;


}

.remark{
    /* Text */
    font-size: 14px;
    padding:10px 0 0 10px;
    `

}

.text-beforediscount{
    /* Text */
    padding:5px 5px 5px 0;
    text-align: right;
    border-left: 0.05em solid #000;
    border-right: 0.05em solid #000;
}



.discount{
    /* Text */
    padding-right: 5px;
    border-left: 0.05em solid #000;
    border-right: 0.05em solid #000;
}

.text-percent_vat{
    /* Text */
    padding:5px 5px 5px 0;
    border-left: 0.05em solid #000;
    border-right: 0.05em solid #000;

}

.bahttext{
    /* Text */
    font-size: 13px;
    padding-left:20px;  height: 40px;
    border-top: 0.05em solid #000;
    background-color: #ccc;
}

.grandtotal{
    /* Text */
    width:170px; padding-right: 5px;
    border-top: 0.05em solid #000;
    border-left: 0.05em solid #000;
    border-right: 0.05em solid #000;
    background-color: #aaa;
}


.beforediscount{
    /*Number*/
    padding:5px 25px 0px 0;

}

.subtotal{
    /*Number*/

    padding-right:25px;

}

#sub-total {
    /* Text */
    padding:5px 5px 0px 0;
    border-left: 0.05em solid #000;
    border-right: 0.05em solid #000;
}

.sub-total {
    /*Number*/
    padding:5px 25px 0px 0;
}



.include_vat{
    /*Number*/
    padding:0px 25px 5px 5px;



}



.total{
   /*Number*/
    width:130px;
    padding-right:25px;
    margin-top: 5px;
    border-top: 0.05em solid #000;
    background-color: #ccc;
}
/*------/. Footer-----*/

.footer{
    padding-bottom: <?=$model->footer_height?>
}

.doc-info-table{

}
.doc-info{
    height: 36px;
    font-size: 10px;
}

table.table-body{

}
table th{

}
  <?=$model->style?>
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="print-page-view" ng-init="Title='<?=$this->title?>'">


  <div class="row">
    <div class="col-xs-9">
      <?=$model->header ?>
      <hr />
      <div class="" style="border:1px solid #0e59ff; height:120px; padding:5px;">
        BODY
      </div>
      <hr />
      <?=$model->footer ?>
      <hr />
      <?=$model->signature ?>

    </div>
    <div class="col-xs-3">
      <?= DetailView::widget([
          'model' => $model,
          'attributes' => [
              //'id',
              'name',
              //'logo',
              'header_height',

              'body_height',
              'footer_height',

              'pagination',
              'paper_size',
              'style',
              'company.name',
          ],
      ]) ?>

    </div>
  </div>


</div>
