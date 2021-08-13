<?php
 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use dosamigos\multiselect\MultiSelect;

use common\models\ItemsHasGroups;
use common\models\ItemgroupCommon;
 
$this->title = Yii::t('common', 'Inventory');
$this->params['breadcrumbs'][] = $this->title;
$company    = \common\models\Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
$workdate   = Yii::$app->session->get('workdate');
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
        width:2000px;
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
            .text-orange{
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
            width: 2000px; 
            overflow-x: scroll;
            overflow-y: hidden;
            height:20px;
        }
        .scroll-div2 { 
            width: 2000px; 
   
        }
        

</style>
<div class="row">
    <div class="col-xs-12">
        <?= Html::img($company->logoViewer,['class' => 'img-company-logo']) ?>
        <h4 ><?=$this->title;?>, <?=Yii::t('common','For years')?> :  <span class="bg-primary" style="padding:0px 5px 0px 5px;"><?= date('Y',strtotime($workdate))?></span> </h4> 
    </div>
    <div class="col-xs-12 mb-5">        
        <?= $company->name;?>   
        <div ><?= Yii::t('common','Last update')?> : <span class="last-update bg-yellow px-5">00:00</span><span class="text-filters"></span></div>     
    </div>
</div>

<div class="row search-box">
    <div class="col-sm-8 ">
        <label for="month-filter"><?= Yii::t('common','Month filter')?></label>
        <?php  
        //https://github.com/2amigos/yii2-multi-select-widget
        //composer require 2amigos/yii2-multi-select-widget "*"
        echo MultiSelect::widget([
            'id'        => "month-filter",
            "options"   => ['multiple'=>"multiple"], // for the actual multiselect
            'data'      => [ 
                'm-1' => Yii::t('common','{:date} month',[':date' => 1]), 
                'm-2' => Yii::t('common','{:date} month',[':date' => 2]), 
                'm-3' => Yii::t('common','{:date} month',[':date' => 3]), 
                //'q-1' => Yii::t('common','Quarter {:q}',[':q' => 1]),

                'm-4' => Yii::t('common','{:date} month',[':date' => 4]), 
                'm-5' => Yii::t('common','{:date} month',[':date' => 5]), 
                'm-6' => Yii::t('common','{:date} month',[':date' => 6]), 
                //'q-2' => Yii::t('common','Quarter {:q}',[':q' => 2]),

                'm-7' => Yii::t('common','{:date} month',[':date' => 7]), 
                'm-8' => Yii::t('common','{:date} month',[':date' => 8]), 
                'm-9' => Yii::t('common','{:date} month',[':date' => 9]), 
                //'q-3' => Yii::t('common','Quarter {:q}',[':q' => 3]),

                'm-10' => Yii::t('common','{:date} month',[':date' => 10]), 
                'm-11' => Yii::t('common','{:date} month',[':date' => 11]), 
                'm-12' => Yii::t('common','{:date} month',[':date' => 12]), 
                //'q-4' => Yii::t('common','Quarter {:q}',[':q' => 4]),

                'st-1' => Yii::t('common','Cost'),
                'v-1' => Yii::t('common','Total')
            ], // data as array
            'value'     => [ 
                'm-1', 
                'm-2', 
                'm-3', 
                //'q-1',

                'm-4', 
                'm-5', 
                'm-6', 
                //'q-2',

                'm-7', 
                'm-8', 
                'm-9', 
                //'q-3',

                'm-10', 
                'm-11', 
                'm-12', 
                //'q-4',
                'st-1',
                'v-1'
            ], // if preselected
            'name'      => 'month-filter', 
            "clientOptions" =>  [
                    "includeSelectAllOption" => true,
                    'numberDisplayed' => 2
            ], 
        ]);
        ?>
         
    </div>
    <div class="col-sm-4 col-xs-12 pull-right mb-10">      
        <button type="button" class="btn pull-right btn-default-ew mb-10" id="btn-refresh"> <i class="fa fa-refresh"></i> <?= Yii::t('common','ReCalculate')?></button>       
        <div class="input-group" style="width:70%;">            
            <select name="groups" class="form-control" ></select>            
        </div> 
        <input id="myInput" class="form-control hidden" type="text" placeholder="<?=Yii::t('common','Search')?>...">
    </div>
    <div class="col-xs-12 export-xls"> </div> 
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

<?php 
 
$Yii = 'Yii';
$js=<<<JS

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


