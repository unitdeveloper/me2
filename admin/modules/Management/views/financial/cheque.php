<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use kartik\widgets\ActiveForm; 
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use common\models\SalesPeople;
use common\models\Customer;
use admin\modules\Management\models\FunctionManagement;
use common\models\ViewRcInvoice;
 
$this->title = Yii::t('common','Payment Receipt');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Payment Receipt'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

 
function itemGroup($data){
  $GroupID = FunctionManagement::findItemInGroup($data);
  $GroupID = explode(',', $GroupID);
  $GroupID = array_merge($data,$GroupID);
  foreach ($GroupID as $key => $var) {
    $GroupID[$key] = (int)$var;
  }
  return $GroupID;
}

?>
<style type="text/css">
  
  @media print{
    .btn-print{
      display: none;
    }
    .remark span{
      color: red;
    }
    .pagination,
    .remark-bottom,
    form#invlice-search,
    .export-row{
      display: none;
    }
    .dataCalc{
      border:0px;
    }
    .textComment{
      border:0px;
    }
    a[href]:after {
      content: none !important;
    }
    .borderless td, 
    .borderless th {
        border: none !important;
    }    
  }
  .btn-print{
      background-color: rgb(253,253,253);
      border-bottom: 1px solid #ccc;
      margin-bottom: 20px;
  }


	.input-group-addon{
		background-color: rgb(249,249,249) !important;
		border: 1px solid #999 !important;

	}

  a.view-receipt{
    padding: 0 5px 0 5px;
     
  }

  a.view-receipt:hover{
    color: red;
  }
  .select2-selection{
    height: 34px !important;

  }
  .select2-container--krajee .select2-selection--single .select2-selection__placeholder {
    color: #999;
     
  }

  .select2-container .select2-selection--single .select2-selection__rendered {
    padding-top: 5px;   
  }

  .text-sum{
    margin:20px 0 0 0;
  }

  .text-sumVal{
    margin:20px 0 0 0;
    border-bottom: 5px double #ccc;
  }

  .sum-footer{
    margin-top: 10px;
    border-bottom: 1px solid #ccc;
  }

  .modal
  {
    overflow: hidden;
    background:none !important;

  }


  .modal-dialog{
     box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
  }

  .box-color{
    width:20px !important;
    height:20px;
    border:1px solid #ccc;
    position:absolute;
    margin-left:-25px;
  }

  table{
    font-family:  Arial, Helvetica, sans-serif;
  }
</style>

<div class="sales-report" ng-init="Title='<?=$this->title?>'">

  <?=$this->render('_filter')?>
  



