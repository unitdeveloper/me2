<?php

use yii\helpers\Html;
use kartik\widgets\SwitchInput;

?>
<div class="row footer-zone" style="margin-bottom:45px; display:none;">
    <div class="col-md-6">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group highlight-addon field-remark">
                    <label class="control-label" for="remark">หมายเหตุ</label>
                    <textarea id="remark" class="form-control" name="remark" rows="6" ></textarea>                    
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 ">
        <div class=" ">
            <div class=" " style="margin-top: 25px; border: 1px solid #ccc;">
                <table class="table" style="margin-bottom: 0px;">
                    <tbody>
                        <tr class="panel-heading bg-gray">
                            <th colspan="2">รวมเป็นเงิน:</th>
                            <td align="right" class="before-discount"> </td>
                        </tr>
                        <tr class="text-primary">
                            <th> ส่วนลด: </th>
                            <td>
                                <div class="form-group highlight-addon field-percent_discount">
                                    <div class="input-group">
                                        <input type="number" id="percent_discount" class="text-right form-control" name="percent_discount" placeholder="0" />
                                        <span class="input-group-addon">
                                            <span class="">
                                                <i class="fa fa-percent" aria-hidden="true"></i>
                                            </span>
                                        </span>
                                    </div>                                 
                                </div>
                            </td>
                            <td align="right" style="padding-right: 0px; ">
                                <div class="form-group highlight-addon field-discount">
                                    <input type="number" id="discount" class="text-right no-border form-control " name="discount" value="0.00"  placeholder="0"
                                        style="background-color: transparent; font-size:15px; margin-right: -15px;" >                                    
                                </div>
                            </td>
                        </tr>
                        <tr class="text-primary">
                            <th colspan="2">หลังหักส่วนลด: </th>
                            <td class="text-right after-discount">0</td>
                        </tr> 
                        <tr class="text-success before-vat-row" style="display: none;">
                            <th colspan="2">ยอดก่อนรวมภาษี </th>
                            <td class="text-right">
                                <span id="ew-before-vat">0</span>
                            </td>
                        </tr>
                        <tr class="text-success">
                            <td style="width: 150px;">
                                <div>  ภาษีมูลค่าเพิ่ม <span class=" ">0</span> %
                                    <select name="vat_percent" class="form-control" id="vat_percent"> 
                                        <option  value="7"  >Vat</option> 
                                        <option  value="0"  selected="selected">No Vat</option> 
                                    </select>
                                </div>
                            </td>
                            <th>
                                <div class="show-vat-type" style="margin-top: 20px; display: none;">
                                    <select name="include_vat" class="form-control" id="include_vat">                                        
                                        <option value="0" class=" ">Vat ใน (ราคาสินค้ารวมภาษีฯแล้ว)</option> 
                                        <option value="1" class=" " selected="selected">Vat นอก (ราคาสินค้าไม่รวมภาษีฯ)</option>                                         
                                    </select>
                                </div>
                            </th>
                            <td class="text-right" id="ew-after-vat">0.00 </td>
                        </tr>
                        <tr class="bg-info">
                            <th colspan="2">จำนวนเงิน รวมทั้งสิ้น: </th>
                            <td class="text-right subtotal">0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default" style="margin-top:20px;">
            <div class="panel-body">
                <div class="row">
                    <div class=" ">
                        <div class="col-xs-9">
                            <div class="form-group highlight-addon field-withholdtaxswitch">
                                <label class="control-label" style="position:absolute; margin-left:70px;"
                                    for="withholdtaxswitch">หัก ณ ที่จ่าย</label>

                                <div class="form-group">
                                    <?php echo SwitchInput::widget([
                                        'name' => 'withholdtaxswitch',
                                        'id' => 'withholdtaxswitch', 
                                        'items' => [
                                            ['label' => 'Low', 'value' => 1],
                                        ],
                                        'pluginOptions' => ['size' => 'mini'],
                                        'labelOptions' => ['style' => 'font-size: 12px'],
                                        'pluginEvents' => [
                                            "switchChange.bootstrapSwitch" => "function() { 
                                                if($(this).is(':checked')){
                                                    $('.tax-toggle').fadeIn();
                                                }else{
                                                    $('.tax-toggle').fadeOut();
                                                    $('select#withholdtax').val(0).change();
                                                }  
                                            }",
                                        ]
                                    ]);
                                    ?>
                                     
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-xs-3 tax-toggle">
                            <select name="withholdtax" class="form-control" id="withholdtax">
                                <option value="0" class=" " selected="selected">0</option>
                                <option value="0.5" class=" ">0.5 </option>
                                <option value="0.75" class=" ">0.75 </option>
                                <option value="1" class=" ">1 </option>
                                <option value="2" class=" ">2 </option>
                                <option value="3" class=" ">3 </option>
                                <option value="5" class=" ">5 </option>
                                <option value="10" class=" ">10 </option>
                                <option value="15" class=" ">15 </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row tax-toggle">
                    <div class="col-xs-12">
                        <div class="bg-default">
                            <div class="row">
                                <div class="col-xs-8">
                                    หักภาษี ณ ที่จ่าย <span class="">0</span> %
                                </div>
                                <div class="col-xs-4 text-right">
                                    <span  class="after_withholdtax">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group highlight-addon field-total-balance">
                    <input type="hidden" id="total-balance" class="form-control" name="balance" value="0.00" readonly="" style="background: transparent; color: rgb(0, 0, 0);" autocomplete="off" data="0">
                </div>
            </div>
            <div class="panel-footer bg-dark">
                <div class="row">
                    <div class="col-xs-8">
                        ยอดชำระ
                    </div>
                    <div class="col-xs-4 text-right">
                        <span class="grandTotalPayment" style="font-size:20px;">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
$Yii    = 'Yii';
$js     =<<<JS
   
$('body').on('click', 'input#discount', function(){
    $(this).select().focus();
});

$('body').on('change', 'input#discount', function(){
    $('#percent_discount').val('');
    totalSummary($('body').find('.before-discount').attr('value'));    
});

$('body').on('change', 'input#percent_discount', function(){
    let total       = $('body').find('.before-discount').attr('value');
    let discount    = (total * $(this).val())/ 100;
                      $('body').find('input#discount').val(discount.toFixed(2));
    setTimeout(() => {
        totalSummary(total);
    }, 200);    
});

$('body').on('change', 'select#vat_percent, select#include_vat, select#withholdtax, input[name="no"], input[name="inv_date"], input[name="ext_document"]', function(){
    totalSummary($('body').find('.before-discount').attr('value'));
});

$('body').on('click', '#withholdtaxswitch', function(){
    if($('input#withholdtaxswitch').is(':checked')){
        $('.tax-toggle').fadeIn();
    }else{
        $('.tax-toggle').fadeOut();
        $('select#withholdtax').val(0).change();
    }    
})

$(document).ready(function(){
    let header = localStorage.getItem('payment-header') ? JSON.parse(localStorage.getItem('payment-header')) : [];
    
    $('input#discount').val(header.discount);
    $('#vat_percent').val(header.vat);
    $('#include_vat').val(header.include_vat);
    $('#withholdtax').val(header.withholdingTax);
    $('#remark').val(header.remark);
    $('input[name="no"]').val(header.inv_no);
    $('input[name="inv_date"]').val(header.inv_date);
    $('input[name="ext_document"]').val(header.ext_doc);
    
    if(header.withholdingTax > 0){
        $('input#withholdtaxswitch').prop('checked', true);
    }else{
        $('input#withholdtaxswitch').prop('checked', false);
    }
})
JS;
$this->registerJs($js,\yii\web\View::POS_END);
?>
  