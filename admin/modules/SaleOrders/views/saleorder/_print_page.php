<?php
use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;
use common\models\Company; 
$Company        = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();
$Bahttext       = new FunctionBahttext();
$Fnc            = new FunctionSaleOrder();
$docName        = 'ใบสั่งขาย';
$docNameEn      = 'Sale Orders';
if(isset($_GET['doc']))     $docName    = $_GET['doc'];
if(isset($_GET['docEn']))   $docNameEn  = $_GET['docEn'];
//$vat          = $model->vat_percent; 
//$BeforeDisc   = $Fnc->getTotalSaleOrder($dataProvider->models);
//$Discount     = $model->discount;
// หักส่วนลด (ก่อน vat)
//$subtotal     = $BeforeDisc - $Discount ;
// if($model->include_vat == 1){ 
//     // Vat นอก
//     $InCVat   = ($subtotal * $vat )/ 100;
//     $total    = ($InCVat + $subtotal);
//     }else {
//     // Vat ใน
//     $InCVat   = $subtotal - ($subtotal / 1.07);
//     $total    = $subtotal;
// }
// $PercentDiscount = $Discount/$subtotal*100;
// if($BeforeDisc==0){
//     $PercentDiscount = 0;
// }else {
//     $PercentDiscount = $Discount/$BeforeDisc*100;  
// }
    // ความสูงของหัวกระดาษ
    $bodyPage       = 110;
    // ระยะ หัวกระดาษ กับ เนื้อหา   (ยิ่งเยอะ ยิ่งสูง)                     
    $topPage        = 25;
    // ระยะขอบล่าง
    $footer         = 0;
    //ขนาด font item.code & item.description
    $fontSize       = '11px';
    if(isset($_GET['fontsize']))    $fontSize   = $_GET['fontsize'];
    // Header
    $cellHeight     = 'height:25px';
    // Body Content
    $bodyHeight     = '124mm';
    // จำนวนรายการ ต่อ 1 หน้า
    $pageSize       = 15;
    if(isset($_GET['pagesize']))    $pageSize   = $_GET['pagesize'];
    $footerPage     = $footer - $topPage;
    Yii::$app->session->set('footer',$footerPage);
    Yii::$app->session->set('toppage',$topPage);
    Yii::$app->session->set('pagesize',$pageSize);
    $barcode = $Company->id.' '.$model->id;
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
    <title><?=$model->no?></title>
    <style>
        .page{
            padding-right: 15px;
            font-size: 10px;
            height: 20px;
        }
        .header{
            position: relative;             
            padding-top: <?=$topPage?>px; 
            margin: 0 -10mm 0 -10mm;          
        }
        div.header div.body-template{
            position: absolute;
            top:1mm;             
            height: <?=$bodyHeight?>;            
            width: 100%;
            left: 0px;            
            margin: 1mm 0 0 0;            
        }
        .body{
            padding-top: <?=$bodyPage?>px; 
            height: 405px;
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
            width:45px; padding: 0 15px 0 15px;
        }
        .item-code{
            width:110px; 
        }
        .item-desc{
            font-size: <?=$fontSize;?>;            
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
            width:145px; padding-right:25px;
        }
        /*------/. Body-----*/
        /*------Footer-----*/
        .footer{
            margin: 0 -15mm 0 -15mm;            
        }
        .remark{
            /* Text */
            font-size: 14px;
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
            background-color: #ccc;
        }
        .grandtotal{
            /* Text */
            width:170px; padding-right: 5px; 
            border-top: 0.05em solid #000;  
            border-left: 0.05em solid #000;  
            border-right: 0.05em solid #000;  
            background-color: #aaa;
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
            background-color: #ccc;
        }     
        /*------/. Footer-----*/
        .footer{
            padding-bottom: <?=Yii::$app->session->get('footer');?>px;
        }
        .doc-info-table{
            
        }
        .doc-info{
            height: 36px;
            font-size: 10px;
        }
        table.table-body{
            
        }
        table th{
            
        }
        .sale-sign{
            position:absolute;
            left:101px;
            bottom:85px;
            height:30px;
        }

        .barcode{
            position:absolute !important;
            left:40.2%;
            bottom:10px;
            height:30px;
        }

        .print-date{
            position:absolute !important;
            left:12%;
            bottom:30px;
            font-size:10px;
            color:#999;
        }
    </style>
