<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

use common\models\SetupSysMenu;
use admin\modules\apps_rules\models\SysRuleModels;


use kartik\widgets\ActiveForm;
use kartik\icons\Icon;

use yii\grid\GridView;
//use kartik\grid\GridView;


use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;

$Bahttext     = new FunctionBahttext();

$Fnc          = new FunctionSaleOrder();

$myCompany    = Yii::$app->session->get('Rules')['comp_id'];
$myRule       = Yii::$app->session->get('Rules');
$SalePeople   = $myRule['sales_id'];

$this->title  = $model->no;


if(empty($model->customer->code)){

  return Yii::$app->response->redirect(Url::to(['/SaleOrders/saleorder/update', 'id' => $model->id]));
}

// Sales = 3
// ไม่อนุญาตให้ดูใบงานที่ไม่ใช่ของตัวเอง
// Policy 1

//$Policy = SetupSysMenu::findOne(1);
//$myPolicy = explode(',',$Policy->rules_id);

//if(in_array($myRule['rules_id'],['3','9'])){
if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SalehearderSearch','view'))){
  if($model->sales_people!=$SalePeople) return Yii::$app->response->redirect(Url::to(['/SaleOrders/saleorder/index']));
}



if($model->vat_percent==0){
  $alert = 'alert-warning';
}else {
  $alert = 'alert-success hidden-xs hidden-sm hidden-md hidden-lg';
}


app\assets\SweetalertAsset::register($this);
?>


<?= $this->render('__menu_shipment',['dataProvider' => $dataProvider,'model' => $model]) ?>
<!--
<div class="alert <?=$alert;?> alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close" style="text-decoration:none; font-size:25px; top:-15px;">×</a>
  <strong>Customer No : <?= $model->customer->code ?> </strong><Br>


                                <?= $model->customer->name ?>

                              <?= $model->customer->address ?>

</div> -->
<?php

if(isset($_GET['action']))
{
  if($_GET['action'] == 'saved'){
    if($model->status=='Release')
    {
      //echo '<script>alert("ข้อมูลถูกส่งแล้ว");</script>';
      echo "<script>$(document).ready(function(){
        swal(
              '".Yii::t('common','ข้อมูลถูกส่งแล้ว !')."',
              '".Yii::t('common','Updated')."',
              'success'
            )
      });</script>";
    }


  }
}

?>

<?php $this->registerCssFile('css/sale_orders.css?update-051017');?>
<div class="row">


