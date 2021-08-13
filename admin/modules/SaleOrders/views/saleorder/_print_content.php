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
            <div style="border: 0.05em solid #000; width: 36%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 12%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 12%;  height: {$body->height}; margin: 0 0 0 -0.2mm;  float: left;"></div>
            <div style="border: 0.05em solid #000; width: 8%;  height: {$body->height}; margin: 0 0 0 -0.2mm;  float: left;"></div>
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

       ($ix > 1 )? $brackpage = '<pagebreak />' : $brackpage = NULL;

       $td =
<<<HTML
{$brackpage }

{$showTable}
<div class="body">
<table border="0" cellpadding="2" cellspacing="0"  width="100%"  style="width: 100%;">
  <thead>
    <tr>
      <th style="height:40px; padding-left:5px;">{$Yii::t('common','No..')}</th>
      <th style="" align="left" >{$Yii::t('common','Code')}</th>
      <th style="" align="left" >{$Yii::t('common','Description')}</th>
      <th style="" align="right">{$Yii::t('common','Quantity')}</th>
      <th style="" align="right">{$Yii::t('common','Unit Price')}</th>
      <th style="" align="right">{$Yii::t('common','Discount')} %</th>
      <th style="padding-right:15px;" align="right">{$Yii::t('common','Amount')}</th>
    </tr>
</thead>
<tbody>
HTML;

    foreach ($data[$i] as $line) {
        $ix++;
        //$unitprice = $line->unit_price - $line->line_discount;
        $unitprice  = $line->unit_price;
        $sumLine    = $line->sumLine;
        $Discount   = $line->line_discount * 1;
        //$amount     = $line->quantity * ($unitprice);

        $td.= '<tr>';

            $td.= '<td valign="top" class="item item-count" align="center" >'.$ix.'. </td>';

            $itemCode      =   $line->items ? $line->items->master_code : '';
            if($line->item == 1414 ) $itemCode = '';

            $td.= '<td valign="top" class="item item-code">'.$itemCode.'</td>';

            ($line->description=='')?
            $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->items->description_th, 0,$subStr).'</td>'
            :
            $td.= '<td valign="top" class="item item-desc">'.mb_substr($line->description,0,$subStr).'</td>';            

            $td.= '<td valign="top" class="item item-measure" align="right" >'.number_format($line->quantity).' '.($line->items ? $line->items->UnitOfMeasure : 'PCS').'</td>';

            $td.= '<td valign="top" class="item item-price" align="right">'.number_format($unitprice,2).'</td>';

            $td.= '<td valign="top" class="item item-discount" align="right">'.number_format($Discount).'</td>';

            $td.= '<td valign="top" style="padding-right:15px;" class="item item-amount" align="right">'.number_format($sumLine,2).'</td>';

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
