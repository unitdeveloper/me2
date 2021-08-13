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

use admin\modules\Management\models\FunctionManagement;
 
$this->title = Yii::t('common','Sales report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sales report'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
	.input-group-addon{
		background-color: rgb(249,249,249) !important;
		border: 1px solid #999 !important;

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
</style>

<div class="sales-report" ng-init="Title='ยอดขายรายเดือน'">
<?php 
        $gridColumns = [
            ['class' => 'kartik\grid\SerialColumn'],
            
            //'update_date',
            [
              //'attribute' => 'posting_date',
            	'label' => Yii::t('common','Date'),
            	'format' => 'raw',
            	'value' => function($model)
            	{
            		// wordwrap($model->ship_address, 150, "<br/>\r\n") 
                    return date('d/m/Y',strtotime($model->posting_date));

            	}
            ],
            [
            	'label' => Yii::t('common','Customer'),
            	'format' => 'raw',
            	'contentOptions' => [
				        'style'=>'max-width:180px; min-height:100px; overflow: auto; word-wrap: break-word;'
				    ],
            	'value' => function($model)
            	{
            		
                    //return wordwrap($model->customer->name, 100, "<br/>\r\n"); 
                    return "<a href='#modal/{$model->cust_no_}' class='open-modal' data='{$model->cust_no_}' row-data='{$model->id}'>{$model->customer->name}</a>";

            	}
            ],
            //'customer.name',
            //'no_',
            [
            	'attribute' => 'no_',
            	'format' => 'raw',
            	'value' => function($model){
            		return Html::a($model->no_,['/accounting/posted/posted-invoice','id' => base64_encode($model->id)],['target' => '_blank','data-pjax'=>"0"]);
            	}
            ],
            [
            	'label' => Yii::t('common','Amount') .'/'. Yii::t('common','Accessories'),
            	'format' => 'raw',
            	'contentOptions' => ['class' => 'text-right'],
            	'value' => function($model)
            	{
            		$Total = FunctionManagement::getTotalBalance($model,[34,35,36,37,38,39,40,42,43,44,45,71],'Excepted');
            		return $Total;
            	}
            ],
            [
            	'label' => Yii::t('common','Amount') .'/'. Yii::t('common','LED'),
            	'format' => 'raw',
            	'contentOptions' => ['class' => 'text-right'],
            	'value' => function($model)
            	{
            		$Total = FunctionManagement::getTotalBalance($model,[34,35,36,37,38,39,40,42,43,44,45,71],'Equal');
            		return $Total;
            	}
            ],
            [
            	'label' => Yii::t('common','การชำระเงิน'),
            	'format' => 'raw',
            	'contentOptions' => [
				        'style'=>'max-width:200px; min-height:100px; overflow: auto; word-wrap: break-word;'
				      ],
            	'value' => function($model)
            	{
                // return $model->id;
                $Cheque = \common\models\Cheque::find()
                ->joinwith('banklist')
                ->where(['cheque.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(new Expression('FIND_IN_SET(:apply_to, apply_to)'))
                ->addParams([':apply_to' => $model->id]);

                if($Cheque->exists()){

                    $query = $Cheque->all();

                    $data = array();

                    foreach ($query as $key => $models) {

                      $dateCheque   = date('d/m/y',strtotime($models->posting_date));
                       
                      //#######
                      $models->bank_id = '#'.$models->bank_id;

                      if($models->type=='Cash') $models->bank_id = NULL;

                      $models->banklist->name   = $models->banklist->name;
                      if($models->type=='ATM') $models->banklist->name = Yii::t('common','Transfer');


                      $data[]       = "<a href='#{$models->id}' class='view-receipt' data='{$models->id}'>{$models->banklist->name} #{$dateCheque} {$models->bank_id} #{$models->balance}</a>";
                    }
                    //$dateCheque   = date('d/m/y',strtotime($models->posting_date.'+543 Year'));
                    

                    return implode("<br>\r\n",$data);

                }else {

                  return '';

                }
            		
            	}
            ],
            // [
            // 	'label' => Yii::t('common','Amount') .'/'. Yii::t('common','สวิทซ์ปลั๊ก'),
            // 	'format' => 'raw',
            // 	'value' => function($model)
            // 	{
            // 		return '';
            // 	}
            // ],
            
            
            // [
            // 	'label' => Yii::t('common','V-SAFE 2'),
            // 	'format' => 'raw',
            // 	'value' => function($model)
            // 	{
            // 		return '';
            // 	}
            // ],
            // [
            // 	'label' => Yii::t('common','SPD-R'),
            // 	'format' => 'raw',
            // 	'value' => function($model)
            // 	{
            // 		return '';
            // 	}
            // ],
            [
            	'label' => Yii::t('common','Remark'),
            	'format' => 'raw',
            	'contentOptions' => [
				        'style'=>'max-width:150px; min-height:100px; overflow: auto; word-wrap: break-word;'
				    ],
            	'value' => function($model)
            	{
                  $Cheque = \common\models\Cheque::find()
                  ->joinwith('banklist')
                  ->where(['cheque.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                  ->andWhere(new Expression('FIND_IN_SET(:apply_to, apply_to)'))
                  ->addParams([':apply_to' => $model->id]);

                  if($Cheque->exists()){

                      $query = $Cheque->all();

                      $data = array();

                      foreach ($query as $key => $models) {
                        
                        $data[]       = "<span style='color:red'>*  {$models->remark}</span>";
                      }
                      //$dateCheque   = date('d/m/y',strtotime($models->posting_date.'+543 Year'));
                      

                      return implode("<br>\r\n",$data);

                      //$models = $Cheque->one();
                      
                      //return "<p style='color:red'>{$models->remark}</p>";

                  }else {

                    return '';

                  }
            		
            	}
            ],
            

            // [
            // 	'label' => Yii::t('common','Amount'),
            // 	'format' => 'raw',
            // 	'contentOptions' => ['class' => 'text-right'],
            // 	'value' => function($model)
            // 	{
            // 		//return number_format(($model->sumLine - $model->discount) * $model->vat_percent);
            // 		$Total = FunctionManagement::getTotalBalance($model,'All','Equal');
            // 		return $Total;
            // 	}
            // ],


            
        ]; ?>



<div class="row btn-print">
  <div class="col-md-9  col-xs-12" >
        	
        
        <?php $form = ActiveForm::begin(['id' => 'invlice-search','method' => 'GET']); ?>
        <div class="row" style="margin-bottom: 10px;">

          <div class=" ">

            

            <div class="col-sm-6">  
            <label><?=Yii::t('common','Date Filter')?></label>
              <?php

              $startDate  = date('Y-m-').'01';
              $endDate    = date('Y-m-d');

              if(isset($_GET['fdate']))    
              {
                if($_GET['fdate']!='') $startDate   = date('Y-m-d',strtotime($_GET['fdate']));
              }


              if(isset($_GET['tdate'])){      
                if($_GET['tdate']!='') $endDate     = date('Y-m-d',strtotime($_GET['tdate']));

              }

$FromDate   = Yii::t('common','From Date');
$ToDate     = Yii::t('common','To Date');
// With Range
$layout = <<< HTML
	<span class="input-group-addon">$FromDate</span>
	{input1}
	{separator} 
	<span class="input-group-addon">$ToDate</span>
	{input2}
	<span class="input-group-addon kv-date-remove">
	    <i class="glyphicon glyphicon-remove"></i>
	</span>
HTML;

              echo DatePicker::widget([
              		'type' => DatePicker::TYPE_RANGE,
					'name' => 'fdate',
					'value' => $startDate,					
					'name2' => 'tdate',
					'value2' => $endDate,                  
					'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
					'layout' => $layout,
					'pluginOptions' => [
						'autoclose'=>true,
            'format' => 'yyyy-mm-dd'
						//'format' => 'dd-mm-yyyy'
					],
					 
              ]);

              ?>
            </div>

            <div class="col-sm-2"> 
               
	            <div class="input-group" >
                <label><?=Yii::t('common','Sales')?></label>
	                <?= Html::dropDownList('search-from-sale', null,
	                    					ArrayHelper::map(
				                    						SalesPeople::find()->all(),
				                                            	'code',function($model){
				                                                return '['.$model->code.'] '.$model->name. ' '.$model->surname;
				                                            	}
				                                            ),
					                    					[
					                    						'class'=>'form-control',
					                    						'prompt' => Yii::t('common','Every one'),
					                    						
					                    					],
					                    					['options' => [                        
														                      @$_GET['search-from-sale'] => ['selected' => 'selected']
														                    ],
														    ]
			                                             
	                						) 
	                ?>
	               
	            </div>
               
            </div>
             <div class="col-md-3  col-xs-6">
             
          <label><?=Yii::t('common','Customers')?></label>
                  <?php

                  // Html::dropDownList('search-customer', null,
                  //              ArrayHelper::map(
                     //                   Customer::find()->all(),
                     //                                 'code','name'
                     //                                ),
                      //                  [
                      //                    'data-live-search'=> "true",
                      //                    'class'=>'  form-control ',
                      //                    'prompt' => Yii::t('common','All customers'),
                                          
                      //                  ] 
                                                   
                  //            ) 
                  ?>

                  <?php 

                  echo Select2::widget([
              'name' => 'customer',
              'data' => ArrayHelper::map(
                                          Customer::find()->orderBy(['code' => SORT_ASC])->all(),
                                                        'id',function($model){ return '['.$model->code.'] '.$model->name; }
                                                      ),
              'options' => [
                  'placeholder' => Yii::t('common','Customer'),
                  'multiple' => false,
                  'class'=>'form-control  col-xs-12',
              ],
          ]);

          ?>
                 
               
            </div>

            <div class="col-sm-1  " style="padding-top: 25px;">
               
            	<button type="submit" class="btn btn-info"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            </div>
            
          </div><!-- /.col-sm-offset-6 -->
          
        </div><!-- /.row -->

          
        <div class="row">
          <div class="col-sm-6" >
           <?php 
            $payments   = NULL;
            $notpayment = NULL;

            if(isset($_GET['payment'])) {
              if($_GET['payment'] == 'payment')     $payments = 'checked="checked"';
              if($_GET['payment'] == 'not_payment') $notpayment = 'checked="checked"';
 

            }; 

            ?>
            <div class="col-xs-12 well" >
              <div class="col-xs-12">การชำระเงิน </div>
            <div class="col-xs-4">
             <label> <input type="radio" name="payment" value="all" checked="checked" > <?=Yii::t('common','ทั้งหมด')?> </label>
            </div>

            <div class="col-xs-4">

             <label> <input type="radio" name="payment" value="payment" <?=$payments?> > <?=Yii::t('common','ชำระเงินแล้ว')?> </label>
            </div>

            <div class="col-xs-4">
             <label> <input type="radio" name="payment" value="not_payment" <?=$notpayment?> > <?=Yii::t('common','ค้างชำระ')?> </label>
            </div>
           
          </div>
        </div>
      </div>
      <?php ActiveForm::end(); ?>

  
      </div>
      <div class="col-sm-3 col-xs-12 text-right" style="padding-top: 25px;">
         
        <?php
           echo ExportMenu::widget([
                      'dataProvider' => $dataProvider,
                      'columns' => $gridColumns,
                      'columnSelectorOptions'=>[
                          'label' => ' ',
                          'class' => 'btn btn-warning'
                      ],
                      'fontAwesome' => true,
                      'dropdownOptions' => [
                          'label' => 'Export All',
                          'class' => 'btn btn-primary'
                      ],
                       
                  ]); 
          ?>
      </div>
</div>
 

<div class="row"><hr></div>

<div class="row">
	 
  <div class="col-sm-12  " style="font-size: 16px;">

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

    <?php

    if(isset($_GET['search-from-sale'])){
      $SaleMan = \common\models\SalesPeople::find()->where(['code' => $_GET['search-from-sale']]);

      if($SaleMan->exists()){
        $models = $SaleMan->one();
        echo $models->name;
      }
      
    }

    ?>
  </div>
</div>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,

        'pjax' => true, 
        'columns' => $gridColumns,
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=> '<i class="fa fa-fast-backward" aria-hidden="true"></i>',   // Set the label for the "first" page button
            'lastPageLabel'=>'<i class="fa fa-fast-forward" aria-hidden="true"></i>',    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>15,    // Set maximum number of page buttons that can be displayed
            ],
         
    ]); ?>

</div>



<!-- Modal -->
<div id="chequeModal" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header bg-green">
        <button type="button" class="close " data-dismiss="modal">&times;</button>
        <h4 class="modal-title">การรับเช็ค/วางบิล</h4>
      </div>
      <div class="modal-body">
		<div class="ew-body-cheque">
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
      
     	<button type="button" name="Select" class="btn btn-success-ew getInv">
	    <i class="fa fa-check" aria-hidden="true"></i> <?=Yii::t('common','Select')?></button>
       

      </div>
    </div>

  </div>
</div>
 


<script type="text/javascript">

  $('body').on('click','a.view-receipt',function(){
    $('#chequeModal').modal('show'); 

    EditCheque($(this).attr('data'));
    $('.post-cheque').show();
    $('.getInv').attr('class','btn btn-warning post-cheque').html('<i class="fa fa-save" aria-hidden="true"></i> <?=Yii::t('common','Edit')?>'); 

  });

  function EditCheque($id){

    $('.modal-body').hide('slow');
    $('div.loading-content').show();
    $('div.ew-body-cheque').html('<br><br><br><br>');

 

    setTimeout(function(e){ 
      $.ajax({ 

            url:"index.php?r=accounting/cheque/update",
            type: "GET", 
            data: {id:$id},
            async:false,
            success:function(getData){
                

                $('div.loading-content').hide();
                $('div.ew-body-cheque').html(getData);
                $('.modal-body').slideToggle( "slow" );

                //$('.getInv').attr('class','btn btn-info post-cheque').html('<i class="fa fa-save" aria-hidden="true"></i> <?=Yii::t('common','Save')?>');
            }

      });
    }, 1000); 
  }

  // $('body').on('click','.post-cheque-edit',function() {

      
  //     $('div.loading-content').show();
      


  //     var form = $(this);
  //     var formData = form.serialize();
  //     $.ajax({
  //         url: 'index.php?r=/accounting/cheque/update',
  //         type: form.attr("method"),
  //         data: formData,
  //         success: function (getData) {
  //             //alert('Test');
  //             $('div.loading-content').hide();
  //             $('div.ew-body-cheque').html(getData);
             

  //             $('.post-cheque').hide();
              
  //         },
  //         error: function () {
  //             alert("Something went wrong");
  //         }
  //     });
  // }).on('submit', function(e){
  //     e.preventDefault();
  // });




  $('body').on('click','.close-modal-cheque',function(){
    window.location.reload();
  });


	$('body').on('click','.getInv',function(){
		getFilterPostedInv();
    
    //$(this).children('i').attr('class','fa fa-save');
	});


  $('body').on('click','button.post-cheque',function(){

    $('form[id="form-posted-inv"]').submit();

    $('button[data-dismiss="modal"]').addClass('close-modal-cheque'); 
    // var $obj = $('#form-posted-inv').serialize();

    // $.ajax({ 

    //         url:"index.php?r=accounting/cheque/create",
    //         type: "POST", 
    //         data: {post:$obj},
    //         async:false,
    //         success:function(getData){
                

    //             $('div.loading-content').hide();
    //             $('div.ew-body-cheque').html(getData);
    //             $('.modal-body').slideToggle( "slow" );

    //             $('.getInv').attr('class','btn btn-info post-cheque').html('<i class="fa fa-save" aria-hidden="true"></i> <?=Yii::t('common','Save')?>');
    //         }

    //   });
  });

	function getFilterPostedInv()
    {

      var $obj = [];

      if($('.ew-checked:checked').serialize()!='')
      {

          $('tr').each(function(i, el) {

            var iCheck = $(el).children('td').find('input.ew-checked');

            if (iCheck.is(':checked')) {
               $obj.push({id:iCheck.attr('row-data'),cust:iCheck.attr('data'),bal:iCheck.attr('bal')});
            }
            

          });

          //console.log($obj);
          renderChequeForm($obj);

        
      }else{
        
        swal(
          'Please select one of the options.',
          'That thing is still around?',
          'warning'
        );

        return false;
      }

    }

    function renderChequeForm($obj){

			
  		$('.modal-body').hide('slow');
  		$('div.loading-content').show();
  		$('div.ew-body-cheque').html('<br><br><br><br>');

  		setTimeout(function(e){ 
  			$.ajax({ 

  			      url:"index.php?r=accounting/cheque/create",
  			      type: "GET", 
  			      data: {data:$obj},
  			      async:false,
  			      success:function(getData){
  			          

  			          $('div.loading-content').hide();
  			          $('div.ew-body-cheque').html(getData);
  			          $('.modal-body').slideToggle( "slow" );

                  $('.getInv').attr('class','btn btn-info post-cheque').html('<i class="fa fa-save" aria-hidden="true"></i> <?=Yii::t('common','Save')?>');


  			      }

  			});
  		}, 1000); 
    }



	$('body').on('click','.open-modal',function(){

		$('#chequeModal').modal('show'); 

		loadCheque($(this).attr('row-data'),$(this).attr('data'));
    $('.post-cheque').show();
    $('.post-cheque').attr('class','btn btn-success-ew getInv').html('<i class="fa fa-check" aria-hidden="true"></i> <?=Yii::t('common','Select')?>');

     
		//$('.ew-body-cheque').html($(this).attr('data'));

	});




	function loadCheque($id,$cust)
	{
	  $('div.ew-body-cheque').html('<br><br><br><br>');	
	  $('.modal-body').hide();
	  $('div.loading-content').show();
	  setTimeout(function(e){ 
	    $.ajax({ 

	          url:"index.php?r=accounting/cheque/posted-inv-list",
	          type: "GET", 
	          data: {id:$id,cust:$cust},
	          async:false,
	          success:function(getData){
	              

	              $('div.loading-content').hide();
	              $('div.ew-body-cheque').html(getData);
	              $('.modal-body').slideToggle( "slow" );



	          }

	    });
	  }, 1000); 
	}


  $('body').on('click','.ew-delete-cheque',function(){
        if (confirm('ยืนยันการทำรายการ ! ')) { 
            var $id = $(this).attr('data');
            $.ajax({ 

                url:"index.php?r=accounting/cheque/delete&id="+$id,
                type: "POST", 
                data: {id:$id},
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
     })
</script>







