<?php
 
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\widgets\TimePicker;

use kartik\widgets\DatePicker;
use common\models\BankList;
use common\models\BankAccount;


/* @var $this yii\web\View */
/* @var $model common\models\Cheque */
/* @var $form yii\widgets\ActiveForm */

/* $_POST['data'] = (object); */


if(isset($_POST['data'])) \Yii::$app->session->set('apply_to',$_POST['data']);

//$Company = \common\models\Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();
 
?>
<style type="text/css">
  .bootstrap-timepicker-widget{
    box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);
  }
</style>
<div class="cheque-form">
 
    <?php $form = ActiveForm::begin([
        'id' => 'form-posted-inv',
        'enableClientValidation' => true,
        'enableAjaxValidation' => false,
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>
    
    <div class="row">

      <div class="col-sm-4">
        <?php if($model->type=='') $model->type = 'Cash'; ?>
        <?=$form->field($model,'type')->dropDownList(
        [
          'Cash' => Yii::t('common','Cash'),
          'Cheque' => Yii::t('common','Cheque'),
          'ATM' => Yii::t('common','Transfer money').' ('.Yii::t('common','ATM').')',
        ],['class' => 'form-control cash-receipt']) ?>
      </div>
      <div class="col-sm-2">
      
        <?=$form->field($model,'source_id')->textInput(['readonly' => true])?>
      </div>
       
    </div>


    <div class="row">
      <div class="  bank-row" >
          <div class="col-sm-1">
            <?php if($model->isNewRecord){
                $imageFile = 'krungsri.png';
              }else {
                $imageFile = $model->banklist->imageFile;
                }  ?>
            <img src="uploads/<?=$imageFile?>?update=1" id="img-bank"  height="50px" style="margin:10px;">
             
          </div>
          <div class="col-sm-3">
              
          <?php if($model->bank=='') $model->bank = '0'; ?>

          <?=$form->field($model, 'bank')->dropDownList(
          ArrayHelper::map(BankList::find()
            ->orderBy(['name' => SORT_ASC])
            ->all(),'id','name'),['class' => 'form-control select-bank'])
          ?>
          </div>
           <div class="col-sm-4">
              <?= $form->field($model, 'bank_branch')->textInput(['value' => '-','maxlength' => true]) ?>
              
          </div>
          <div  class="col-sm-4 text-right" >
            <?=$model->isNewRecord
                    ? ''
                    : '<a href="#" class="btn btn-danger ew-delete-cheque" data="'.$model->id.'"><i class="fa fa-trash-o" aria-hidden="true"></i> '.Yii::t('common','Delete').'</a>';
            ?>
          </div>
          
      </div>
    </div>
    
    <div class="row" style="margin-top: 50px;">
      <div class="col-sm-8">        
        <div class="well">
          <div class="row ">              
              <div class="col-sm-6">                
                  <?php
                      if($model->posting_date=='') $model->posting_date = date('Y-m-d');

                      echo $form->field($model, 'posting_date')->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => Yii::t('common','Cheque date').'...','autocomplete' => 'off'],
                        'value' => $model->posting_date,  
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'removeButton' => false,
                        'pluginOptions' => [
                            //'format' => 'dd/mm/yyyy',
                            'format' => 'yyyy-mm-dd',
                            'autoclose'=>true
                        ]
                      ])->label(Yii::t('common','Cheque date')); 

                  ?>


              </div>

              <div class="col-sm-6">
                  <?php if($model->bank_id =='') $model->bank_id = '000-0-00000-0'; ?>

                  <?= $form->field($model, 'bank_id')->textInput(['maxlength' => true,'readonly' => $model->isNewRecord ? 'readonly' : false ])->label(Yii::t('common','Cheque ID')) ?>
              </div>
              
            
          </div>



          <?php

            $renders  = '';
       
            if(Yii::$app->request->post('data')){
              $raws     = Yii::$app->request->post('data');              
            }else{              
              $raws     = \common\models\Cheque::find()->where(['source_id' => $model->source_id])->all();
            }
            
            $balance_cheque     = 0;
            $totals             = 0;
            $i                  = 0;
            foreach ($raws as $key => $value) {

              if(Yii::$app->request->post('data')){
                $status           = $value['status'];
                $rowId            = $value['id'];
                $id               = $value['id'];
                $no               = '';
              }else{
                $status           = $value->apply_to_status;
                $rowId            = $value->id;
                $id               = $value->apply_to;
                $balance_cheque   = $value->balance;
                $cheque_date      = $value->posting_date;
                $no               = $value->apply_to_no;
              }

               
                $i++;
                
                if($status == 'Posted'){
                  $source = \common\models\RcInvoiceHeader::findOne($id);
                }else{
                  $source = \common\models\SaleInvoiceHeader::findOne($id);
                }

                
                $id               = '';
                $date             = '';
                $no               = $no;
                $balance          = $balance_cheque * 1;
                $totalBalance     = 0;
                $ivDate           = '';
                $discount         = 0;
                $inv_vat          = 0;
                $include_vat      = 0;
                $inv_include_vat  = 0;
                $iVTotal      = $balance_cheque;
                //$status       = '';
                
                if($source != null){
                  
                  $id             = $source->id;
                  $date           = Yii::$app->request->post('data') ? $source->posting_date : $cheque_date;
                  $no             = $source->no_;

                  $iVTotal        = $source->sumtotals->total *1;

                  $balance        = (Yii::$app->request->post('data') ? $source->sumtotals->total : $balance_cheque) * 1;
                  $totalBalance+= $iVTotal;
                  $totals     += Yii::$app->request->post('data') ? $source->sumtotals->total : $balance_cheque;
                  $ivDate         = date('Y-m-d',strtotime($source->posting_date));

                  $discount       = $source->discount;
                  $status         = $source->status;
               
                  $inv_vat        = $source->vat_percent;
                  $inv_include_vat= $source->include_vat;
                }else{

                  $totals     += Yii::$app->request->post('data') ? 0 : $balance_cheque;
                }

                $renders.= '<tr data-key="'.$rowId.'">
                              <td>'.$i. ' <input type="text" data-key="'.$id.'" value="'.$rowId.'" name="row-id['.$rowId.']" class="hidden"  /></td>
                              <td>'.date('Y-m-d', strtotime($date)).'</td>
                              <td >'.$no.' <small class="pull-right">'.$ivDate.'</small></td>    
                              <td class="hidden">'.$no.'</td>                          
                              <td class="text-right">'.number_format($iVTotal,2).'</td>                              
                              <td class="hidden">'.$balance.'<td>
                              <td class="text-right">
                                <input type="text" data-key="'.$id.'" value="'.$balance.'" name="row-balance['.$rowId.']" class="form-control text-right row-balance" style="background: #48e681; color: blue;" />
                                <input type="text" data-key="'.$id.'" value="'.$status.'" name="row-status['.$rowId.']" class="hidden"  />
                                <input type="text" data-key="'.$id.'" value="'.$iVTotal.'" name="row-inv_total['.$rowId.']" class="hidden"  />
                                <input type="text" data-key="'.$id.'" value="'.$discount.'" name="row-discount['.$rowId.']" class="hidden"  />
                                <input type="text" data-key="'.$id.'" value="'.$inv_vat.'" name="row-inv_vat['.$rowId.']" class="hidden"  />
                                <input type="text" data-key="'.$id.'" value="'.$inv_include_vat.'" name="row-inv_include_vat['.$rowId.']" class="hidden"  />
                              </td>
                            </tr>';

              
            }

            $renders.= ' <tfoot>
                            <tr>           
                              <th class="bg-gray"  ># </th>
                              <th class="bg-gray text-right" colspan="3">'.number_format($totalBalance,2).'</th>
                              <th class="bg-gray text-right" colspan="2"><span class="total-balance">'. number_format($totals,2) .'</span></th>
                            </tr>
                          </tfoot>
                      ';

            ?>

            <table class="table table-bordered" id="export_table">
              <thead>
                <tr>
                  <th class="bg-primary" style="width:30px;">#</th>
                  <th class="bg-primary" style="width:100px;"><?=Yii::t('common','Date')?></th>
                  <th class="bg-primary"   ><?=Yii::t('common','No')?></th>
                  <th class="hidden"><?=Yii::t('common','No')?></th>
                  <th class="bg-primary text-right"><?=Yii::t('common','Balance')?></th>
                
                  <th class="bg-primary text-right" colspan="2" style="width:150px;"><?=Yii::t('common','Receive')?></th>
                </tr>
              </thead>
              <?=$renders?>        
            </table>


          <div class="row">
                <div class="col-xs-6">
                  
                </div>

                <div class="col-xs-6">

                

                <?php 

                    if($model->balance=='') {
                      $balance = 0;
                    }else {
                      $balance = $model->balance_cheque;
                    }

                    if(isset($_POST['data'])){

                      
                      foreach ($_POST['data'] as $key => $models) {
                          
                          $balance += $models['bal'];

                      }

                    }

                  ?>


                  <?= $form->field($model, 'balance',['addon' => ['append' => ['content'=>'฿']]])->textInput([
                    'class' => 'form-control input-lg text-right',
                    'placeholder' => '***** 0000.00', 
                    //'style' => 'background: #48e681; color: blue;',
                    //'readonly' => true,
                    'value' => $balance]) ?>


                  <?= $form->field($model, 'transfer_time')->widget(TimePicker::classname(), 
                    ['pluginOptions' => [
                        'showSeconds' => false,
                        'showMeridian' => false,
                        'minuteStep' => 1,
                        'secondStep' => 5,
                    ]]);
                    ?>
                    
                  </div>
                
          </div>

          


      </div>
      <div class="row" style="margin-top: 50px;">
        <div class="col-sm-12"><?=$form->field($model,'remark')->textarea()?></div>
        <div class="col-xs-12 font-roboto">
            <?=Yii::t('common','Create by')?> :  <?=$model->user ? $model->user->profile->name : Yii::$app->user->identity->username?>  <br />
            <?=Yii::t('common','Create date')?> : <?=$model->create_date?>

        </div>
      </div>

    </div>
    
    
    <div class="col-sm-4">
        <div class="" style="margin-right: 15px;">
          <div class="row bg-info">
            <div class="col-sm-12 bg-info">

                  
                <?=$form->field($model, 'tranfer_to')->dropDownList(
                ArrayHelper::map(BankAccount::find()->orderBy(['name' => SORT_ASC])->all(),
                                    'id',
                                    function($model){
                                      return $model->bank_no.' ('.$model->banklist->name.') '.$model->name;
                                    }
                                    ),['class' => 'form-control select-bank'])->label('=> '.Yii::t('common','To account'))
                ?>
                <?= $form->field($model, 'bank_account')->hiddenInput(['value' => '-','maxlength' => true])->label(false) ?>



            </div>
            <div class="col-sm-4 bg-info"><img src="<?=Yii::$app->session->get('logo');?>" class="img-responsive"></div>
            <div class="col-sm-8 bg-info">
              
              <?=$form->field($model,'know_date')->dropDownList(
                [
                  '1' => Yii::t('common','Define date'),
                  '0' => Yii::t('common','Not sure')
                ],['class' => 'form-control know-date']) ?>

              <span class="pdc-zone" style="<?=($model->isNewRecord ? ' ' : ($model->know_date == 0 ? 'display:none;' : ''))  ?>">
                <?php
                  if($model->post_date_cheque=='') $model->post_date_cheque = date('Y-m-d');

                  echo $form->field($model, 'post_date_cheque')->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => Yii::t('common','วันที่เงินเข้าบัญชี').'...'],
                    'value' => $model->post_date_cheque,  
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'removeButton' => false,
                    'pluginOptions' => [
                        //'format' => 'dd/mm/yyyy',
                        'format' => 'yyyy-mm-dd',
                        'autoclose'=>true
                    ]
                  ])->label(Yii::t('common','วันที่เงินเข้าบัญชี')); 

              ?>
              </span>
            </div>

           
              
          </div>
        <div class="row">
          <?php 
            if($model->isNewRecord){
              $ImgTransferBank = 'kasikorn.png';
            }else {
              $ImgTransferBank = $model->bankaccount->banklist->imageFile;
            }  ?>
          <div class="col-sm-12 text-center" style="padding: 10px; border:1px solid #ccc; margin-top:5px;">
            <img src="uploads/<?=$ImgTransferBank?>" id="comp-bank-img" style="height: 120px;  ">
          </div>
        </div>
        </div>
      </div>

    </div>
 
 

    <div class="form-group <?=Yii::$app->request->isAjax ? 'hidden' : ' '?>">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Save') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
 
