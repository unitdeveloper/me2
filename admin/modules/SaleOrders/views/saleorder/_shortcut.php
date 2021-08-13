<?php

use yii\helpers\Html;
 
use kartik\widgets\DatePicker;

?>
<style>
.loading-sale,
.loading-production,
.loading-invoice{
    transition: width 2s;
}

</style>
<div class="row">
    
    <div class="col-sm-4">
        <div class="box box-solid">
            <div class="box-header with-border bg-danger">
              <i class="fas fa-envelope text-red"></i>

              <h3 class="box-title">ส่งใบงาน</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body show-release-table">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
    <div class="col-sm-4">
        <div class="box box-solid">
            <div class="box-header with-border bg-warning">
              <i class="fas fa-hourglass-half text-aqua"></i>

              <h3 class="box-title"><?=Yii::t('common','Waiting Confirm')?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body show-waiting-table">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
    <div class="col-sm-4">
        <div class="box box-solid">
            <div class="box-header with-border bg-success">
                <i class="fa fa-check text-success"></i>

                <h3 class="box-title"><?=Yii::t('common','Confirmed')?></h3>
                <a href="#" class="reload-section pull-right" ><i class="fas fa-sync-alt"></i> Refresh</a>
            </div>
            <!-- /.box-header -->
            <div class="box-body show-confirm-table">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
    <!-- <div class="col-sm-3">
        <div class="box box-solid">
            <div class="box-header with-border bg-green">
                <i class="far fa-file-pdf text-red"></i>
                <h3 class="box-title"><?=Yii::t('common','Invoiced')?></h3>
            </div>
             
            <div class="box-body show-invoiced-table">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            
        </div>
    </div> -->
</div>


