<?php

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;


$Bahttext = new FunctionBahttext();
$Fnc = new FunctionSaleOrder();

 

$vat          = $model->vat_percent; 
$BeforeDisc   = $Fnc->getTotalSaleOrder($dataProvider->models);

$Discount     = $model->discount;

// หักส่วนลด (ก่อน vat)
$subtotal     = $BeforeDisc - $Discount ;


    if($model->include_vat == 1){ 

        // Vat นอก


        $InCVat   = ($subtotal * $vat )/ 100;

        $total    = ($InCVat + $subtotal);
        }else {

        // Vat ใน

         

        $InCVat   = $subtotal - ($subtotal / 1.07);

        $total    = $subtotal;
    }
    
    if($BeforeDisc==0){
      $PercentDiscount = 0;
    }else {
      $PercentDiscount = $Discount/$BeforeDisc*100;  
    }


    // เนื่อหา                          
    $topPage        = 0;

    // Content Render Top
    $bodyPage       = 55;

    $footer         = 0;

    $fontSize       = '14px';

    $content        = 430;

    // margin content table
    $boxPosition    = '1mm 0 0 0';



    if(isset($_GET['fontsize']))    $fontSize   = $_GET['fontsize'];
    

    
    // Header
    $cellHeight     = 'height:25px';



    // Body
    
    $bodyHeight     = '120mm';

     
    if(isset($_GET['papersize'])){

        if($_GET['papersize']=='A3')
        {
            // Body
            $bodyHeight = '125mm';
            $footer         = 10;


        }else if($_GET['papersize']=='A4'){

             
            // Body
            $bodyPage     = 45;
            $content        = 420;
             
            $footer         = 45;

        } 
    } 




    $footerPage     = $footer - $topPage;

    Yii::$app->session->set('toppage',$topPage);


    
?>




<?php
 

?>

<!DOCTYPE html>
<html>
<head>
    <title><?=$model->no_?></title>
    <style>

        <?php if(!isset($_GET['papersize'])) :?>

        @page {
            /*size: 21cm 29.7cm;
            size: 21.5cm 29.7cm;*/
        }
        <?php endif; ?>

        body{
            font-family: 'saraban', sans-serif; 
            font-size:15px;

        }
        .page{
            
            /* 
            padding-right: 15px;
            font-size: 11px;
            height: 20px;*/

             
        }

        .header{
            padding-top: <?=$topPage?>px; 
            margin: 0 0mm 0 0mm;
            
            
        }

        .header .body-template{
            position: absolute;
            
            
            width: 100%;
            left: 0px;
            margin: <?=$boxPosition?>;
             
              
             
        }

        .body{
            padding-top: <?=$bodyPage?>px; 
           
            margin: 0 0mm 0 0mm;

           
        }
        




        /*------Body-----*/

        .item{
            
            font-size: <?=$fontSize;?>;
            /*font-weight: normal;*/
            height:40px;
        }

        .item-count{
            width:40px; 
             
        }

        .item-code{
             
            width:120px; 
        }

        .item-desc{

            font-size: <?=$fontSize;?>;
            
        }

        .item-measure{
            
            width:75px; padding-right:5px;
            font-size: <?=$fontSize;?>;
        }

        .item-price{
            
            width:85px; 
            padding-right:2px;
            font-size: <?=$fontSize;?>;
        }

        .item-discount{

            width:30px;
             font-size: <?=$fontSize;?>;
        }

        .item-amount{
            
            width:90px; padding-right:10px;
            font-size: <?=$fontSize;?>;
        }


        /*------/. Body-----*/




        /*------Footer-----*/
        .footer{
            margin: 0 0 0 0;
             
            
        }

        .remark{
            /* Text */
            font-size: 13px;
            padding:10px 0 0 10px; 
            border-left: 0.05em solid #000;
            border-top: 0.05em solid #000;

        }

        .text-beforediscount{
            /* Text */
            padding:5px 5px 5px 0;
            text-align: right;
            border-left: 0.05em solid #000;
            border-right: 0.05em solid #000;
            border-top: 0.05em solid #000;
        }

        

        .discount{
            /* Text */
            padding-right: 5px;
            border-left: 0.05em solid #000;
            border-right: 0.05em solid #000;

        }

        .text-percent_vat{
            /* Text */
            padding:5px 5px 5px 0;
            border-left: 0.05em solid #000;
            border-right: 0.05em solid #000;

        }

        .bahttext{
            /* Text */
            font-size: 13px;
            padding-left:20px;  height: 40px; 
            border-top: 0.05em solid #000;  
            border-bottom: 0.05em solid #000;
            border-left: 0.05em solid #000;
        }

        .grandtotal{
            /* Text */
            width:150px; padding-right: 5px; 
            border-top: 0.05em solid #000;  
            border-left: 0.05em solid #000;  
            border-right: 0.05em solid #000;  
            border-bottom: 0.05em solid #000;
        }


        .beforediscount{
            /*Number*/
            padding:5px 10px 0px 0;
            border-right: 0.05em solid #000;
            border-top: 0.05em solid #000;
             
        }

        .subtotal{
            /*Number*/
            
            padding-right:10px; 
            border-right: 0.05em solid #000;

        }

        #sub-total {
            /* Text */
            padding:5px 5px 0px 0;
            border-left: 0.05em solid #000;
            border-right: 0.05em solid #000;
            
        }

        .sub-total {
            /*Number*/
            padding:5px 10px 0px 0;
            border-right: 0.05em solid #000;
        }

        

        .include_vat{
            /*Number*/
            padding:0px 10px 5px 5px;
            border-right: 0.05em solid #000; 
             
              
        }

        

        .total{
           /*Number*/
            width:100px; 
            padding-right:10px;  
            margin-top: 5px; 
            border-top: 0.05em solid #000;
            border-right: 0.05em solid #000;
            border-bottom:0.05em solid #000;
        }     
        /*------/. Footer-----*/
    
       
    </style>
