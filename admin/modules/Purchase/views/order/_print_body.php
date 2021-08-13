<?php
$table_top      = (int)$header->height + (int)$print->margin_top + 75;
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
            <div style="border: 0.05em solid #000; width: 39%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 17%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 11%;  height: {$body->height}; margin: 0 0 0 -0.2mm;  float: left;"></div>
            <div style="border: 0.05em solid #000;              height: {$body->height}; margin: 0 37px 0 -0.2mm; float: left;"></div>
        </div>
HTML;

($print->show_table==0)? $showTable = ' ' : $showTable = $table;
?>
<?php

$Yii            = 'Yii';
$subStr         = 50;
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
      <th style="font-size:12px; height:40px; padding-left:-5px;">ลับดับ</th>
      <th style="font-size:12px;" colspan="2">{$Yii::t('common','Description')}</th>
      <th colspan="2"  style="font-size:12px;  padding-right:-20px;" align="center">{$Yii::t('common','Quantity')}</th>
      <th style="font-size:12px; padding-right:-1mm;" align="center">{$Yii::t('common','Unit Price')}</th>
      <th style="font-size:12px; padding-right:-7mm;" align="center">{$Yii::t('common','Amount')}</th>
    </tr>
</thead>
<tbody>
HTML;
       foreach ($data[$i] as $line) {
           $ix++;
           $unitprice = $line->unitcost;
           $amount    = $line->quantity * $line->unitcost;
           $td.= '<tr>';
               $td.= '<td valign="top" class="item item-count" align="center" >'.$ix.'. </td>';
               $itemCode      =   $line->items_no ? $line->items_no : $line->items->master_code;
               if($line->item==1414) $itemCode = '';
               $td.= '<td valign="top" class="item item-code">'.$itemCode.'</td>';
               if($line->description==''){
                    $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->items->description_th, 0,$subStr).'</td>';
                 }else {
                    //$td.= '<td valign="top" class="item item-desc">'.mb_substr($line->description,0,$subStr).'</td>';
                    $td.= '<td valign="top" class="item item-desc"><div style=" ">'.($line->description).'</div></td>';
                 }
                $td.= '<td valign="top" class="item po-quantity" align="right" >'.number_format($line->quantity, 2).'</td>';
                $td.= '<td valign="top" class="item po-measure" align="left" >'.($line->unitofmeasures != null ? $line->unitofmeasures->UnitCode : ' ').'</td>';
                $td.= '<td valign="top" class="item item-price" align="right">'.number_format($unitprice,2).'</td>';
                $td.= '<td valign="top" class="item item-amount" align="right">'.number_format($amount,2).'</td>';
           $td.= '</tr>';
       }
       $td.= '</tbody>';
       $td.= '</table>';
       $td.= '</div>';
       echo $td;
   }
?>


<!-- Footer on Last Page -->
<?php if(isset($_GET['footer'])): ?>
<?php if($_GET['footer'] == 1) : ?>
<sethtmlpagefooter name="ewinFooter" page="O" value="on" show-this-page="1" />

<?php endif; ?>
<?php endif; ?>
<!-- /. Footer on Last Page -->