<div class="modal fade modal-full" id="modal-sale-order-action" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Description')?></h4>
            </div>
            <div class="modal-body font-roboto" style="background-color: #ecf0f5 !important; margin-top: -5px;">
                <div class="row ">
                    <div class="col-xs-3">
                        <div class="mt-10 mb-10">
                            No : 
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="mt-10 mb-10">
                            <span id="so-no">_</span>                              
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3">
                        <div class="mt-10 mb-10">
                            <?=Yii::t('common','Customer')?> :
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="mt-10 mb-10">
                            <span id="cust-name">_</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="mt-10 mb-10 ">
                            <?=Yii::t('common','Transport')?>
                        </div>
                    </div>
                    <div class="col-xs-5">
                        <div class="mt-10 mb-10"  style="margin-top: 27px;">
                            <div class="row">
                                <div class="col-md-8 col-sm-6">
                                    <span class="transport">
                                        <?=\kartik\widgets\Select2::widget([
                                                'name' => 'transport',
                                                'data' => \yii\helpers\ArrayHelper::map(
                                                    \common\models\TransportList::find()
                                                    ->where(['comp_id'  => Yii::$app->session->get('Rules')['comp_id']])
                                                    ->orderBy(['name'   => SORT_ASC])
                                                    ->all(),
                                                    'id',function($model){ return $model->name; }),
                                                'options' => [
                                                    'placeholder' => Yii::t('common','Transport'),
                                                    'multiple' => false,
                                                    'class'=>'form-control ',
                                                    'id' => 'transport'
                                                ],
                                                'pluginOptions' => ['allowClear' => true],
                                                //'value' => 0
                                            ]);

                                            

                                            
                                        ?>
                                    </span>
                                </div>
                                <div class="col-md-4 col-sm-6" style="margin-top: -27px;"> 
                                    <label for="ship_date"><?=Yii::t('common','Ship Date')?></label> 
                                    <?=DatePicker::widget([
                                                'type'      => DatePicker::TYPE_COMPONENT_APPEND,
                                                'name'      => 'ship_date',
                                                'options'   => ['id'    => 'ship_date'],                                            
                                                'value'     => date('Y-m-d'),  
                                                'removeButton' => false,     
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'yyyy-mm-dd'
                                                ]                                            
                                        ]);
                                    ?>  
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-xs-12  mt-5">
                                    
                                    <div class="panel panel-info panel-shipment">
                                         
                                        <div class="panel-body">                                         
                                    
                                            <div class="row">
                                                <div class="col-sm-12 mt-5">
                                                    <label for="ship_name"><?=Yii::t('common','Customer Name')?></label> 
                                                    <input type="text" name="ship_name" id="ship_name" class="form-control" />

                                                    <label for="ship_address" class="mt-5"><?=Yii::t('common','Ship Address')?></label>
                                                    <input type="text" name="ship_address" id="ship_address" class="form-control" />

                                                    <label for="ship_phone" class="mt-5"><?=Yii::t('common','Ship Phone')?></label>
                                                    <div class="row">
                                                        <div class="col-xs-6">                                                            
                                                            <input type="text" name="ship_phone" id="ship_phone" class="form-control" />
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <button type="button" class="btn btn-success-ew save-modify" style="display:none;"><i class="far fa-save"></i> <?=Yii::t('common','Save')?></button>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                                
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            
                        </div>                        
                    </div>
                    <div class="col-xs-4">
                        <div class="row">
                            <div class="col-xs-5">
                                                    
                                
                                <label for="pre_inv_no"><?=Yii::t('common','Tax Invoice')?></label>
                                    
                                <div class="input-group">
                                    <input type="text" value="" name="pre_inv_no" id="pre_inv_no" class="form-control" style="background-color: #fbe4e4; min-width: 118px;" />  
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default type-docment-click" data-text="GN">GN</button>
                                    </span>
                                </div>                                   
                               
                                
                               
                                
                            </div>
                            <div class="col-xs-7"> 
                                <label for="posting_date"><?=Yii::t('common','Posting Date')?></label> 
                                <?=DatePicker::widget([
                                            'type'      => DatePicker::TYPE_COMPONENT_APPEND,
                                            'name'      => 'posting_date',
                                            'options'   => ['id'    => 'posting_date'],                                            
                                            'value'     => date('Y-m-d'),       
                                            'removeButton' => false,  
                                            'pluginOptions' => [
                                                'autoclose'=>true,
                                                'format' => 'yyyy-mm-dd'
                                            ]                                            
                                    ]);
                                ?>  
                            </div>
                        </div>
                        <div class="mt-10">
                            <button type="button" class="btn btn-info-ew btn-lg pull-right" id="print-all-modal"   target="_blank"><i class="fa fa-print"></i> Print All</button>
                        </div>
                    </div>
                </div>

                 
                
                <div class="row">
                    <div class="col-sm-3">
                        <div class="box box-solid box-info ">
                            <div class="box-header with-border">
                            <i class="fas fa-cogs"></i>
                                <h3 class="box-title"><?=Yii::t('common','Production')?></h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body show-production-table">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <div class="box box-solid box-primary   packing-box">
                            <div class="box-header with-border">
                                <i class="fas fa-cubes"></i>
                                <h3 class="box-title"><?=Yii::t('common','Packing')?></h3>
                                <li class="pull-right" style="list-style-type: none;">
                                    <a href="#" class="text-muted btn btn-default-ew btn-xs create-only-shipment"><i class="fa fa-plus"></i></a>
                                </li>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body show-shipment-table">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                    
                    <div class="col-sm-4">
                        <div class="box box-solid box-warning  ">
                            <div class="box-header with-border">
                                <i class="fas fa-file-invoice text-red"></i>
                                <h3 class="box-title"><?=Yii::t('common','Tax Invoice')?></h3>
                                <li class="pull-right" style="list-style-type: none;">
                                    <a href="#" class="text-muted btn btn-default-ew btn-xs create-only-bill"><i class="fa fa-plus"></i></a>
                                </li>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body show-invoice-table">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                <a href="#" class="btn btn-danger" id="btn-create-invoice"><i class="fas fa-forward"></i> เปิดบิล (อัตโนมัติ)</a>
            </div>
        </div>
    </div>
