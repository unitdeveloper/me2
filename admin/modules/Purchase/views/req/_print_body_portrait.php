<?php
$table_top      = (int)$header->height + (int)$print->margin_top + 47;
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
            <div style="border: 0.05em solid #000; width: 46%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 10%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 10%;  height: {$body->height}; margin: 0 0 0 -0.2mm; float: left;"></div>
            <div style="border: 0.05em solid #000; width: 15%;  height: {$body->height}; margin: 0 0 0 -0.2mm;  float: left;"></div>
            <div style="border: 0.05em solid #000;              height: {$body->height}; margin: 0 37px 0 -0.2mm; float: left;"></div>
        </div>
HTML;

($print->show_table==0)? $showTable = ' ' : $showTable = $table;
?>
<?php
 

$Yii            = 'Yii';
$subStr         = 40;
if(isset($_GET['substr']))      $subStr     = $_GET['substr'];

   // Set line amount per page.
   $PerPage         = $body->pagesize;
   $HeaderHeight    = $header->height - Yii::$app->session->get('toppage');
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
    <table border="0" cellpadding="0" cellspacing="0"  style="width: 100%;" class="table-body">
        <thead>        
            <tr>          
            
                <th class="border-bottom-start pr-count" align="left">{$Yii::t('common','#')}</th>

                <th class="border-bottom-start pr-list" align="center" >{$Yii::t('common','List')}</th>

                <th class="border-bottom-start" align="right">{$Yii::t('common','Quantity')}</th>

                <th class="border-bottom-start" align="center">{$Yii::t('common','Unit')}</th>

                <th class="border-bottom-start" align="right">{$Yii::t('common','Price/Unit')}</th>            

                <th class="border-bottom-start pr-amount" align="right">{$Yii::t('common','Total Amount')}</th>

            </tr>
        </thead>
    <tbody>
HTML;
       foreach ($data[$i] as $line) {
            $ix++;
            $unitprice = $line->unitcost;
            $qty       = $line->quantity * 1;
            $amount    = $line->quantity * $line->unitcost;
            $td.= '<tr>';
                $td.= '<td valign="top" class="item pr-count" align="left" >'.$ix.'. </td>';
                // $itemCode      =   $line->items_no;
                // $td.= '<td valign="top" class="item item-code">'.$itemCode.'</td>';
                if($line->description==''){
                    $td.= '<td valign="top" class="item desc">'.mb_substr($line->items->description_th, 0,$subStr).'</td>';
                }else {
                    $td.= '<td valign="top" class="item desc">'.mb_substr($line->description,0,$subStr).'</td>';
                }
                $td.= '<td valign="top" class="item item-qty" align="right" >'.number_format($qty,2).' </td>';
                
                $td.= '<td valign="top" class="item po-measure" align="center" >
                            '.($line->unitofmeasures != null ? $line->unitofmeasures->UnitCode : ' ').'
                        </td>';
                //$td.= '<td valign="top" class="item item-date" align="center">'.$line->expected_date.'</td>';
                $td.= '<td valign="top" class="item price" align="right">'.number_format($unitprice,3).'</td>';
                $td.= '<td valign="top" class="item pr-amount" align="right">'.number_format($amount,3).'</td>';


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