</head>
<body>
<?php 
 
  //$salePeople   = $model->sales_people;
//   $tumbol       = '';
//   $amphur       = '';
//   $province     = '';
//   $zipcode      = '';
//   $address      = $model->customer->address.' ';
//   $textAmphur   = 'อ.';

//   if(!empty($model->customer->province)){
//     if($model->customer->province == 1) $textAmphur = NULL;
//   }
  

// //   if(!empty($model->customer->district))  $tumbol       = $model->customer->districttb->DISTRICT_NAME;
// //   if(!empty($model->customer->city))      $amphur       = $textAmphur.$model->customer->citytb->AMPHUR_NAME;
// //   if(!empty($model->customer->province))  $province     = $model->customer->provincetb->PROVINCE_NAME;
// if(!empty($model->customer->postcode))  $zipcode      = $model->customer->postcode;

 

                                    
//   if(!empty($model->customer->district)){  
//       if($model->customer->provincetb->PROVINCE_ID==1){
//           $tumbol       = 'แขวง'.$model->customer->districttb->DISTRICT_NAME;
//       }else{
//           $tumbol       = 'ต.'.$model->customer->districttb->DISTRICT_NAME;
//       }
      
//   }

//   if(!empty($model->customer->city))      $amphur       = $textAmphur.$model->customer->citytb->AMPHUR_NAME;

//   if(!empty($model->customer->province)){  
//       if($model->customer->provincetb->PROVINCE_ID==1){
//           $province     = $model->customer->provincetb->PROVINCE_NAME;
//       }else{
//           $province     = 'จ.'.$model->customer->provincetb->PROVINCE_NAME;
//       }
      
//   }
//if(!empty($model->sales->code))         $salePeople   = '['.$model->sales_people.'] '.$model->sales->name;
//$address .= $tumbol.$amphur.$province.$zipcode;
  $address  = $model->customer->locations->address;