</div>

 
<div class="modal fade" id="modal-print-all">
    <div class="modal-dialog"  style="width:90%;">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-print"></i> PRINT ALL</h4>
            </div>
            <div class="modal-body" >
            <div class="row">
            <div class="col-sm-4">                
                <button type="button" onclick="window.frames['iPrint-SO'].print();" class="btn btn-info-ew btn-flat"><i class="fa fa-print"></i> <?=Yii::t('common','Sale Order')?></button>
                <button type="button" onclick="document.getElementById('iPrint-SO').contentDocument.location.reload(true);" class="btn btn-primary-ew btn-flat"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="col-sm-4">
                <button type="button" onclick="window.frames['iPrint-SHIP'].print();" class="btn btn-primary-ew btn-flat"><i class="fa fa-print"></i> <?=Yii::t('common','Delivery ticket')?></button>
                <button type="button" onclick="document.getElementById('iPrint-SHIP').contentDocument.location.reload(true);" class="btn btn-primary-ew btn-flat"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="col-sm-4">
                <button type="button" onclick="window.frames['iPrint-INV'].print();" class="btn btn-danger-ew btn-flat"><i class="fa fa-print"></i> <?=Yii::t('common','Tax Invoice')?></button>
                <button type="button" class="btn btn-primary-ew btn-flat reload-iframe-inv"><i class="fa fa-refresh"></i></button>
            </div>
            </div>
                <div class="row" >
                    <div class="col-sm-4 print-so-section mt-1" style="height:59vh !important;"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
                    <div class="col-sm-4 ">
                        <div class="print-ship-section mt-1"  style="height:25vh !important;"></div>
                        <br >
                        <button type="button" onclick="window.frames['iPrint-TR'].print();" class="btn btn-primary-ew btn-flat"><i class="fa fa-print"></i></button>
                        <a target="_blank" class="btn btn-primary-ew btn-flat print-delivery"><i class="fa fa-print"></i> <?=Yii::t('common','Delivery')?></a>
                        <button type="button" onclick="document.getElementById('iPrint-TR').contentDocument.location.reload(true);" class="btn btn-primary-ew btn-flat"><i class="fa fa-refresh"></i></button>
                
                        <div class="print-ship-section-pack mt-1"  style="height:25vh !important;"></div>
                        
                    </div>
                    <div class="col-sm-4 print-inv-section mt-1" style="height:59vh !important;"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
                </div>
            </div>
            <div class="modal-footer bg-primary">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
 
            </div>
        </div>
    </div>
