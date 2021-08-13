<?php
use yii\helpers\Html;
use common\models\Company;

$comp = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();


$header = '4cm';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sale Report</title>
    <style type="text/css">

        /* ################# CONFIG ##################### */
        .row {
            
        }

        .row .col-xs-1, 
        .row .col-xs-2,
        .row .col-xs-3,
        .row .col-xs-4,
        .row .col-xs-5,
        .row .col-xs-6,
        .row .col-xs-7,
        .row .col-xs-8,
        .row .col-xs-9,
        .row .col-xs-10, 
        .row .col-xs-11,
        .row .col-xs-12{
            padding:0px !important;
            margin:0px !important;
        }
        
        .header{
             
            
        }
         
        .body-table{
            padding-top: <?=$header?>;
            height: 20cm;
        }



        .body-heading{
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            
        }






        /* ################## CONTENT #################### */

        .sum-line .footer-value{
            font-size: 14px;
            border-top: 1px dotted #000;
            border-bottom: 1px  dotted #000;
            padding: 3mm;
            margin:0 3mm 0 3mm;

        }

        .sum-line .footer-text{
            font-size: 14px;
            padding: 3mm;
 
            margin:0 3mm 0 3mm;
        }

        .tbDetail{
            padding-left: 0.5cm;
        }

       
    </style>
</head>

<body>
<htmlpageheader name="ewinHeader"  style="display:block;">
<div class="header">
    <div class="row ">   

        <div class="row">
            <div class="col-xs-10">
                <div class="col-xs-12"><?=$comp->name?></div>
                <div class="col-xs-12"><span class="h4">รายงานขายเงินสด เรียงตามเลขที่</span></div>
                <div class="col-xs-12">
                    <div class="col-xs-4">
                        <div class="col-xs-3">วันที่จาก</div>
                        <div class="col-xs-3">1 มิ.ย. 2560</div>
                        <div class="col-xs-3">ถึง</div>
                        <div class="col-xs-3">30  มิ.ย. 2560</div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-4">
                        <div class="col-xs-3">เลขที่จาก</div>
                        <div class="col-xs-3">- </div>
                        <div class="col-xs-3">ถึง</div>
                        <div class="col-xs-3">๙๙๙๙๙๙๙๙๙</div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-4">
                        <div class="col-xs-3">รหัสลูกค้า</div>
                        <div class="col-xs-3">- </div>
                        <div class="col-xs-3">ถึง</div>
                        <div class="col-xs-3">ฮ012</div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-4">
                        <div class="col-xs-3">พนักงานขาย</div>
                        <div class="col-xs-3">01-P</div>
                        <div class="col-xs-3">ถึง</div>
                        <div class="col-xs-3">01-P</div>
                    </div>
                </div>
            </div>

            <div class="col-xs-2 text-right" style="padding-right: -1px;">
                <div class="col-xs-12" style="margin-bottom: 3mm;">
                    <div class="col-xs-6"><?=Yii::t('common','Page')?> : </div>
                    <div class="col-xs-6"> {PAGENO} / {nb} </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-6"><?=Yii::t('common','Date')?> : </div>
                    <div class="col-xs-6">  <?=date('d/m/y',strtotime(date('Y-m-d').'+543 Years'))?> </div>
                </div>
            </div>

        </div>

    </div> 

    <div class="row">
        <div class="row text-center body-heading">

            <div class="col-xs-5" style="padding-top: 3mm;">
                <div class="col-xs-2"><div class="htable">เลขที่</div></div>
                <div class="col-xs-1"><div class="htable">วันที่</div></div>
                <div class="col-xs-2"><div class="htable">รหัสลูกค้า</div></div>
                <div class="col-xs-2"><div class="htable">ชื่อลูกค้า</div></div>
                <div class="col-xs-3"><div class="htable">รหัสพนักงานขาย</div></div>
                <div class="col-xs-1"><div class="htable">V</div></div>
                <div class="col-xs-1" style="padding-right: -1px;">ส่วนลด</div>
            </div>
            <div class="col-xs-7" style="padding-top: 3mm;">
                <div class="col-xs-2">มูลค่าสินค้า</div>
                <div class="col-xs-1">VAT.</div>
                <div class="col-xs-2">รวมทั้งสิ้น</div>
                <div class="col-xs-2">ยอดรับเกิน</div>
                <div class="col-xs-2">รับด้วย ง/ส</div>
                <div class="col-xs-1">รับด้วยเช็ค</div>
                <div class="col-xs-2"  style="padding-right: -1px;">ภาษี ณ ที่จ่าย</div>
            </div>

            <div class="col-xs-7" style="padding-top: 1mm; padding-bottom: 3mm;">
                <div class="col-xs-2">รายละเอียด</div>
                <div class="col-xs-2 text-right">จำนวน</div>
                <div class="col-xs-2">ราคาต่อหน่วย</div>
                <div class="col-xs-2">ส่วนลด</div>
                <div class="col-xs-2">จำนวนเงิน</div>
                <div class="col-xs-2" style="padding-right: -1px;">จากใบสั่งขาย</div>
            </div>

        </div>
    </div>
</div> 
</htmlpageheader>

 



<htmlpagefooter name="ewinFooter" style="display:none">
     
 
    
    <div class="row">
        <div class="col-xs-6 text-right" > 
            <div class="col-xs-2 sum-line text-right pull-right" style="padding-right: -1px;"><div class="footer-value">0</div></div>
            <div class="col-xs-4 sum-line text-right pull-right"><div class="footer-text">  รวม 6 ใบ </div></div>
        </div>
         
        <div class="col-xs-6">
            

            <div class="col-xs-2 sum-line text-right"><div class="footer-value">1 </div></div>
            <div class="col-xs-2 sum-line text-right"><div class="footer-value">2 </div></div>
            <div class="col-xs-2 sum-line text-right"><div class="footer-value">3 </div></div>
            <div class="col-xs-2 sum-line text-right"><div class="footer-value">4 </div></div>
            <div class="col-xs-2 sum-line text-right"><div class="footer-value">5 </div></div>
            <div class="col-xs-2 sum-line text-right" style="padding-right: -1px;"><div class="footer-value">6 </div></div>

            
        </div>
    </div>


</htmlpagefooter> 




<sethtmlpageheader name="ewinHeader" page="O" value="on" show-this-page="1" /> 
<sethtmlpagefooter name="ewinFooter" page="O" value="on" show-this-page="1" /> 
 



</body>
</html>
