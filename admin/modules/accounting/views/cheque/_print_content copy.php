<?php
 

$Yii            = 'Yii';
$subStr         = 50;
$table_top      = (int)$header->height + (int)$print->margin_top + 65;
$showHeader     = Yii::$app->request->get('head') == 'false' ? 'visibility: hidden;' : ' ';
?>

<?php

$table=<<<HTML
    <div style="
        position:absolute; 
        overflow: visible; 
        top:{$table_top}; 
        left:0px;
        margin-left: 20px;
        margin-right: 55px;
        width:100%; 
        height:{$body->height};        
        ">
        <div style="border: 0.05em solid #000; height: 30px;"></div> 
        <div style="border: 0.05em solid #000; width: 0.5%;   height: {$body->height}; margin: -30 0 0 0; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 14%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 14%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 14%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 18%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 9%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
        <div style="border: 0.05em solid #000; width: 16%;  height: {$body->height}; margin: 0 0 0 -0.2mm;  float: left;"></div>             
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
  <thead >
    <tr >
      <th style="height:40px; padding-left:5px;"><span style="{$showHeader}"> # </span></th>
      <th align="left"><span style="{$showHeader}">เลขที่ใบกำกับ </span></th>
      <th align="center"><span style="{$showHeader}">{$Yii::t('common','Date')}</span></th>
      <th align="center"><span style="{$showHeader}">{$Yii::t('common','Due date')}</span></th>
      <th align="right"><span style="{$showHeader}">{$Yii::t('common','Amount')}</span></th>
      <th align="right"><span style="{$showHeader}">{$Yii::t('common','Discount')}</span></th>       
      <th align="right"><span style="{$showHeader}">ยอดชำระ</span></th>
      <th align="right"><span style="{$showHeader}">ยอดคงค้าง</span></th>
    </tr>
</thead>
<tbody>
HTML;

    foreach ($data[$i] as $line) {
        $ix++;
         
        $amount    = $line->balance;
        $d      = date('d', strtotime($line->invoice->posting_date));
        $m      = date('m', strtotime($line->invoice->posting_date));
        $yTh    = date('Y', strtotime($line->invoice->posting_date)) + 543;
        $y      = date('y', strtotime($yTh));

        $d_p    = date('d', strtotime($line->post_date_cheque));
        $m_p    = date('m', strtotime($line->post_date_cheque));
        $yTh_p  = date('Y', strtotime($line->post_date_cheque)) + 543;
        $y_p    = date('y', strtotime($yTh_p));

        $ivDiscount     = $line->inv->discount;
        $ivTotal        = $line->inv->sumtotals->total;
        $Received       = $line->balance;

        $remain         = $ivTotal - $Received;

        $td.= '<tr>';
            $td.= '<td valign="top" class="item item-count" align="center" >'.$ix.'. </td>';
            $td.= '<td valign="top" class="item">'.$line->apply_to_no.'</td>';
            $td.= '<td valign="top" class="item" align="center">'.$d.'/'.$m.'/'.$y.'</td>';
            $td.= '<td valign="top" class="item" align="center">'.$d_p.'/'.$m_p.'/'.$y_p.'</td>';
            $td.= '<td valign="top" class="item" align="right">'.number_format($ivTotal,2).'</td>';
            $td.= '<td valign="top" class="item" align="right">'.number_format($ivDiscount,2).'</td>';
             
            $td.= '<td valign="top" class="item" align="right">'.number_format($Received,2).'</td>';
            $td.= '<td valign="top" class="item" align="right">'.number_format($remain,2).'</td>';
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