</div>

 
<div class="modal fade" id="modal-modify-sale-header" data-key="" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Sales Order')?></h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-xs-2  text-right"><div class="mt-5"></div></div>
                    <div class="col-xs-5">
                        <input type="text" class="form-control  " name="SO" value="" readonly/>
                    </div>
                    <div class="col-xs-5">
                        <?=DatePicker::widget([
                                    'type'      => DatePicker::TYPE_COMPONENT_APPEND,
                                    'name'      => 'order_date',
                                    'options'   => ['id'    => 'order_date'],                                            
                                    'value'     => date('Y-m-d'),  
                                    'removeButton' => false,     
                                    'pluginOptions' => [
                                        'format' => 'yyyy-mm-dd'
                                    ]                                            
                            ]);
                        ?>
                        <input type="text" class="form-control  " name="customer_name" value="" readonly/>                       
                    </div>
                </div>

                <hr class="style19"/>
                <div class="row">
                            <div class="col-xs-2  text-right"><div class="mt-5"><?=Yii::t('common','Payment term')?></div></div>
                            <div class="col-xs-5">
                                <select id="payment_term" class="form-control" name="payment_term">
                                    <option value="0">เงินสด</option>
                                    <option value="7">7 วัน</option>
                                    <option value="15">15 วัน</option>
                                    <option value="30" selected="">30 วัน</option>
                                    <option value="45">45 วัน</option>
                                    <option value="60">60 วัน</option>
                                    <option value="90">90 วัน</option>
                                </select>
                            </div>
                            <div class="col-xs-5">
                                <?=DatePicker::widget([
                                                    'type'      => DatePicker::TYPE_COMPONENT_APPEND,
                                                    'name'      => 'paymentdue',
                                                    'options'   => ['id'    => 'paymentdue'],                                            
                                                    'value'     => date('Y-m-d'),  
                                                    'removeButton' => false,     
                                                    'pluginOptions' => [
                                                        //'autoclose'=>true,
                                                        'format' => 'yyyy-mm-dd'
                                                    ]                                            
                                            ]);
                                        ?>
                            </div>
                </div>

                <hr class="style19"/>
                <div class="row">
                    <div class="col-xs-2 text-right"><div class="mt-5"><?=Yii::t('common','Discount')?></div></div>
                    <div class="col-xs-5">
                        <div class="input-group">
                            <input type="text" step="any" id="discount-percent" name="percent_discount" value="0.00" data="0" class="form-control money" style="text-align: right;"> 
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                    <div class="col-xs-5">
                        <div class="input-group">
                        <input type="text" step="any" id="discount-amount" name="discount" value="0.00" readonly data="0" class="form-control money" style="text-align: right;" placeholder="0">
                            <span class="input-group-addon"><?=Yii::t('common','$')?></span>
                        </div>
                        
                    </div>
                            
                </div>

                <hr class="style19"/>
                <div class="row">
                    <div class="col-xs-2  text-right"><div class="mt-5"><?=Yii::t('common','Tax')?></div></div>
                    
                    <div class="col-xs-5">
                        <select name="vat_percent" id="vat_percent" class="form-control">
                        <?php 
                            $VatType = \common\models\VatType::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();
                            foreach ($VatType as $key => $vat) {
                                echo "<option value='{$vat->vat_value}'>{$vat->name}</option>";
                            }
                        ?>
                        </select>
                    </div>
                    <div class="col-xs-5">
                        <select id="include_vat" class="form-control" name="include_vat">
                            <option value="0" selected="">Vat ใน (ราคาสินค้ารวมภาษีฯแล้ว)</option>
                            <option value="1">Vat นอก (ราคาสินค้าไม่รวมภาษีฯ)</option>
                        </select>
                    </div>
                </div>
                
                
               

                <hr class="style19"/>
                <div class="row">
                    <div class="col-xs-2 text-right"><div class="mt-5"><?=Yii::t('common','Remark')?></div></div>
                    <div class="col-xs-10">
                    <textarea id="remark" class="form-control" name="remark" rows="2" placeholder="<?=Yii::t('common','Remark')?>"></textarea>
                </div>
                </div>
            </div>
            <div class="modal-footer bg-teal">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                <button type="button" class="btn btn-success salve-and-close" style="display: none;"><i class="fa fa-save"></i> <?=Yii::t('common','Save')?></button>
            </div>
        </div>
    </div>
</div>



<?=$this->render('_shortcut_script')?>
<?php
$Yii = 'Yii';

