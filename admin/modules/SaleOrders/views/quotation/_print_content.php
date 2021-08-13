<?php
use admin\models\FunctionBahttext;
$Yii            = 'Yii';
$subStr         = 50;
$table_top      = (int)$header->height + (int)$print->margin_top + 65;
?>

<?php
$table=<<<HTML

        <div style="
            position:absolute; 
            overflow: visible; 
            top:{$table_top}; 
            left:0px;
            margin-left:20px;
            margin-right:60px;
            width:100%; 
            height:{$body->height};                 
         " >
            <div style="border: 0.05em solid #000; height: 30px;"></div>
            <div style="border: 0.05em solid #000; width: 1%;   height: {$body->height}; margin: -30 0 0 0; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 15%;  height: {$body->height}; margin: 0 0 0 -2.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 45%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 12%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 10%;  height: {$body->height}; margin: 0 0 0 -0.2mm;  float: left;"></div>
            <div style="border: 0.05em solid #000;              height: {$body->height}; margin: 0 37px 0 -0.2mm; float: left;"></div>      
        </div> 
HTML;

($print->show_table==0)? $showTable = ' ' : $showTable = $table;
?>

<?php

if(isset($_GET['substr']))      $subStr     = $_GET['substr'];

   // Set line amount per page.
   $PerPage         = $body->pagesize;
   $HeaderHeight    = 220 - Yii::$app->session->get('toppage');
   $AllData         = $dataProvider->getTotalCount();
   $data            = $dataProvider->models;
   $data            = array_chunk($data, $PerPage);

   $ix              = 0;
   for ($i=0; $i < count($data); $i++) {
       if($ix > 1 ){
           $brackpage = '<pagebreak />';
       }else {
           $brackpage = NULL;
       }
       $td =
<<<HTML
{$brackpage }

{$showTable}
<div class="body">
<table border="0" cellpadding="2" cellspacing="0"  width="100%"  style="width: 100%;">
  <thead>
    <tr>
      <th style="height:40px; padding-left:5px;">ลับดับ</th>
      <th style="" align="left" >{$Yii::t('common','Code')}</th>
      <th style="" align="left" >{$Yii::t('common','Description')}</th>
      <th style="" align="center">{$Yii::t('common','Quantity')}</th>
      <th style="" align="center">{$Yii::t('common','Unit Price')}</th>
      <th style="padding-right:5px;" align="center">{$Yii::t('common','Amount')}</th>
    </tr>
</thead>
<tbody>
HTML;
       foreach ($data[$i] as $line) {
           
          
           $unitprice = $line->unit_price;
           $amount    = $line->quantity * $line->unit_price;
           if($amount != 0) $ix++;
           
           $td.= '<tr>';

               $td.= '<td valign="top" class="item item-count" align="center" >'.($amount != 0 ? $ix.'. ' : '').' </td>';

               $itemCode      =   $line->items->master_code;
               if($line->items->No=='1^x') $itemCode = '';

               $td.= '<td valign="top" class="item item-code">'.$itemCode.'</td>';

               if($line->description==''){

                    $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->items->description_th, 0,$subStr).'</td>';

                 }else {

                    $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->description,0,$subStr).'</td>';

                 }

               $td.= '<td valign="top" class="item item-measure" align="right" >
                       '.($line->quantity != 0 ? number_format($line->quantity).' '.$line->items->UnitOfMeasure : '').'
                     </td>';

               $td.= '<td valign="top" class="item item-price" align="right">
                       '.($unitprice != 0 ? number_format($unitprice,2) : '').'
                      </td>';

               $td.= '<td valign="top" class="item item-amount" align="right">
                       '.($amount != 0 ? number_format($amount,2) : '').'
                      </td>';

           $td.= '</tr>';
       }
       $td.= '</tbody>';
       $td.= '</table>';
       $td.= '</div>';
       echo $td;
   }
?>
<?php if($print->show_footer_at_last==1){ ?>
<sethtmlpagefooter name="ewinFooter" page="O" value="on" show-this-page="1" />
<?php } ?>
