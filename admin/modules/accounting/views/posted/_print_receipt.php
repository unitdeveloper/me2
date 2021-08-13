<?php
 

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
        margin-left: 20px;
        margin-right: 60px;
        width:100%; 
        height:{$body->height};        
        ">
        <div style="border: 0.05em solid #000; height: 30px;"></div> 
        <div style="border: 0.05em solid #000; width: 0.5%;   height: {$body->height}; margin: -30 0 0 0; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 14%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 44%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 10%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 9%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 7%;  height: {$body->height}; margin: 0 0 0 -0.2mm;  float: left;"></div>             
    </div>
HTML;

 
?>

<?php

   // Set line amount per page.
   $showTable       = $print->show_table==0 ? ' ' : $table;
   $subStr          = (isset($_GET['substr'])) ? $_GET['substr'] : $subStr;
   $PerPage         = $body->pagesize;
   $HeaderHeight    = 220 - Yii::$app->session->get('toppage');
   $AllData         = $dataProvider->getTotalCount();
   $data            = $dataProvider->models;
   $data            = array_chunk($data, $PerPage);
   $ix              = 0;

   for ($i=0; $i < count($data); $i++) {
       $brackpage = $ix > 1 ? '<pagebreak />' : NULL;
       $td =
<<<HTML
{$brackpage }

{$showTable}
<div class="body">
<table border="0" cellpadding="2" cellspacing="0"  width="100%"  style="width: 100%;">
  <thead>
    <tr>
      <th style="height:40px; padding-left:5px;"> # </th>
      <th style="" align="left" >{$Yii::t('common','No')}</th>
      <th style="" align="left" >{$Yii::t('common','Detail')}</th>
      <th style="" align="center">{$Yii::t('common','Quantity')}</th>
      <th style="" align="center">{$Yii::t('common','Price/Unit')}</th>
      <th style="" align="center">{$Yii::t('common','Discount')}</th>
      <th style="padding-right:5px;" align="center">{$Yii::t('common','Total')}</th>
    </tr>
</thead>
<tbody>
HTML;

    foreach ($data[$i] as $line) {
        $ix++;
        $unitprice = $line->unit_price;
        $amount    = $line->quantity * $line->unit_price;

        $td.= '<tr>';
            $td.= '<td valign="top" class="item item-count" align="center" >'.$ix.'. </td>';
            $td.= '<td valign="top" class="item item-code">'.($line->items->No=='1^x' ? '' : $line->items->master_code).'</td>';
            $line->code_desc_==''
                ? $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->items->description_th, 0,$subStr).'</td>'
                : $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->code_desc_,0,$subStr).'</td>';            
            $td.= '<td valign="top" class="item" style="padding-right:5px; width:70px;" align="right" >'.number_format($line->quantity).' '.$line->items->UnitOfMeasure.'</td>';
            $td.= '<td valign="top" class="item" style="padding-right:5px; width:70px;" align="right">'.number_format($unitprice,2).'</td>';
            $td.= '<td valign="top" class="item" style="padding-right:5px; width:50px;" align="right">'.($line->line_discount > 0 ? ($line->line_discount * 1).'%' : ' ').'</td>';
            $td.= '<td valign="top" style="padding-right:5px; width:80px;" class="item" align="right">'.number_format($amount,2).'</td>';
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