$js =<<<JS

    

    // โหลดเองเมื่อเลิกจับเมาส์
    var timer;
    var timeCall = 180000;
    $(document).on('mousemove', function(e){
      clearInterval(timer);

      timer = setInterval(function() {
                loadPage();
            }, timeCall);
    });

    $(document).on('keyup', function(e){
      clearInterval(timer);

      timer = setInterval(function() {
                loadPage();
            }, timeCall);
    });


    $(document).ready(function(){  
        loadPage();        
    });


    $('body').on('click', '.reload-section', function(){
        loadPage();  
    });


    $('body').on('click', '#print-all-modal', function(){
        $('#modal-print-all').modal("show");
        let soId = $(this).closest('#modal-sale-order-action').attr('data-key');
 
    });

    const getSaleHeader = (obj, callback) => {
        $('.loading').show();
        fetch("?r=SaleOrders/saleorder/get-header-ajax", {
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
            setTimeout(() => {
                $('.loading').hide();
            }, 800);
        })
        .catch(error => {
            console.log(error);
        });
    }

    const updateSaleHeader = (obj, callback) => {
        fetch("?r=SaleOrders/saleorder/update-header-ajax", {
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

    $('body').on('keyup', '#discount-percent', function(){
        let percent = $(this).val();
        let sumline = $(this).closest('#modal-modify-sale-header').attr('data-sumline');
        let discount = percent * sumline /100;
        console.log(discount);
        $('#discount-amount').val(discount)
    })

    $('body').on('change', '#vat_percent', function(){

        let id  = $(this).closest('#modal-modify-sale-header').attr('data-key');
        let val = parseInt($(this).val());
        if(val > 0){
            $('#include_vat').closest('div').fadeIn();
            $('#include_vat').val(0);
        }else{
            $('#include_vat').closest('div').fadeOut();
            $('#include_vat').val(null);
        }

        
    });

    $('body').on('click', '.modify-sale-header', function(){
        let el  = $(this);
        let id  = $(this).closest('tr').attr('data-key');
        getSaleHeader({id:id}, res => {
            $('#modal-modify-sale-header').modal("show").attr('data-key', id).attr('data-sumline', res.raw.sumline);
            $('#modal-modify-sale-header input[name="SO"]').val(res.raw.no);
            $('#modal-modify-sale-header input[name="customer_name"]').val(res.raw.customer_name);

            $('#modal-modify-sale-header #payment_term').val(res.raw.payment_term);
            $('#modal-modify-sale-header #paymentdue').val(res.raw.paymentdue).trigger('change');

            $('#modal-modify-sale-header #order_date').val(res.raw.order_date);
            $('#modal-modify-sale-header #order_date').val(res.raw.order_date).trigger('change');

            $('#modal-modify-sale-header #vat_percent').val(res.raw.vat_percent);
            if(res.raw.vat_percent > 0){
                $('#include_vat').closest('div').show();
            }else{
                $('#include_vat').closest('div').hide();
            }
            $('#modal-modify-sale-header #include_vat').val(res.raw.include_vat);

            $('#modal-modify-sale-header #discount-percent').val(res.raw.percent_discount);
            $('#modal-modify-sale-header #discount-amount').val(res.raw.discount);

            $('#modal-modify-sale-header #remark').val(res.raw.remark);
            $('.salve-and-close').hide();
        })
        
      
    });

    $('body').on('click', '.salve-and-close', function(){
 
        let el   = $(this);
        let data = {
                    raw : [
                            {
                                value : $('#payment_term').val(),
                                field : 'payment_term'
                            },
                            {
                                value : $('#paymentdue').val(),
                                field : 'paymentdue'
                            },
                            {
                                value : $('#order_date').val(),
                                field : 'order_date'
                            },
                            {
                                value : $('#vat_percent').val(),
                                field : 'vat_percent'
                            },
                            {
                                value : $('#include_vat').val(),
                                field : 'include_vat'
                            },
                            {
                                value : $('#discount-percent').val(),
                                field : 'percent_discount'
                            },
                            {
                                value : $('#discount-amount').val(),
                                field : 'discount'
                            },
                            {
                                value : $('#remark').val(),
                                field : 'remark'
                            }
                        ],
                id: $(this).closest('#modal-modify-sale-header').attr('data-key')           
            };
        // เข้าระหัสก่อนส่ง {param: btoa(JSON.stringify(data))}
        $('.loading').show();
        updateSaleHeader(data, res => {

            if(res.status==200){
                setTimeout(() => {
                    $('.loading').hide();
                }, 1000);
                
                $('#modal-modify-sale-header').modal("hide");
            }
        });
       

    });

    $('body').on('change', '#modal-modify-sale-header input, #modal-modify-sale-header select, #modal-modify-sale-header textarea', function(){
        $(this).closest('div').addClass('has-success');
        $('.salve-and-close').show();
    });
    
    $('body').on('click', '.reload-iframe-inv', function(){
        
        $('body').find('.print-inv-section').append('<i class="fa fa-refresh fa-spin"></i>');
        setTimeout(() => {
            $('body').find('.print-inv-section .fa-refresh').remove();
            document.getElementById('iPrint-INV').contentDocument.location.reload(true);

            //$('body').find('#iPrint-INV').contentDocument.location.reload(true);
        }, 1000);
        

    })
JS;

$this->registerJs($js,Yii\web\View::POS_END);
?>

