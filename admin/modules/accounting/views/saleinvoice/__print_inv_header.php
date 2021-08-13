<?php

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;

$Bahttext       = new FunctionBahttext();
$Fnc            = new FunctionSaleOrder();

// $vat            = $model->vat_percent; 
// $BeforeDisc     = $Fnc->getTotalSaleOrder($dataProvider->models);
// $Discount       = $model->discount;

// // หักส่วนลด (ก่อน vat)
// $subtotal       = $BeforeDisc - $Discount ;

// if($model->include_vat == 1){ 
//     // Vat นอก
//     $InCVat = ($subtotal * $vat )/ 100;
//     $total  = ($InCVat + $subtotal);
//     }else {
//     // Vat ใน
//     $InCVat = $subtotal - ($subtotal / 1.07);
//     $total  = $subtotal;
// }

// if($BeforeDisc==0){
//     $PercentDiscount = 0;
// }else {
//     $PercentDiscount = $Discount/$BeforeDisc*100;  
// }
                    
    $topPage    = 30;
    $bodyPage   = 70;
    $footer     = 125;

    $footerPage = $footer - $topPage;
    Yii::$app->session->set('toppage',$topPage);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?=$model->no_?></title>
    <style>
        .page{
            
            border: 0px solid #ccc;
            padding-right: 25px;
           
            height: 20px;
             
        }

        .header{
            padding-top: <?=$topPage?>px; 
            margin: 0 -15mm 0 -15mm;
            border: 0px solid #ccc;
            
        }

        .body{
            padding-top: <?=$bodyPage?>px; 
            height: 405px;
            margin: 0 -15mm 0 -15mm;
            border: 0px solid #ccc;
             
        }

        /*------Body-----*/

        .item{
            font-size: 12px; 
            /* font-weight: normal; */
            border:0px solid #ccc;
            height:23px;
        }

        .item-count{
            width:60px; padding: 0 15px 0 15px;
        }

        .item-code{
            width:110px; 
        }

        .item-desc{
            
        }

        .item-measure{
            font-size: 13px; 
            width:90px; padding-right:5px;
        }

        .item-price{
            font-size: 13px; 
            width:85px; padding-right:5px;
        }

        .item-discount{
            font-size: 13px; 
            width:60px; padding-right:5px;
        }

        .item-amount{
            font-size: 13px; 
            width:145px; padding-right:25px;
        }

        /*------/. Body-----*/


        /*------Footer-----*/
        .footer{
            margin: 0 -15mm 0 -15mm;
            border: 1px solid #fff;
             
        }

        .remark{
            font-size: 13px;
            padding-left:75px; border: 1px solid #fff;
        }

        .text-beforediscount{
            padding-right: 3px;
            
        }

        .beforediscount{
            font-size: 13px; 
            padding-right:25px; 
        }

        .discount{
            font-size: 13px; 
            border: 1px solid #fff;
            padding-right: 3px;
        }

        .subtotal{
            font-size: 13px; 
            padding-right:25px; border: 1px solid #fff;
        }

        .text-percent_vat{
            font-size: 13px; 
            border: 1px solid #fff;

        }

        .include_vat{
            font-size: 13px; 
            padding-right:25px;
        }

        .bahttext{
            font-size: 13px;
            padding-left:75px;  border: 1px solid #fff;
        }

        .total{
            font-size: 13px; 
            width:150px; padding-right:25px;  margin-top: 5px; border: 1px solid #fff;
        }     
        /*------/. Footer-----*/
        .footer{
            padding-bottom: <?=$footerPage?>px;
        }
    </style>
