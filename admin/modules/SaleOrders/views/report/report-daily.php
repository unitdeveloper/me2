<!-- Styles -->
<style>
#chartPersale {
  width: 100%;
  height: 500px;
  z-index: 3000;
}

#SalChartPerMonth {
  width: 100%;
  height: 500px;
}

#chartdiv_line {
	width		: 100%;
	height		: 500px;
	font-size	: 11px;
}
.sort-link{
    cursor: pointer;
}

.ew-ng-click,.ew-ng-click-expand{
    cursor: pointer;
}



tbody > tr > td{
    /*padding: 0 !important;*/
}
tfoot > tr > td {
    color: blue;
}

.tr-expand{
    background-color: #ecf0f5;
}
.expand-close{
    text-align: right;

}
#expand-close-bt	{
    cursor: pointer;
    padding: 10px 15px 0 0;
}
.reportfrom .row .col-xs-12{
  padding:-15 !important;

}
</style>
<?php
//app\assets\SaleDashBoardAsset::register($this);
 $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
 ?>
<!-- Resources -->
<angular ng-controller="saleReportCtr">
 

<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />

<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>

<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
<script src="https://www.amcharts.com/lib/3/pie.js"></script>



<link href="css/sale_dashboard.css" rel="stylesheet">

<script src="js/grap1.js?v=4"></script>

<div class="dashboard-sale" >
 
    <div class="row">

        <div class="col-md-12">
            <div class="well" style="margin:0px 0 70px 0;">

                <div class=" ">

                        <div class="col-md-4">
                          <div class="col-md-offset-1">
                            <div class="col-md-11 col-xs-12" style="min-width:180px; height: 85px;  background-color: #13AD67; border: 1px solid #13AD67; border-radius: 5px; margin:10px 0px 0px 0px;">
                            <div class="row" style="height: 50%;">
                              <div class="col-xs-12" style="width: 100%; height: 100%; border-bottom: 1px solid #fff;"> <p style="color: #fff; text-align: left;">สินค้าขายดี </p>
                              <p style="line-height: 1px; color: #fff;">ตู้คอนซูมเมอร์</p>
                              </div>
                            </div>
                            <div class="row" style="height:50%">
                              <div class="col-xs-6" style="height: 100%; border-right: 1px solid #fff; font-size: 12px; margin-top: 2px;">
                              <p style="color: #fff; text-align: left; margin-left: 2px;">สินค้าขายยาก</p>
                              <p style="line-height: 1px; color: #fff;">ตู้คอนซูมเมอร์</p>
                              </div>

                              <div class="col-xs-6" style="height: 100%; font-size: 12px; margin-top: 2px;">
                              <p style="color: #fff; text-align: left; margin-left: 2px;">สินค้าใกล้หมด </p>
                              <p style="line-height: 1px; color: #fff;">ตู้คอนซูมเมอร์</p>
                              </div>

                            </div>
                          </div>
                          </div>
                        </div>

                        <div class="col-md-4" style="margin:0px;">
                            <div class="col-md-offset-1">
                            <div class="col-md-11 col-xs-12" style="min-width:180px; height: 85px;  background-color: #F8B62C; border: 1px solid #F8B62C; border-radius: 5px; margin:10px 0px 0px 0px; ">
                                <div class="row" style="height: 50%;">
                                  <div class="col-xs-12" style="width: 100%; height: 100%; border-bottom: 1px solid #fff;"><p style="color: #fff; text-align: left;">จำนวนลูกค้า </p>
                                  <p style="line-height: 1px; color: #fff;">{{getCustomerCount}} ราย</p></div>
                                </div>
                                <div class="row" style="height:50%">
                                  <div class="col-xs-6" style="height: 100%; border-right: 1px solid #fff; font-size: 12px; margin-top: 2px;">
                                  <p style="color: #fff; text-align: left; margin-left: 2px;">เพิ่มใหม่</p>
                                  <p style="line-height: 1px; color: #fff;">{{getCustomerNew}} ราย</p>
                                  </div>

                                  <div class="col-xs-6" style="height: 100%; font-size: 12px; margin-top: 2px;">
                                  <p style="color: #fff; text-align: left; margin-left: 2px;">ยกเลิก</p>
                                  <p style="line-height: 1px; color: #fff;">{{getCustomerCancel}} ราย</p>
                                  </div>
                                </div>
                              </div>
                          </div>
                        </div>

                        <div class="col-md-4">
                            <div class="col-md-offset-1">
                            <div class="col-md-11 col-xs-12" style="min-width:180px;
                            height: 85px;
                            background-color: #3097C5;
                            border: 1px solid #3097C5;
                            border-radius: 5px;
                            margin:10px 0px 0px 0px;
                            z-index: 50;
                            ">
                                <div class="row" style="height: 50%; "  >
                                  <div class="col-xs-12" style="width: 100%;
                                  height: 100%;
                                  border-bottom: 1px solid #fff;
                                  position: relative;"><p style="color: #fff; text-align: left;">ยอดขายรวม </p>
                                  <p  style="line-height: 1px; color: #fff; ">{{ records| sumByColumn:'iVthisyear' | number :0}} บาท</p>
                                  <a href="#"  f data-toggle="modal" data-target="#summaryDetailModal" style="position: absolute; right: 5px; bottom: 0px; color: #fff;"><i class="fa fa-search-plus" aria-hidden="true" ></i> Detail</a></div>
                                </div>
                                <div class="row" style="height:50%">
                                  <div class="col-xs-6" style="height: 100%; border-right: 1px solid #fff;  font-size: 12px; margin-top: 2px;">
                                  <p style="color: #fff; text-align: left; margin-left: 2px;">โมเดิร์นเทรด</p>
                                  <p style="line-height: 1px; color: #fff;">0 บาท</p>
                                  </div>

                                  <div class="col-xs-6" style="height: 100%; font-size: 12px; margin-top: 2px;">
                                  <p style="color: #fff; text-align: left; margin-left: 2px;">ร้านค้า</p>
                                  <p style="line-height: 1px; color: #fff;">{{ records| sumByColumn:'iVthisyear' | number :0}} บาท</p>
                                  </div>


                                </div>
                            </div>
                        </div>
                </div>

                

                <div class="row">

                    <div class="col-md-12">
                        <div class="row">
                        <div class="col-md-12" style="margin-top: 50px; margin-right: 10px;">
                            <h4 style="margin-right: 60%;">ภาพรวมยอดขายของบริษัท</h4>
                            <div id="chartdiv"></div>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-5" style="margin-top: 50px;">
                        <h4 style="margin-right: 50%;">ภาพรวมยอดขายแต่ละพื้นที่</h4>
                        <div id="chartdivdonut"></div>
                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>

 
 