$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#export_table tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });

    topFunction();
  });
  

  $('#export_table tbody tr').each((key,value) => {
     $(value).find('.key').html(key + 1);
  });

  // Show loading
  $('#export_wrapper').html(`
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> {$Yii::t("common","Calculating data please wait a minute")} .... </div>
            <img src="images/icon/loader2.gif" />
            <h4 class="count-time"></h4>
        </div>
    `);

  setTimeout(() => {

    $("body")
      .addClass("sidebar-collapse")
      .find(".user-panel")
      .hide();
      
    renders(0);  
    
  },2000);



 
});


var seconds = 0; 
 
    
function incrementSeconds() {
    seconds += 1;
    $('.count-time').html(seconds + ' {$Yii::t("common","seconds")}');
}


 


let renders = (recal) => {
    
    var counting = setInterval(incrementSeconds, 1000);
    $.ajax({ 
        url:"index.php?r=items/stock/monthly-ajax",
        type: 'POST', 
        data: {recal:recal},
        async:true,
        success:function(getData){
            $('#export_wrapper').html(getData);
            $('#btn-refresh i').removeClass('fa-spin');
            $('#export_table tbody tr').each((key,value) => { $(value).find('.key-number').html(key + 1); });

            seconds = 0; 
            clearInterval(counting);

            setTimeout(() => {
                $('body').find('span.last-update').html($('.row-data').attr('data-date'));
            }, 1000);
            
            $('.scroll-div1').show();
            // Select option ---> 
            setTimeout(() => {
                var groupId = recal == 1 ? 0 : $('select[name="groups"] option:selected').val();
                $('select[name="groups"]').html('');
                $('#export_table .merge-group').each((key,e) => { 
                    $('select[name="groups"]')
                    .append('<option data="true" value="'+$(e).data('key')+'" data-text="'+$(e).text()+'" >'+$(e).text()+'</option>')
                });
                $(function() {
                    // choose target dropdown
                    var select = $('select[name="groups"]');
                   
                    select.html(select.find('option[data="true"]').sort(function(x, y) {
                        // to change to descending order switch "<" for ">"
                        return $(x).text() > $(y).text() ? 1 : -1;
                    }));
                    select.prepend('<option value="0" selected>{$Yii::t("common","All")}</option>');
                });
            }, 500);
            // <--- Select option

            // Export to excel
            setTimeout(() => {
                $("#export_table").tableExport({
                    headings: true,                    // (Boolean), display table headings (th/td elements) in the <thead>
                    footers: true,                     // (Boolean), display table footers (th/td elements) in the <tfoot>
                    formats: ["xlsx", "txt"],    // (String[]), filetypes for the export ["xls", "csv", "txt"]
                    fileName: "{$this->title}",         // (id, String), filename for the downloaded file
                    bootstrap: true,                   // (Boolean), style buttons using bootstrap
                    position: "top" ,                // (top, bottom), position of the caption element relative to table
                    ignoreRows: null,                  // (Number, Number[]), row indices to exclude from the exported file
                    ignoreCols: null,                 // (Number, Number[]), column indices to exclude from the exported file
                    ignoreCSS: ".tableexport-ignore"   // (selector, selector[]), selector(s) to exclude from the exported file
                });               
            }, 1500);
            
        }
    });
     
}

$('body').on('click','#btn-refresh',function(){
    $('#export_wrapper').html(`
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> {$Yii::t("common","Calculating data please wait a minute")} .... <span class="count-time"></span> </div>            
            <img src="images/icon/loader2.gif" />
            <h4 class="count-time"></h4>
        </div>
    `);

    $('#btn-refresh i').addClass('fa-spin');
    $('.scroll-div1').hide();
    seconds = 0;      
    renders(1);    
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
    //renders(0);
    // $("#export_table .merge-group").filter(function() {
    //     $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    //    // $(this).toggle($(this).closest('tr').attr('data-row').toLowerCase().indexOf(id) > -1)
    // });

    //topFunction();
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

    $('#month-filter').multiselect({
        //enableFiltering: true,
        includeSelectAllOption: true,
        allSelectedText: '{$Yii::t("common","All Selected")}',
        nSelectedText: '{$Yii::t("common","Selected")}',
        nonSelectedText: '{$Yii::t("common","None Selected")}',
        onSelectAll: function () {
            eventFilterMonth();
        },
        onDeselectAll: function () {
            eventFilterMonth();
        },
        onChange: function(option, checked) {
            eventFilterMonth();
        }
        
    });
});


//$('body').on('click','.export-xls',function(){
    
//})


JS;
$this->registerJS($js,\yii\web\View::POS_END);

?>
<?php $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.2.5/css/tableexport.min.css');?>
<?php $this->registerJsFile('@web/js/js-xlsx-master/xlsx.core.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/Blob.js-master/Blob.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/FileSaver.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.3.5/js/tableexport.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>  
  
 