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

                              


?>




<?php
/*<!-- Header -->
                                                  ,--,    
                                           ,--.,---.'|    
    ,---,.           .---.   ,---,       ,--.'||   | :    
  ,'  .' |          /. ./|,`--.' |   ,--,:  : |:   : |    
,---.'   |      .--'.  ' ;|   :  :,`--.'`|  ' :|   ' :    
|   |   .'     /__./ \ : |:   |  '|   :  :  | |;   ; '    
:   :  |-, .--'.  '   \' .|   :  |:   |   \ | :'   | |__  
:   |  ;/|/___/ \ |    ' ''   '  ;|   : '  '; ||   | :.'| 
|   :   .';   \  \;      :|   |  |'   ' ;.    ;'   :    ; 
|   |  |-, \   ;  `      |'   :  ;|   | | \   ||   |  ./  
'   :  ;/|  .   \    .\  ;|   |  ''   : |  ; .';   : ;    
|   |    \   \   \   ' \ |'   :  ||   | '`--'  |   ,/     
|   :   .'    :   '  |--" ;   |.' '   : |      '---'      
|   | ,'       \   \ ;    '---'   ;   |.'                 
`----'          '---"             '---'               

<!-- /. Header -->*/

?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style>
        .page{
            
            border: 1px solid #fff;
        }
        .header{
             
            /*margin: 0 -15mm 0 -15mm;*/
            
        }
        .body{
            height: 405px;
            /*margin: 0 -15mm 0 -15mm;*/
            border: 0px solid #ccc;
             
        }
        




        /*------Body-----*/

        .item{
            font-size: 0.95em;
            font-weight: normal;
            border:0px solid #ccc;
            height:27px;
        }

        .item-count{
            width:35px; padding: 0 15px 0 15px;
        }

        .item-code{
            width:110px; 
        }

        .item-desc{
            
        }

        .item-measure{
            width:90px; padding-right:5px;
        }

        .item-price{
            width:75px; padding-right:5px;
        }

        .item-discount{
             width:67px; padding-right:5px;
        }

        .item-amount{
            width:150px; padding-right:25px;
        }


        /*------/. Body-----*/




        /*------Footer-----*/
        .footer{
            /*margin: 0 -15mm 0 -15mm;*/
            border: 1px solid #fff;
             
        }

        .remark{
            font-size: 13px;
            padding-left:75px; border: 1px solid #fff;
        }

        .text-beforediscount{
            padding-right: 3px;
            padding:5px;  border: 1px solid #fff; text-align: right;
        }

        .beforediscount{
            padding-right:25px; border: 1px solid #fff;
        }

        .discount{
            border: 1px solid #fff;
            padding-right: 3px;
        }

        .subtotal{
            padding-right:25px; border: 1px solid #fff;
        }

        .text-percent_vat{
            border: 1px solid #fff;

        }

        .include_vat{
            padding-right:25px; padding-top: 8px; border: 1px solid #fff; height: 38px;
        }

        .bahttext{
            font-size: 13px;
            padding-left:75px;  height: 45px; border: 1px solid #fff;
        }

        .total{
            width:150px; padding-right:25px;  margin-top: 5px; border: 1px solid #fff;
        }     
        /*------/. Footer-----*/

    </style>
</head>

<body>


