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
    
    $PercentDiscount = $Discount/$subtotal*100;



    // เนื่อหา                          
    $topPage        = 0;

    $bodyPage       = 35;

    $footer         = 0;

    $fontSize       = '12px';



    if(isset($_GET['fontsize']))    $fontSize   = $_GET['fontsize'];
    

    
    // Header
    $cellHeight = 'height:25px';



    // Body
    $bodyHeight = '120mm';




    $footerPage     = $footer - $topPage;

    Yii::$app->session->set('toppage',$topPage);


    if(isset($_GET['papersize'])){

        if($_GET['papersize']=='A3')
        {
            // Body
            $bodyHeight = '125mm';
            $footer         = 10;


        }else if($_GET['papersize']=='A4'){

            // Body
            $bodyHeight = '125mm';
            $footer         = 10;

        } 
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
            
           
            padding-right: 15px;
            font-size: 11px;
            height: 20px;
             
        }

        .header{
            padding-top: <?=$topPage?>px; 
            margin: 0 -10mm 0 -10mm;
            
            
        }

        .header .body-template{
            position: absolute;
            
            
            width: 100%;
            left: 0px;
            margin: 1mm 0 0 0;
              
             
        }

        .body{
            padding-top: <?=$bodyPage?>px; 
           
            margin: 0 -10mm 0 -10mm;

           
        }
        




        /*------Body-----*/

        .item{
            /*font-size: 0.95em;*/
            font-size: <?=$fontSize;?>;
            font-weight: normal;
            height:27px;
        }

        .item-count{
            width:45px; padding: 0 15px 0 10px;
        }

        .item-code{
            width:110px; 
        }

        .item-desc{
            font-size: <?=$fontSize;?>;
            
        }

        .item-measure{
            width:90px; padding-right:5px;
            font-size: <?=$fontSize;?>;
        }

        .item-price{
            width:75px; padding-right:5px;
            font-size: <?=$fontSize;?>;
        }

        .item-discount{
             width:67px; padding-right:5px;
             font-size: <?=$fontSize;?>;
        }

        .item-amount{
            width:145px; padding-right:25px;
            font-size: <?=$fontSize;?>;
        }


        /*------/. Body-----*/




        /*------Footer-----*/
        .footer{
            margin: 0 -15mm 0 -15mm;
             
            
        }

        .remark{
            /* Text */
            font-size: 13px;
            padding:10px 0 0 10px; 

        }

        .text-beforediscount{
            /* Text */
            padding:5px 5px 5px 0;
            text-align: right;
            border-left: 0.05em solid #000;
            border-right: 0.05em solid #000;
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
        }

        .grandtotal{
            /* Text */
            width:170px; padding-right: 5px; 
            border-top: 0.05em solid #000;  
            border-left: 0.05em solid #000;  
            border-right: 0.05em solid #000;  
        }


        .beforediscount{
            /*Number*/
            padding:5px 25px 0px 0;
             
        }

        .subtotal{
            /*Number*/
            
            padding-right:25px; 

        }

        #sub-total {
            /* Text */
            padding:5px 5px 0px 0;
            border-left: 0.05em solid #000;
            border-right: 0.05em solid #000;
        }

        .sub-total {
            /*Number*/
            padding:5px 25px 0px 0;
        }

        

        .include_vat{
            /*Number*/
            padding:0px 25px 5px 5px; 
             
              

        }

        

        .total{
           /*Number*/
            width:130px; 
            padding-right:25px;  
            margin-top: 5px; 
            border-top: 0.05em solid #000;
        }     
        /*------/. Footer-----*/
    
       
    </style>
</head>

<body>



<htmlpageheader name="ewinHeader" style="display:block; " >


<div style="text-align: right;margin-right: 20px;">
    <div style="text-align: center; font-size: 20px;">CT</div> 
    <div style="margin-top:-48px;"><?=Yii::t('common','Page')?> : {PAGENO} / {nb} </div>
</div>
<div style="text-align: center; font-size: 16px;">
    <p><?=Yii::t('common','ใบส่งสินค้าชั่วคราว')?></p>
</div>