<div class="row export-row">
    <div class="col-xs-6"></div>
    <div class="col-xs-6 text-right">
        <div class="pull-left"  style="margin-top: 0px; margin-bottom: 10px;">
            <button class="btn btn-success-ew" onclick="window.print()"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
        </div>
        <div  > 
        <?php
            echo ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => [                                               
                [                           
                    'label' => Yii::t('common','Date'),
                    'format' => 'raw',
                    'value' => function($model){                               
                        return date('d/m/Y',strtotime($model->posting_date));            
                    }            
                ],

                [
                    'label' => Yii::t('common','Customer'),
                    'format' => 'raw',                          
                    'value' => function($model){
                        return $model->customer['name'].' ('.$model->customer->getAddress()['province'].')';                                
                    }
                ],
                
                [
                    'label' => Yii::t('common','Document No'),
                    'format' => 'html',
                    'value' => function($model){            
                    return $model->no_;                             
                    },
                    // 'footer' => Yii::t('common','รวม'),
                ],
                
                [
                    'label' => Yii::t('common','Balance'),
                    'format' => 'raw', 
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],                     
                    'value' => function($model){            
                    return $model->getTotal();
                    }                            
                ], 


                [
                    'label' => Yii::t('common','Amount') .'/'. Yii::t('common','Accessories'),
                    'format' => 'raw',                      
                    'value' => function($model){
                    return $model->getTotalBalance($model,itemGroup([4,14]),'Excepted');
                    },
                ],

                [
                    'label' => Yii::t('common','Amount') .'/'. Yii::t('common','Switch/Plug'),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'footerOptions' => ['class' => 'text-right'],
                    'value' => function($model){                            
                    return $model->getTotalBalance($model,[14],'Equal');                            
                    }                 
                ],            

                [
                    'label' => Yii::t('common','Amount') .'/'. Yii::t('common','LED'),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'footerOptions' => ['class' => 'text-right'],
                    'value' => function($model){                                         
                    return $model->getTotalBalance($model,itemGroup([4]),'Equal');                             
                    },                           
                ],

                [
                    'label' => Yii::t('common','การชำระเงิน'),
                    'format' => 'raw',                           
                    'value' => function($model){

                      if($model->cheque != null){
                          $query = $model->cheque;          
                          $data   = array();

                          foreach ($query as $key => $models) {            
                            //$dateCheque   = $models->know_date == 1 ? date('d/m/y',strtotime($models->posting_date)) : Yii::t('common','Not sure');                                
                            //#######
                            //$models->bank_id = '#'.$models->bank_id;            
                            //if($models->type=='Cash') $models->bank_id = NULL;
                            //if($models->type=='ATM')  $models->bank_id = NULL;            
  
                            //if($models->type=='ATM') $models->banklist->name = Yii::t('common','Transfer');            
                            $balance = $models->balance_cheque;                                          

                            // ถ้า approve แล้วให้มีสีเขียว
                            //$bgStatus = NULL;
                            // $ApproveCheck  = \common\models\Approval::find()->where(['source_id' => $models->id]);
                            //if($ApproveCheck->exists()) $bgStatus = 'bg-green';

                            //$data[]       = "{$models->banklist->name} #{$dateCheque} {$models->bank_id}  #{$balance} ";       
                            $data[]       = "{$balance}";       
                          }
                          return implode("+",$data);            
                      }else {            
                          return '';            
                      }                            
                    }                         
                ],

                

                [
                    'label' => Yii::t('common','Remark'),
                    'format' => 'raw',
                    'value' => function($model){
         
                        if($model->cheque != null){
                            $query = $model->cheque;          
                            $data = array();            
                            foreach ($query as $key => $models) {                               
                              $data[]       = "{$models->banklist->name}  {$models->remark} ";
                            }                                              
                            return implode(",",$data);           
                        }else {            
                        return '';            
                        }                            
                    }
                ],

                [
                    'label'   => Yii::t('common','ยอดชำระ'),
                    'format'  => 'raw',
                    'value'   => function($model){

                    if($model->cheque != null){
                        $query    = $model->cheque;
                        $balance  = 0;
                        foreach ($query as $key => $models) {                       
                            $balance+= $models->balance_cheque;
                        }
                            
                        return $balance;
                    }else {
                        return 0;
                    }            		
                    },             
                ],

                'sales_people',

                ],
                'columnSelectorOptions'=>[
                    'label' => ' ',
                    'class' => 'btn btn-warning'
                ],
                'fontAwesome' => true,
                'selectedColumns'=> [0,1,2,3,4,5,6,7,8,9,10],
                'dropdownOptions' => [
                    'label' => Yii::t('common','Export All'),
                    'class' => 'btn btn-primary'
                ],
                'target' => ExportMenu::TARGET_BLANK,
                'filename' => 'Payment-'.date('ymd_H_i_s')                         
            ]); 
        ?>
        </div>
    </div>
</div>

<div class="row">	 
  <div class="col-sm-6  " style="font-size: 16px;">
    ยอดขายประจำเดือน 
    <?php
      if(isset($_GET['fdate'])){
        $fdate = date('m',strtotime($_GET['fdate']));
        $tdate = date('m',strtotime($_GET['tdate']));
        if($fdate==$tdate){
          echo Yii::t('common',date('M',strtotime($_GET['fdate'])));
        }else {
          echo Yii::t('common',date('M',strtotime($_GET['fdate']))).' ถึง '.Yii::t('common',date('M',strtotime($_GET['tdate'])));
        }        
      }
    ?> 
    ปี 
    <?php
      if(isset($_GET['fdate'])){
        $fYear = date('y',strtotime($_GET['fdate']));
        $tYear = date('y',strtotime($_GET['tdate']));
        if($fYear==$tYear){
          echo date('Y',strtotime($_GET['fdate']));
        }else {
          echo date('Y',strtotime($_GET['fdate'])).' ถึง '.date('Y',strtotime($_GET['tdate']));
        }        
      }
    ?>     
  </div>

  <div class="col-sm-6">
    Sale : <?php
    if(isset($_GET['search-from-sale'])){
      $SaleMan = \common\models\SalesPeople::find()->where(['code' => $_GET['search-from-sale']]);
      if($SaleMan->exists()){
        $models = $SaleMan->one();
        echo $models->code. ' '.$models->name;
      }      
    }
    ?>
  </div>