</head>
<body>
<htmlpageheader name="ewinHeader" style="display:block; " >
<div class="header">
    <table  border="0" cellpadding="0" cellspacing="0" style="width:100%; " level="0" >
        <tr>
            <td valign="top" colspan="2" width="565">
            </td>
            <td valign="top">
                <!-- ส่วนของเลขที่และวันที่เอกสาร -->
                <table style="width:100%;  font-size: 14px;" border="0" cellpadding="0" cellspacing="0">
                    <tr>                         
                         <td align="right" style="height:30px; font-size:12px; padding-right:12px;"><b><?=Yii::t('common','Page')?> : {PAGENO} / {nb} </b></td>
                     </tr>                     
                    <tr >                        
                        <td align="center" style="height:60px;"> <b><?= $model->no_ ?></b>  </td>
                    </tr>
                    <tr >                        
                        <td align="center" valign="bottom" style="border: 0px solid #ccc;">                     
                        <b><?= date('d/m',strtotime($model->posting_date)) ?>/<?= date('y',strtotime($model->posting_date.' + 543 Years')) ?></b>
                        </td>
                    </tr>    
                </table>
            </td>
        </tr>
    </table>

    <table style="width:100%;" border="0" cellpadding="0" cellspacing="0" >
        <tr>
            <td valign="top" colspan="3">
                <table  style="margin-top: 8px; width:100%; line-height: 1.5; font-size:14px;"   border="0" cellpadding="0" cellspacing="0" >
                    <tr>
                        <td valign="top"  style="padding:12px 2px 8px 10px;   ">
                            <!-- <p style="margin-top: -100px;"> -->
                            <b>
                               <?php                                      
                                    if($model->customer->show_lang_addr){                                         
                                        $cusName    = $model->customer->name_en;
                                        $address    = $model->customer->address_en;
                                    }else {                                         
                                        $cusName    = $model->customer->name;                                    
                                        $tumbol     = '';
                                        $amphur     = '';
                                        $province   = '';
                                        $zipcode    = '';
                                        $address    = $model->customer->address.' ';
                                       // $address    = $model->cust_address.' ';
                                        $textAmphur = $model->customer->province == 1 ? '' : 'อ.' ;
                                                                     
                                        if(!empty($model->customer->district)){  
                                            if($model->customer->provincetb->PROVINCE_ID==1){
                                                $tumbol       = 'แขวง'.$model->customer->districttb->DISTRICT_NAME;
                                            }else{
                                                $tumbol       = 'ต.'.$model->customer->districttb->DISTRICT_NAME;
                                            }  

                                        }

                                        if(!empty($model->customer->city))      $amphur       = $textAmphur.$model->customer->citytb->AMPHUR_NAME;
                                        if(!empty($model->customer->province)){  
                                            if($model->customer->provincetb->PROVINCE_ID==1){
                                                $province     = $model->customer->provincetb->PROVINCE_NAME;
                                            }else{
                                                $province     = 'จ.'.$model->customer->provincetb->PROVINCE_NAME;
                                            }
                                            
                                        }

                                        if(!empty($model->customer->postcode))  $zipcode      = $model->customer->postcode;
                                        //if(!empty($model->sales->code))         $salePeople   = '['.$model->sales_people.'] '.$model->sales->name;
                                        $address .= $tumbol.' '.$amphur.' '.$province.' '.$zipcode;

                                        if(($model->customer->id == '909') || ($model->customer->code == '999')){
                                            $address                        = NULL;
                                            $model->customer->headoffice    = 0;
                                            $model->doc_type                = 'Cash';
                                        }
                                    }
    
                                ?>     
                                </b>
                                <p><b> ชื่อลูกค้า : <?= $cusName ?></b></p> 
                                 <br>
                                <p >
                                    <b>ที่อยู่ : <?= wordwrap(($address), 150, "<br/>\r\n") ?> 
                                    <br/>
                                    <?=$model->customer->address2?>
                                    </b>
                                </p>                            
                            </p>
                            <b>
                                <?php 
                                    // if(strlen($address) <= 150){
                                    //     echo '<br>';
                                    // }
                                    if($model->customer->headoffice == 1 ){
                                        $headeroffice =  ' สำนักงานใหญ่';
                                    }else {
                                        $headeroffice =  ' สาขาที่ '.$model->customer->branch ;
                                    }

                                ?>
                            </b>
                            <table><tr><td style="height: 10px;"></td></tr></table>      
                            <p ><b>เลขประจำตัวผู้เสียภาษี : <?= $model->customer->vat_regis ?>  <?=$headeroffice ?>  </b><p>
                                                    
                            <p><b>โทร : <?= $model->customer->phone ?> แฟกซ์ : <?= $model->customer->fax ?></b></p>                                 
                        </td>
                        <td valign="top" style="width:250px;">
                            <table style="font-size:14px;" width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="text-align: center; height:40px;">
                                        <b>
                                            <?php if($model->sale_id): ?>
                                                <?php if($model->doc_type=='Sale'): ?>
                                                <?= $model->salesPeople->code ?> - <?= $model->salesPeople->name ?> <?= $model->salesPeople->surname ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; height:35px;">
                                        <b>
                                            <?php
                                                if($model->payment_term == '0'){
                                                    if(($model->doc_type=='Sale') || ($model->doc_type=='Cash')){
                                                        echo Yii::t('common','Cash');
                                                    }
                                                    $color = 'color:#fff;';
                                                }else {
                                                    echo $model->payment_term.' '.Yii::t('common','Day');
                                                    $color = NULL;
                                                }
                                            ?>
                                        </b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; height:35px; <?=$color?>">
                                        <b><?= date('d/m/Y',strtotime($model->paymentdue.' + 543 Years')) ?></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; height:35px;">
                                        <b><?= $model->ext_document;  ?>  </b>        
                                    </td>
                                </tr>
                            </table>
                        </td>

                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
 