</div>



<div class="row">
  <div class="col-xs-12">
     
  </div>
</div>
























<script type="text/javascript">

  $(document).ready(function(){

    // Disable Cash
    //$('option[value="0"]').attr('disabled','disabled');
    //$('#cheque-bank_id').attr('readonly','readonly');

  });

  $('body').on('change','.cash-receipt',function(){
    if($(this).val()==='Cash'){

      $('.bank-row').slideUp('slow');

      $('#cheque-bank').val(0);
      $('#cheque-bank_id').attr('readonly','readonly');

      $('#img-bank').attr('src','uploads/note-icon-63268.png');

      $('label[for="cheque-bank_id"]').text('<?=Yii::t('common','Bank ID')?>');

      if($('#cheque-bank_id').val()==''){
        $('#cheque-bank_id').val('000-0-00000-0');
      }
      

      $('option[value="0"]').attr('disabled',false);

      $('#cheque-tranfer_to').val(4);

    }else if($(this).val()==='ATM'){

      $('.bank-row').slideDown('slow');

      $('#cheque-bank_id').attr('readonly',false);

      $('label[for="cheque-bank_id"]').text('<?=Yii::t('common','Bank ID')?>');


      // Default Kasikorn
      $('.select-bank').val(1);
      $('img[id="img-bank"]').attr('src','uploads/kasikorn.png');

      $('option[value="0"]').attr('disabled','disabled');

    }else {

      // Default Kasikorn
      $('.select-bank').val(1);
      $('img[id="img-bank"]').attr('src','uploads/kasikorn.png');


      $('label[for="cheque-bank_id"]').text('<?=Yii::t('common','Cheque ID')?>');

      $('.bank-row').slideDown('slow');

      $('#cheque-bank_id').attr('readonly',false);

      $('option[value="0"]').attr('disabled','disabled');
       

    }
    
  });

   $('body').on('change','select#cheque-bank',function(){

    if($(this).val()==0)
     {
       $('.bank-row').slideUp('slow');
       $('#cheque-type').val('Cash');
     }

     $.ajax({
      url: "index.php?r=accounting/bank-list/ajax-view",
      data: {id:$(this).val()},
      success: function(getData){
          var obj = jQuery.parseJSON(getData);
          $('img#img-bank').attr('src','uploads/'+obj.img);

      }
     })

     
  })






  $('#form-posted-inv').on('beforeSubmit', function(e) {

      
      $('div.loading-content').show();
      
 

      var form      = $(this);
      var formData  = form.serialize();

      $.ajax({
          url: 'index.php?r=accounting/cheque/<?=$model->isNewRecord ? 'create' : 'update'?>&id=<?=$model->id?>',
          type: form.attr("method"),
          data: formData,
          success: function (getData) {

              $('div.loading-content').hide();
              $('div.ew-body-cheque').html(getData);
              $('.post-cheque').hide();
              
          },
          error: function () {
              alert("Something went wrong");
          }
      });
  }).on('submit', function(e){
      e.preventDefault();
  });


  $('body').on('change','select#cheque-tranfer_to',function(){
    $('img#comp-bank-img').fadeOut();

    var $data = {id:$(this).val()};
    $.ajax({
      url: "index.php?r=accounting/bank-account/ajax-bank-account",
      data: $data,
      success: function(getData){
          var obj = jQuery.parseJSON(getData);

          $('img#comp-bank-img').attr('src','uploads/'+obj.img);
          $('img#comp-bank-img').fadeIn('slow');
          return false;
      },
      error:function(){
        alert('Error');
        return false;
      }
     })
  });

</script>