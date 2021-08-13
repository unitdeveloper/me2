<?php

use admin\modules\SaleOrders\models\FunctionSaleOrder;
 
$Fnc = new FunctionSaleOrder();

$SumTotal           = $model->sumtotal;
 
?>

 <div class="row panel panel-default" >
    <table class="table" >
        <tr style=" background-color: #ccc;">             
            <td class="text-beforediscount" width="80">
                <div style="position: absolute;"><label>รวมเป็นเงิน </label></div>               
            </td>
            <td class="beforediscount" align="right" style="padding-right: 20px;" id="ew-line-total" data="<?=$SumTotal->sumline?>">
                <?= number_format($SumTotal->sumline,2) ?>
            </td>             
        </tr>
        <tr>
            <td class="discount" align=" " valign="top">                 
                 <p>ส่วนลด </p>                  
            </td>           
            <td class="subtotal"  align="right">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="input-group">
                            <input type="text" step="any" id="ew-discount-percent" value="<?=number_format($model->percent_discount,2);?>" data="<?=$model->percent_discount;?>" class="form-control money" style="text-align: right; background:#fff7f7;"> 
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                    <div class="col-xs-6">                        
                        <input type="text" step="any" id="ew-discount-amount" readonly value="<?=number_format($SumTotal->discount,2);?>" data="<?=$SumTotal->discount;?>"  class="form-control money" style="text-align: right;" > 
                    </div>
                </div> 
                
            </td>           
        </tr>  
        <tr>
            <td colspan="2">
                <div class="row">
                    <div class="col-xs-8">จำนวนเงินหลังหักส่วนลด</div>
                    <div class="col-xs-4 text-right"><p style="padding-right: 10px;"><?= number_format($SumTotal->subtotal,2) ?> </p></div>
                </div>                 
            </td>
                 
        </tr> 
        <?php if ( $model->vat_percent > 0 ): ?>
        <tr>             
            <td colspan="2" class="text-percent_vat" align=" " valign="bottom">
            <div class="row">
                    <div class="col-xs-6">
                        <?php if($model->include_vat == 0): // Vat ใน ?>
                            <p>ยอดก่อนภาษี</p>
                        <?php endif; ?>
                            <p>ภาษีมูลค่าเพิ่ม VAT <?= $SumTotal->vat ?> % </p>
                    </div>
                    <div class="col-xs-6 text-right"> 
                        
                        <?php if($model->include_vat == 0): // Vat ใน ?>
                            <p style="padding-right: 10px;"><?= number_format($SumTotal->subtotal - $SumTotal->incvat,2) ?> </p>
                        <?php endif; ?>
                            <p style="padding-right: 10px;"><?= number_format($SumTotal->incvat,2) ?></p>
                    </div>
                </div>  
            </td>
                       
        </tr>
        <?php endif; ?>

        <tr style=" background-color: #000; color:#fff; font-size: 16px;">            
             <td class="bahttext"  style="">
                  <div style="position: absolute;"><label>รวมเป็นเงิน </label></div>
             </td>              
            <td class="total" align="right" style="padding-right: 20px;">
                <?= number_format($SumTotal->total,2) ?>                            
            </td>
        </tr>
    </table>
</div>
