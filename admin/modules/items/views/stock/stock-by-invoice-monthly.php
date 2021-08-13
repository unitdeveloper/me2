<?php
 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use dosamigos\multiselect\MultiSelect;

use common\models\ItemsHasGroups;
use common\models\ItemgroupCommon;
 
$this->title = Yii::t('common', 'Stock by bill');
$this->params['breadcrumbs'][] = $this->title;
$company    = \common\models\Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
$workdate   = Yii::$app->session->get('workdate');
$cache      = Yii::$app->cache;
$data2016   = $cache->get('StockByInvoiceAjax&years:2016'.'&comp:'.$company->id);
$data2017   = $cache->get('StockByInvoiceAjax&years:2017'.'&comp:'.$company->id);
$data2018   = $cache->get('StockByInvoiceAjax&years:2018'.'&comp:'.$company->id);
$data2019   = $cache->get('StockByInvoiceAjax&years:2019'.'&comp:'.$company->id);
$dataJan    = $cache->get('Stock-By-Invoice-Monthly&years:'.date('Y').'&month:1&comp:'.$company->id);
$dataFeb    = $cache->get('Stock-By-Invoice-Monthly&years:'.date('Y').'&month:2&comp:'.$company->id);
$dataMar    = $cache->get('Stock-By-Invoice-Monthly&years:'.date('Y').'&month:3&comp:'.$company->id);
$dataApr    = $cache->get('Stock-By-Invoice-Monthly&years:'.date('Y').'&month:4&comp:'.$company->id);
$dataMay    = $cache->get('Stock-By-Invoice-Monthly&years:'.date('Y').'&month:5&comp:'.$company->id);
$dataJun    = $cache->get('Stock-By-Invoice-Monthly&years:'.date('Y').'&month:6&comp:'.$company->id);
$dataJul    = $cache->get('Stock-By-Invoice-Monthly&years:'.date('Y').'&month:7&comp:'.$company->id);
$dataAug    = $cache->get('Stock-By-Invoice-Monthly&years:'.date('Y').'&month:8&comp:'.$company->id);
$dataSep    = $cache->get('Stock-By-Invoice-Monthly&years:'.date('Y').'&month:9&comp:'.$company->id);
$dataOct    = $cache->get('Stock-By-Invoice-Monthly&years:'.date('Y').'&month:10&comp:'.$company->id);
$dataNov    = $cache->get('Stock-By-Invoice-Monthly&years:'.date('Y').'&month:11&comp:'.$company->id);
$dataDec    = $cache->get('Stock-By-Invoice-Monthly&years:'.date('Y').'&month:12&comp:'.$company->id);

$count2016  = isset($data2016->count) ? $data2016->count : 0;
$count2017  = isset($data2017->count) ? $data2017->count : 0;
$count2018  = isset($data2018->count) ? $data2018->count : 0;
$count2019  = isset($data2019->count) ? $data2019->count : 0;

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
        z-index: 10;
    }
    .search-box-up{
        position: fixed;
        width: 100%;
        background: rgba(69, 70, 92, 0.96);
        top: 0px;
        z-index: 2000;
        margin-left: -15px; 
        padding-left:100px;
    }

    .search-box-down{
        position: relative;
        /* width: auto;
        background: none;
        top: 0px;
        z-index: 1;
        padding: 10px 0px 0px; */
    }

    .img-company-logo{
        display:none;
    }

    #export_table{
        width:100%;
    }

    #export_table .th-number{
        min-width: 80px;
    }

    .bg-w1 {
        background: rgba(236, 236, 236, 0.15);
    }

    .select2-selection{
        /* height: 34px !important; */

    }
    .item-code-on-modal {
        padding: 10px;
        background: #16ada6;
        color: #fff;
        font-size: 18px;
    }

    .open-modal-row-detail-month:hover,
    td.invoice-detail:hover,
    .open-modal-row-detail:hover{
        background: #3fbbea !important;
        color: #fff !important;
    }

    .bg-blur{
        background: rgba(153, 153, 153, 0.18) !important;
    }
    .bg-blur-more{
        background: rgb(71, 143, 182) !important;
    }
    .text-blur{
        color: rgba(204, 204, 204, 0.35) !important;
    }

    @media (max-width: 767px) {
        .search-box-up{
            position: fixed;
            width: 100%;
            background: rgba(69, 70, 92, 0.96);
            top: 0px;
            z-index: 2000;
            padding: 10px 0px 0px 0px;
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
        }

    }

    @media print {
            @page {
                size: A3 landscape;
                margin:0px;
                /* size: A4 portrait; */
            }

            .text-green,
            .text-red,
            .text-orange,
            .bg-orange,
            .bg-green{
                color:#000 !important;
            }

            .img-responsive{
                width:30px;
                margin-right:10px;
            }
            .search-box,
            #myBtn{
                display: none;
            }
            .img-company-logo{
                width:80px;
                display: block;
            }

            .wmd-view-topscroll {
                visibility: hidden;
            }
            .wmd-view-topscroll, .wmd-view {
                overflow-x: hidden !important; 
            }

            td.q-1,
            th.q-1,
            td.q-2,
            th.q-2,
            td.q-3,
            th.q-3,
            td.q-4,
            th.q-4{
                background-color: #d6d6d626 !important;
            }

            td.st-1,
            th.st-1{
                background-color: #ccc !important;
            }

            caption{
                display:none;
            }
        }

        .wmd-view-topscroll, .wmd-view {
            overflow-x: scroll;
            overflow-y: hidden;             
            border: none 0px RED;
        }

        .wmd-view-topscroll { height: 20px; }
        /* .wmd-view { height: 800px; } */
         .scroll-div1 { 
            width: 1500px; 
            overflow-x: scroll;
            overflow-y: hidden;
            height:20px;
        }
        .scroll-div2 { 
            width: 1500px; 
   
        } 
        