</div>



  <?php 
    $gridColumns = [
      [
        'class' => 'kartik\grid\SerialColumn',
        'headerOptions' => ['class' => 'bg-dark'],
        'contentOptions' => function($model){
          if($model->status == 'Open'){
            return ['class' => 'alert-warning'];
          }else {                  
            return ['class' => 'alert-info'];
          }
          
        },
      ],

      [
        'label' => Yii::t('common','Date'),
        'headerOptions' => ['class' => 'bg-gray'],
        'contentOptions' => ['class' => 'font-roboto'],
        'format' => 'raw',
        'value' => function($model){
              return date('d/m/Y',strtotime($model->posting_date));
        }
      ],
      
      [
        'label' => Yii::t('common','Customer'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'bg-gray'],
        'contentOptions' => ['style'=>'max-width:200px; min-height:100px; overflow: auto; word-wrap: break-word;', 'class' => 'bg-info'],
        'value' => function($model){
            $name = $model->customer['name'];
            $subString = (mb_substr($name,0, 20). ' ' .(strlen($name) > 20 ? '...' : null));
            $html =  "<a href='#modal/{$model->cust_no_}' title='{$name}' class='open-modal' data-status='{$model->status}' data='{$model->cust_no_}' row-data='{$model->id}'>{$name} ({$model->customer->fullAddress['province']})</a>";
            return $html;
        }
      ],

      [
        'label' => Yii::t('common','Document No'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'bg-gray'],
        'footerOptions' => ['class' => 'text-right  font-roboto', 'style' => 'position:relative;'],
        'value' => function($model){
          $star = '';
          if ($model->discount >= 3000){
            $star = '<sup><i class="fas fa-star text-orange" alt="มีส่วนลด" title="มีส่วนลด"></i></sup>';
          }
          if ($model->status == 'Posted'){
            return Html::a($model->no_. ' '.$star,['/accounting/posted/posted-invoice','id' => base64_encode($model->id)],['target' => '_blank']);
          }else{
            return Html::a($model->no_. ' '.$star,['/accounting/saleinvoice/update','id' => $model->id],['target' => '_blank']);
          }            		
        },
        'footer' => (@$_GET['total-summary']=='all') ? '                  
            <div style="padding-top:27px; position:absolute; right:2px;" >
            <div class="text-sum" style="padding-top: 5px;padding-bottom: 17px;">รวมทั้งสิ้น</div>
              <input type="text" style="width:300px;"  class="textComment  text-right"  value="'.Yii::$app->session->get('textComment').'"> 
            </div>' : ''
      ],
      

      [
        'label' => Yii::t('common','Amount') .'/'. Yii::t('common','Accessories'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'text-right bg-gray'],
        'contentOptions' => ['class' => 'text-right font-roboto'],
        'footerOptions' => ['class' => 'text-right', 'style' => 'max-width:90px;'],
        'value' => function($model){
          $Total = $model->getTotalBalance($model,itemGroup([4,14]),'Excepted');       
          return number_format($Total,2);
        },
        'footer' => (@$_GET['total-summary']=='all') ? '
                    <div class="sum-footer">'.number_format(ViewRcInvoice::getFooterRowTotal($dataProvider->models,itemGroup([4,14]),'Excepted'),2).'</div>
                    <div class="text-sumVal" data="'.ViewRcInvoice::getSumFooter($dataProvider->models).'">
                      '.number_format(ViewRcInvoice::getSumFooter($dataProvider->models),2).'
                    </div>
                    <div class="text-sum" style="margin-top: 12px;">                             
                      <input type="text" class="dataCalc text-right" style="width:100%;"> %
                    </div>' : '',
          
      ],

      [
        'header' =>  Html::a(Yii::t('common','Switch/Plug'),["/items/items","SearchItems[groups]" => 14 ],['target' => '_blank']),
        'format' => 'raw',
        'headerOptions' => ['class' => 'text-right bg-gray'],
        'contentOptions' => ['class' => 'text-right font-roboto'],
        'footerOptions' => ['class' => 'text-right', 'style' => 'max-width:90px;'],
        'value' => function($model){                
          $Total = $model->getTotalBalance($model,[14],'Equal');
          return number_format($Total,2);                
        },
        'footer' => (@$_GET['total-summary']=='all') ? '
                      <div class="sum-footer sum-led">'.number_format(ViewRcInvoice::getFooterRowTotal($dataProvider->models,[14],'Equal'),2).'</div>
                      <div class="text-sumVal" data="'.ViewRcInvoice::getSumFooter($dataProvider->models).'">
                        '.number_format(ViewRcInvoice::getSumFooter($dataProvider->models),2).'
                      </div>
                      <div style="margin-top:15px;">                              
                        <input type="text" class="dataTotal no-border text-right" style="width:100%;">
                      </div>' : '',
      ],            


      [
        'label' => Yii::t('common','Amount') .'/'. Yii::t('common','LED'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'text-right bg-gray'],
        'contentOptions' => ['class' => 'text-right font-roboto'],
        'footerOptions' => ['class' => 'text-right', 'style' => 'max-width:90px;'],
        'value' => function($model){                
          $Total = $model->getTotalBalance($model,itemGroup([4]),'Equal');
          return number_format($Total,2);                
        },
        'footer' => (@$_GET['total-summary']=='all') ? '
                      <div class="sum-footer sum-led">
                        '.number_format(ViewRcInvoice::getFooterRowTotal($dataProvider->models,itemGroup([4]),'Equal'),2).'
                      </div>
                      <div class="text-sumVal" data="'.ViewRcInvoice::getSumFooter($dataProvider->models).'">
                        '.number_format(ViewRcInvoice::getSumFooter($dataProvider->models),2).'
                      </div>
                      <div style="margin-top:15px;">                              
                        <input type="text" class="dataTotal no-border text-right" style="width:100%;">
                      </div>' : '',
      ],

      [
        'label'   => Yii::t('common','การชำระเงิน'),
        'format'  => 'raw',
        'headerOptions' => ['class' => 'bg-gray'],
        'value'   => function($model){

          if($model->cheque != null){
            
              $query  = $model->cheque;
              $html   = '<table class="table borderless" >';
              
              foreach ($query as $key => $models) {
                $dateCheque   = $models->know_date == 1 ? date('d/m/y',strtotime($models->posting_date)) : Yii::t('common','Not sure');                                         
                if($models->type=='Cash') $models->bank_id = NULL;
                if($models->type=='ATM')  $models->bank_id = NULL;
                if($models->type=='ATM') $models->banklist->name = Yii::t('common','Transfer');
                $balance      = number_format($models->balance,2);
                //$TotalBalance = number_format($models->sumTotal,2);

                // ถ้า approve แล้วให้มีสีเขียว
                $bgStatus = ' ';
                $ApproveCheck  = \common\models\Approval::find()->where(['source_id' => $models->id]);
                if($ApproveCheck->exists()) $bgStatus = 'bg-success';

                $bankName = $models->balance > 0 ? $models->banklist->name : ' ';
                $html.= "<tr class='{$bgStatus}'  data-key='{$models->id}'>
                            <td style='width:50px;'>{$bankName}</td>
                            <td style='width:50px;'>{$dateCheque}</td>
                            <td class='text-right font-roboto'>
                              <a 
                                data='{$models->id}' 
                                href='#{$models->id}' 
                                class='view-receipt text-aqua' 
                                title=".$models->user->profile->name.' '.$models->create_date.">{$balance}</a> 
                            </td>                                                                                         
                          </tr>";

                if($models->remark != ''){
                  if ($model->discount > 0){
                    $remark = Yii::t('common','Discount')." : ".number_format($model->discount)."<br />";
                    $remark.= $models->remark;
                    
                  }else{
                    $remark= $models->remark;
                  }

                  $html.= '<tr  class="'.$bgStatus.'"  data-key="'.$models->id.'">                               
                              <td colspan="3">
                                <div class="text-red" title="'.$remark.'">' . (mb_substr($remark,0, 30). ' ' .(strlen($remark) > 30 ? '...' : null)) . '</div>
                                <div>'. ($models->bank_id != '' ? 'No.: '.$models->bank_id : '') . '</div>
                              </td>
                          </tr>';
                }
              }
              $html.= '</table>';
              return '<div style="margin:-10px -8px 0px -8px;">'.$html.'</div>';
          }else {
            return '';
          }            		
        },             
      ],
      
      [
        'label'   => Yii::t('common','ยอดชำระ'),
        'format'  => 'raw',
        'headerOptions' => ['class' => 'bg-gray'],
        'contentOptions' => ['class' => 'text-right total-payment'],
        'value'   => function($model){
          if($model->cheque != null){
              
              $total    = 0;
              foreach ($model->cheque as $models) {        
                $total += $models->sumTotal;                       
              }
                  
              return number_format($total,2);
          }else {
            return number_format(0,2);
          }            		
        },             
      ],

  ]; ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'showFooter' => true,    
    //'tableOptions' => ['class' => 'table table-bordered table-striped','id'=>'tb-cheque'],
    //'pjax' => true, 
    'responsiveWrap' => false,
    'columns' => $gridColumns,
    'pager' => [
      'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
      'prevPageLabel' => '«',   // Set the label for the "previous" page button
      'nextPageLabel' => '»',   // Set the label for the "next" page button
      'firstPageLabel'=> Yii::t('common','First'),   // Set the label for the "first" page button
      'lastPageLabel'=> Yii::t('common','Last'),    // Set the label for the "last" page button
      'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
      'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
      'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
      'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
      'maxButtonCount'=>5,    // Set maximum number of page buttons that can be displayed
      ],         
  ]); ?>