</htmlpageheader> 
<htmlpagefooter name="ewinFooter" style="display:none">     
 <div class="footer">
    <table style="width:100%; font-size: 13px;" border="0" cellpadding="0" cellspacing="0" >
        <tr>
            <td colspan="4" ></td>
            <td> </td>
            <td valign="top" align="left" style="padding-left: 35px; width: 150px"> 
                <b>รวมเป็นเงิน </b><br>
            </td>
            <td class="beforediscount" align="right" valign="top"  style="height: 25px;"> 
                <b><?= number_format($model->sumtotals->sumline,2) ?></b>
            </td>
        </tr>
        <tr>
            <td class="remark"  valign="top" colspan="4" rowspan="3" ><b><?=$model->remark ?></b></td>
            <td> </td>
            <td style=" padding-left: 35px;" align="left" valign="top" >
               <!--  รวมเป็นเงิน<br> -->
                <b style="display:none;">
                    <?=Yii::t('common','Discount')?>
                        <?php /* $model->percent_discount 
                            ? '('.number_format($model->sumtotals->percentdis).' %)'
                            : ''*/
                    ?>
                </b>        
            </td>
            <td class="beforediscount" align="right"  valign="top" style="height:8px;">               
                <b style="display:none;"><?php // number_format($model->discount,2) ?> </b>
            </td>             
        </tr>
        <tr>
            <td   style="padding:5px;  " valign="bottom"><!-- หลังหักส่วนลด  --> </td>
            <td class="discount" align="right" valign="top" style="height: 10px; "></td>           
            <td class="subtotal" align="right" valign="top" style="height: 30px; padding-top:-10px;">
                <b>
                <?=$model->include_vat == 0  // Vat ใน 
                    ? number_format($model->sumtotals->subtotal - $model->sumtotals->incvat,2)
                    : number_format($model->sumtotals->subtotal,2)     
                ?>
                </b>             
            </td>           
        </tr>       
        <tr>
            <td style="padding:0px;"> 
               <!--  ราคารวม  <br>-->
                <!-- ภาษีมูลค่าเพิ่ม VAT <br>-->
            </td>                
            <td class="text-percent_vat" align="right" valign="top" style="height: 28px; " ><b><?= $model->sumtotals->vat ?> % </b></td>
            <td class="include_vat" align="right" valign="top" style="">                
                <b><?= number_format($model->sumtotals->incvat,2) ?></b>
            </td>           
        </tr>
        <tr>            
            <td class="bahttext" colspan="6"  style="">
                <b>(<?= $Bahttext->ThaiBaht($model->sumtotals->total) ?>)</b>
            </td>              
            <td class="total" align="right" style="height: 55px;">
                <b><?= number_format($model->sumtotals->total,2) ?></b>                            
            </td>
        </tr>
    </table>
</div>
</htmlpagefooter> 
<sethtmlpageheader name="ewinHeader" page="O" value="on" show-this-page="1" />
<?php if(isset($_GET['footer'])): ?>
<?php if($_GET['footer'] != 1) : ?>
<sethtmlpagefooter name="ewinFooter" page="O" value="on" show-this-page="1" /> 
<?php endif; ?>
<?php endif; ?>
</body>
</html>
