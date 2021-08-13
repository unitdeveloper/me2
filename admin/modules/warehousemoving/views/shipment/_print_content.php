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
            
         " >
            <div style="border: 0.05em solid #000; height: 30px;"></div> 
            <div style="border: 0.05em solid #000; width: 0.8%;   height: {$body->height}; margin: -30 0 0 0; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 14%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 48%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 10%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 11%;  height: {$body->height}; margin: 0 0 0 -0.2mm;  float: left;"></div>             
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

       ($ix > 1 )? $brackpage = '<pagebreak />' : $brackpage = NULL;

       $td =
<<<HTML
{$brackpage }

{$showTable}
<div class="body">
<table border="0" cellpadding="2" cellspacing="0"  width="100%"  style="width: 100%;">
  <thead>
    <tr>
      <th style="height:40px; padding-left:5px;"> </th>
      <th style="" align="left" > </th>
      <th style="" align="left" > </th>
      <th style="" align="right"> </th>
      <th style="" align="right"> </th>
      <th style="padding-right:15px;" align="right"> </th>
    </tr>
</thead>
<tbody>
HTML;

    foreach ($data[$i] as $line) {
        $ix++;
        $unitprice = $line->unit_price;
        $amount    = $line->Quantity * $line->unit_price;

        $td.= '<tr>';

            $td.= '<td valign="top" class="item item-count" align="center" >'.$ix.'. </td>';

            $itemCode      =   $line->items->master_code;
            if($line->items->No=='1^x') $itemCode = '';

            $td.= '<td valign="top" class="item item-code">'.$itemCode.'</td>';

            ($line->code_desc_=='')?
            $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->items->description_th, 0,$subStr).'</td>'
            :
            $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->code_desc_ , $subStr).'</td>';            

            $td.= '<td valign="top" class="item item-measure" align="right" >'.number_format($line->Quantity).' '.$line->items->UnitOfMeasure.'</td>';

            $td.= '<td valign="top" class="item item-price" align="right">'.number_format($unitprice,2).'</td>';

            $td.= '<td valign="top" style="padding-right:15px;" class="item item-amount" align="right">'.number_format($amount,2).'</td>';

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