</div>




<!-- Modal -->
<div id="chequeModal" class="modal fade modal-full" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog ">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header bg-green" style="cursor: move;">
        <button type="button" class="close " data-dismiss="modal">&times;</button>
        <h4 class="modal-title">การรับเช็ค/วางบิล</h4>
      </div>
      <div class="modal-body mb-10">
          <div class="ew-body-cheque mb-10">
          <br><br><br><br>
          </div>	
      </div>
      <div class="text-center loading-content" style="position: absolute; top: 40%; right: 45%;  display: none;">
        <i class="fa fa-spinner fa-spin fa-2x fa-fw text-info" aria-hidden="true"></i>
        <div class="blink"> Loading </div>
      </div>
      <div class="modal-footer">     
        <button type="button" class="btn btn-default  pull-left" data-dismiss="modal">
        <i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></button>  
                  
        <a href="#"  class="btn btn-info print-cheque" target="_blank" style="display:none;">
        <i class="fa fa-print" aria-hidden="true"></i> <?=Yii::t('common','Print')?></a>
        
        <button type="button" name="Select" class="btn btn-primary getInv">
        <i class="fa fa-check" aria-hidden="true"></i> <?=Yii::t('common','Select')?></button>     

        
      
      </div>
    </div>
  </div>
</div>
 