</div>




<script>
$(document).ready(function(){
  $(".knob").knob();

  // setTimeout(function(e){
  //       LoadMap('All',400,7,0,'ภาพรวมตำแหน่งของลูกค้า');

  //   }, 10000);

})
// app.controller("saleReportCtr", function($scope) {
//     // $scope.records = [
//     //    { "name":"Alfreds Futterkiste","amount":"500,000"},
//     //    { "name":"Berglunds snabbköp","amount":"100,000" },
//     //    { "name":"Centro comercial Moctezuma","amount":"600,000" },
//     //    { "name":"Ernst Handel","amount":"400,000" },
//     // ]

// });




// app.controller('saleReportCtr', ['$scope', '$http', function ($scope, $http) {
//     $http.get('index.php?r=SaleOrders/report/sale-balance')
//     .success(function(data) {
//         //$scope.records = {data:data};
//         console.log(data);
//     });
// }]);


app.service('dataServices', function($http) {
    delete $http.defaults.headers.common['X-Requested-With'];
    this.getData = function() {
        // $http() returns a $promise that we can add handlers with .then()
        return $http({
            method: 'GET',
            url: 'index.php?r=SaleOrders/report/sale-balance',
            params: '',
            dataType:'async',
            headers : {'Content-Type': 'application/json'}
         });

     }
});

app.filter('sumByColumn', function () {
      return function (collection, column) {
        var total = 0;
        var item = 0;

        angular.forEach(collection,function (item) {
          total += parseInt(item[column]);
        });

        return total;
      };
    });