<div class="panel panel-info" ng-init="Title='<?=$this->title?>'">
    <div class="panel-heading" style="position: relative;" id="SaleOrder" ew-so-id="<?=$_GET['id']?>" ew-status="<?=$model->status ?>">
      <?= Icon::show('file-text-o') ?> <?=Yii::t('common','Sale Order') ?>
      <div class="print" style="position: absolute; right: 30px; top: 10px;">
        <?php if($model->status!='Open'): ?>
          <!-- <a href="index.php?r=SaleOrders/saleorder/print-ship&id=<?=$_GET['id']?>" target="_blank"><?= Icon::show('print') ?> Print Tracking</a> -->
        <?php endif; ?>


      </div>

      <!-- Next Previous -->
      <div style="position: absolute; right: 10px; top: -15px;  z-index: 500;">
        <?php


          if(in_array($myRule['rules_id'],['3'])){

            $Prev = common\models\SaleHeader::find()->where(['<','id',$model->id])
            ->andWhere(['comp_id' => $myCompany])
            ->andWhere(['sale_header.sales_people' => $SalePeople])
            ->orderBy(['id' => SORT_DESC])->limit(1)->all();


            $Next = common\models\SaleHeader::find()->where(['>','id',$model->id])
            ->andWhere(['comp_id' => $myCompany])
            ->andWhere(['sale_header.sales_people' => $SalePeople])
            ->orderBy(['id' => SORT_ASC])->limit(1)->all();

          }else {

            $Prev = common\models\SaleHeader::find()->where(['<','id',$model->id])
            ->andWhere(['comp_id' => $myCompany])
            ->orderBy(['id' => SORT_DESC])->limit(1)->all();

            $Next = common\models\SaleHeader::find()->where(['>','id',$model->id])
            ->andWhere(['comp_id' => $myCompany])
            ->orderBy(['id' => SORT_ASC])->limit(1)->all();
          }
          //$Prev->query->andWhere(['sale_header.sales_people' => $SalePeople]);

          $Previous = 0;
          $PreID = 0;
          foreach ($Prev as $value) {
            $Previous = 'index.php?r=warehousemoving/default/view&id='.$value->id;
            $PreID = $value->id;

          }
          if($PreID==0) $Previous = '#';




          $NextBt = 0;
          $NexId = 0;
          foreach ($Next as $value) {
            $NextBt = 'index.php?r=warehousemoving/default/view&id='.$value->id;
            $NexId = $value->id;

          }
          if($NexId==0) $NextBt = '#';


        ?>
          <ul class="page-btn">
            <li><a href="<?=$Previous;?>" class="btn btn-xs btn-flat btn-default-ew"><i class="fa fa-step-backward" aria-hidden="true"></i></a></li>
            <li><a href="<?=$NextBt;?>" class="btn btn-xs btn-flat btn-default-ew"><i class="fa fa-step-forward" aria-hidden="true"></i></a></li>
          </ul>
      </div>
      <!-- /.Next Previous -->

    </div>

    <div class="panel-body">
        <div class="customer-form">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li class="active">
                      <a href="#General" data-toggle="tab" aria-expanded="true">
                      <?= Icon::show('user', [], Icon::BSG) ?>
                      <?=Yii::t('common','General'); ?> </a>
                  </li>
                  <li class="">
                      <a href="#Invoicing" data-toggle="tab" aria-expanded="false">
                      <?= Icon::show('barcode', [], Icon::BSG) ?>
                      <?=Yii::t('common','Invoicing'); ?> </a>
                  </li>
                  <li class="">
                      <a href="#Shipping" data-toggle="tab" aria-expanded="false">
                      <?= Icon::show('shopping-cart', [], Icon::BSG) ?>
                      <?=Yii::t('common','Ship<span class="hidden-xs">ping</span>'); ?></a>
                  </li>


                </ul>
                <div class="tab-content">
                    <div class="tab-pane  active" id="General">
                        <div class="row">
                            <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">

                              <div class="well">
                                  <div class="row">

                                  <div class="col-sm-4 hidden-xs"><?= Yii::t('common','Code') ?>  : </div>
                                  <div class="col-lg-8"><a href="index.php?r=customers/customer/view&id=<?=$model->customer->id?>" target="_blank"><i class="fa fa-address-card-o" aria-hidden="true"></i> <?= $model->customer->code ?> </a></div>

                                  <div class="col-sm-4"><?= Yii::t('common','Customer')?> : </div>
                                  <div class="col-lg-8"><?= $model->customer->name ?> </div>

                                  <div class="col-sm-4 hidden-xs"  >  </div>
                                  <div class="col-lg-8"><?= $model->customer->address ?>

                                  </div>


                                  <div class="col-sm-4 hidden-xs">  </div>
                                  <div class="col-lg-8">
                                  <?= Yii::t('common','Phone') ?> : <?= $model->customer->phone ?>
                                  <?= Yii::t('common','Fax') ?> : <?= $model->customer->fax ?></div>
                                </div>



                              </div>
                            </div>

                            <div class="col-md-6 col-md-5">



                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','Order No') ?> : </div><div class="col-lg-8"><b><?= $model->no ?> </b></div>
                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','Order Date') ?> : </div><div class="col-lg-8"><?= $model->order_date ?> </div>


                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','Vat') ?> : </div><div class="col-lg-8"><?= $model->vattb->name ?> </div>



                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','PO.') ?> : </div><div class="col-lg-8"><?=$model->ext_document ?> .</div>





                            </div>



                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane fade" id="Invoicing">
                            <!-- The timeline -->
                            <div class="row">
                            <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">


                                <div class="col-sm-4 hidden-xs"> <?=Yii::t('common','Customer No') ?> : </div>
                                <div class="col-lg-8"><?= $model->customer->code ?> </div>

                                <div class="col-sm-4 hidden-xs"> <?=Yii::t('common','Customer Name') ?> : </div>
                                <div class="col-lg-8"><?= $model->customer->name ?> </div>

                                <div class="col-sm-4 hidden-xs">  </div>
                                <div class="col-lg-8">
                                  <?= $model->customer->address ?>

                                </div>



                                <div class="col-sm-4 hidden-xs">  </div>
                                <div class="col-lg-8"><?=Yii::t('common','Phone')?> : <?= $model->customer->phone ?> <?=Yii::t('common','Fax') ?> : <?= $model->customer->fax ?></div>
                            </div>
                            <div class="col-md-6 col-md-5">
                                <div class="row">
                                  <label class="col-sm-3"><?=Yii::t('common','Payment')?> :  </label>
                                    <?php
                                      if($model->payment_term!='0')
                                      echo $model->payment_term.' วัน';
                                      else
                                      echo 'เงินสด';
                                    ?>

                                </div>

                                <div class="row">
                                  <label class="col-sm-3"><?=Yii::t('common','Due Date')?> :  </label><?= $model->paymentdue ?>
                                </div>


                            </div>
                        </div>

                    </div>
                    <!-- /.tab-pane -->

                    <div class="tab-pane fade " id="Shipping">
                        <div class="row">


                        <?php $form = ActiveForm::begin([
                              'id' => 'Form-SaleOrder',
                              'action' => ['saleorder/update-header','id' => $model->id],
                              'method'=> 'post'
                              ]); ?>



                         <?php if(Yii::$app->session->get('Rules')['rules_id']==4):   ?>

                            <div class="col-xs-9">

                              <?= $form->field($model,'transport')->textInput()->label(Yii::t('common','Transport By')) ?>

                              <?= $form->field($model,'ship_address')->textInput()->label(Yii::t('common','Ship Address'))  ?>
                            </div>


                            <div class="col-xs-3">

                              <div><label>Save</label></div>
                               <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                            </div>
                          <?php else: ?>

                          <div class="col-sm-12"><?= $model->ship_address ?></div>

                          <?php endif; ?>


                          <?php ActiveForm::end(); ?>


                        </div>
                        <div class="row">
                        <div class="col-sm-12">
                        <?=Yii::t('common','Create By') ?> : [<?= $model->user_id ?>] <?= $model->users->username ?>
                        </div>
                        <div class="col-sm-12">
                        <?=Yii::t('common','Update By') ?> : [<?= $model->update_by ?>] <?= $model->update_date ?>
                        </div>
                        </div>
                    </div>
                     <!-- /.tab-pane -->
                 </div>
                 <!-- /.tab-content -->
             </div>

            <div class="row">
                <div class="col-xs-12">
                  <div class="box">

                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding">
                    <?php

                    Yii::$app->session->set('vat',$model->include_vat);



                    $gridColumns = [
                                  //'order_no',
                                  ['class' => 'yii\grid\SerialColumn'],
                                  // [
                                  //   'attribute' => 'type',
                                  //   'format' => 'html',
                                  //   'contentOptions' => ['class' => 'hidden-xs'],
                                  //   'headerOptions' => ['class' => 'hidden-xs'],
                                  //   'footerOptions' => ['class' => 'hidden-xs'],
                                  //   'value' => function($model){
                                  //     return $model->type;
                                  //   },
                                  //   'footer' => '<div></div>',
                                  // ],
                                  [
                                      'attribute' => 'Photo',
                                      'format' => 'raw',
                                      'contentOptions' => ['class' => 'relative'],
                                      'label' => Yii::t('common', 'Image'),
                                      'value' => function($model){


                                          if(isset($model->item_no))
                                          {
                                            $photo = 'images/product/'.$model->itemstb->ItemGroup.'/'.$model->itemstb->Photo;
                                            $item  = $model->itemstb->No;

                                          }else {
                                            $photo = 'images/nopic.png';
                                            $item  = '';
                                          }

                                          // return Html::a(Html::img($photo,
                                          //   ['style'=>'width:50px;']), ['/items/items/view','id'=>$model->itemstb->No],['target'=>'_blank']);

                                         //return '<span id="ew-Item-Info" ew-item-no="'.$item.'" class="btn">'.Html::img($photo,['style'=>'width:50px;']).'</span>';

                                         return '<span id="ew-Item-Info" ew-item-no="'.$item.'" class="btn">'.Html::img($model->itemstb->getPicture(),['style'=>'width:50px;']).'</span>';






                                      }
                                  ],

                                  [
                                    'attribute' => 'item_no',
                                    'format' => 'html',
                                    'contentOptions' => ['class' => 'hidden-xs'],
                                    'headerOptions' => ['class' => 'hidden-xs'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => function($model){
                                      return $model->itemstb['master_code'];
                                    },
                                    'footer' => '<div></div>',
                                  ],
                                    //'itemstb.Description',
                                  [
                                    'label' => Yii::t('common','Description'),
                                    'value' => function($model){

                                      if($model->description=='') $model->description = $model->itemstb->description_th;

                                      return $model->description;
                                    }
                                  ],
                                  [
                                     'attribute' => 'quantity',
                                     'format' => 'html',
                                     'headerOptions' => ['class' => 'text-right'],
                                     'contentOptions' => ['class' => 'text-right'],
                                     'value' => function($model){
                                        return number_format($model->quantity,2);
                                     }
                                  ],
                                    //'quantity',
                                    'itemstb.UnitOfMeasure',
                                    //'unit_measure',
                                    //'unit_price',





                              ];
              ?>
              <?=  GridView::widget([
                  'dataProvider'=> $dataProvider,
                  //'filterModel' => $searchModel,
                  'showFooter' => true,
                  'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
                  'columns' => $gridColumns,
                  //'responsive'=>true,
                  //'hover'=>true,
                  //'summary' => false,
              ]);
              ?>


                    </div>
                    <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
                </div>
            </div>










        </div>
    </div>
</div>

</div>






<?php

//if(Yii::$app->session->get('Rules')['rules_id'] == 4 || Yii::$app->session->get('Rules')['rules_id'] == 2) {
if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','ew-app-zone'))){
  if($model->status == 'Pre-Cancel')
  {
    // เมื่อกด Confirm จะเปลี่ยนสถานะเป็น Cancel
    $ConfCon    = 'Confirm-Cancel';

    // เมื่อกด Reject จะเปลี่ยนสถานะเป็น Checking
    $RejCon     = 'Checking';
  }else {

    $ConfCon  = 'Confirm';

    $RejCon     = 'Reject';
  }

  $btnApprove = '
  <div class="row ew-app-zone">
    <div class="col-md-offset-8">
    <div class="col-sm-12">
      <div class="row">
        <div class="col-xs-6">
        <button class="btn btn-danger btn-lg ew-btn-app-click" id="ew-reject" ew-data="'.$RejCon.'">
          <i class="fa fa-eject" aria-hidden="true"></i> '.Yii::t('common','Reject').'<span class="hidden-xs"> (F7)</span></button>
        </div>
        <div class="col-xs-6 text-right">
        <button class="btn btn-success  btn-lg ew-btn-app-click" id="ew-confirm" ew-data="'.$ConfCon.'">
          <i class="fa fa-check-square-o" aria-hidden="true"></i> '.Yii::t('common','Accept').'<span class="hidden-xs"> (F10)</span></button>
        </div>
      </div>
    </div>
    </div>
  </div>';
  if($model->status=='Release' || $model->status=='Pre-Cancel')
  {
    echo $btnApprove;
  }
}


?>


<?php echo  $this->render('../../../SaleOrders/views/modal/__modal_approve',['model' => $model]) ?>
<?php echo  $this->render('../../../SaleOrders/views/modal/__modal_item_info',['model' => $model]) ?>




<?php $this->registerJsFile('js/item-picker.js?update-051017');?>
<?php $this->registerJsFile('js/action_sale_invoice.js?v=03.08.15'); ?>

<?php echo  $this->render('../../../SaleOrders/views/modal/_tracking') ?>
<?php $this->registerJsFile('js/saleorders/saleorder-form.js?update=191017'); ?>
<?php $this->registerJsFile('js/saleorders/saleorder_index.js?update-051017'); ?>
