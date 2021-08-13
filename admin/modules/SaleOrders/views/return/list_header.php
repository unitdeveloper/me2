<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use kartik\widgets\ActiveForm;
use common\models\SalesPeople;
use common\models\Customer;

use kartik\widgets\Select2;


$this->title = Yii::t('common','Customer item sale');
?>
<style type="text/css">
    
 
    .text-page{
        counter-increment: page;        
        content: counter(page);
    }
    
  @media print{
    @page {
        margin-top:21px !important;
        size: A4 portrait; 
    }
    body{
        font-family: 'saraban', 'roboto', sans-serif; 
        font-size:10px !important;
    }

    body table{
        font-size:9px !important;
    }

    .text-page{
        counter-increment: page !important;        
        content: counter(page) !important;
    }

    .text-page:after{
        content: "Page " counter(page) " of " counter(pages); 
        /* content: counter(page);*/
    }

    
    .btn-print{
      display: none;
    }
    .remark span{
      color: red;
    }
    .pagination,
    .search-box,
    caption{
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

  .payment-detail-modal:hover,
  .invoice-detail:hover {
    background: #3fbbea !important;
  }

  .select2-results{
    font-family: 'tahoma';
  }

  @media (max-width: 767px) {
        .search-box{
           margin-top:50px;
        }

        #vat-change {
            margin-top: 10px;
        }

  }
</style>
<div class="row btn-print ">
  <div class="col-xs-12" >
        <?php $form = ActiveForm::begin(['id' => 'search-sale-return','method' => 'POST']); ?>
        <div class="row" style="margin-bottom: 10px;">
          <div class=" ">
            <div class="col-lg-4 col-md-6 col-sm-4">  
            <label><?=Yii::t('common','Date Filter')?></label>
              <?php

$FromDate   = Yii::t('common','From Date');
$ToDate     = Yii::t('common','To Date');
// With Range
$layout = <<< HTML
	<span class="input-group-addon">$FromDate</span>
	{input1}
 
	<span class="input-group-addon">$ToDate</span>
	{input2}
	<span class="input-group-addon kv-date-remove">
	    <i class="glyphicon glyphicon-remove"></i>
	</span>
HTML;

              echo DatePicker::widget([
              		'type' => DatePicker::TYPE_RANGE,
					'name' => 'fdate',
					'value' => Yii::$app->request->get('fdate') ? date('Y-m-d',strtotime(Yii::$app->request->get('fdate'))) : date('Y-m').'-01',					
					'name2' => 'tdate',
					'value2' => Yii::$app->request->get('tdate') ? date('Y-m-d',strtotime(Yii::$app->request->get('tdate'))) : date('Y-m-t'),                  
					'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
                    'layout' => $layout,
                    'options' => ['autocomplete'=>'off'],
                    'options2' => ['autocomplete'=>'off'],
					'pluginOptions' => [
						'autoclose'=>true,
                        'format' => 'yyyy-mm-dd',
						//'format' => 'dd-mm-yyyy'
					],
              ]);
              ?>
            </div>

            <div class="col-sm-5 col-xs-12">
              <div class="row">
                <div class="col-xs-8">
                    <label><?=Yii::t('common','Customers').' ('.Yii::t('common','Head Office').')'?> </label>
                    <?php 
                      $keys = 'customers&comp:'.Yii::$app->session->get('Rules')['comp_id'];
                      $customerList = Yii::$app->cache->get($keys);                  
                      if($customerList){
                        $customer = $customerList;
                      }else{
                        $customer = ArrayHelper::map(
                          Customer::find()
                          ->where(['or', 
                            ['id'       => 909], 
                            ['comp_id'  =>  Yii::$app->session->get('Rules')['comp_id']]
                          ])
                          ->andWhere(['status'=>'1'])
                          ->andWhere(['headoffice' => 1])
                          ->orderBy(['code' => SORT_ASC])
                          ->all(),
                                  'id',
                                  function($model){ 
                                    return '['.$model->code.'] '.$model->name; 
                                  }
                          );
                    
                        Yii::$app->cache->set($keys,$customer, 1);
                      }
                    ?>
                    <?= Select2::widget([
                        'name'  => 'customer',
                        'id'    => 'customer',
                        'data'  => $customer,
                        'options' => [
                            'placeholder' => Yii::t('common','Customer'),
                            'multiple' => false,
                            'class' => 'form-control',
                        ],
                        'pluginOptions' => [
                          'allowClear' => true
                        ],
                        'value' => Yii::$app->request->get('customer') ?  Yii::$app->request->get('customer') : ''
                    ]);
                  ?>
                </div>
                <div class="col-xs-4"> 
                  <label><?=Yii::t('common','Branch')?></label>
                  <select class="form-control  mb-10" name="branch-filter" >
                      <option value="1"><?=Yii::t('common','Head Office')?></option>
                      <option value="2"><?=Yii::t('common','Head Office')?> <?=Yii::t('common','And')?> <?=Yii::t('common','Branch')?></option>
                  </select> 
                  <small class="show-branch" style="display:none;">
                    <a href="#"><i class="fas fa-sitemap"></i> <?=Yii::t('common','Show brach')?></a>
                  </small>
                </div>
              </div>
              
              <div class="row">
                <div class="col-xs-12">
                  <div class="box box-solid box-info" id="branch-list" style="display:none;">
                    <div class="box-header with-border ">
                      <i class="fa fa-text-width"></i> 
                      <h3 class="box-title"> </h3>
                    </div> 
                    <div class="box-body"> </div> 
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-2 col-xs-12">
              <label><?=Yii::t('common','Vat')?></label>
              <select class="form-control  mb-10" name="vat-filter" >
                  <option value="0"><?= Yii::t('common','All') ?></option>
                  <option value="Vat">Vat</option>
                  <option value="No">No Vat</option>
              </select> 
              
            </div>     
            <div class="col-sm-1  col-xs-12 text-right" style="padding-top: 25px;">
            	<button type="button" class="btn btn-info btn-search-return" id="btn-search-return" data-key="all"><i class="fa fa-search" aria-hidden="true"></i> <?= Yii::t('common','Search')?></button>              
            </div>
            
            <div class="col-sm-12" style="margin-top: 10px;">
              <div class="input-group" >
                <label><?=Yii::t('common','Sales')?></label>
                <?= Html::dropDownList('search-from-sale', null,
                ArrayHelper::map(
                        SalesPeople::find()
                        ->where(['status'=> 1])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->orderBy(['code' => SORT_ASC])
                        ->all(),
                                'id',function($model){
                                    return '['.$model->code.'] '.$model->name. ' '.$model->surname;
                                }
                                ),
                        [
                            'class'=>'form-control',
                            'prompt' => Yii::t('common','Every one'),
                            'options' => [                        
                            @$_GET['search-from-sale'] => ['selected' => 'selected']
                            ],
                        ]                                     
                    ) 
                  ?>          
              </div>   
            </div>
          </div><!-- /.col-sm-offset-6 -->
          
        </div><!-- /.row -->
      <?php ActiveForm::end(); ?>
    </div>
     
</div>

<?php
$Yii        = 'Yii';
$firstDate  = date('Y-m-01');
$now        = date('Y-m-d');


$jsx=<<<JS


  let state = {
      fdate: $('input[name="fdate"]').val(),
      tdate: $('input[name="tdate"]').val(),
      vat:$('select[name="vat-filter"]').val(),
      custList:[],
      branch:$('select[name="branch-filter"]').val(),
      getBom:0,
      saleId:$('select[name="search-from-sale"]').val()
  };

  const changeState = async () => {
    let fdate   = $('input[name="fdate"]').val();
    let tdate   = $('input[name="tdate"]').val();
    let cust    = $('select[name="customer"]').val();
    let vat     = $('select[name="vat-filter"]').val();
    let custList= [parseInt($('select#customer').val())];
    let branch  = $('select[name="branch-filter"]').val();
    let getBom  = 0;
    let saleId  = $('select[name="search-from-sale"]').val();

    await $('input:checkbox[name="customer[]"]:checked').each(function() {
        custList.push(parseInt($(this).val()));
    });

    return state = {
      fdate:fdate,
      tdate:tdate,
      cust:cust,
      vat:vat,
      custList:custList,
      branch:branch,
      getBom:getBom,
      saleId:saleId
    }
  }

  $('body').on('change', 'select[name="search-from-sale"]', async function(){
     
    await changeState().then(res => {
      console.log(state);
    });
   
  });

  $('body').on('change', 'input', async function(){
     
    await changeState().then(res => {
      console.log(state);
    });
   
  });

  $('body').on('change', 'select', async function(){
     
    await changeState().then(res => {
      console.log(state);
    });
   
  });

    


  const renderBranchList = (data, callback) => {
    let body = ``
    data.raws.map((model, keys) => {
        body+= `
            <tr>
                <td>` +(keys + 1) + `</td>
                <td><a href="?r=customers%2Fcustomer%2Fview&id=`+model.id+`" target="_blank">` + model.branch + `</a> </td>
                <td>` + model.name + ` ` + (model.head == 1 ? '<span class="text-yellow"><i class="fas fa-star"></i></span>' : '') + ` </td>
                <td class="text-center"><input type="checkbox" checked  name="customer[]" value="` + model.id + `"/> </td>
            </tr>
        `;
    })

    let table = `
        <table class="table table-bordered" id="export_table">
            <thead>
                <r class="bg-gray">
                    <th>#</th>
                    <th>{$Yii::t('common','Branch')}</th>
                    <th>{$Yii::t('common','Name')}</th>
                    <th class="check-all-branch">
                        <label for="check-all-branch">
                            <input type="checkbox" id="check-all-branch" checked/> 
                            {$Yii::t('common','All')} 
                        </label>
                    </th>
                </r>
            </thead>
            <tbody>` + body + `</tbody>
        </table>
    `;

    callback(table);

    var ExportTable = $('#export_table').DataTable({
            "paging": false,
            "searching": true,
            "info" : false,
            "order": [[ 2, "desc" ]]
        });
  }

  const changeBranch = async (cust) => {
    fetch("?r=customers%2Fcustomer%2Fbranch-list", {
            method: "POST",
            body: JSON.stringify({cust:cust}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {
                    
            if(res.status===403){
                $.notify({
                    // options
                    icon: 'fas fa-clock',
                    message: res.message
                },{
                    // settings
                    type: 'warning',
                    delay: 10000,
                    z_index:3000,
                });    
                $.notify(
                    {
                        message: res.calculating.years + ' : ' + parseInt(res.percent) + '%'
                    },
                    {
                        type: 'info',
                        delay: 10000,
                        z_index:3000
                    }
                );            
            }else{
                renderBranchList(res, html => {
                    $('#branch-list .box-title').html(res.name);
                    $('#branch-list .box-body').html(html);
                });

                changeState().then(res => {
                      console.log(state);
                    });
                
            }
        })
        .catch(error => {
            console.log(error);
        });
  }


  $('body').on('change', 'select[name="branch-filter"]', function(){
    let val = parseInt($(this).val());
    if(val===1){
        $('.show-branch').hide();
        $('#branch-list').hide();
        $('#branch-list .box-title').html('');
        $('#branch-list .box-body').html('');
        changeState().then(res => {
                      console.log(state);
                    });
    }else{
        $('.show-branch').show();        
        let cust    = $('select[name="customer"]').val();
        changeBranch(cust);
    }
    
  });


  $('body').on('click', '.show-branch', function(){
    $('#branch-list').toggle();
  })

  $('body').on('click', '#check-all-branch', function(){
      $('input:checkbox[name="customer[]"]').not(this).prop('checked', this.checked);
  })

JS;
$this->registerJS($jsx,\yii\web\View::POS_END);
?>