?>
<htmlpageheader name="ewinHeader" style="display:block; " >
<div class="header">  
    <table   border="0" cellpadding="0" cellspacing="0" style="width:100%; padding-bottom: 1px;">
        <tr>
            <td valign="top" >
            <?PHP if($model->vat_percent != 0): ?>
                <img src="<?=$Company->logoViewer; ?>" style="width: 100px;">
            <?php endif; ?>                 
            </td>
            <td valign="top" align="center" style="font-size: 12px; text-align: center; <?php if($model->vat_percent <= 0) echo 'padding-left: -200px;' ?>">              
                <?PHP if($model->vat_percent != 0): ?>
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="height: 40px;">
                                <h4 style="font-size: 16px;"><?=$Company->name; ?></h4><br>
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 20px;">
                                <span style="font-size: 12px;"><?=$Company->name_en; ?></span> <br>
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 20px;">
                                <span style="font-size: 12px;"><?=$Company->vat_address; ?>  อ.<?=$Company->vat_city; ?> จ.<?=$Company->vat_location; ?> <?=$Company->postcode; ?></span> <br>
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 20px;">
                                <?=$Company->phone; ?> <?=$Company->fax; ?> <?=$Company->mobile; ?>
                            </td>
                        </tr>
                    </table>                 
                <?php else: ?>
                   <h1>CT</h1>
                <?php endif; ?>         
            </td>
            <td align="center" valign="top" width="183" rowspan="2">
                <table style="width:100%; border: 1px solid #000; padding:10px; margin-bottom: 5px; background-color: #ccc;" border="0" cellpadding="0" cellspacing="0" >
                    <tr>
                        <td>                        
                        <span  >
                          <?=Yii::t('common',$docName)?> <br>
                          <?=$docNameEn?> <br>
                        </span>
                        <!-- <div style="text-align: right;margin-right: 20px; margin-top: 20px;"><?=Yii::t('common','Page')?> : {PAGENO} / {nb} </div>     -->
                        </td>
                    </tr>                    
                </table>  
                <table style="width:100%; border: 1px solid #000; font-size: 10px; margin-bottom: 5px; " border="0" cellpadding="0" cellspacing="0">
                    <tr >
                        <td align="center" style="border-bottom: 1px solid #000; width: 50px; height: 40px;">เลขที่<br>No.<br></td>
                        <td align="center" style="border-bottom: 1px solid #000; border-left: 1px solid #000; width: 80px;"><?= $model->no ?> </td>
                    </tr>
                    <tr >
                        <td align="center" style=" height: 40px;">วันที่<br>Date.<br></td>
                        <td align="center" style="border-left: 1px solid #000;  "><?= date('d/m/y',strtotime($model->order_date.' + 543 Years')); ?></td>
                    </tr>    
                </table>                 
            </td>
        </tr>
        <tr>
            <td valign="bottom" colspan="2" rowspan="2"  >
                <div class="row">
                    <?PHP if($model->vat_percent != 0): ?>
                    <div class="col-sm-12" style="margin-top: 0px;">
                        เลขประจำตัวผู้เสียภาษี <?=$Company->vat_register; ?> 
                        <span style="margin-left: 30px; ">
                        <?=$Company->headofficetb->data_char; ?></span>
                    </div>
                    <?php endif; ?>    
                </div>
            </td>            
        </tr>
    </table>  
    <table width="100%"   border="0" cellpadding="0" cellspacing="0" >
        <tr>
            <td valign="top" style="
            border-top: 1px solid #000;
            border-left: 1px solid #000;  
            padding: 15px 15px 0 15px;  width: 455px;">               

                <p>รหัสลูกค้า : <?= $model->customer->code ?></p>
                <table><tr><td style="height: 3px;"></td></tr></table>
                <p>ชื่อลูกค้า : <?= $model->customer->name ?></p> 
                <table><tr><td style="height: 3px;"></td></tr></table>
                <p>ที่อยู่ : <?= wordwrap($address, 150, "<br/>\r\n") ?></p>                  
     
            </td>         
            <td  class="doc-info-table"   valign="top" style=""  rowspan="2" >       
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #000; font-size: 12px;  ">
                    <tr > 
                        <td class="doc-info" style="text-align: left; padding:5px 0 0 5px; border-right: 1px solid #000; ">
                            พนักงานขาย <br>
                            <?PHP if($model->vat_percent != 0): ?> SALE MAN <?php endif; ?> <br>
                        </td>
                        <td class="doc-info"  style="text-align: center; padding:5px 0 0 5px; width: 180px;">
                            <?= $model->sales->code ?> - <?= $model->sales->name ?> <?= $model->sales->surname ?>
                        </td>
                    </tr>
                    <tr  > 
                        <td class="doc-info"  style="text-align: left; padding:5px 0 0 5px; border-top: 1px solid #000;border-right: 1px solid #000; ">
                            เครดิต <br>
                           <?PHP if($model->vat_percent != 0): ?> TERM OF PAYMENT <br> <?php endif; ?>
                        </td>
                        <td class="doc-info" style="text-align: center;  padding:5px 0 0 5px; border-top: 1px solid #000;">
                            <?php if($model->payment_term!='0'){
                                    echo $model->payment_term.' วัน';  
                                }else {
                                   echo Yii::t('common','Cash');  
                                }  

                            ?> 
                        </td>
                    </tr>
                    <tr > 
                        <td class="doc-info" style="text-align: left;  padding:5px 0 0 5px; border-top: 1px solid #000;border-right: 1px solid #000; ">
                            กำหนดชำระ <br>
                            <?PHP if($model->vat_percent != 0): ?> DUE DATE<br> <?php endif; ?>
                        </td>
                        <td class="doc-info" style="text-align: center; padding:5px 0 0 5px; border-top: 1px solid #000;">
                            <?php 
                                if($model->payment_term!='0'){
                                    if(date('Y',strtotime($model->paymentdue)) > 1970)  echo date('d/m/y',strtotime($model->paymentdue.' + 543 Years'));
                                }
                            ?>
                        </td>
                    </tr>
                    <tr> 
                        <td class="doc-info" style="text-align: left; padding:5px 0 0 5px; border-top: 1px solid #000;border-right: 1px solid #000;">
                        <p style="margin-top: 3px;">
                            ใบสั่งซื้อ เลขที่ <br>
                            <?PHP if($model->vat_percent != 0): ?> PO.NO.<br></p> <?php endif; ?>
                        </td>
                        <td class="doc-info" style="text-align: center; border-top: 1px solid #000;">
                            <?= $model->ext_document ?>
                        </td>
                    </tr>
                </table>
            </td>
           
        </tr>
        <tr>
            <td valign="bottom" style="
            border-bottom: 1px solid #000;
            border-left: 1px solid #000;  
            padding: 15px 15px 0 15px;  ">
                  <?php 

                    if($model->customer->headoffice == 1 ){
                        $headeroffice =  ' สำนักงานใหญ่';
                    }else {
                        $headeroffice =  NULL;
                    }

                ?>

                โทร : <?= $model->customer->phone ?> แฟกซ์ : <?= $model->customer->fax ?> <br>
                
                <?PHP if($model->vat_percent != 0): ?>
                เลขประจำตัวผู้เสียภาษี : <?= $model->customer->vat_regis ?>  <?=$headeroffice ?> 
                <?php endif; ?>
                
               
            </td>

        </tr>
    </table>
            
    <div class="body-template">
        <div style="border: 0.05em solid #000; height: 45px; margin: 0 0 -12mm 0;"></div>
        <div style="border: 0.05em solid #000; width: 10mm; height: <?=$bodyHeight?>; margin: 0 0 0 0; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 30mm; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 80mm; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 22mm; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 23mm; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm;  float: left;"></div>
        <div style="border: 0.05em solid #000; height: <?=$bodyHeight?>; margin: 0 0 0 -0.2mm; float: left;"></div>      
    </div> 

    
    