</head>

<body>

<?php
$tumbol         = '';
$amphur         = '';
$province       = '';
$zipcode        = '';
$address        = $model->cust_address.' ';

$textAmphur     = 'อ.';


if($model->province == 1) $textAmphur = NULL;

if(!empty($model->customer->district))  $tumbol       = 'ต.'.$model->customer->districttb->DISTRICT_NAME;

if(!empty($model->customer->city))      $amphur       = $textAmphur.$model->customer->citytb->AMPHUR_NAME;

if(!empty($model->customer->province))  $province     = 'จ.'.$model->customer->provincetb->PROVINCE_NAME;

if(!empty($model->customer->postcode))  $zipcode      = $model->customer->postcode;

 

$address .= $tumbol.$amphur.$province.$zipcode;

?>

<htmlpageheader name="ewinHeader" style="display:block; " >


<div style="text-align: right;margin-right: 20px;">
    <div style="text-align: center; font-size: 20px;">CT</div> 
    <div style="margin-top:-48px;"><?=Yii::t('common','Page')?> : {PAGENO} / {nb} </div>
</div>
<div style="text-align: center; font-size: 16px;">
    <p>
    <?php
        if($model->doc_type=='Credit-Note'){
            echo Yii::t('common','ใบลดหนี้ / ส่งคืนสินค้า');
        }else{
            echo Yii::t('common','ใบส่งสินค้าชั่วคราว');
        }
    ?>
    </p>
</div>