<div class="remark-bottom">
หมายเหตุ : <i class="fas fa-star text-orange"></i> มีส่วนลด (มากกว่า 3,000 บาท)
</div>

<?php $this->registerJsFile('https://code.jquery.com/ui/1.12.1/jquery-ui.js',['depends' => [\yii\web\JqueryAsset::className()]]);?>

<?php
$Yii = 'Yii';

$jsh=<<<JS
let state = {data:[]}
JS;
$this->registerJs($jsh,\yii\web\View::POS_HEAD);


$js =<<<JS

$(document).ready(function(){

  

  setTimeout(() => {
    $("body")
      .addClass("sidebar-collapse")
      .find(".user-panel")
      .hide();
  },500);
});

$('body').on('click','.ew-table tr', function(event) {
    if (event.target.type !== 'checkbox') {
      $(':checkbox', this).trigger('click');        
    } 

    if($(this).find('input[name="inv[]"]').is(":checked")){
      $(this).find('td').addClass('bg-primary');
    }else{
      $(this).find('td').removeClass('bg-primary');
    }
      
});

  // $(document).ready(function() {
      
    //   $('.ew-table tr').click(function(event) {
    //     if (event.target.type !== 'checkbox') {

    //       $(':checkbox', this).trigger('click');
    //       $(this).removeClass('bg-yellow');
           
    //     }else{
    //         $(this).addClass('bg-yellow');
    //     }

    //     console.log($(this).find('input[name="inv[]"]'));
         
    //   });
    // });