app.controller('saleReportCtr', function($scope, dataServices) {

    $scope.records = null;
    dataServices.getData().then(function(dataResponse) {

        $scope.records = dataResponse.data.raw;

    });


    $scope.setSortProperty = function(propertyName) {
        if ($scope.sortProperty === propertyName) {
            $scope.sortProperty = '-' + propertyName;
        } else if ($scope.sortProperty === '-' + propertyName) {
            $scope.sortProperty = propertyName;
        } else {
            $scope.sortProperty = propertyName;
        }
    }




    $scope.getCustomerCount = 0;
    $.ajax({url:"index.php?r=SaleOrders/ajax/customer-count",type: "GET",data : {param:'count'}, success:function(getData){
            $scope.getCustomerCount = getData;
        }
    });

    $scope.getCustomerCancel = 0;
    $.ajax({url:"index.php?r=SaleOrders/ajax/customer-count",type: "GET",data : {param:'status'}, success:function(getData){
            $scope.getCustomerCancel = getData;
        }
    });


    $scope.getCustomerNew = 0;
    $.ajax({url:"index.php?r=SaleOrders/ajax/customer-count",type: "GET",data : {param:'new'}, success:function(getData){
            $scope.getCustomerNew = getData;
        }
    });


    // $scope.getMTotal = function(){
    //     var total = 0;
    //     for(var i = 0; i < $scope.records.length; i++){
    //         var product = $scope.records[i];
    //         total += product.thismonth;
    //     }
    //     return total;
    // }

    // $scope.getQTotal = function(){
    //     var total = 0;
    //     for(var i = 0; i < $scope.records.length; i++){
    //         var product = $scope.records[i];
    //         total += product.Q_amount;
    //     }
    //     return total;
    // }


    // $scope.getYTotal = function(){
    //     var total = 0;
    //     for(var i = 0; i < $scope.records.length; i++){
    //         var product = $scope.records[i];
    //         total += product.Y_amount;
    //     }
    //     return total;
    // }




    // $scope.getMiVTotal = function(){
    //     var total = 0;
    //     for(var i = 0; i < $scope.records.length; i++){
    //         var product = $scope.records[i];
    //         total += product.iVthismonth;
    //     }
    //     return total;
    // }

    // $scope.getQiVTotal = function(){
    //     var total = 0;
    //     for(var i = 0; i < $scope.records.length; i++){
    //         var product = $scope.records[i];
    //         total += product.iVquater;
    //     }
    //     return total;
    // }


    // $scope.getYiVTotal = function(){
    //     var total = 0;
    //     for(var i = 0; i < $scope.records.length; i++){
    //         var product = $scope.records[i];
    //         total += product.iVthisyear;
    //     }
    //     return total;
    // }
});



// ======== Jquery ========


$('body').on('click','.ew-ng-click',function(){

    var salesCode = $(this).attr('ew-sales');

    var $MathRand = Math.round(Math.random() * 1000) + 1;

    var $list = '<tr class="tr-expand">\n<td colspan="8" style="padding:0 !important;">\r\n'+
                    '<div class="expand-close"><span id="expand-close-bt"  data="ew-'+$MathRand+'">'+
                    '<i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></span></div>\r\n'+
                    '<div class="expand-detail" data="ew-'+$MathRand+'">'+
                        '<div class="text-center"><i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i><br> Loading <br></div>'+
                    '</div>\r\n'+
                '</td>\n</tr>';



    $($list).insertAfter($(this).closest('tr'));

    $(this).attr('class','success hidden-xs ew-ng-click-expand ng-binding ew-'+$MathRand).attr('data','ew-'+$MathRand);


    $('div[data="ew-'+$MathRand+'"]').hide();


    RemoveOtherContent('ew-'+$MathRand);

    setTimeout(function(e){
        LoadContent($MathRand,salesCode);

    }, 1000);


    $('div[data="ew-'+$MathRand+'"]').slideToggle();



});

$('body').on('click','.ew-ng-click-expand,#expand-close-bt',function(e){

    var nameThis = $(this).attr('data');

    // $('div[data="'+nameThis+'"]').slideToggle('fast',function(){
    //     $(this).parent('td').parent('tr').remove();

    // });

    $('div[data="'+nameThis+'"]').closest('tr').children('td').children('div').css('overflow','hidden');
    $('div[data="'+nameThis+'"]').closest('tr').children('td').children('div').animate({height: '0px',opacity: 0}, 500);

    setTimeout(function(e){
        $('div[data="'+nameThis+'"]').parent('td').parent('tr').remove();
    }, 501);
    //$('div[data="'+nameThis+'"]').parent('td').parent('tr').remove();

    $('.'+nameThis).attr('class','success hidden-xs ew-ng-click ng-binding');




});

// $('body').on('click','.expand-close',function(e){

//     //$(this).slideToggle('fast',function(){
//         $(this).closest('tr').children('td').children('div').animate({height: '0px',opacity: 0}, 500);
//         $(this).parent('td').parent('tr').remove();
//     //});
//     //$(this).parent().parent().remove();

//     var nameThis = $(this).attr('data');
//     $('.'+nameThis).attr('class','success hidden-xs ew-ng-click ng-binding');