<htmlpageheader name="ewinHeader" style="display:block; " >
<div class="header">

    <table  border="0" cellpadding="0" cellspacing="0" style="width:100%; " level="0" >
         
        <tr>
            <td valign="top" colspan="2" width="500">
                 

            </td>
            <td valign="top">
                <!-- ส่วนของเลขที่และวันที่เอกสาร -->
                <table style="width:100%; font-size: 14px; margin-top: 46px;" border="0" cellpadding="0" cellspacing="0">
                    <tr >
                        <td align="center" style="width: 50px;"></td>
                        <td align="center" style=" width: 80px;">

                            <?= $model->no_ ?>  

                        </td>
                    </tr>
                    <tr >
                        <td align="center" style=" height: 35px; "></td>
                        <td align="center" valign="bottom" style="border: 0px solid #ccc;">
                     
                            <?= date('d/m/y',strtotime($model->order_date.' + 543 Years')) ?>

                            <span class="page">  Page : {PAGENO} </span>

                        </td>
                    </tr>    
                </table>

            </td>
        </tr>
    </table>

    <table style="width:100%; font-size: 28px" border="0" cellpadding="0" cellspacing="0" >
        <tr>
            <td valign="top" colspan="3">
                <table  style="margin-top: 25px;" width="100%"   border="0" cellpadding="0" cellspacing="0" >
                    <tr>
                        <td valign="top"  style="width:900px; padding: 8px 8px 8px;  height: 200px;">
                            <!-- <p style="margin-top: -100px;"> -->
                               <?php 
                                     
                              
                                    if($model->customer->province!='')
                                    {
                                        $findProvince   = 'จ.'.$model->customer->provincetb->PROVINCE_NAME;

                                        if( strpos( $model->cust_address, $findProvince )) {

                                            str_replace($model->cust_address, 'จ.'.$model->customer->provincetb->PROVINCE_NAME, 'จ.'.$model->customer->provincetb->PROVINCE_NAME);
                                        }else {

                                            $model->cust_address = $model->cust_address.' '.$findProvince;

                                        }

                                        
                                    }


                                    if($model->customer->postcode!='')
                                    {
                                        $findPost   = $model->customer->postcode;

                                        if( strpos( $model->cust_address, $findPost )) {

                                            str_replace($model->cust_address, $model->customer->postcode, $model->customer->postcode);
                                        }else {
                                            $model->cust_address = $model->cust_address.' '.$findPost;

                                        }
                                    } 
                                   


    
                                ?>     

                                ชื่อลูกค้า : <?= $model->customer->name ?><br> <br>
                                ที่อยู่ : <?= wordwrap($model->cust_address, 150, "<br/>\r\n") ?> <br>
                                
                                
                            </p>

                            <br>
                            <br>

                            <?php 

                                if($model->customer->headoffice == 1 ){
                                    $headeroffice =  ' สำนักงานใหญ่';
                                }else {
                                    $headeroffice =  NULL;
                                }

                            ?>
                            
                            เลขประจำตัวผู้เสียภาษี : <?= $model->customer->vat_regis ?>  <?=$headeroffice ?>  <br>
                             
                            
                            โทร : <?= $model->customer->phone ?> แฟกซ์ : <?= $model->customer->fax ?>
                                 
                        </td>
                     
                        <td  valign="top" style="height: 380px; border: 1px solid #fff;">
                   
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr > 
                                    <td style="text-align: left; width: 100px;  font-size: 10px;; height: 95px;">
                                       <!--  พนักงานขาย  --><br>
                                        <!-- SALESMAN --><br>
                                    </td>
                                    <td style="text-align: left; width: 400px; font-size: 25px; ">
                                        <?= $model->sales->code ?> - <?= $model->sales->name ?> <?= $model->sales->surname ?>
                                    </td>
                                </tr>
                                <tr  > 
                                    <td style="text-align: left; width: 320px;  font-size: 25px; padding:5px 0 0 5px; height: 65px;">
                                        <!-- เงื่อนไขการชำระะเงิน  --><br>
                                        <!-- TERM OF PAYMENT --> <br>
                                    </td>
                                    <td style="text-align: center; width: 350px;  font-size: 25px; ">
                                        <?php 
                                            if($model->payment_term == '0'){
                                                echo Yii::t('common','Cash');  
                                            }else {
                                                echo $model->payment_term.' วัน';
                                            }
                                        ?> 

                                    </td>
                                </tr>
                                <tr > 
                                    <td style="text-align: left; width: 100px;  font-size: 25px; padding:5px 0 0 5px;height: 65px;">
                                        <!-- กำหนดชำระ --> <br>
                                        <!-- DUE DATE --><br>
                                    </td>
                                    <td style="text-align: center; width: 350px;  font-size: 25px; padding:5px 0 0 5px;">
                                        
                                        <?= date('d/m/Y',strtotime($model->paymentdue.' + 543 Years')) ?>
                                    </td>
                                </tr>
                                <tr> 
                                    <td style="text-align: left; width: 100px;  font-size: 25px; padding:5px 0 0 5px;  height: 65px;">
                                    <p style="margin-top: 3px;">
                                        <!-- ใบสั่งซื้อ เลขที่  --><br>
                                       <!--  PO.NO. --><br></p>
                                    </td>
                                    <td style="text-align: center; width: 350px;  font-size: 15pt; ">
                                        <?= $model->ext_document ?>
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