$(function(){
  //   $('#chequeModal').draggable({
  //     handle: '.modal-header'
  // });
})

$('body').on('keyup','.dataCalc',function(){
  var subTotal  = $('.text-sumVal').attr('data') * $(this).val() /100;
  var total     = subTotal + ($('.text-sumVal').attr('data') *1);
  $('.dataTotal').val(number_format(total.toFixed(2)));
});


  $('body').on('click','a.view-receipt',function(){
    $('#chequeModal').modal('show'); 
    EditCheques($(this).attr('data'));
    $('.post-cheque').show();
    $('.getInv').attr('class','btn btn-warning post-cheque').html('<i class="fa fa-save" ></i> {$Yii::t('common','Save')}'); 

  });


  $('#chequeModal').on('show.bs.modal',function(){   
    
  });

  $('#chequeModal').on('hidden.bs.modal',function(){   
    setTimeout(() => {
      $('body').find('a.print-cheque').attr('href','#').hide(); 
    }, 500);    
  });

  

  function EditCheques(id){
    $('.modal-body').hide('slow');
    $('div.loading-content').show();
    $('div.ew-body-cheque').html('<br><br><br><br>');
    setTimeout(function(e){ 
      $.ajax({ 
            url:'index.php?r=accounting/cheque/updates',
            type: 'GET', 
            data: {id:id},
            async:false,
            success:function(res){
              var obj = jQuery.parseJSON(res);
            
              $('div.loading-content').hide();
              $('div.ew-body-cheque').html(obj.html);
              $('.modal-body').slideToggle('slow');

              $('body').find('a.print-cheque').attr('href','index.php?r=accounting/cheque/print&id='+obj.id).show();

              setTimeout(() => {
                 
                $("#export_table").tableExport({
                      headings: true,                     // (Boolean), display table headings (th/td elements) in the <thead>
                      footers: true,                      // (Boolean), display table footers (th/td elements) in the <tfoot>
                      formats: ["xlsx"],                  // (String[]), filetypes for the export ["xls", "csv", "txt"]
                      fileName: "Cheque",                 // (id, String), filename for the downloaded file
                      bootstrap: true,                    // (Boolean), style buttons using bootstrap
                      position: "bottom" ,                   // (top, bottom), position of the caption element relative to table
                      ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file
                      ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file
                      ignoreCSS: ".tableexport-ignore",   // (selector, selector[]), selector(s) to exclude from the exported file          
                  }); 
              }, 1500);    
            }
      });
    }, 1000); 
  }

  $('body').on('click','.close-modal-cheque',function(){
    window.location.reload();
  });

	$('body').on('click','.getInv',function(){
		getFilterPostedInv();
	});


  $('body').on('click','button.post-cheque',function(){
    $('form[id="form-posted-inv"]').submit();
    $('button[data-dismiss="modal"]').addClass('close-modal-cheque'); 
  });

	function getFilterPostedInv(){

    var obj = [];
    if($('.ew-checked:checked').serialize()!=''){
        $('tr').each(function(i, el) {
          var iCheck = $(el).children('td').find('input.ew-checked');
          if (iCheck.is(':checked')) {
             obj.push({id:iCheck.attr('row-data'),cust:iCheck.attr('data'),bal:iCheck.attr('bal'),status:iCheck.attr('status')});
          }
        });
        //console.log(obj);
        renderChequeForm(obj);
    }else{
      swal(        
        "{$Yii::t('common','Please select one of the options.')}",
        "{$Yii::t('common','That thing is still around?')}",
        'warning'
      );
      return false;
    }

  }

  function renderChequeForm(obj){		
		$('.modal-body').hide('slow');
		$('div.loading-content').show();
		$('div.ew-body-cheque').html('<br><br><br><br>');

		setTimeout(function(e){ 
			$.ajax({ 
        url:'index.php?r=accounting/cheque/create',
        type: 'POST', 
        data: {data:obj},
        async:false,
        success:function(getData){          
            $('div.loading-content').hide();
            $('div.ew-body-cheque').html(getData);
            $('.modal-body').slideToggle( 'slow' );
            $('.getInv').attr('class','btn btn-info post-cheque').html('<i class="fa fa-save" aria-hidden="true"></i> {$Yii::t('common','Save')}');
        }
			});
		}, 1000); 
  }



	$('body').on('click','.open-modal',function(){
    let status = $(this).attr('data-status');
    if(status=='Open'){
      swal(        
        "{$Yii::t('common','Not allowed')}",
        "{$Yii::t('common','Please post the document before doing it again.')}",
        'warning'
      );
    }else{
      $('#chequeModal').modal('show'); 
      loadCheque($(this).attr('row-data'),$(this).attr('data'));
      $('.post-cheque').show();
      $('.post-cheque').attr('class','btn btn-primary-ew getInv').html('<i class="fa fa-check" aria-hidden="true"></i> {$Yii::t('common','Select')}');
    }
  });




	function loadCheque(id,cust){
	  $('div.ew-body-cheque').html('<br><br><br><br>');	
	  $('.modal-body').hide();
    $('div.loading-content').show();
    let fdate = $('#fdate').val();
    let tdate = $('#tdate').val();

	  setTimeout(function(e){ 
	    $.ajax({
        url:'index.php?r=accounting/cheque/posted-inv-list',
        type: 'GET', 
        data: {id:id,cust:cust,fdate:fdate, tdate:tdate},
        async:false,
        success:function(getData){
            $('div.loading-content').hide();
            $('div.ew-body-cheque').html(getData);
            $('.modal-body').slideToggle( 'slow' );
        }
	    });
	  }, 1000); 
	}


  $('body').on('click','.ew-delete-cheque',function(){
    let id = $(this).attr('data');
    if (confirm("{$Yii::t('common','Do you want to confirm ?')}")){
      $.ajax({
            url:'index.php?r=accounting/cheque/delete&id='+id,
            type: 'POST', 
            data: {id:id},
            async:false,
            success:function(getData){ 
              let res = jQuery.parseJSON(getData); 
              if(res.status==200){
                $('div.ew-body-cheque').html(res.message);
                $('#chequeModal').modal('hide'); 
                $('body').find('a[data="'+id+'"]').closest('tr').remove();
                $('body').find('a[data="'+id+'"]').closest('tr').find('.total-payment').html('')
              }else{   
                $('div.ew-body-cheque').html(getData);
                setTimeout(function(e){ 
                  $('#chequeModal').modal('hide'); 
                  window.location.reload();
                }, 1000);  
              }              
            }
      });
    }
  });

 