<div class="header">
    

    <table  border="0" cellpadding="0" cellspacing="0" style="width:100%; " level="0" >
         
        <tr>
            <td valign="top" colspan="2"  >
                <table border="0" cellpadding="0" cellspacing="0" style="width:100%;  " >
                    <tr>
                        <td height="95"  valign="top">
                                  
                               <p  > รหัสลูกค้า : <?= wordwrap($model->customer->code, 180, "<br/>\r\n") ?></p>    
                               <table><tr><td style="height: 3px;"></td></tr></table>
                               <p  > ชื่อลูกค้า : <?= wordwrap($model->customer->name, 180, "<br/>\r\n") ?></p>
                               <table><tr><td style="height: 3px;"></td></tr></table>
                               <p  > ที่อยู่ : <?= mb_substr(wordwrap($address,180, "<br/>\r\n"),0,150) ?>  </p>
                                
                        </td>
                    </tr> 
                <tr>
                   <td valign="bottom" height="55"  >
                       
                      
                       <p> โทร : <?= $model->customer->phone ?>   แฟกซ์ : <?= $model->customer->fax ?> </p>
                       <table><tr><td style="height: 3px;"></td></tr></table>
                       <p> ขนส่งโดย : <?=mb_substr($model->saleOrder 
                                            ? ($model->saleOrder->transportList
                                                ? $model->saleOrder->transportList->name
                                                : $model->saleOrder->transport) 
                                            : $model->customer->transport, 0,120) ?>  </p>
                
                   </td>
               </tr>       
               
                </table>
            </td>
            <td valign="top" style="width: 300px; ">
                <!-- ส่วนของเลขที่และวันที่เอกสาร -->
                

                <table style="width:100%; font-size: 13px; " border="0" cellpadding="0" cellspacing="0">
                     
                     
                    <tr >
                        <td align="right" style="width: 100px;"><?=Yii::t('common','เลขที่ใบกำกับ')?></td>
                        <td align="center" >
                              
                            <?= $model->no_ ?>  

                        </td>
                    </tr>

                    <tr >
                        <td align="right" style=" <?=$cellHeight?> "><?=Yii::t('common','Date')?></td>
                        <td align="center"  >
                     
                        <?= date('d/m',strtotime($model->posting_date)) ?>/<?= date('y',strtotime($model->posting_date.'+ 543 Years')) ?>

                            

                        </td>
                    </tr> 
                    <tr >
                        <td align="right" style=" <?=$cellHeight?> "><?=Yii::t('common','Payment term')?></td>
                        <td align="center"  >                     
                            <?php 
                                if($model->payment_term == '0'){
                                    echo Yii::t('common','Cash');  
                                    $color = 'color:#fff;';
                                }else {
                                    echo $model->payment_term.' วัน';
                                    $color = NULL;
                                }
                            ?>
                        </td>
                    </tr>  
                       
                    <tr >
                        <td align="right" style=" <?=$cellHeight?> "><?=Yii::t('common','Due date')?></td>
                        <td align="center"  style="<?=$color?>">                     
                            <?= date('d/m/y',strtotime($model->paymentdue.' + 543 Years')) ?>                          
                        </td>
                    </tr>  
                    <tr >
                        <td align="right" style=" <?=$cellHeight?> "><?=Yii::t('common','Saleorder No.')?></td>
                        <td align="center"  >                     
                            <?= $model->saleOrder ? $model->saleOrder->no : $model->ext_document;  ?>                           
                        </td>
                    </tr>   
                    <tr >
                        <td align="right" style=" <?=$cellHeight?> "><?=Yii::t('common','Salesman')?></td>
                        <td align="center"  >                     
                            <?= $model->salesPeople->code ?> - <?= $model->salesPeople->name ?> <?= $model->salesPeople->surname ?>                         
                        </td>
                    </tr> 
                </table>

            </td>
        </tr>
    </table>

    
    <div class="body-template">
         <div style="border-bottom: 1px solid #000; height: 45px; margin: 0 0 -12mm 0;"></div>  
        <!--<div style="border: 2px solid #000; width: 10mm; height: <?=$bodyHeight?>; margin: 0 0 0 0; float: left;"></div>
        <div style="border: 2px solid #000; width: 116.1mm; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 2px solid #000; width: 22mm; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 2px solid #000; width: 22.5mm; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm;  float: left;"></div>
        <div style="border: 2px solid #000; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm; float: left;"></div>
       -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td height="45px;" width="40"  style="border-top: 0.05em solid #000; border-left: 0.05em solid #000;"> </td>
                <td  style="border-top: 0.05em solid #000; border-left: 0.05em solid #000;"> </td>
                <td width="75"  style="border-top: 0.05em solid #000; border-left: 0.05em solid #000;"> </td>
                <td width="85"  style="border-top: 0.05em solid #000; border-left: 0.05em solid #000;"> </td>
                <td width="120"  style="border-top: 0.05em solid #000; border-left: 0.05em solid #000; border-right: 0.05em solid #000;"> </td>
            </tr>
            <tr>
                <td height="<?=$content?>"  style="border-top: 0.05em solid #000; border-left: 0.05em solid #000; border-bottom: 0.05em solid #000; "> </td>
                <td  style="border-top: 0.05em solid #000; border-left: 0.05em solid #000; border-bottom: 0.05em solid #000; "> </td>
                <td  style="border-top: 0.05em solid #000; border-left: 0.05em solid #000; border-bottom: 0.05em solid #000; "> </td>
                <td  style="border-top: 0.05em solid #000; border-left: 0.05em solid #000; border-bottom: 0.05em solid #000; "> </td>
                <td  style="border-top: 0.05em solid #000; border-left: 0.05em solid #000; border-right: 0.05em solid #000; border-bottom: 0.05em solid #000; "> </td>
            </tr>

        </table>
      
    </div> 
    
