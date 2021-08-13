<?php
use yii\helpers\Html;
use admin\models\FunctionBahttext;

$Yii = 'Yii';
$Bahttext   = new FunctionBahttext();


if(isset($_GET['doc']))     $docName    = $_GET['doc'];
if(isset($_GET['docEn']))   $docNameEn  = $_GET['docEn'];

// ความสูงของหัวกระดาษ
$bodyPage   = $print->header_height ?: 110;

// ระยะ หัวกระดาษ กับ เนื้อหา   (ยิ่งเยอะ ยิ่งสูง)
$topPage    = $print->margin_top ?: 25;

// ระยะขอบล่าง
$footer     = 0;

//ขนาด font item.code & item.description
$fontSize   = $header->fontsize ?: '9px';
if(isset($_GET['fontsize']))    $fontSize   = $_GET['fontsize'];

// Header
$cellHeight = 'height:25px';

// Body Content default 124mm;
$bodyHeight = $print->body_height ?: '124mm';
// จำนวนรายการ ต่อ 1 หน้า default 15;

$footerPage     = $footer - $topPage;

Yii::$app->session->set('footer',$footerPage);
Yii::$app->session->set('toppage',$topPage);
 
$htmlPrintFooter            = strtr($print->footer,$defineHeader);
 
$htmlPrintSign              = strtr($print->signature,$defineHeader);


// INITIAL STYLE WATER MARK
($header->watermark->img_alpha)? $watermark_opacity = 'opacity:'.$header->watermark->img_alpha.';'  : $watermark_opacity = null;
($header->watermark->img_width)? $watermark_width   = 'width:'.$header->watermark->img_width.';'    : $watermark_width = null;

$watermark_marginTop    = preg_replace("/[^0-9]/", '', $header->height);
$watermark_watermarkTop = preg_replace("/[^0-9]/", '', $header->watermark->top);
 
$watermark_margin_top   = ((- ($watermark_marginTop + 50)) + $watermark_watermarkTop);

($header->watermark->border)?       $watermark_border       = 'border:'.$header->watermark->border.' solid;'        : $watermark_border = null; 
($header->watermark->border_color)? $watermark_border_color = 'border-color:'.$header->watermark->border_color.';'  : $watermark_border_color = null; 

?>



<!DOCTYPE html>
<html>

<?php 

$html =
<<<HTML
<head>
    <title>{VALUE_TITLE}</title>
    <style>
        {$print->style}
        .page{
            /* padding-right: 15px;
            font-size: 10px;
            height: 20px; */
          
        }
        .header{
            padding-top: {$header->top}px;
            margin:0 -10mm 0 -10mm;
        }

        div.header div.body-template{
            position: absolute;
            top:0;             
            height: {$header->height};            
            width: 100%;
            left: 0px;            
            margin: 1mm 0 0 0;            
        }
        
        /*------Body-----*/
        .body{
            padding-top: {$header->height}px;
            height: {$body->height}px;
            margin: 0 -10mm 0 -10mm;
        }


        .item{
            /*font-size: 0.95em;*/
            font-size: {$fontSize};
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
            font-size: {$fontSize};
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
            width:100px; padding-right:5px;
        }
        /*------/. Body-----*/

        /*------Footer-----*/
        .footer{
            margin: 0 -15mm 0 -15mm;
        }

        .remark{
            /* Text */
            font-size: 14px;
            line-height: 1.5;
            padding:10px 0 0 10px;
        }
        .text-beforediscount{
            /* Text */
            padding:5px 5px 5px 0;
            text-align: right;
            border-left: 0.01em solid #000;
            border-right: 0.01em solid #000;
        }
        .discount{
            /* Text */
            padding-right: 5px;
            border-left: 0.01em solid #000;
            border-right: 0.01em solid #000;
        }
        .text-percent_vat{
            /* Text */
            padding:5px 5px 5px 0;
            border-left: 0.01em solid #000;
            border-right: 0.01em solid #000;
        }

        .bahttext{
            /* Text */
            font-size: 13px;
            padding-left:20px;  height: 40px;
            border-top: 0.01em solid #000;
        
        }

        .grandtotal{
            /* Text */
            width:170px; padding-right: 5px;
            border-top: 0.01em solid #000;
            border-left: 0.01em solid #000;
            border-right: 0.01em solid #000;
       
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
            border-left: 0.01em solid #000;
            border-right: 0.01em solid #000;
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
            border-top: 0.01em solid #000;
           
        }
        /*------/. Footer-----*/
        .footer{
            padding-bottom: {$Yii::$app->session->get('footer')}px;
        }
      
        .wht-text-{
            border-top: 0.01em solid #000;
        }
        .wht-text{
            font-size: 13px;
            padding-right:5px;  height: 40px;
            border-top: 0.01em solid #000;
            border-right: 0.01em solid #000;
            border-left: 0.01em solid #000;
         ​  
        }

        .wht-total{
            width:130px;
            padding-right:25px;
            margin-top: 5px;
            border-top: 0.01em solid #000;
            
        }

        .doc-info{
            height: 36px;
            font-size: 10px;
        }
        img.img-watermark { 
            {$watermark_opacity}
            {$watermark_width}
        }

        .marks{ 
            z-index:110;    
            {$watermark_marginTop};
            {$watermark_watermarkTop};
            width:{$header->watermark->img_width};
            margin-top:{$watermark_margin_top};
            margin-left:{$header->watermark->left};       
            padding:{$header->watermark->padding};
            border-radius: {$header->watermark->radius};
            font-size:{$header->watermark->size};            
            color:{$header->watermark->color};
            {$watermark_border};
            {$watermark_border_color};
            {$header->watermark->css};            
        }
    </style>
    </head>
HTML;
echo strtr($html,$defineHeader);
?>


<body>
<htmlpageheader name="ewinHeader" style="display:block; " >
    <div class="header">
        <?=strtr($print->header,$defineHeader)?>        
        <div class="marks">
                <?php 
                    if($header->watermark->switch===0){  
                        echo $header->watermark->text;                    
                    }else { 
                        echo Html::img($header->watermark->img,['class' => 'img-watermark']);
                    } 
                ?>        
        </div>        
    </div>    
</htmlpageheader>


<!-- Footer on Every Page -->
<htmlpagefooter name="ewinFooterFirst" style="display:none">
    <div class="footer" style="margin:0 -10mm 0 -10mm; ">          
        <div>
            <p style="text-align: right; margin-right: 0px; margin-bottom: 20px; font-size: 10px;">
                <?php if(Yii::$app->request->get('autopage')!='hidden') { ?> Page : {PAGENO} / {nb} <?php } ?>
            </p>
        </div>        
    </div>  
</htmlpagefooter>

<htmlpagefooter name="ewinFooter" style="display:none">
    
    <div class="footer" style="margin:0 -10mm 0 -10mm; ">          
        <div ><?=$htmlPrintFooter?> </div>
        <div><?=$htmlPrintSign?></div>        
    </div>  
 
</htmlpagefooter>

<sethtmlpageheader name="ewinHeader" page="O" value="on" show-this-page="1" />
<?php if($print->show_footer_at_last==0){ ?>
    <sethtmlpagefooter name="ewinFooter" page="O" value="on" show-this-page="1" />
<?php }else { ?>
    <sethtmlpagefooter name="ewinFooterFirst" page="O" value="on" show-this-page="1" />
<?php } ?>
</body>
</html>
