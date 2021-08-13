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
            <div style="border: 0.05em solid #000; width: 55%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 15%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div> 
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
   $PerPage         = 10;
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
      <th style="font-size:12px;" align="left">{$Yii::t('common','Code')}</th>
      <th style="font-size:12px;" >{$Yii::t('common','Description')}</th>
      <th style="font-size:12px; padding-right:10px;" align="right">{$Yii::t('common','Quantity')}</th>
      <th style="font-size:12px; padding-right:10px;" align="right">{$Yii::t('common','Measure')}</th>
    </tr>
</thead>
<tbody>
HTML;
       foreach ($data[$i] as $line) {
           $ix++;
           $unitprice = 0;
           $amount    = 0;
           $td.= '<tr >';
               $td.= '<td valign="top" class="item item-count" align="center" >'.$ix.'. </td>';
               $itemCode      =   $line->code ? $line->code : $line->items->master_code;
               if($line->item==1414) $itemCode = '';
               $td.= '<td valign="top" class="item item-code">'.$itemCode.'</td>';
               if($line->name==''){
                    $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->items->name, 0,$subStr).'</td>';
                 }else {
                    $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->name,0,$subStr).'</td>';
                 }
                $td.= '<td valign="top" class="item item-quantity"  align="right" >'.number_format($line->quantity).'</td>';
                $td.= '<td valign="top" class="item item-measures"  align="right"  >'.$line->measures.'</td>'; 
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