</div>

</htmlpageheader>
 

<style type="text/css">
    .footer{
        padding-bottom: <?=$footerPage?>px;
    }
</style>
<htmlpagefooter name="ewinFooter" style="display:none">
 
 <div class="footer" style="margin:0 0 0 0; "> 

    <div style=" ">

        <table style="width:100%; " border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td class="remark" valign="top" colspan="5" rowspan="4" style="line-height: 1.5; padding-right:2px; font-size:13px;">
                    <label><?=Yii::t('common','Remark')?> : </label>
                    <div style="">                         
                        &nbsp;&nbsp;<?=$model->remark; ?>  
                    </div>
                </td>

                <td class="text-beforediscount"    >
                    รวมเป็นเงิน<br>
                    <!-- NET TOTAL  --><br>
                   
                </td>
                <td class="beforediscount" align="right">
                    <?= number_format($BeforeDisc,2) ?>

                </td> 
                
            </tr>
            <tr>
                 
                <td class="discount"    align="right" valign="top" >
                     <?=Yii::t('common','Discount')?>
                     <?php if ($model->percent_discount){ ?>
                      (<?=number_format($PercentDiscount)?> %)
                     <?php } ?>
                </td>
                 
               
                <td class="subtotal"  align="right">
                    <?= number_format($model->discount,2) ?> 
                </td>
                 
               
            </tr>
            
            <tr>
                
                <td id="sub-total"    align="right" valign="top" >
                    <?=Yii::t('common','Total after discount')?>
                </td>
               
                
                <td class="sub-total"    align="right" valign="top" >
                    <?= number_format($subtotal,2) ?>
                </td>
               
            </tr>
            


            <tr>
                
                    
                <td class="text-percent_vat"   align="right" valign="bottom"> ภาษีมูลค่าเพิ่ม VAT  <?= $vat ?> % </td>

                <td class="include_vat" align="right" valign="bottom" >
                    <?php if($model->vat_percent > 0) { ?>
                        <?php if($model->include_vat == 0): // Vat ใน ?>

                        <p><?= number_format($subtotal - $InCVat,2) ?> </p>

                        <?php endif; ?>
                    

                    <?= number_format($InCVat,2) ?>
                    <?php } ?>
                </td>
               
            </tr>


            <tr>
                
                 <td class="bahttext" colspan="5"  >
                    (<?= $Bahttext->ThaiBaht(abs($total)) ?>)
                 </td>
                 <td class="grandtotal" align="right"   >
                    <?=Yii::t('common','Grand total')?>
                 </td>
                  
                <td class="total" align="right" >
                    <?= number_format($total,2) ?>                            
                </td>
            </tr>

        </table>

    </div> 
    <div style="">
        <div style="margin-left: 20px;line-height: 20px; margin-top:3mm;"><?=$text1?></div>
        <table border="0" cellpadding="0" cellspacing="0" width="100%">

            <tr>
                <td style="padding-left: 10px; height: 15mm">ผู้รับสินค้า __________________ วันที่ ____/____/____</td> 
                <td style="padding-left: 10px;" align="center" valign="bottom">
                    <p> ผู้รับมอบอำนาจ __________________ </p><br>
                    <p>  วันที่ ____/____/____ </p>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 10px;" colspan="2">ผู้ส่งสินค้า __________________ วันที่ ____/____/____</td>
            </tr>

        </table>
    </div> 

</div>
</htmlpagefooter> 


<?php if(isset($_GET['footer'])): ?>
<?php if($_GET['footer'] != 1) : ?>
<sethtmlpagefooter name="ewinFooter" page="O" value="on" show-this-page="1" /> 
<?php endif; ?>
<?php endif; ?>
 
<sethtmlpageheader name="ewinHeader" page="O" value="on" show-this-page="1" />


</body>
</html>
