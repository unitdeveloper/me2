<?php
 
use yii\helpers\Html;
use yii\helpers\Url;
 
 
use common\models\ItemsHasGroups;
use common\models\ItemgroupCommon;
 
$this->title = Yii::t('common', 'Inventory');
$this->params['breadcrumbs'][] = $this->title;

$comp       = Yii::$app->session->get('Rules')['comp_id'];
$company    = \common\models\Company::findOne($comp);
$workdate   = Yii::$app->session->get('workdate');
 
function getListMenu($id,$i){

    $models = ItemgroupCommon::find()
    ->where(['child' => $id])
    ->orderBy(['sequent' => SORT_ASC])
    ->all();

    $html = '';
    $padding = 20;
    foreach ($models as $key => $model) {

        if($model->countItem > 0){
            $html.= '<tr class="bg-gray">'; 
            $html.= '   <th class="pl-10 item-name" ><h5>'.$model->name.' ('.$model->countItem.')</h5></th>';
            $html.= '   <th class="text-right item-inven">'.Yii::t('common','Quantity').'</th>';
            $html.= '</tr>';
        }

        
        $groups = ItemsHasGroups::find()->where(['group_id' => $model->id])->all();
        foreach ($groups as $key => $group) {

            $Inven  = $group->items->ProductionBom > 0
                        ? $group->items->myItems->last_possible
                        : $group->items->myItems->last_stock;

            $code   = $group->items->barcode != '' 
                        ? '<div>'.$group->items->master_code.'</div> <div class="hidden">'.$group->items->barcode.'</div>'
                        : $group->items->master_code;

            $name   = $group->items->alias != ''
                        ? '<div style="font-family: 15px;">'.Html::a($group->items->alias,['/items/items/view-only','id' => $group->items->id],['target' => '_blank']).'</div>
                            <small class="text-dark">'.$group->items->description_th.'</small>'
                        : Html::a($group->items->description_th,['/items/items/view-only','id' => $group->items->id],['target' => '_blank']);

            $img    = Html::img($group->items->picture,['class' => 'img-responsive','style' => 'max-width:80px;']);

            $html.= '<tr>'; 
            $html.= '   <td class="set-xs-padding">
                            <div class="col-md-3 col-sm-4 hidden-xs">
                                <span class="pull-left mr-5">'.$img.'</span>
                                '.Html::a($code,['/items/items/view-only','id' => $group->items->id],['target' => '_blank']).'
                            </div>
                            <div class="col-md-9 col-sm-8">
                                <span class="pull-left mr-5 hidden-sm hidden-md hidden-lg">'.$img.'</span>'.$name.'
                                <div class="hidden-sm hidden-md hidden-lg text-gray">'.$code.'</div>
                            </div>
                        </td>';
            $html.= '   <td class="text-right inventory">'.Html::a(number_format($Inven),['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($group->items->id)],['target' => '_blank']).'</td>';
            $html.= '</tr>';
        }
        

        if($model->child > 0){            
            $html.= getListMenu($model->id,$i);            
        }

    }
 
    return $html;
}

 

?>
 
<style>
    .pl-10{
        padding-left:10px !important;
    }
    .pl-20{
        padding-left:20px !important;
    }
    #myInput {
        background-color: #fbffff;
    }
    .search-box-up{
        position: fixed;
        width: 100%;
        background: rgba(69, 70, 92, 0.96);
        top: 0px;
        z-index: 2000;
        padding: 10px 0px 0px;
        padding-right: 232px;
    }

    .search-box-down{
        position: relative;
        width: auto;
        background: none;
        top: 0px;
        z-index: 1000;
        padding: 10px 0px 0px;
    }

    .img-company-logo{
        display:none;
    }

    @media (max-width: 767px) {
        .search-box-up{
            position: fixed;
            width: 100%;
            background: rgba(69, 70, 92, 0.96);
            top: 0px;
            z-index: 2000;
            padding: 10px 0px 0px;
        }

        .search-box-down{
            position: relative;
            width: auto;
            background: none;
            top: 0px;
            z-index: 2000;
            padding: 10px 0px 0px;
        }
        
        .set-xs-padding{
            padding-left: 0px !important;
            height:100px;
        }

        td.inventory{
           /* width:80px !important;*/
        }

        #export_wrapper{
            margin: 0px -15px 0px -15px;
        }

    }

    @media print {
            @page {
                size: A4 landscape;
            }
 
            .img-responsive{
                width:30px;
                margin-right:10px;
            }
            .search-box,
            #myBtn{
                visibility: hidden;
            }
            .img-company-logo{
                width:80px;
                display: block;
            }
        }