// FORM 

$('body').on('keyup', 'input.row-balance', function(){
  
  let data = [];
  let total = 0;
  $('input.row-balance').each(function( ) {
    
    data.push({
      id:$(this).attr('data-key'),
      value: $(this).val()
    });
    total+= $(this).val() * 1;
  });

  state.data = data;
  
  $('body').find('.total-balance').html(total);
  $('body').find('input#cheque-balance').val(total);
});



$("body").on("keypress", 'input.row-balance', function(e) {
  // Disable form submit on enter.
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    e.preventDefault();
    return false;
  }
});


$('body').on('change', '#cheque-know_date', function(){
  let data = $(this).val();
  if(parseInt(data)===1){
    $('body').find('.pdc-zone').show();
  }else{
    $('body').find('.pdc-zone').hide();
  }
});

$('body').on('click', 'input#cheque-balance', function(){
  // alert('แก้ไขรายการในตารางเท่านั้น');
  // $('body').find('input.row-balance:first').focus();
})

JS;
$this->registerJs($js,\yii\web\View::POS_END);
?>



<?php $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.2.5/css/tableexport.min.css');?>
<?php $this->registerJsFile('@web/js/js-xlsx-master/xlsx.core.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/Blob.js-master/Blob.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/FileSaver.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.3.5/js/tableexport.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>  
  