// });

function RemoveOtherContent(id)
{
    $.each($('div.expand-detail'), function (index, el) {

        if($(el).attr('data')!=id){
            //alert($(value).attr('data'));
            $(el).closest('tr').children('td').children('div').animate({height: '0px',opacity: 0}, 500);
            setTimeout(function(e){
                $(el).parent('td').parent('tr').remove();
            }, 501);



        }
    });
}

function LoadContent(e,$sales)
{

    $.ajax({

        url:"index.php?r=SaleOrders/report/sale-people-chart",
        type: "GET",
        data: {id:e,saleCode:$sales},
        async:true,
        success:function(getData){

            //$('div.expand-detail').html(getData);
            $('div[data="ew-'+e+'"]').html(getData).fadeIn('slow');


        }

    });
}

 

</script>








 




<!-- HTML -->






<!-- Modal -->
<div id="summaryDetailModal" class="modal modal-full fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?=Yii::t('common','Sales Report')?></h4>
      </div>
      <div class="modal-body">


        <div class="row">
            <div class="col-sm-12">
                <div class="well" >
                    <table class="table table-condensed" width="100%" id="ew-sale-report-table">
                        <thead>
                            <tr>

                                <!-- Mobile -->
                                <th rowspan="2" class="text-center hidden-sm hidden-md hidden-lg" style="vertical-align:middle;">
                                <h4><a ng-click="setSortProperty('code')" class="sort-link"><?=Yii::t('common','Sale People')?></a></h4></th>

                               
                                <th colspan="2" class="text-center bg-success hidden-sm hidden-md hidden-lg"><h5><?=Yii::t('common','Sale Invoice Amount')?></h5></th>

                                <!-- /.Mobile -->

                                <th  rowspan="2" colspan="2" valign="middle" align="center" style="vertical-align:middle;" class="text-center hidden-xs">
                                <h4><a ng-click="setSortProperty('name')" class="sort-link"><?=Yii::t('common','Sale People')?></a></h4>


                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <input type="text" name="q" class="form-control" id="ew-s" placeholder="ค้นหา..." ng-model="searchKeyword">
                                            <span class="input-group-btn">
                                              <button type="button" name="search" id="ew-s-btn" class="btn btn-default btn-flat"><i class="fa fa-search"></i>
                                              </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                </th>

                                
                                <th colspan="2" class="text-center bg-success hidden-xs"><h5><?=Yii::t('common','Sale Invoice Amount')?></h5></th>

                            </tr>


                            <tr>


                                

                                <th class="text-center active"><a ng-click="setSortProperty('iVthismonth')" class="sort-link"><?=Yii::t('common',date('M'))?></a></th>
                                
                                <th class="text-center active"><a ng-click="setSortProperty('iVthisyear')" class="sort-link"><?=Yii::t('common','All year')?></a></th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="x in records | orderBy:sortProperty | filter: searchKeyword" class="ew-ng-click-X" ew-sales="{{x.code}}">
                                <td class="success"><h4>{{x.code}}</h4></td>
                                <td class="success hidden-xs">{{x.name}}</td>

                               

                                <td class="text-center info">{{x.iVthismonth | number : 0}}</td>
                                
                                <td class="text-center info">{{x.iVthisyear | number : 0}}</td>

                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="bg-gray hidden-sm hidden-md hidden-lg"><?=Yii::t('common','Sum');?>  </td>
                                <td class="bg-gray hidden-xs" colspan="2"><?=Yii::t('common','Sum');?> </td>


                               

                                <td class="text-center bg-gray">{{ records| sumByColumn:'iVthismonth' | number :0}}</td>
                                
                                <td class="text-center bg-gray">{{ records| sumByColumn:'iVthisyear' | number :0}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>


        <?php if(Yii::$app->session->get('Rules')['rules_id']!=3): ?>
        <div class="row">
                <div class="col-sm-12">
                    <h4>ยอดขาย เดือน <?=Yii::t('common',date('F'))?></h4>
                </div>

                <div class="col-sm-12">
                    <div id="SalChartPerMonth"></div>
                </div>

        </div>


        <?php endif; ?>




     <!--
        <div class="row">
            <div class="col-sm-12">
                <div id="chartdiv_line"></div>
            </div>
        </div> -->


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal"><i class="fa fa-power-off" aria-hidden="true"></i>  <?=Yii::t('common','Close');?></button>
      </div>
    </div>

  </div>
</div>



</angular>