</style>
<div class="row">
    <div class="col-xs-12">
        <?= Html::img($company->logoViewer,['class' => 'img-company-logo']) ?>
        <h4 >
            <?=$this->title;?>, <?=Yii::t('common','For years')?> :  
            <span class="work-years-on-header bg-primary" style="padding:0px 5px 0px 5px;"><?= date('Y',strtotime($workdate))?></span> 
            <span class="count-item count-item-on-head bg-yellow" style="padding:0px 5px 0px 5px;"></span> 
        </h4> 
    </div>
    <div class="col-xs-12">        
        <?= $company->name;?>   
        <div><?= Yii::t('common','Last update')?> : <span class="last-update">00:00</span><span class="text-filters"></span></div>   
        <div class="vat-status"></div>  
        <span class="last-calculate"></span> 
    </div>
</div>

<div class="row search-box">
    <div class="col-sm-6 text-right">
        
    </div>
    <div class="col-sm-6 col-xs-12 pull-right mb-10">
        <div class="row">
            <div class="col-sm-9 col-xs-6">
                <input id="myInput" class="form-control mb-10" type="text" placeholder="<?=Yii::t('common','Search')?>...">
                
            </div>
            <div class="col-sm-3 col-xs-6">
                <select class="form-control  mb-10" id="vat-change" >
                    <option value="0"><?=Yii::t('common','All')?></option>
                    <option value="Vat">Vat</option>
                    <option value="No">No Vat</option>
                </select>    
            </div>
            
        </div>
        
          
            
        <!-- <div class="input-group" style="width:70%;">            
            <select name="groups" class="form-control" ></select>            
        </div>  -->
        
    </div>
    
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-6  mb-10">
                <div> <?= Yii::t('common','Count rows') ?> : <span class="count-rows"></span></div>
                <div class="percent-load"></div>
            </div>
            <div class="col-sm-6 col-xs-12 text-right mb-10 hidden">
                <button class="btn <?= $data2017 ? 'btn-success' : 'btn-default' ?> get-data-on-years" data-year="2017" type="button">2017 <?= $data2017 ? '('.number_format($count2017).')' : '' ?></button>
                <button class="btn <?= $data2018 ? 'btn-success' : 'btn-default' ?> get-data-on-years" data-year="2018" type="button">2018 <?= $data2018 ? '('.number_format($count2018).')' : '' ?></button>
                <button class="btn <?= $data2019 ? 'btn-success' : 'btn-default' ?> get-data-on-years" data-year="2019" type="button">2019 <?= $data2019 ? '('.number_format($count2019).')' : '' ?></button>
            </div> 
            <div class="col-xs-12 pull-right">
                <table class="table no-border">
                    <thead>
                        <tr class="">  
                            <th class="text-right"><button class="btn btn-default btn-calculate-month" data-month="01"><?=Yii::t("common","Jan")?></button></th>
                            <th class="text-right"><button class="btn btn-default btn-calculate-month" data-month="02"><?=Yii::t("common","Feb")?></button></th>
                            <th class="text-right"><button class="btn btn-default btn-calculate-month" data-month="03"><?=Yii::t("common","Mar")?></button></th>
                            <th class="text-right"><button class="btn btn-default btn-calculate-month" data-month="04"><?=Yii::t("common","Apr")?></button></th>
                            <th class="text-right"><button class="btn btn-default btn-calculate-month" data-month="05"><?=Yii::t("common","May")?></button></th>
                            <th class="text-right"><button class="btn btn-default btn-calculate-month" data-month="06"><?=Yii::t("common","Jun")?></button></th>
                            <th class="text-right"><button class="btn btn-default btn-calculate-month" data-month="07"><?=Yii::t("common","Jul")?></button></th>
                            <th class="text-right"><button class="btn btn-default btn-calculate-month" data-month="08"><?=Yii::t("common","Aug")?></button></th>
                            <th class="text-right"><button class="btn btn-default btn-calculate-month" data-month="09"><?=Yii::t("common","Sep")?></button></th>
                            <th class="text-right"><button class="btn btn-default btn-calculate-month" data-month="10"><?=Yii::t("common","Oct")?></button></th>
                            <th class="text-right"><button class="btn btn-default btn-calculate-month" data-month="11"><?=Yii::t("common","Nov")?></button></th>
                            <th class="text-right"><button class="btn btn-default btn-calculate-month" data-month="12"><?=Yii::t("common","Dec")?></button></th>                        
                        </tr>
                        <tr class="">  
                            <th class="text-right"><a href="#" class="btn-Recalculate-month <?=$dataJan ? 'text-green' : '' ?>" data-month="01"><i class="fas fa-sync-alt"></i> <?=Yii::t("common","Jan")?></a></th>
                            <th class="text-right"><a href="#" class="btn-Recalculate-month <?=$dataFeb ? 'text-green' : '' ?>" data-month="02"><i class="fas fa-sync-alt"></i> <?=Yii::t("common","Feb")?></a></th>
                            <th class="text-right"><a href="#" class="btn-Recalculate-month <?=$dataMar ? 'text-green' : '' ?>" data-month="03"><i class="fas fa-sync-alt"></i> <?=Yii::t("common","Mar")?></a></th>
                            <th class="text-right"><a href="#" class="btn-Recalculate-month <?=$dataApr ? 'text-green' : '' ?>" data-month="04"><i class="fas fa-sync-alt"></i> <?=Yii::t("common","Apr")?></a></th>
                            <th class="text-right"><a href="#" class="btn-Recalculate-month <?=$dataMay ? 'text-green' : '' ?>" data-month="05"><i class="fas fa-sync-alt"></i> <?=Yii::t("common","May")?></a></th>
                            <th class="text-right"><a href="#" class="btn-Recalculate-month <?=$dataJun ? 'text-green' : '' ?>" data-month="06"><i class="fas fa-sync-alt"></i> <?=Yii::t("common","Jun")?></a></th>
                            <th class="text-right"><a href="#" class="btn-Recalculate-month <?=$dataJul ? 'text-green' : '' ?>" data-month="07"><i class="fas fa-sync-alt"></i> <?=Yii::t("common","Jul")?></a></th>
                            <th class="text-right"><a href="#" class="btn-Recalculate-month <?=$dataAug ? 'text-green' : '' ?>" data-month="08"><i class="fas fa-sync-alt"></i> <?=Yii::t("common","Aug")?></a></th>
                            <th class="text-right"><a href="#" class="btn-Recalculate-month <?=$dataSep ? 'text-green' : '' ?>" data-month="09"><i class="fas fa-sync-alt"></i> <?=Yii::t("common","Sep")?></a></th>
                            <th class="text-right"><a href="#" class="btn-Recalculate-month <?=$dataOct ? 'text-green' : '' ?>" data-month="10"><i class="fas fa-sync-alt"></i> <?=Yii::t("common","Oct")?></a></th>
                            <th class="text-right"><a href="#" class="btn-Recalculate-month <?=$dataNov ? 'text-green' : '' ?>" data-month="11"><i class="fas fa-sync-alt"></i> <?=Yii::t("common","Nov")?></a></th>
                            <th class="text-right"><a href="#" class="btn-Recalculate-month <?=$dataDec ? 'text-green' : '' ?>" data-month="12"><i class="fas fa-sync-alt"></i> <?=Yii::t("common","Dec")?></a></th>                        
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div ng-init="Title='<?=$this->title;?>'">
    <div class="row" >
        <div class="col-xs-12 ">    
            <div class="wmd-view-topscroll">
                <div class="scroll-div1" style="display: none;">
                </div>
            </div>     
            <div id="export_wrapper" class="table-responsive wmd-view"></div>
        </div>
    </div>