</div>

</htmlpageheader> 
<!-- ____________________________________________________________________________________________________ -->
<!-- Footer on Every Page --> 

<htmlpagefooter name="ewinFooter" style="display:none">   
 <div class="footer" style="margin:0 -10mm 0 -10mm; "> 
    <div style="border: 0.05em solid #000;">
        <table style="width:100%; font-size: 12px " border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td class="remark" valign="top" colspan="5" rowspan="4" >
                    <label><?=Yii::t('common','Remark')?> : </label>
                    <div style="color: red;">                         
                        &nbsp;&nbsp;<?=wordwrap($model->remark, 45, "<br/>\n", false); ?>  
                    </div>
                </td>
                <td class="text-beforediscount"    >
                    รวมเป็นเงิน<br>
                    <!-- NET TOTAL  --><br>                   
                </td>
                <td class="beforediscount" align="right">
                    <?= number_format($model->sumtotal->sumline,2) ?>
                </td>                 
            </tr>
            <tr>                  
                <td class="discount"    align="right" valign="top" >
                     <?=Yii::t('common','Discount')?>
                     <?php if ($model->percent_discount){ ?>
                        (<?=number_format($model->sumtotal->percentdis);?>%)
                     <?php } ?>
                </td>                
                <td class="subtotal"  align="right">
                    <?= number_format($model->sumtotal->discount,2) ?>
                </td>               
            </tr>            
            <tr>                
                <td id="sub-total"    align="right" valign="top" >
                    <?=Yii::t('common','Total after discount')?>
                </td>             
                <td class="sub-total"    align="right" valign="top" >
                    <?= number_format($model->sumtotal->subtotal,2) ?>
                </td>               
            </tr>
            <tr>
                <td class="text-percent_vat"   align="right" valign="bottom"> 
                    <?php if($model->include_vat == 0): // Vat ใน ?>
                        <p>ยอดก่อนภาษี</p>
                    <?php endif; ?>
                        <p>ภาษีมูลค่าเพิ่ม VAT  <?= $model->sumtotal->vat ?> % </p>
                </td>
                <td class="include_vat" align="right" valign="bottom" >                    
                    <?php if($model->include_vat == 0): // Vat ใน ?> 
                         <?= number_format($model->sumtotal->subtotal - $model->sumtotal->incvat,2) ?>  
                    <?php endif; ?>
                        <p><?= number_format($model->sumtotal->incvat,2) ?></p>
                </td>               
            </tr>
            <tr>                
                 <td class="bahttext" colspan="5"  >
                    (<?= $Bahttext->ThaiBaht($model->sumtotal->total) ?>)
                 </td>
                 <td class="grandtotal" align="right"   >
                    <?=Yii::t('common','Grand total')?>
                 </td>
                  
                <td class="total" align="right" >
                    <?= number_format($model->sumtotal->total,2) ?>                            
                </td>
            </tr>
        </table>
    </div>      
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding-left: 20px;" colspan="3"><?=$text1?></td>
            <td >
                <?PHP if($model->vat_percent != 0): ?>
                    <div  style="padding-top: 5px;">
                        <span style="text-align: center;">
                        <?=$text2?>
                        </span>
                    </div>
                <?php endif; ?>
            </td>
        </tr>  
        <tr>
            
            <td align="center" style="padding: 10px;">
                <br>
                <div >        
                 <br><br>
                    ...........................................
                    <table><tr><td style="height: 3px;"></td></tr></table>
                      <p style="font-size: 12px;">
                      ผู้สั่งขายสินค้า<br>
                      AUTHORIZED SIGNATURE

                      </p>
                      </p>
                </div>  
                
            </td>
            <td align="center" style="padding: 10px;">
                <br>
                <div >
                    <br><br>
                    ...........................................
                    <table><tr><td style="height: 3px;"></td></tr></table>
                      <p style="font-size: 12px;">
                      ผู้จัดสินค้า<br>
                      AUTHORIZED SIGNATURE

                      </p>
                      </p>
                </div>  
            </td>
            <td align="center" style="padding: 10px;">
                <br>
                <div >
                    <br><br>
                     ...........................................
                     <table><tr><td style="height: 3px;"></td></tr></table>
                      <p style="font-size: 12px;">
                      ผู้รับมอบอำนาจ<br>
                      AUTHORIZED SIGNATURE

                      </p>
                      </p>
                </div>  
            </td>
        </tr>

    </table>
    
    <div style="text-align: right; margin-right: 0px; margin-bottom: 20px; font-size: 10px;"><?=Yii::t('common','Page')?> : {PAGENO} / {nb} </div>
    
</div>
<div class="sale-sign">
    <?php 
        // ## Default show sign
        // You can disable sign.
        // [x] r?=...&sign=false
        //
        if(isset($_GET['sign'])){
            if($_GET['sign']=='true'){
                if($model->sales->sign_img!='') echo '<img src="'.$model->sales->unsign.'" height="32px">'; 
            }            
        }else {
            if($model->sales->sign_img!='') echo '<img src="'.$model->sales->unsign.'" height="32px">'; 
        }        
    ?>  
</div>
<div class="print-date"><?=date('Y/m/d H:i:s')?></div>
<div class="barcode">
    <barcode code="<?= $barcode ?>" type="C39" /></p>
</div>
</htmlpagefooter> 
<sethtmlpageheader name="ewinHeader" page="O" value="on" show-this-page="1" />
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
<?php if(isset($_GET['footer'])): ?>
<?php if($_GET['footer'] != 1) : ?>
<sethtmlpagefooter name="ewinFooter" page="O" value="on" show-this-page="1" /> 
<?php endif; ?>
<?php endif; ?>
<!-- /. Footer on Every Page --> 
</body>
</html>
