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
            margin-left:20px;
            margin-right:60px;
            width:100%; 
            height:{$body->height};                 
         " >
            <div style="border: 0.05em solid #000; height: 30px;"></div>
            <div style="border: 0.05em solid #000; width: 1%;   height: {$body->height}; margin: -30 0 0 0; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 55%;  height: {$body->height}; margin: 0 0 0 -2.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 12%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 15%;  height: {$body->height}; margin: 0 0 0 -0.2mm;  float: left;"></div>
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
       
      <th style="" align="center" colspan="2" >{$Yii::t('common','Product Code / Detail')}</th>
      <th style="" align="right">{$Yii::t('common','Quantity')}</th>
      <th style="" align="right">{$Yii::t('common','Price Per Unit')}</th>
      <th style="padding-right:5px;" align="right">{$Yii::t('common','Amount')}</th>
    </tr>
</thead>
<tbody>
HTML;
        foreach ($data[$i] as $line) {

            // Initial
            if ($line->code_no_!='1^x') $ix++;
            
            if($line->code_desc_==''):
                $DESCRIPTION = mb_substr($line->items->description_th, 0,$subStr);
            else:
                $DESCRIPTION = mb_substr($line->code_desc_,0,$subStr);
            endif;        
            
            //$ITEMCODE   = $line->items->master_code;
            $ITEMCODE   = $line->crossreference->no;
            $QUANTITY   = number_format(abs($line->quantity)).' '.$line->items->UnitOfMeasure;
            $UNITPRICE  = number_format(abs($line->unit_price),2);
            $AMOUNT     = number_format(abs($line->quantity * $line->unit_price),2);

            // ITEM TEXT
            if($line->item=='1414'){ 
                switch ($line->code_no_) {
                    case '1^x':
                        // CASE ...
                        $ITEMCODE   = '';
                        $QUANTITY   = '';
                        $UNITPRICE  = '';
                        $AMOUNT     = '';
                        break;
                    
                    default:
                        // MANUAL CODE
                        $ITEMCODE   = $line->code_no_;
                        $QUANTITY   = number_format(abs($line->quantity)).' '.$line->items->UnitOfMeasure;
                        $UNITPRICE  = number_format(abs($line->unit_price),2);
                        $amount     = number_format(abs($line->quantity * $line->unit_price),2);
                        break;
                }                          
            }
            
            $td.= '<tr>';
            $td.= ' <td valign="top" class="item item-count" align="center" >'.(($line->code_no_!='1^x')? $ix : ' ').'</td>';
            $td.= ' <td valign="top" colspan="2" class="item item-desc">'.$ITEMCODE.' '.$DESCRIPTION.'</td>';
            $td.= ' <td valign="top" align="right" class="body-qty" >'.$QUANTITY.'</td>';
            $td.= ' <td valign="top" align="right" class="body-price">'.$UNITPRICE.'</td>';
            $td.= ' <td valign="top" align="right" class="body-amount" style="padding-right:5px;">'.$AMOUNT.'</td>';
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