</div>
<button  id="myBtn" class="btn btn-default" style="position: fixed; bottom: 5px; right: 10px; z-index: 99; color:red;"><i class="fas fa-arrow-up"></i> Top</button>


 
<div class="modal fade" id="modal-row-detail">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Detail</h4>
            </div>
            <div class="text-center item-code-on-modal" style="width:100%;"></div>
            <div class="table-responsive">
                <table class="table table-bordered table-warning">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?= Yii::t("common","Posting Date") ?></th>
                            <th><?= Yii::t("common","Bill") ?></th>
                            <th><?= Yii::t("common","Items") ?></th>
                            <th><?= Yii::t("common","Name") ?></th>
                            <th class="text-right"><?= Yii::t("common","Quantity") ?></th>
                        </tr>
                    </thead>
                    <tbody class="tbody-modal"></tbody>
                </table>   
            </div>        
            <div class="modal-footer ">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?= Yii::t('common','Close')?></button>
                <a  href="#" class="btn btn-primary btn-modal-footer" target="_blank"><i class="fa fa-list"></i> <?= Yii::t('common','Product detail')?></a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-invoice-detail">
    <div class="modal-dialog modal-primary" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Invoice detail</h4>
            </div>  
            <div class="modal-body">
                <h4 ><?= Yii::t("common","Date") ?> : <span class="date-inv-modal"></span></h4>    
                <div class="table-responsive">          
                    <table class="table table-bordered table-warning">
                        <thead>
                            <tr class="bg-gray">
                                <th>#</th>
                                <th><?= Yii::t("common","Items") ?></th>
                                <th><?= Yii::t("common","Name") ?></th>
                                <th class="text-right"><?= Yii::t("common","Quantity") ?></th>
                            </tr>
                        </thead>
                        <tbody class="tbody-modal"></tbody>
                    </table>  
                </div> 
            </div>         
            <div class="modal-footer ">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?= Yii::t('common','Close')?></button>
                <a  href="#" class="btn btn-primary btn-modal-footer" target="_blank"><i class="fa fa-print"></i> <?= Yii::t('common','Print invoice')?></a>
            </div>
        </div>
    </div>
</div>


<?php 
 
$Yii        = 'Yii';
$thisYears  = date('Y');
$thisMonth  = date('m');

$js=<<<JS

let state = {
    progress : false,
    data : []
};


const loadingDiv = `
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> {$Yii::t("common","Calculating data please wait a minute")} .... </div>
            <h4 class="years-callulate"></h4>
            <img src="images/icon/loader2.gif" height="122"/>
            <div class="row row-progress">
                <div class="col-sm-3"> </div>
                <div class="col-sm-6 text-center">
                    <div class="progress active">
                        <div class="progress-bar progress-bar-primary progress-bar-striped installing-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" 
                        style="width: 0%;" >
                        <span class="sr-only">0% Complete (success)</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger-ew cancel-load-data" ><i class="fa fa-hand-paper"></i> {$Yii::t("common","Stop")}</button>
                </div>
                <div class="col-sm-3"> </div>
            </div>
            <h4 class="count-time"></h4>
        </div>`;

