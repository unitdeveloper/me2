<?php
 
use yii\helpers\Html;
use yii\helpers\Url;
 
 
use common\models\ItemsHasGroups;
use common\models\ItemgroupCommon;
 
$this->title = Yii::t('common', 'Consumption');
$this->params['breadcrumbs'][] = $this->title;
 
$company    = \common\models\Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
$workdate   = Yii::$app->session->get('workdate');
$GLOBALS['reloaded']    = $recal;
 
 
 
function getListMenu($header,$i,$ii){

    global $reloaded;

    $models = ItemgroupCommon::find()
    ->where(['child' => $header->id])
    ->orderBy(['sequent' => SORT_ASC])
    ->all();
 

    
    $html = ' ';
    $padding = 20;
    if($i == 1){
        $html.= '<thead>'."\r\n";
        $html.= '   <tr class="bg-gray">'."\r\n";
        $html.= '       <th style="width:50px;">'.Yii::t('common','#').'</th>';
        $html.= '       <th style="width:150px;">'.Yii::t('common','Name').'</th>';  
        $html.= '       <th style="width:350px;">'.Yii::t('common','Detail').'</th>';            
        $html.= '       <th class="text-right th-number m-1 col">'.Yii::t('common','{:date} month',[':date' => 1]).'</th>';
        $html.= '       <th class="text-right th-number m-2 col">'.Yii::t('common','{:date} month',[':date' => 2]).'</th>';
        $html.= '       <th class="text-right th-number m-3 col">'.Yii::t('common','{:date} month',[':date' => 3]).'</th>';
        $html.= '       <th class="text-orange th-number text-right q-1 col">'.Yii::t('common','Quarter {:q}',[':q' => 1]).'</th>';
        
        $html.= '       <th class="text-right th-number m-4 col">'.Yii::t('common','{:date} month',[':date' => 4]).'</th>';
        $html.= '       <th class="text-right th-number m-5 col">'.Yii::t('common','{:date} month',[':date' => 5]).'</th>';
        $html.= '       <th class="text-right th-number m-6 col">'.Yii::t('common','{:date} month',[':date' => 6]).'</th>';
        $html.= '       <th class="text-orange th-number text-right q-2 col">'.Yii::t('common','Quarter {:q}',[':q' => 2]).'</th>';

        $html.= '       <th class="text-right th-number m-7 col">'.Yii::t('common','{:date} month',[':date' => 7]).'</th>';
        $html.= '       <th class="text-right th-number m-8 col">'.Yii::t('common','{:date} month',[':date' => 8]).'</th>';
        $html.= '       <th class="text-right th-number m-9 col">'.Yii::t('common','{:date} month',[':date' => 9]).'</th>';
        $html.= '       <th class="text-orange th-number text-right q-3 col">'.Yii::t('common','Quarter {:q}',[':q' => 3]).'</th>';

        $html.= '       <th class="text-right th-number m-10 col">'.Yii::t('common','{:date} month',[':date' => 10]).'</th>';
        $html.= '       <th class="text-right th-number m-11 col">'.Yii::t('common','{:date} month',[':date' => 11]).'</th>';
        $html.= '       <th class="text-right th-number m-12 col">'.Yii::t('common','{:date} month',[':date' => 12]).'</th>';
        $html.= '       <th class="text-orange th-number text-right q-4 col">'.Yii::t('common','Quarter {:q}',[':q' => 4]).'</th>';
        
        $html.= '       <th class="text-green text-right th-number st-1 col">'.Yii::t('common','Stock').'</th>';
        $html.= '   </tr>'."\r\n";
        $html.= '</thead>'."\r\n";
        $html.= '<tbody id="data">'."\r\n";
    }
    
    foreach ($models as $key => $model) {
        $i++;
 
        foreach (ItemsHasGroups::find()->where(['group_id' => $model->id])->all() as $key => $group) {
            $ii++;
            if($reloaded ==1){
                // Re calculate (slow)
                $Tmp = $group->items->tmpItemStockMonthlyReload;
            }else{
                // call already in hour (fast)
                $Tmp = $group->items->tmpItemStockMonthly;
            }

            $html.= '       <tr key="'.$group->id.'" data-row="'.$model->id.'" data-date="'.$Tmp->last_update.'" class="row-data">'."\r\n"; 
            $html.= '           <td class="text-center key-number" style="font-family: roboto;"> </td>'."\r\n";

            if($key == 0) {               
            $html.= '           <td data-key="'.$model->id.'" rowspan="'.$model->countItem.'" valign="middle" align="center" style="vertical-align: middle;" class="bg-w1 merge-group"><h5>'.$model->name.'</h5></td>'."\r\n";
            }

            $html.= '           <td>'.$group->items->description_th.'</td>'."\r\n";

            $Jan        = $Tmp->Jan;
            $Feb        = $Tmp->Feb;
            $Mar        = $Tmp->Mar;
            $Quarter1   = ($Jan + $Feb + $Mar) / 3;

            $Apr        = $Tmp->Apr;
            $May        = $Tmp->May;
            $Jun        = $Tmp->Jun;
            $Quarter2   = ($Apr + $May + $Jun) / 3;

            $Jul        = $Tmp->Jul;
            $Aug        = $Tmp->Aug;
            $Sep        = $Tmp->Sep;
            $Quarter3   = ($Jul + $Aug + $Sep) / 3;

            $Oct        = $Tmp->Oct;
            $Nov        = $Tmp->Nov;
            $Dec        = $Tmp->December;
            $Quarter4   = ($Oct + $Nov + $Dec) / 3;

            $inven      = $Tmp->inven;
            // $janx        = $group->items->getInvenByMonth(1);
            // $feb        = $group->items->getInvenByMonth(2);
            // $mar        = $group->items->getInvenByMonth(3);
            // $Quarter1   = $jan + $feb + $mar;
            // $api        = $group->items->getInvenByMonth(4);
            // $may        = $group->items->getInvenByMonth(5);
            // $june       = $group->items->getInvenByMonth(6);
            // $Quarter2   = $api + $may + $june;
            

            $html.= '           <td class="text-right m-1 col">'.Html::a(number_format($Jan),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id),'WarehouseSearch[month]' => 1],['target' => '_blank']).'</td>'."\r\n";
            $html.= '           <td class="text-right m-2 col">'.Html::a(number_format($Feb),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id),'WarehouseSearch[month]' => 2],['target' => '_blank']).'</td>'."\r\n";
            $html.= '           <td class="text-right m-3 col">'.Html::a(number_format($Mar),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id),'WarehouseSearch[month]' => 3],['target' => '_blank']).'</td>'."\r\n";
            $html.= '           <td class="text-orange text-right bg-w1 q-1 col" style="font-family: roboto;">'.number_format($Quarter1).'</td>'."\r\n";

            $html.= '           <td class="text-right m-4 col">'.Html::a(number_format($Apr),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id),'WarehouseSearch[month]' => 4],['target' => '_blank']).'</td>'."\r\n";
            $html.= '           <td class="text-right m-5 col">'.Html::a(number_format($May),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id),'WarehouseSearch[month]' => 5],['target' => '_blank']).'</td>'."\r\n";
            $html.= '           <td class="text-right m-6 col">'.Html::a(number_format($Jun),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id),'WarehouseSearch[month]' => 6],['target' => '_blank']).'</td>'."\r\n";
            $html.= '           <td class="text-orange text-right bg-w1 q-2 col" style="font-family: roboto;">'.number_format($Quarter2).'</td>'."\r\n";

            $html.= '           <td class="text-right m-7 col">'.Html::a(number_format($Jul),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id),'WarehouseSearch[month]' => 7],['target' => '_blank']).'</td>'."\r\n";
            $html.= '           <td class="text-right m-8 col">'.Html::a(number_format($Aug),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id),'WarehouseSearch[month]' => 8],['target' => '_blank']).'</td>'."\r\n";
            $html.= '           <td class="text-right m-9 col">'.Html::a(number_format($Sep),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id),'WarehouseSearch[month]' => 9],['target' => '_blank']).'</td>'."\r\n";
            $html.= '           <td class="text-orange text-right bg-w1 q-3 col" style="font-family: roboto;">'.number_format($Quarter3).'</td>'."\r\n";
            
            $html.= '           <td class="text-right m-10 col">'.Html::a(number_format($Oct),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id),'WarehouseSearch[month]' => 10],['target' => '_blank']).'</td>'."\r\n";
            $html.= '           <td class="text-right m-11 col">'.Html::a(number_format($Nov),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id),'WarehouseSearch[month]' => 11],['target' => '_blank']).'</td>'."\r\n";
            $html.= '           <td class="text-right m-12 col">'.Html::a(number_format($Dec),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id),'WarehouseSearch[month]' => 12],['target' => '_blank']).'</td>'."\r\n";
            $html.= '           <td class="text-orange text-right bg-w1 q-4 col" style="font-family: roboto;">'.number_format($Quarter4).'</td>'."\r\n";
            
            $html.= '           <td class="text-right bg-dark st-1 col" style="font-family: roboto;">'.Html::a(
                '<span class="'.($inven < 0 ? 'text-red' : 'text-green').'">'.number_format($inven).'</span>',['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id)],['target' => '_blank']).'</td>'."\r\n";
            $html.= '       </tr>'."\r\n";
           
            
        }
        
        $model->child > 0 ?  $html.= getListMenu($model,$i,$ii) : NULL;      

        
    }

    $i == 1 ? $html.= '<tbody>'."\r\n" : NULL;
   
    
     
    return $html;
}

 

?> 
<div class="scroll-div2">
    <table class="table table-bordered table-hover" id="export_table" >     
        <?php 
            $i = 0;
            $ii = 0;
            $html = '';
                foreach ($group as $key => $model) {
                    $i++;                             
                    $html.= getListMenu($model,$i,$ii);                                  
                }
            echo $html;             
        ?>
    </table>
</div>
 