</style>
<div class="row">
    <div class="col-xs-12">
        <?= Html::img($company->logoViewer,['class' => 'img-company-logo']) ?>
        <h4><?=$this->title;?> <?=Yii::t('common','For date')?> : <?= date('d',strtotime($workdate))?> <?= Yii::t('common',date('M',strtotime($workdate)))?> <?= date('Y',strtotime($workdate))?></h4>
    </div>
    <div class="col-xs-12">
        
        <?= $company->name;?>
    </div>
</div>
<div class="row search-box">
    <div class="col-sm-4 col-xs-12 pull-right mb-10">
        <input id="myInput" class="form-control" type="text" placeholder="<?=Yii::t('common','Search')?>...">
    </div>
</div>

<div ng-init="Title='<?=$this->title;?>'">
    <div class="row" style="font-family: roboto;">
        <div class="col-xs-12 ">
            <div id="export_wrapper">                
                <table class="table table-bordered" id="export_table" >

                    <?php 
                        
                        $keys = 'items-stock-index&comp:'.$comp;
                        if(Yii::$app->cache->get($keys)){
                            echo Yii::$app->cache->get($keys);
                        }else{
                            $html   = '';
                            $i      = 0;
                            foreach ($group as $key => $model) {
                                $i++;
                                $html.= '<thead  >
                                            <tr class="bg-dark" >
                                                <td colspan="2"><h4 class="pointer" data-toggle="collapse" data-target="#content-'.$i.'" aria-expanded="true" aria-controls="content-'.$i.'">'.$model->name.'</h4></td>         
                                            </tr>
                                        </thead> ';

                                $html.= '<tbody class="collapse in" id="content-'.$i.'" >
                                            '.getListMenu($model->id,$i).'
                                        </tbody>';
                            }

                            Yii::$app->cache->set($keys, $html, 10);
                            echo Yii::$app->cache->get($keys);
                        }

                    ?>
                    
                    
                </table>
            </div>
        </div>
    </div>
</div>
<button  id="myBtn" class="btn btn-default" style="position: fixed; bottom: 5px; right: 10px; z-index: 99; color:red;"><i class="fas fa-arrow-up"></i> Top</button>

<?php 
 
$Yii = 'Yii';
$js=<<<JS

$('body').on('click','#myBtn',function(){
    topFunction();
});
// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {

    if (document.body.scrollTop > 5 || document.documentElement.scrollTop > 5) {
        $('.search-box').addClass('search-box-up').removeClass('search-box-down');
        // $('.search-box').css({
        //     'position':'fixed',
        //     'top': '0px',
        //     'zIndex': '2000',
        //     'width' : '100%',
        //     'padding': '10px 0 0 0',
        //     'background': '#ccc'
        // })
    }else{
        $('.search-box').addClass('search-box-down').removeClass('search-box-up');
        // $('.search-box').css({
        //     'position':'relative',
        //     'padding': 'false',
        //     'width' : 'auto',
        //     'background': 'none'
        // })
    }

    if (document.body.scrollTop > 10 || document.documentElement.scrollTop > 10) {
        document.getElementById("myBtn").style.display = "block";
    } else {
        document.getElementById("myBtn").style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}


$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#export_table tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });

    topFunction();
  });

/*
  var table = $('table#export_table');
    // ยกเลิก เพราะ เรียงตัวเลข (แต่ไม่เรียงกลุ่ม)
    $('.item-inven-cancel')
        .wrapInner('<span title="sort this column"/>')
        .each(function(){
            
            var th = $(this),
                thIndex = th.index(),
                inverse = false;
            
            th.click(function(){
                
                table.find('td').filter(function(){
                    
                    return $(this).index() === thIndex;
                    
                }).sortElements(function(a, b){
                    
                    return $.text([a]) > $.text([b]) ?
                        inverse ? -1 : 1
                        : inverse ? 1 : -1;
                    
                }, function(){
                    
                    // parentNode is the element we want to move
                    return this.parentNode; 
                    
                });
                
                inverse = !inverse;
                    
            });
                
        });
*/
});



JS;
$this->registerJS($js,\yii\web\View::POS_END,'Yii');
//$this->registerJsFile('https://rawgit.com/padolsey/jQuery-Plugins/master/sortElements/jquery.sortElements.js',['depends' => [\yii\web\JqueryAsset::className()]]);

?>