$('body').on('click','#myBtn',function(){
    topFunction();
});
// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {

    if (document.body.scrollTop > 150 || document.documentElement.scrollTop > 150) {
        $('.wmd-view-topscroll').addClass('search-box-up').removeClass('search-box-down');
        $('.wmd-view-topscroll .scroll-div1').css('width','2100px');
    }else{
        $('.wmd-view-topscroll').addClass('search-box-down').removeClass('search-box-up');

    }

    // if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
    //     document.getElementById("myBtn").style.display = "block";
    // } else {
    //     document.getElementById("myBtn").style.display = "none";
    // }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}


let filterTable  = (search) => {
    $("#export_table  tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(search) > -1)
    });

    $('#export_table tbody tr').each((key,value) => {
        $(value).find('.key').html(key + 1);
    });
}

$(document).ready(function(){

    
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    filterTable(value);

    topFunction();
  });



  // Show loading
  $('#export_wrapper').html(loadingDiv);

  setTimeout(() => {

    $("body")
      .addClass("sidebar-collapse")
      .find(".user-panel")
      .hide();
  },2000);


  renders(0,'{$thisYears}', '{$thisMonth}');  
 
});


var seconds = 0; 
 
    
function incrementSeconds() {
    seconds += 1;
    $('.count-time').html(seconds + ' {$Yii::t("common","seconds")}');
}
 

const getChildRow = (data,callback) => {
    let newData = [];
    data.map(model => {
        if(model.id){
            newData.push({
                    id: model.id,
                    code: model.code,
                    row: model.row,
                    name: model.name,
                    parent: model.parent,
                    date: model.date,
                    base_unit: model.base_unit,
                    qty: model.qty * model.base_unit
            })
        }
        if(Array.isArray(model)){            
            getChildRow(model, res => {
                res.map( e => {
                    newData.push(e);
                })
            })                
        }

    });
    callback(newData);
}

const filterData = (data, years, vat) => {
    let tmp     = [];
    let temp    = [];    
    let countData = data ? data.length : 0;

    $('.count-rows').html(number_format(countData)+ ' {$Yii::t("common","Rows")}');

    let newData = [];
    let count   = 0;

    if(vat === 'Vat'){
        data = data.filter(model => model.vat > 0 ? model : null);
    }else if(vat === 'No'){
        data = data.filter(model => model.vat === 0 ? model : null);
    }


    data 
        ? data.map(model => {

            model.status == 500 ? swal('Error : ' + model.message, model.id + ' | ' + model.code + ' | ' + model.name, "warning") : null;

            if (newData.some(e => e.id === model.id)) {
                // ถ้ามีอยู่แล้ว                    
                newData = newData.map(el => el.id === model.id
                                ?   Object.assign({}, el, {
                                        qty : el.qty + model.qty,
                                        jan : new Date(model.date).getMonth() + 1 === 1 ? (el.jan + model.qty) : el.jan,
                                        feb : new Date(model.date).getMonth() + 1 === 2 ? (el.feb + model.qty) : el.feb,
                                        mar : new Date(model.date).getMonth() + 1 === 3 ? (el.mar + model.qty) : el.mar,
                                        apr : new Date(model.date).getMonth() + 1 === 4 ? (el.apr + model.qty) : el.apr,
                                        may : new Date(model.date).getMonth() + 1 === 5 ? (el.may + model.qty) : el.may,
                                        jun : new Date(model.date).getMonth() + 1 === 6 ? (el.jun + model.qty) : el.jun,
                                        jul : new Date(model.date).getMonth() + 1 === 7 ? (el.jul + model.qty) : el.jul,
                                        aug : new Date(model.date).getMonth() + 1 === 8 ? (el.aug + model.qty) : el.aug,
                                        sep : new Date(model.date).getMonth() + 1 === 9 ? (el.sep + model.qty) : el.sep,
                                        oct : new Date(model.date).getMonth() + 1 === 10 ? (el.oct + model.qty) : el.oct,
                                        nov : new Date(model.date).getMonth() + 1 === 11 ? (el.nov + model.qty) : el.nov,
                                        dec : new Date(model.date).getMonth() + 1 === 12 ? (el.dec + model.qty) : el.dec,
                                        count: (model.count ? model.count : 1)  + el.count
                                    })
                                :   el
                            )
                
            }else{
                // ถ้ายังไม่มี
                if(model.id){
                    newData.push({
                        id: model.id,
                        code: model.code,
                        row: model.row,
                        vat: model.vat,
                        name: model.name,
                        parent: model.parent,
                        date: model.date,
                        base_unit: model.base_unit,
                        qty: model.qty,
                        cost: model.cost,
                        inven: model.inven,
                        jan: new Date(model.date).getMonth() + 1 === 1 ? (model.jan ? model.jan : model.qty) : 0,
                        feb: new Date(model.date).getMonth() + 1 === 2 ? (model.feb ? model.feb : model.qty) : 0,
                        mar: new Date(model.date).getMonth() + 1 === 3 ? (model.mar ? model.mar : model.qty) : 0,
                        apr: new Date(model.date).getMonth() + 1 === 4 ? (model.apr ? model.apr : model.qty) : 0,
                        may: new Date(model.date).getMonth() + 1 === 5 ? (model.may ? model.may : model.qty) : 0,
                        jun: new Date(model.date).getMonth() + 1 === 6 ? (model.jun ? model.jun : model.qty) : 0,
                        jul: new Date(model.date).getMonth() + 1 === 7 ? (model.jul ? model.jul : model.qty) : 0,
                        aug: new Date(model.date).getMonth() + 1 === 8 ? (model.aug ? model.aug : model.qty) : 0,
                        sep: new Date(model.date).getMonth() + 1 === 9 ? (model.sep ? model.sep : model.qty) : 0,
                        oct: new Date(model.date).getMonth() + 1 === 10 ? (model.oct ? model.oct : model.qty) : 0,
                        nov: new Date(model.date).getMonth() + 1 === 11 ? (model.nov ? model.nov : model.qty) : 0,
                        dec: new Date(model.date).getMonth() + 1 === 12 ? (model.dec ? model.dec : model.qty) : 0,
                        count: count
                    })
                }
            }
        })
        : null;
    
    function compare( a, b ) {
        if ( a.name < b.name ){
            return -1;
        }
        return 0;
    }

    newData.sort( compare );   
    //localStorage.setItem('item_on_years:'+ years, JSON.stringify(newData));
    return newData;
}