<!-- Content 

                ,'\   |\
               / /.:  ;;
              / :'|| //
             (|.| ||;'
             / ||,;'-.._
            : ,;,`';:..-`
            |:|'`-(\\
            ::: \-'\`'
             \\\ \,-`.
              `'\ `.,-`-._      ,-._
       ,-.       \  `.,-' `-.  / ,..`.
      / ,.`.      `.  \ _.-' \',: ``\ \
     / / :..`-'''``-)  `.   _.:''  ''\ \
    : :  '' `-..''`/    |-''  |''  '' \ \
    | |  ''   ''  :  EW |__..-;''  ''  : :
    | |  ''   ''  |     ;    / ''  ''  | |
    | |  ''   ''  ;    /-.../_ ''_ '' _| |
    | |  ''  _;:_/    :._  /-.'',-.'',-. |
    : :  '',;'`;/     |_ ,(   `'   `'   \|
     \ \  \(   /\ (17):,'  \
      \ \.'/  : /    ,)    /
       \ ':   ':    / \   :
        `.\    :   :\  \  |
                \  | `. \ |..-_
             ___ ) |.  `/___-.-`
               ,'  -.'.  `. `'        _,)
               \'\(`.\ `._ `-..___..-','
                  `'      ``-..___..-'


 /. Content -->
 


<!-- ____________________________________________________________________________________________________ -->


<!-- Footer  

,,
`""*$e..
     ""*$w.
         "$$i.
           "*$$n.
              "$$$l.
                "$$$$Ew...       ..o:
                  "$$$$$$$$$$$$$$$$    ..    ,.
               ".    "*EWIN    2$o..o$$. .$$$$
                "$$o. .$$$$$o. ...$$$$$$$$$$$$$$$$
          ""$o.   "*$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$:
             "$$.    $$$$$$$$$$$"**""*"'   ew  * "l
               "$$$o.$$$$$$$$$$
                "*$$$$$$$$$$$$$$$$$o..oooooo..           .$
                       .X$$$$$$$$$$$P""     ""*oo,,     ,$$
                      $$$""$$$$$$$$:    .        ""*****"
                    .*"    $$$$$$$$$$.$;      .
                         .o$""   "$$$$$$$.  .$;
                                  $$$$$$$$$$$$
                                  "  "$$$$$$"
                                      $$$*"
    eWiNL                            .$"
                                     "




 /. Footer -->


<!-- Footer on Every Page --> 
<?php if(isset($_GET['footer'])): ?>
<?php if($_GET['footer'] != 1) : ?>
<style type="text/css">
    .footer{
        padding-bottom: 143px;
    }
</style>
<htmlpagefooter name="ewinFooter" style="display:none">

     
 <div class="footer">
    <table style="width:100%; font-size: 12px" border="0" cellpadding="0" cellspacing="0" >
        <tr>
            <td class="remark"  valign="top" colspan="4" rowspan="3" >
               
                <div style="">
                    &nbsp;&nbsp;<?=$model->remark ?>
                </div>
            </td>

            <td class="text-beforediscount" colspan="2">
                รวมเป็นเงิน<br>
                <!-- NET TOTAL  --><br>
               
            </td>
            <td class="beforediscount" align="right">
                <?= number_format($BeforeDisc,2) ?>

            </td> 
            
        </tr>

        
        <tr>
            <td   style="padding:5px;   border: 1px solid #fff;" valign="bottom"> 

                <!-- หลังหักส่วนลด  --> 
            </td>
            <td class="discount" align="right" valign="top">
                 
                 <p>ส่วนลด </p> 
                 
            </td>
           
            <td class="subtotal"  align="right">
                <p style="height: 25px;"> 
                    <?= number_format($model->discount,2) ?> 
                 </p>
                <p><?= number_format($subtotal,2) ?> </p>
            </td>
           
        </tr>
        


        <tr>
            <td   style="padding:0px; " valign="bottom"> 

               <!--  ราคารวม  <br>-->

                <!-- ภาษีมูลค่าเพิ่ม VAT <br>-->

            </td>
                
            <td class="text-percent_vat" align="right" valign="bottom"><?= $vat ?> % </td>

            <td class="include_vat" align="right" valign="bottom" >
                
                <?php if($model->include_vat == 0): // Vat ใน ?>

                <p><?= number_format($subtotal - $InCVat,2) ?> </p>

                <?php endif; ?>

                <?= number_format($InCVat,2) ?>

            </td>
           
        </tr>


        <tr>
            
             <td class="bahttext" colspan="6"  style="">
                (<?= $Bahttext->ThaiBaht($total) ?>)
             </td>
              
            <td class="total" align="right" >
                <?= number_format($total,2) ?>                            
            </td>
        </tr>

    </table>
</div>
</htmlpagefooter> 
<sethtmlpagefooter name="ewinFooter" page="O" value="on" show-this-page="1" /> 
<?php endif; ?>
<?php endif; ?>
<!-- /. Footer on Every Page --> 

<sethtmlpageheader name="ewinHeader" page="O" value="on" show-this-page="1" />

 

</body>
</html>
