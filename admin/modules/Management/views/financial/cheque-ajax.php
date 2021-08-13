<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use kartik\widgets\ActiveForm;
 
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use common\models\SaleHeader;
use common\models\SalesPeople;
use common\models\Customer;

use kartik\widgets\Select2;
//use kartik\select2\Select2;
use yii\web\JsExpression;
use admin\modules\Management\models\FunctionManagement;

use common\models\ViewRcInvoice;
 
$this->title = Yii::t('common','Payment Receipt');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Payment Receipt'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

 

?>
<style type="text/css">
  
  @media print{
    .btn-print{
      display: none;
    }
    .remark span{
      color: red;
    }
    .pagination{
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
        <button type="button" name="Select" class="btn btn-primary-ew getInv">
        <i class="fa fa-check" aria-hidden="true"></i> <?=Yii::t('common','Select')?></button>      
      </div>
    </div>
  </div>
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
  $('body').find('.submit-form-search').attr('type','button');
  setTimeout(() => {
    $("body")
      .addClass("sidebar-collapse")
      .find(".user-panel")
      .hide();
  },500);
});


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
    EditCheque($(this).attr('data'));
    $('.post-cheque').show();
    $('.getInv').attr('class','btn btn-warning post-cheque').html('<i class="fa fa-save" ></i> {$Yii::t('common','Save')}'); 

  });

  function EditCheque(id){
    $('.modal-body').hide('slow');
    $('div.loading-content').show();
    $('div.ew-body-cheque').html('<br><br><br><br>');
    setTimeout(function(e){ 
      $.ajax({ 
            url:'index.php?r=accounting/cheque/update',
            type: 'GET', 
            data: {id:id},
            async:false,
            success:function(getData){
                $('div.loading-content').hide();
                $('div.ew-body-cheque').html(getData);
                $('.modal-body').slideToggle('slow');
            }
      });
    }, 1000); 
  }

  $('body').on('click','.close-modal-cheque',function(){
    //window.location.reload();
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
		$('#chequeModal').modal('show'); 
		loadCheque($(this).attr('row-data'),$(this).attr('data'));
    $('.post-cheque').show();
    $('.post-cheque').attr('class','btn btn-primary-ew getInv').html('<i class="fa fa-check" aria-hidden="true"></i> {$Yii::t('common','Select')}');
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
                $('div.ew-body-cheque').html(getData);
                setTimeout(function(e){ 
                  $('#chequeModal').modal('hide'); 
                  window.location.reload();
                }, 1000);                
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



$("body").on("keypress", 'input', function(e) {
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