const renderTable = (data, years, vat, calback) => {

        //console.log(data); 
        //let keys    = 'item_on_years:'+ years;
        //let newData = localStorage.getItem(keys) ? JSON.parse(localStorage.getItem(keys)) : filterData(data, years, vat); 
        let newData = filterData(data, years, vat); 
        
        //console.log(newData);
       
        let html = `<table class="table table-bordered" id="export_table">
                        <thead>
                        <tr class="">
                            <th>#</th>
                            <th class="hidden">{$Yii::t("common","Vat")}</th>
                            <th style="min-width:140px;">{$Yii::t("common","Code")}</th>
                            <th style="min-width:350px;">{$Yii::t("common","Name")}</th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Jan")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Feb")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Mar")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Apr")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","May")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Jun")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Jul")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Aug")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Sep")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Oct")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Nov")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Dec")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Quantity Total")}</span></th>
                            <th class="text-right"><span class="mr-10">{$Yii::t("common","Cost")}</span></th>                            
                            </tr>
                            </thead>
                        <tbody>`;

            let i       = 0;
            
            if(vat === 'Vat'){
                newData = newData.filter(model => model.vat > 0 ? model : null);
            }else if(vat === 'No'){
                newData = newData.filter(model => model.vat === 0 ? model : null);
            }

            newData.map((model, key) => {

                i++;                
                let cost  = (model.cost > 0 ? model.cost : 0) * 1;
                    html+= '    <tr data-key="'+ model.id +'">';
                    html+= '        <td>'+i+'</td>';           
                    html+= '        <td class="hidden">'+ model.vat +'</td>';
                    html+= '        <td class="font-roboto"><a href="?r=items%2Fitems%2Fview&id='+ model.id +'" target="_blank" >'+ model.code +'</a></td>';
                    html+= '        <td><a href="?r=items%2Fitems%2Fview&id='+ model.id +'" target="_blank">'+ model.name +'</a></td>';
                    html+= '        <td data-month="1"  class="text-right font-roboto '+(model.jan > 0 ? 'bg-orange open-modal-row-detail-month pointer' : 'bg-gray')+'">'+ (number_format(model.jan)) +'</td>';
                    html+= '        <td data-month="2"  class="text-right font-roboto '+(model.feb > 0 ? 'bg-orange open-modal-row-detail-month pointer' : 'bg-gray')+'">'+ (number_format(model.feb)) +'</td>';
                    html+= '        <td data-month="3"  class="text-right font-roboto '+(model.mar > 0 ? 'bg-orange open-modal-row-detail-month pointer' : 'bg-gray')+'">'+ (number_format(model.mar)) +'</td>';
                    html+= '        <td data-month="4"  class="text-right font-roboto '+(model.apr > 0 ? 'bg-orange open-modal-row-detail-month pointer' : 'bg-gray')+'">'+ (number_format(model.apr)) +'</td>';
                    html+= '        <td data-month="5"  class="text-right font-roboto '+(model.may > 0 ? 'bg-orange open-modal-row-detail-month pointer' : 'bg-gray')+'">'+ (number_format(model.may)) +'</td>';
                    html+= '        <td data-month="6"  class="text-right font-roboto '+(model.jun > 0 ? 'bg-orange open-modal-row-detail-month pointer' : 'bg-gray')+'">'+ (number_format(model.jun)) +'</td>';
                    html+= '        <td data-month="7"  class="text-right font-roboto '+(model.jul > 0 ? 'bg-orange open-modal-row-detail-month pointer' : 'bg-gray')+'">'+ (number_format(model.jul)) +'</td>';
                    html+= '        <td data-month="8"  class="text-right font-roboto '+(model.aug > 0 ? 'bg-orange open-modal-row-detail-month pointer' : 'bg-gray')+'">'+ (number_format(model.aug)) +'</td>';
                    html+= '        <td data-month="9"  class="text-right font-roboto '+(model.sep > 0 ? 'bg-orange open-modal-row-detail-month pointer' : 'bg-gray')+'">'+ (number_format(model.sep)) +'</td>';
                    html+= '        <td data-month="10" class="text-right font-roboto '+(model.oct > 0 ? 'bg-orange open-modal-row-detail-month pointer' : 'bg-gray')+'">'+ (number_format(model.oct)) +'</td>';
                    html+= '        <td data-month="11" class="text-right font-roboto '+(model.nov > 0 ? 'bg-orange open-modal-row-detail-month pointer' : 'bg-gray')+'">'+ (number_format(model.nov)) +'</td>';
                    html+= '        <td data-month="12" class="text-right font-roboto '+(model.dec > 0 ? 'bg-orange open-modal-row-detail-month pointer' : 'bg-gray')+'">'+ (number_format(model.dec)) +'</td>';
                    html+= '        <td  class="text-right font-roboto bg-green open-modal-row-detail pointer" data-id="'+ model.id +'">'+ (number_format(model.qty)) +'</td>';
                    html+= '        <td  class="text-right font-roboto bg-primary">'+ cost +'</td>';
                    html+= '    </tr>';                                 
            })
            
                    html+= '    </tbody>';
        html+= '</table>';
        
 
        calback({
            i:i,
            html:html,
            raw:newData
        });
}


