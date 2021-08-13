<!-- Styles -->
<style>
#chartPersale {
  width: 100%;
  height: 500px;
}	

#SalChartPerMonth {
  width: 100%;
  height: 500px;
}   									
 
#chartdiv_line {
	width		: 100%;
	height		: 150px;
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
 
 ?>
<!-- Resources -->
<angular ng-controller="saleReportCtr"> 

 

<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />

<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<!-- <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script> -->
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
<script src="https://www.amcharts.com/lib/3/pie.js"></script>
<script src="https://www.amcharts.com/lib/3/ammap.js"></script>
<script src="https://www.amcharts.com/lib/3/maps/js/worldLow.js"></script>
 
<link href="css/sale_dashboard.css" rel="stylesheet">

<?php //<script src="js/grap1.js"></script> ?>
<script src="js/donut.js"></script>
<script src="js/donut1.js"></script>  

<script src="js/map.js"></script>




 


<script>

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
            headers : {'Content-Type': 'application/json'} 
         });
         
     }
});

app.filter('sumByColumn', function () {
      return function (collection, column) {
        var total = 0;

        collection.forEach(function (item) {
          total += parseInt(item[column]);
        });

        return total;
      };
    });

app.controller('saleReportCtr', function($scope, dataServices) {

    $scope.records = null;
    dataServices.getData().then(function(dataResponse) {
       
        $scope.records = dataResponse.data;
        
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
// ========/.Jquery ========

</script>









<!-- Chart code -->
<script>



var SalePeople = [];


function SalePeopleContent()
{
    $.ajax({ 

        url:"index.php?r=SaleOrders/report/sale-balance",
        type: "GET", 
        data: '',
        async:true,
        success:function(getData){
             
            var obj = jQuery.parseJSON(getData);

            $.each(obj, function (index, value) {

                    

                SalePeople.push({
                    saleCode:value["code"], 
                    //value1:value["value1"], 
                    salesYears:value["thismonth"],  
                });


            });

        }

    }); 
}

$(document).ready(function(){
    SalePeopleContent();
    
    var chart = AmCharts.makeChart("SalChartPerMonth", {
        "hideCredits":true,
        "theme": "light",
        "type": "serial",
        "dataProvider": SalePeople,
        "valueAxes": [{
            "stackType": "3d",
            "unit": "฿",
            "position": "left",
            "title": "Sale People",
        }],
        "startDuration": 1,
        "graphs": [{
            "balloonText": "[[category]]: <b>[[value]]</b>",
            "fillAlphas": 0.9,
            "lineAlpha": 0.2,
            "title": "2004",
            "type": "column",
            "valueField": "salesYears"
        }, 
        {
            "balloonText": " [[category]]: <b>[[value]]</b>",
            "fillAlphas": 0.9,
            "lineAlpha": 0.2,
            "title": "2005",
            "type": "column",
            "valueField": "year2005"
        }],
        "plotAreaFillAlphas": 0.1,
        "depth3D": 60,
        "angle": 30,
        "categoryField": "saleCode",
        "categoryAxis": {
            "gridPosition": "start"
        },
        "export": {
            "enabled": false
        }
    });
    jQuery('.chart-input').off().on('input change',function() {
        var property    = jQuery(this).data('property');
        var target      = chart;
        chart.startDuration = 0;

        if ( property == 'topRadius') {
            target = chart.graphs[0];
            if ( this.value == 0 ) {
            this.value = undefined;
            }
        }

        target[property] = this.value;
        chart.validateNow();
    });

})


</script>



<script>
// generate data
var SaleHeaderData = [];

function generateSaleHeaderData() {

     
    $.ajax({ 

        url:"index.php?r=SaleOrders/report/json-sale-header-armchart",
        type: "GET", 
        data: '',
        async:true,
        success:function(getData){
            //chartData = getData;
            var obj = jQuery.parseJSON(getData);
            // chartData.push({
            //         date: obj.date,
            //         value1: obj.value1,
            //         value2: obj.value2
            //     });

            $.each(obj, function (index, value) {

                    

                SaleHeaderData.push({
                    date:value["date"], 
                    //value1:value["value1"], 
                    value2:value["value2"],  
                });


            });

        }

    }); 
 
}
generateSaleHeaderData();
 
   
 

var chart = AmCharts.makeChart("chartdiv_line", {
    "hideCredits":true,
    "type": "serial",
    "theme": "light",
    "marginRight": 80,
    "dataProvider": SaleHeaderData,
    "valueAxes": [{
        "axisAlpha": 0.1
    }],

    "graphs": [{
        "balloonText": "[[title]]: [[value]]",
        "columnWidth": 20,
        "fillAlphas": 1,
        "title": "daily",
        "type": "column",
        "valueField": "value2"
    }, {
        "balloonText": "[[title]]: [[value]]",
        "lineThickness": 2,
        "title": "intra-day",
        "valueField": "value1"
    }],
    "zoomOutButtonRollOverAlpha": 0.15,
    "chartCursor": {
        "categoryBalloonDateFormat": "MMM DD JJ:NN",
        "cursorPosition": "mouse",
        "showNextAvailable": true
    },
    "autoMarginOffset": 5,
    "columnWidth": 1,
    "categoryField": "date",
    "categoryAxis": {
        "minPeriod": "hh",
        "parseDates": true
    },
    "export": {
        "enabled": false
    }
});


</script>

<!-- HTML -->



 
       
      
        <div class="row">
            <div class="col-sm-12">
                <div class="well" >
                    <table class="table table-condensed" width="100%" id="ew-sale-report-table">
                        <thead>
                            <tr>

                                <!-- Mobile -->
                                <th rowspan="2" class="text-center hidden-sm hidden-md hidden-lg" style="vertical-align:middle;">
                                <h4><a ng-click="setSortProperty('code')" class="sort-link"><?=Yii::t('common','Sale People')?></a></h4></th> 
                                
                                <th colspan="2" class="text-center danger hidden-sm hidden-md hidden-lg"><h5><?=Yii::t('common','Sale Order Amount')?></h5></th>

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

                                <th colspan="3" class="text-center danger hidden-xs"><h5><?=Yii::t('common','Sale Order Amount')?></h5></th>

                                <th colspan="3" class="text-center bg-success hidden-xs"><h5><?=Yii::t('common','Sale Invoice Amount')?></h5></th>


                            </tr>


                            <tr>
                                
                                
                                <th class="text-center active"><a ng-click="setSortProperty('thismonth')" class="sort-link"><?=Yii::t('common',date('M'))?></a></th>
                                <th class="text-center active hidden-xs"><a ng-click="setSortProperty('Q_amount')" class="sort-link"><?=Yii::t('common','Quarter')?></a></th>
                                <th class="text-center active"><a ng-click="setSortProperty('Y_amount')" class="sort-link"><?=Yii::t('common','All year')?></a></th>


                                <th class="text-center active"><a ng-click="setSortProperty('iVthismonth')" class="sort-link"><?=Yii::t('common',date('M'))?></a></th>
                                <th class="text-center active hidden-xs"><a ng-click="setSortProperty('iVquater')" class="sort-link"><?=Yii::t('common','Quarter')?></a></th>
                                <th class="text-center active"><a ng-click="setSortProperty('iVthisyear')" class="sort-link"><?=Yii::t('common','All year')?></a></th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="x in records | orderBy:sortProperty | filter: searchKeyword" class="ew-ng-click-X" ew-sales="{{x.code}}">
                                <td class="success"><h4>{{x.code}}</h4></td>
                                <td class="success hidden-xs">{{x.name}}</td>
                
                                <td class="text-center warning">{{x.thismonth | number : 0}}</td>
                                <td class="text-center warning hidden-xs">{{x.Q_amount | number : 0}}</td>
                                <td class="text-center warning">{{x.Y_amount | number : 0}}</td>
                                

                                <td class="text-center info">{{x.iVthismonth | number : 0}}</td>
                                <td class="text-center info hidden-xs">{{x.iVquater | number : 0}}</td>
                                <td class="text-center info">{{x.iVthisyear | number : 0}}</td>
                           
                            </tr> 
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="bg-gray hidden-sm hidden-md hidden-lg"><?=Yii::t('common','Sum');?>  </td>
                                <td class="bg-gray hidden-xs" colspan="2"><?=Yii::t('common','Sum');?> </td>
                                
                
                                <td class="text-center bg-gray">{{ records| sumByColumn:'thismonth' | number :0 }}</td>
                                <td class="text-center bg-gray hidden-xs">{{ records| sumByColumn:'Q_amount' | number :0}}</td>
                                <td class="text-center bg-gray">{{ records| sumByColumn:'Y_amount' | number :0}}</td>
                                

                                <td class="text-center bg-gray">{{ records| sumByColumn:'iVthismonth' | number :0}}</td>
                                <td class="text-center bg-gray hidden-xs">{{ records| sumByColumn:'iVquater' | number :0}}</td>
                                <td class="text-center bg-gray">{{ records| sumByColumn:'iVthisyear' | number :0}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

 


        
            <div class="well">
                
                   
        <div class="row">
             <div class="col-sm-12">
                <div id="chartdiv_line"  style="border: 1px solid #ccc;"></div>
            </div>
            <div class="col-sm-12">
                <?php
                        use common\models\SalesPeople;
                        use dosamigos\google\maps\LatLng;
                        use dosamigos\google\maps\overlays\InfoWindow;
                        use dosamigos\google\maps\overlays\Marker;
                        use dosamigos\google\maps\Map;

                        use dosamigos\google\maps\services\DirectionsWayPoint;
                        use dosamigos\google\maps\services\TravelMode;
                        use dosamigos\google\maps\overlays\PolylineOptions;
                        use dosamigos\google\maps\services\DirectionsRenderer;
                        use dosamigos\google\maps\services\DirectionsService;
                        use dosamigos\google\maps\services\DirectionsRequest;
                        use dosamigos\google\maps\overlays\Polygon;
                        use dosamigos\google\maps\layers\BicyclingLayer;
                         


                        $coord = new LatLng(['lat'=>13.612698,'lng'=>100.305129]);
                        $map = new Map([
                            'center'=>$coord,
                            'zoom'=>8,
                            'width'=>'100%',
                            'height'=>'400',
                        ]);


                        foreach($contacts as $model){

                          $district = '';
                          if($model->district!='') $district = $model->districttb->DISTRICT_NAME;

                          $province = '';
                          if($model->province!='') $province = $model->provincetb->PROVINCE_NAME;

                          $city     = '';
                          if($model->city!='')      $city       = $model->citytb->AMPHUR_NAME;

                           
                          if($model->latitude=='')  $model->latitude  = substr($model->zipcode->latitude, 0,6).rand(10,100);
                          if($model->longitude=='') $model->longitude = substr($model->zipcode->longitude, 0,6).rand(10,100);

                          if(SalesPeople::find()->where(['code' => explode(',',$model->owner_sales)])->count()>0)
                                {
                                    $sales = SalesPeople::find()
                                    ->where(['code' => explode(',',$model->owner_sales)])
                                    ->all();
                                    $salpeople = '';

                                    foreach ($sales as $people) {
                                        $salpeople.= '<div>['.$people->code.'] '.$people->name.'</div>'; 
                                    }
                                     

                                }else {
                                    $salpeople = '-';
                                }

                          $coords = new LatLng(['lat'=>$model->latitude,'lng'=>$model->longitude]);  
                          $marker = new Marker(['position'=>$coords]);

                         

                          $marker->attachInfoWindow(
                            new InfoWindow([
                                'content'=>"
                                     
                                            <h4>{$model->name} </h4>
                                              <table class='table table-striped table-bordered table-hover'>
                                                <tr>
                                                    <td>ที่อยู่</td>
                                                    <td>{$model->address} </td>
                                                </tr>
                                                <tr>
                                                    <td> </td>
                                                    <td>ต.{$district} อ.{$city} </td>
                                                </tr>
                                                 
                                                <tr>
                                                    <td> </td>
                                                    <td>จ.{$province} </td>
                                                </tr>
                                                <tr>
                                                    <td>โทร</td>
                                                    <td>{$model->phone} </td>
                                                </tr>
                                                <tr>
                                                    <td>ผู้ดูแล</td>
                                                    <td>{$salpeople} </td>
                                                </tr>
                                              </table>",

                                
                                
                            ])
                          );
                           
                          
                          
                          $map->addOverlay($marker);  
                          
                          
                             
                        }


                        
  
                        ?>


                <div style="border: 1px solid #ccc;" class="google-map">
                    <?php echo $map->display(); ?>
                </div>

            </div>
            
        </div> 
        </div>
       

 


</angular>