<div class="header">
    

    <table  border="0" cellpadding="0" cellspacing="0" style="width:100%; " level="0" >
         
        <tr>
            <td valign="top" colspan="2"   >
                <table border="0" cellpadding="0" cellspacing="0" style="width:100%; " >
                    <tr>
                        <td height="112"  valign="top">
                                  
                               <p style="font-size: 13px;"> รหัสลูกค้า : <?= wordwrap($model->customer->code, 150, "<br/>\r\n") ?></p>    
                               <table><tr><td style="height: 3px;"></td></tr></table>
                               <p style="font-size: 13px;"> ชื่อลูกค้า : <?= wordwrap($model->customer->name, 150, "<br/>\r\n") ?></p>
                               <table><tr><td style="height: 3px;"></td></tr></table>
                               <p style="font-size: 13px;"> ที่อยู่ : <?= wordwrap($model->cust_address, 150, "<br/>\r\n") ?>   </p>
                                
                        </td>
                    </tr> 
                <tr>
                   <td valign="bottom">
                       
                      
                       <p> โทร : <?= $model->customer->phone ?>   แฟกซ์ : <?= $model->customer->fax ?> </p>
                       <p> ขนส่งโดย : <?= $model->customer->transport ?> </p>
                
                   </td>
               </tr>       
               
                </table>
            </td>
            <td valign="top" style="width: 270px;">
                <!-- ส่วนของเลขที่และวันที่เอกสาร -->
                

                <table style="width:100%; font-size: 13px; " border="0" cellpadding="0" cellspacing="0">
                     
                     
                    <tr >
                        <td align="right" style="width: 50px;"><?=Yii::t('common','เลขที่ใบกำกับ')?></td>
                        <td align="center" >
                              
                            <?= $model->no_ ?>  

                        </td>
                    </tr>

                    <tr >
                        <td align="right" style=" <?=$cellHeight?> "><?=Yii::t('common','Date')?></td>
                        <td align="center"  >
                     
                            <?= date('d/m/y',strtotime($model->order_date.' + 543 Years')) ?>

                            

                        </td>
                    </tr> 
                    <tr >
                        <td align="right" style=" <?=$cellHeight?> "><?=Yii::t('common','Payment term')?></td>
                        <td align="center"  >
                     
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
                        <td align="right" style=" <?=$cellHeight?> "><?=Yii::t('common','Due date')?></td>
                        <td align="center"  >
                     
                            <?= date('d/m/y',strtotime($model->paymentdue.' + 543 Years')) ?>

                            

                        </td>
                    </tr>  
                    <tr >
                        <td align="right" style=" <?=$cellHeight?> "><?=Yii::t('common','Saleorder No.')?></td>
                        <td align="center"  >
                     
                            <?= $model->ext_document ?>

                            

                        </td>
                    </tr>   
                    <tr >
                        <td align="right" style=" <?=$cellHeight?> "><?=Yii::t('common','Salesman')?></td>
                        <td align="center"  >
                     
                            <?= $model->sales->code ?> - <?= $model->sales->name ?> <?= $model->sales->surname ?>

                            

                        </td>
                    </tr> 
                </table>

            </td>
        </tr>
    </table>

    
    <div class="body-template">
        <div style="border: 0.05em solid #000; height: 45px; margin: 0 0 -12mm 0;"></div>
        <div style="border: 0.05em solid #000; width: 10mm; height: <?=$bodyHeight?>; margin: 0 0 0 0; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 116.1mm; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 22mm; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 22.5mm; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm;  float: left;"></div>
        <div style="border: 0.05em solid #000; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm; float: left;"></div>


      
    </div> 
    
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




<!-- Footer on Every Page --> 

<style type="text/css">
    .footer{
        padding-bottom: <?=$footerPage?>px;
    }
</style>
<htmlpagefooter name="ewinFooter" style="display:none">
 
 <div class="footer" style="margin:0 -10mm 0 -10mm; "> 

    <div style="border:0.05em solid #000;">

        <table style="width:100%; font-size: 12px " border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td class="remark" valign="top" colspan="5" rowspan="4" >
                    <label><?=Yii::t('common','Remark')?> : </label>
                    <div style="">
                         
                        &nbsp;&nbsp;<?=wordwrap($model->remark, 45, "<br/>\n", false); ?>  
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
                     <?=Yii::t('common','Discount')?> (<?=number_format($PercentDiscount)?> %)
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
                    
                    <?php if($model->include_vat == 0): // Vat ใน ?>

                    <p><?= number_format($subtotal - $InCVat,2) ?> </p>

                    <?php endif; ?>

                    <?= number_format($InCVat,2) ?>

                </td>
               
            </tr>


            <tr>
                
                 <td class="bahttext" colspan="5"  >
                    (<?= $Bahttext->ThaiBaht($total) ?>)
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
    <div style="margin-top:3mm;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="padding-left: 20px;" colspan="2"><?=$text1?></td>
            </tr>
             

            <tr>
                <td style="padding-left: 10px; height: 23mm">ผู้รับสินค้า __________________ วันที่ ____/____/____</td> 
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
<!-- /. Footer on Every Page --> 

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
<sethtmlpageheader name="ewinHeader" page="O" value="on" show-this-page="1" />


</body>
</html>