const htmlRender = (input, recal, vat, years) => {

    var counting = setInterval(incrementSeconds, 1000);

    seconds = 0; 
    clearInterval(counting);

    renderTable(input.data.raw, years, vat, res =>{
        $('.count-item').html('(' +number_format(res.i)+ ') {$Yii::t("common","items")}');
        $('#export_wrapper').html(res.html);
        var table = $('#export_table').DataTable({
                        "paging": false,
                        "searching": false
                    });
        
        var data = table
            .column( 1 )
            .data()
            .sort();

            $('.scroll-div1').show();
    });       

    $('#btn-refresh i').removeClass('fa-spin');
    $('#export_table tbody tr').each((key,value) => { $(value).find('.key-number').html(key + 1); });

    setTimeout(() => {
        $('body').find('span.last-update').html(input.data.timestamp);
    }, 1000);

    // Export to excel
    $("#export_table").tableExport({
        headings: true,                     // (Boolean), display table headings (th/td elements) in the <thead>
        footers: true,                      // (Boolean), display table footers (th/td elements) in the <tfoot>
        formats: ["xlsx"],                  // (String[]), filetypes for the export ["xls", "csv", "txt"]
        fileName: "{$this->title}-" + years,// (id, String), filename for the downloaded file
        bootstrap: true,                    // (Boolean), style buttons using bootstrap
        position: "top" ,                   // (top, bottom), position of the caption element relative to table
        ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file
        ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file
        ignoreCSS: ".tableexport-ignore"    // (selector, selector[]), selector(s) to exclude from the exported file
    });   
    
    
    $('body').find('caption button').text('{$Yii::t("common","Export to Excel")}')

    $('.percent-load').html('{$Yii::t("common","Data")} : <span class="' + (input.data.percent < 100 ? 'bg-red blink ' : 'bg-success') + 'px-5 mr-10">'+ number_format(input.data.percent.toFixed(0))+ '%</span>');
    clearInterval(state.progress);
    

    $('body').find('.vat-status').html('{$Yii::t("common","Vat")} : ' + $('select[id="vat-change"] option:selected').text());
    $('.last-calculate').attr('data-calc',input.data.caltime);
}

const checkServer = async (recal, years, month) => {
    let percent_number_step = $.animateNumber.numberStepFactories.append(' %');
    let percent = 0;
    $('body').find('.cancel-load-data').show();
    await fetch("?r=items/stock/check-server-monthly", {
            method: "POST",
            body: JSON.stringify({recal:recal, years:years, month:month}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {

            let status = res.status;
            if(res.refresh === 'force') {
                setTimeout(() => {
                    //location.reload();  
                    htmlRender({data: state.data[years]}, recal, years);                      
                }, 3000);  
            }

            percent = status === 200 ? 100 : (res.next.key / res.count) * 100 ;

            $('body').find('div.installing-bar').attr('style','width:'+ percent +'%;')
            .animateNumber({ number:percent ,numberStep: percent_number_step });

            let progress = parseFloat(res.percent).toFixed(2);
            $('.percent-load').html('<div ><span class="blink" >{$Yii::t("common","Loading")}...</span> <span class="percent-data"></span> %</div>');            
            $('.percent-data').html(progress);

            $('body').find('.years-callulate').html(res.data.years);
            

            if(status === 200){
                setTimeout(() => {                         
                    //htmlRender(res, recal, years);
                }, 1000);  
                clearInterval(state.progress)
            }

            
        })
        .catch(error => {
            console.log(error);
        });
}

const getDataFromAPI = (obj,callback) => {
    
    fetch("?r=items/stock/stock-by-invoice-monthly-ajax", {
        method: "POST",
        body: JSON.stringify({recal:obj.recal, years:obj.years, month:obj.month}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {
        state.data[obj.years] = res.data;               
        if(res.status===403){
            $.notify({
                // options
                icon: 'fas fa-clock',
                message: res.message
            },{
                // settings
                type: 'warning',
                delay: 10000,
                z_index:3000,
            });    
            $.notify(
                {
                    message: res.calculating.years + ' : ' + parseInt(res.percent) + '%'
                },
                {
                    type: 'info',
                    delay: 10000,
                    z_index:3000
                }
            );            
        }
        callback(res);
    })
    .catch(error => {
        console.log(error);
    });

}



let renders = (recal, years, month) => {
    state.progress = setInterval(() => { checkServer(recal, years, month); }, 3000);    
    if(recal === 1){
        getDataFromAPI({recal:recal, years:parseInt(years), month:parseInt(month)},res => {   
            if(res.status===200){
                htmlRender(res, recal, 'all', years);
            }
            
        })
    }else{ 
        getDataFromAPI({recal:recal, years:years, month:month},res => {  
            if(res.status===200){ 
                htmlRender(res, recal, 'all', years);
            }
        })   

        // if(state.data[years] !== undefined){
        //     htmlRender({data: state.data[years]}, recal, 'all', years);
        // }else{
        //     getDataFromAPI({recal:recal, years:years, month:month},res => {  
        //         if(res.status===200){ 
        //             htmlRender(res, recal, 'all', years);
        //         }
        //     })
        // }
    }
}

$('body').on('click','button.btn-calculate-month',function(){
    let thisMonth = $(this).attr('data-month');
    console.log('click ' + thisMonth);
    $('#export_wrapper').html(loadingDiv);

    $('#btn-refresh i').addClass('fa-spin');
    $('.scroll-div1').hide();
    $('.percent-load').html('');    
    seconds = 0;      
    renders(0, '${thisYears}', thisMonth);   
     
})


$('body').on('click','a.btn-Recalculate-month',function(){
    let thisMonth = $(this).attr('data-month');
    swal({
        title: "{$Yii::t('common','Are you sure?')}",
        text: '{$Yii::t("common","This process may take a long time. (Last time {:calc})",[":calc" => "'+$('.last-calculate').attr('data-calc')+'"])}',
        type: "question",
        showCancelButton: true,
        cancelButtonText: "{$Yii::t('common','No, cancel!')}",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "{$Yii::t('common','Yes, Continue')}"
    }).then(
        function() {
            
            $('#export_wrapper').html(loadingDiv);

            $('#btn-refresh i').addClass('fa-spin');
            $('.scroll-div1').hide();
            $('.percent-load').html('');    
            seconds = 0;      
            renders(1,$('.work-years-on-header').text(), thisMonth);    

        },
        function(dismiss) {
            // dismiss can be 'cancel', 'overlay',
            // 'close', and 'timer'
            if (dismiss === "cancel") {
            }
        }
    );
     
})


$('body').on('click','button.get-data-on-years',function(){
    $('#export_wrapper').html(loadingDiv);
    $('body').find('.row-progress').remove();
    $('.count-item-on-head').html('');
    $('.percent-load').html('');
    seconds = 0; 
    setTimeout(() => {
        renders(1,$(this).attr('data-year'),'{$thisMonth}');        
    }, 500);
     
    $('.work-years-on-header').html($(this).attr('data-year'));   
 
})



// https://jsfiddle.net/dagope/DtRTq/
$(function(){
    $(".wmd-view-topscroll").scroll(function(){
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function(){
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
});


$('body').on("change",'select[name="groups"]', function() {
    var value = $('select[name="groups"] option:selected').text();
    var id = $('select[name="groups"] option:selected').val();

     
    if(id != 0){
        var toShow = $("#data td.merge-group:contains('" + value + "')");  // contains (มีคำว่า)
        $("#data tr").not( $('#data tr').has(toShow) ).hide();
        toShow.each(function() {
            var rspan = $(this).attr('rowspan'),
                father = $(this).parent();
            if (rspan && +rspan > 1) {
                father.nextUntil(':nth-child(' + (father.index() + (+rspan) + 1) + ')').addBack().show();
            } else father.show();
        });

        $('.text-filters').html('<div>{$Yii::t("common","Filter")} : '+ value + '</div>');         
    }else{
        $("#data tr").show();
        $('.text-filters').html('');
    }

});
  

// column filter
$(document).ready(function() {
    let eventFilterMonth = () => {
        //console.log($('#month-filter').val());
        $('#export_table .col').map((key,e) => { 
            $(e).hide(); 
            $(e).removeClass('active-col'); 
        });
        $('#month-filter').val().map((e) => { 
            $('.'+e).show(); 
            $('.'+e).addClass('active-col'); 
        }); 

        let count = $('#month-filter').val().length * 80 +600;
        $('#export_table').attr('style','width:'+ count+'px;');
        $('.scroll-div1').attr('style','width:'+ count+'px;');
        $('.scroll-div2').attr('style','width:'+ count+'px;');

        if(count < 1366){
            $('.wmd-view-topscroll').hide();
        }else{
            $('.wmd-view-topscroll').show();
        }

        // คำนวนต้นทุน
        $('#export_table .v-1-d').map((key,e) => { 
            $(e).prevAll(".active-col").removeClass('bg-black');        // ลบพื้นหลังสีดำทั้งหมด
            $(e).prevAll(".active-col:eq(1)").addClass('bg-black');     // ใส่พื้นหลังสีดำ ในช่องที่นำมาคำนวณ
            let qty     = $(e).prevAll(".active-col:eq(1)").attr('data-val');
            let cost    = parseFloat($(e).prev().text().replace(',','.').replace(' ',''))  
            let total   = qty * cost; 
            $(e).html(total < 0 ? '<span class="text-red">'+number_format(total.toFixed(2))+'</span>' : number_format(total.toFixed(2)));
        });
    }

     
});


$('body').on('click','.open-modal-row-detail, .open-modal-row-detail-month',function(){
    let id = $(this).closest('tr').data('key');
    let thisMonth = parseInt($(this).attr('data-month'));
    let years = $('.work-years-on-header').text();
    let i  = 0;
    let data = state.data[years].raw.filter(model => model.id === id ? model : null);
    let newData = [];
    data.map(model =>  newData.some(e => e.parent === model.parent)
            ? newData = newData.map(el => el.parent === model.parent
                                ?   Object.assign({}, el, { qty : el.qty + model.qty })
                                :   el
                            )
            : model.parent  // ถ้ายังไม่มี 
                ?  newData.push(model) 
                :  null
    );

    let vat = $('#vat-change').val();

    if(vat === 'Vat'){
        newData = newData.filter(model => model.vat > 0 ? model : null);
    }else if(vat === 'No'){
        newData = newData.filter(model => model.vat === 0 ? model : null);
    }

    if(thisMonth > 0){
        newData = newData.filter(el => (new Date(el.date).getMonth() + 1) === thisMonth ?  el : null);
    }
    
    function compare( a, b ) {
        if ( a.date < b.date ){
            return -1;
        }
        return 0;
    }

    newData.sort( compare );

    let itemName    = '';
    let linkId      = '';
    let itemCode    = '';
    let hrefLink    = '';
    let html        = '';

    newData.map(model => {
        i++;

        html+= '    <tr data-key="'+ model.id +'">';
        html+= '        <td>' + i + '</td>';
        html+= '        <td class="font-roboto">' + model.date + '</td>';
        html+= '        <td class="font-roboto invoice-detail pointer" data-id="'+ model.parent +'" data-link="'+ id +'">' + model.no + '</td>';
        html+= '        <td class="font-roboto">' + model.code + '</td>';
        html+= '        <td class="font-roboto">' + model.name + '</td>';
        html+= '        <td class="font-roboto text-right bg-orange invoice-detail pointer"  data-id="'+ model.parent +'" data-link="'+ id +'">' + number_format(model.qty) + '</td>'; 
        html+= '    </tr>';

        itemName = model.name;
        hrefLink = '?r=items%2Fitems%2Fview&id='+ model.id;           
        itemCode = model.code;
    })

 

    $('body').find('#modal-row-detail .modal-title').html(itemName);
    $('body').find('#modal-row-detail .btn-modal-footer').attr('href',hrefLink);
    $('body').find('#modal-row-detail .item-code-on-modal').html(itemCode);
    $('body').find('#modal-row-detail .tbody-modal').html(html);
    if(newData.length > 0){
        $('#modal-row-detail').modal('show');
    }
})

$('body').on('click','button.cancel-load-data',function(){
    fetch("?r=items/stock/stop-process", {
        method: "POST",
        body: JSON.stringify({status:'clear',years:$('.work-years-on-header').text()}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {
 
        if(res.status===200){
            setTimeout(() => {
                location.reload();
            }, 1500);
            
        }
    })
    .catch(error => {
        console.log(error);        
    });
})

$('body').on('change','#vat-change',function(){
    let years = $('.work-years-on-header').text();
    htmlRender({data: state.data[years]}, 0, $(this).val(), $('.work-years-on-header').text());
    var value = $('#myInput').val().toLowerCase();
    setTimeout(() => {
        filterTable(value);
    }, 800);
})


$('body').on('click','.invoice-detail',function(){
    
    let id      = $(this).data('id');
    let dataLink= $(this).attr('data-link');
    let rowItem = $(this).closest('tr').data('key');
    let years   = $('.work-years-on-header').text();
    let html    = '';
    let i       = 0;
    let date    = '';
    let invNo   = '';
    let Title   = '';
    let hrefLink= '';
    let data    = state.data[years].raw.filter(model => model.parent === id ? model : null);

    //console.log(data);
    let newData = [];
    data.map(model => {
        if (newData.some(e => e.row=== model.row)) {
            // ถ้ามีอยู่แล้ว
            newData = newData.map(el => el.row === model.row
                            ?   Object.assign({}, el, { })
                            :   el
                        )
        }else{
            // ถ้ายังไม่มี
            newData.push(model)
            
        }        
    });
    //console.log(newData);
 
    newData.map(model => {
        i++;
        html+= '    <tr class="'+(model.rowItemId === parseInt(rowItem) ? 'bg-yellow' : 'bg-blur text-blur')+'" data-key="'+ model.rowItemId +'">';
        html+= '        <td>' + i + '</td>';           
        html+= '        <td class="font-roboto" style="min-width:125px;">' + model.rowItem + '</td>';

        let detail = data.filter(el => el.row === model.row ? el : null);
        let childTable = '<table class="table table-bordered">';
        let x = 0;
        detail.map(el => {
            x++;
            childTable+= '<tr class="'+(el.id === parseInt(dataLink) ? 'bg-yellow' : 'bg-blur-more text-blur')+'" data-key="'+ el.id +'">';
            childTable+= '  <td>'+ x +'</td>';
            childTable+= '  <td>'+ el.name +'</td>';
            childTable+= '  <td class="text-right" style="min-width: 40px;">'+ el.qty +'</td>';
            childTable+= '</tr>';
        })

            childTable+= '</table>';

        html+= '        <td class="font-roboto">'+
                            '<div>' + model.rowItemName + '</div>'+
                            '<div>' + (detail.length > 1 ? childTable : ' ') + '</div>'+
                        '</td>';
        html+= '        <td class="font-roboto text-right">' + number_format(model.qty) + '</td>'; 
        html+= '    </tr>';

        invNo   = model.no;
        date    = model.date;
        Title   = model.no;
        hrefLink= '?r=accounting%2Fposted%2Fprint&id='+ btoa(model.parent) + '&footer=1'; 
    });

    $('body').find('#modal-invoice-detail .no-inv-modal').html(invNo);
    $('body').find('#modal-invoice-detail .date-inv-modal').html(date);
    $('body').find('#modal-invoice-detail .btn-modal-footer').attr('href',hrefLink);
    $('body').find('#modal-invoice-detail .tbody-modal').html(html);
    $('body').find('#modal-invoice-detail .modal-title').html(Title);
    $('#modal-invoice-detail').modal('show');
})

// debug modal 
// แก้ไข modal ทับกันแล้วทำให้ scrolling ไม่ได้
$('#modal-invoice-detail').on('hidden.bs.modal',function(){
    $('body').addClass('modal-open').attr('style','overflow: auto; margin-right: 0px; padding-right: 0px;');
})

$('#modal-invoice-detail, #modal-row-detail').on('show.bs.modal',function(){
    setTimeout(() => {
        $('body').attr('style','overflow: auto; margin-right: 0px; padding-right: 0px;');
    }, 500);    
})

JS;
$this->registerJS($js,\yii\web\View::POS_END);
$this->registerJsFile('@web/js/jquery.animateNumber.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); 
?>
<?php $this->registerCssFile('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');?>
<?php $this->registerJsFile('//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<?php $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.2.5/css/tableexport.min.css');?>
<?php $this->registerJsFile('@web/js/js-xlsx-master/xlsx.core.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/Blob.js-master/Blob.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/FileSaver.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.3.5/js/tableexport.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>  
  
 