// app.controller('indexController', ['$scope', '$http', '$compile','$sce', '$filter',
// function ($scope, $http, $compile,$sce,$filter) {

  
//   $scope.test = test();
//   function test(){
//     return 'ok';    
//   }



// }]);


 

var chartData = generatechartData('','');

function generatechartData($sdate,$edate) {
    var chartData = [
      // {date: "2017-11-11", amount: 100},
      // {date: "2017-11-12", amount: 7475},
      // {date: "2017-11-13", amount: 40000}

    ];
    var chartItem     = [];
    var baseItemSale  = [];
    var groupItemSale = [];
    var allItems      = [];


    var Jobs  = 0;
    var Total = 0;

    var brand = 'ALL';
    var actionBrand = $('.mode.Active').attr('data-key');
    if(actionBrand!=null){
      brand = actionBrand;
    }


    $.ajax({
      url:'index.php?r=SaleOrders/event/dashboard&sdate='+$sdate+'&edate='+$edate+'&brand='+brand,
      method:'GET',
      async:false,
      success:function(getData){
        var $data = jQuery.parseJSON(getData);


        Jobs    = $data.jobs;
        Total   = $data.total;
        groups  = $data.groups;

        // Line data
        for(var i = 0; i < $data.line.length; i++){
            var dataList = $data.line[i];
            chartData.push({
              date:new Date(dataList.date),
              amount:dataList.amount
            });
        }

        // Groups item  data
        var ObjItems   = $data.items;

        ObjItems = ObjItems.sort(function (a, b) {
            return  b.qty - a.qty;
        });

        var itemLength = ObjItems.length;

        // Pie Chart
        for(var i = 0; i < (itemLength>=10 ? 10:itemLength); i++){
            var itemList = ObjItems[i];
            chartItem.push({
              no      : itemList.no,
              barcode : itemList.barcode,
              qty     : itemList.qty,
              name    : itemList.name,
              total   : itemList.total,
              color   : ''+itemList.color+'',
              group   : itemList.group,
            });
        }

        // Base Sale
        for(var i = 0; i < (itemLength>=20 ? 20:itemLength); i++){
            var itemList = ObjItems[i];
            baseItemSale.push({
              no      : itemList.no,
              barcode : itemList.barcode,
              qty     : itemList.qty,
              name    : itemList.name,
              total   : itemList.total,
              color   : itemList.color,
              img     : itemList.img,
              group   : itemList.group,
            });
        }

        // All Item
        for(var i = 0; i < itemLength; i++){
            var itemList = ObjItems[i];
            allItems.push({
              no      : itemList.no,
              barcode : itemList.barcode,
              qty     : itemList.qty,
              name    : itemList.name,
              total   : itemList.total,
              color   : itemList.color,
              img     : itemList.img,
              group   : itemList.group,
            });
        }

        // Groups Item


        groups = groups.sort(function (a, b) {
            return  b.qty - a.qty;
        });
        for(var i = 0; i < groups.length; i++){
            var itemList = groups[i];
            groupItemSale.push({
              name    : itemList.name,
              qty     : itemList.qty,
              total   : itemList.total,
            });
        }


      }
    });
    return {
      jobs  : Jobs,
      total : Total,
      line  : chartData,
      items : chartItem,
      items2: baseItemSale,
      list  : allItems,
      groups: groupItemSale,
    };
}















app.directive('myChart',function(){

  return {
    restrict: 'E',
    replace:true,

    template: '<div id="chartdiv"></div>',
    link: function (scope, element, attrs) {

      var chart = false;

      var initChart = function() {
        if (chart) chart.destroy();
        var config = scope.config || {};
        var chart = AmCharts.makeChart("chartdiv", {
          "hideCredits":true,
          "theme": "black",
          "type": "serial",
          "marginRight": 15,
          "autoMarginOffset": 20,
          "marginTop":20,
          "marginLeft":10,
          //"dataProvider": chartData.line,
          "valueAxes": [{
            "id": "v1",
            "axisAlpha": 0.1
          }],
          "graphs": [{
            "useNegativeColorIfDown": true,
            "balloonText": "[[category]]<br><b>THB: [[value]]</b>",
            "bullet": "round",
            "bulletBorderAlpha": 1,
            "bulletBorderColor": "#FFFFFF",
            "hideBulletsCount": 50,
            "lineThickness": 2,
            "lineColor": "#fdd400",
            "negativeLineColor": "#67b7dc",
            //"type": "smoothedLine",
            "valueField": "amount"
          }],
          "chartScrollbar": {
            "scrollbarHeight": 5,
            "backgroundAlpha": 0.1,
            "backgroundColor": "#868686",
            "selectedBackgroundColor": "#67b7dc",
            "selectedBackgroundAlpha": 1
          },
          "chartCursor": {
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true
          },
          "categoryField": "date",
          "categoryAxis": {
            "parseDates": true,
            "axisAlpha": 0,
            "minHorizontalGap": 60
          },
          "export": {
            "enabled": false
          },
          
          "listeners": [{
            "event": "rendered",
            "method": function(e) {

                    // Default Date
                    var eventClick = '';
                    if(eventClick === ''){
                        e.chart.lastZoomed.startDate    = new Date(new Date().getFullYear(), 0, 1);               // 2017-01-01
                        e.chart.lastZoomed.endDate      = new Date(new Date().getFullYear(), 11, 31,23,59,59);    // 2017-31-12
                        eventClick = 'set';
                    }
                    
                    // set up generic mouse events
                    var sb = e.chart.chartScrollbar.set.node;
                    sb.addEventListener("mousedown", function() {
                    e.chart.mouseIsDown = true;
                    });
                    
                    e.chart.chartDiv.addEventListener("mouseup", function() {
                    e.chart.mouseIsDown = false;
                    // zoomed finished
                
                    
                    var sDate       = e.chart.lastZoomed.startDate;
                    var startDate   = sDate.getFullYear()+'-'+(sDate.getMonth()+1)+'-'+sDate.getDate();

                    var eDate       = e.chart.lastZoomed.endDate;
                    var endDate     = eDate.getFullYear()+'-'+(eDate.getMonth()+1)+'-'+eDate.getDate();

                    //console.log(startDate+' => '+endDate);
        
                    
                    /**
                     * Zoom Chart
                     * 
                     */

                    
                    // Start Date
                    var OldDateS = scope.formDate;                  // Old Date
                    var NewDateS = new Date(startDate).getTime();   // New Date

                    // End Date
                    var OldDateE = scope.toDate;                    // Old Date
                    var NewDateE = new Date(endDate).getTime();     // New Date

                    // ถ้าวันที่ไม่เปลี่ยน ไม่ต้องดึงข้อมูลใหม่
                    if((OldDateS === NewDateS) && (OldDateE === NewDateE)){

                      console.log('Use data loadded');

                    }else{

                      var now       = new Date();
                      var thisTime  = now.getHours()+':'+now.getMinutes()+':'+now.getSeconds();
                      console.log('Getting new data ' + thisTime);

                      // Get Data from server
                      chartData = '';
                      chartData = generatechartData(startDate,endDate);

                      // Set date to view
                      scope.countDocument   = chartData.jobs;
                      scope.sumTotal        = chartData.total; 
                      scope.formDate        = new Date(startDate).getTime();
                      scope.toDate          = new Date(endDate).getTime();

                       

                      // Update Chart
                      setDataSetPie('amount',chartData);  
                      setDataSet('amount',chartData);
                      setDataSet3('amount',chartData);
                    }
            
                    });
                }

            }, {
            "event": "zoomed",
            "method": function(e) {
                    e.chart.lastZoomed = e;
                }
            }]


        });
 
        chart.dataProvider = chartData.line;    

        // console.log(chartData.line);
       
      
         
        











        var chart1 = AmCharts.makeChart("pieChartdiv", {
            "hideCredits" : true,
            "type": "pie",
            "startDuration": 0,
             "theme": "light",
            "addClassNames": true,
            "legend":{
             	"position":"right",
              "marginRight":100,
              "autoMargins":false
            },
            "innerRadius": "30%",
            "defs": {
              "filter": [{
                "id": "shadow",
                "width": "200%",
                "height": "200%",
                "feOffset": {
                  "result": "offOut",
                  "in": "SourceAlpha",
                  "dx": 0,
                  "dy": 0
                },
                "feGaussianBlur": {
                  "result": "blurOut",
                  "in": "offOut",
                  "stdDeviation": 5
                },
                "feBlend": {
                  "in": "SourceGraphic",
                  "in2": "blurOut",
                  "mode": "normal"
                }
              }]
            },
            "dataProvider": chartData.items,
            "valueField": "qty",
            "titleField": "name",
            "colorField": "color",
            "export": {
              "enabled": true
            },
            "listeners": [{
              "event": "clickSlice",
              "method": function(event) {

                var itemNo = event.dataItem.dataContext;
                $('.item-info-body').html('');
                $('#itemInfoModal').modal('show');
                $.ajax({
                  url:'index.php?r=items/items/view-modal&id='+itemNo.no,
                  method:'GET',
                  success:function(getData){
                     $('.item-info-body').html(getData);
                     $('.sale-summary a').attr('href','index.php?r=SaleOrders/event/sale-line&No='+itemNo.no).attr('target','_blank');
                     $('.colorpicker').val(itemNo.color).attr('data-no',itemNo.no).attr('style','background-color:'+itemNo.color);
                     //$('input[name="group_chart"]').val(itemNo.group).attr('data-key',itemNo.no);
                  }
                })
              }
            }]
          });

          chart1.addListener("init", handleInit);

          chart1.addListener("rollOverSlice", function(e) {
            handleRollOver(e);

          });

          function handleInit(){
            chart1.legend.addListener("rollOverItem", handleRollOver);

          }

          function handleRollOver(e){
            var wedge = e.dataItem.wedge.node;
            wedge.parentNode.appendChild(wedge);
          }


          //Ajax Load data
          function setDataSetPie(field,sendData) {


            //var dataSet = chartData.items;
            var dataSet = sendData.items;

             var baseItemSale = [];


             if(field=='sales'){

               dataSet = dataSet.sort(function (a, b) {
                   return  b.total - a.total;
               });

               for(var i = 0; i < (dataSet.length>=20 ? 20:dataSet.length); i++){
                   var itemList = dataSet[i];
                   baseItemSale.push({
                     no      : itemList.no,
                     barcode :itemList.barcode,
                     qty     :itemList.total,
                     name    : itemList.name,
                     total   : itemList.total,
                     color   : itemList.color,
                   });
               }
             }else {

               dataSet = dataSet.sort(function (a, b) {
                   return  b.qty - a.qty;
               });

               for(var i = 0; i < (dataSet.length>=20 ? 20:dataSet.length); i++){
                   var itemList = dataSet[i];
                   baseItemSale.push({
                     no      : itemList.no,
                     barcode :itemList.barcode,
                     qty     :itemList.qty,
                     name    : itemList.name,
                     total   : itemList.total,
                     color   : itemList.color,
                   });
               }
             }

              

             chart1.dataProvider = baseItemSale;
             chart1.validateData();
          }


            $('body').on('click','button.sort-by-sales',function(){

              setDataSetPie('sales',chartData);

            })

            $('body').on('click','button.sort-by-amount',function(){

              setDataSetPie('amount',chartData);

            })











        var chart2 = AmCharts.makeChart("barChartdiv", {
            "hideCredits" : true,
            "type": "serial",
            "theme": "light",
            "marginRight": 70,
            "dataProvider": chartData.items2,
            "valueAxes": [{
              "axisAlpha": 0,
              "position": "left",
              "title": "Number of Items Sold"
            }],
            "startDuration": 2,
            "graphs": [{
              //"balloonText": "<b>[[category]]: [[value]]</b>",
              "balloonText": "<img src=[[img]] style='vertical-align:bottom; margin-right: 10px; height:80px;'><span style='font-size:14px; color:#000000;'><b>[[value]]</b></span>",
              "fillColorsField": "color",
              "lineAlpha": 0.2,
              "bulletOffset": 15,
              "bulletSize": 22,
              "customBulletField": "img",
              "type": "column",
              "valueField": "qty",
              "fillAlphas": 0.7,
            }],
            "chartCursor": {
              "categoryBalloonEnabled": false,
              "cursorAlpha": 0,
              "zoomable": false
            },
            "categoryField": "name",
            "categoryAxis": {
              "gridPosition": "start",
              "labelRotation": 45
            },
            "export": {
              "enabled": true
            },

            "depth3D": 20,
            "angle": 25,

            "listeners": [{
              "event": "clickGraphItem",
              "method": function(event) {
                //alert(event.item.category);
                var itemNo = event.item.dataContext;
                $('.item-info-body').html('');
                $('#itemInfoModal').modal('show');
                $.ajax({
                  url:'index.php?r=items/items/view-modal&id='+itemNo.no,
                  method:'GET',
                  success:function(getData){
                     $('.item-info-body').html(getData);
                     $('.sale-summary a').attr('href','index.php?r=SaleOrders/event/sale-line&No='+itemNo.no).attr('target','_blank');
                     $('.colorpicker').val(itemNo.color).attr('data-no',itemNo.no).attr('style','background-color:'+itemNo.color);
                     //$('input[name="group_chart"]').val(itemNo.group).attr('data-key',itemNo.no);
                  }
                })

              }
            }]

          });


          function setDataSet(field,sendData) {

            //var dataSet = chartData.items2;
            var dataSet = sendData.items2;

             var baseItemSale = [];

             if(field=='sales'){

               dataSet = dataSet.sort(function (a, b) {
                   return  b.total - a.total;
               });

               for(var i = 0; i < (dataSet.length>=20 ? 20:dataSet.length); i++){
                   var itemList = dataSet[i];
                   baseItemSale.push({
                     no      : itemList.no,
                     barcode : itemList.barcode,
                     qty     : itemList.total,
                     name    : itemList.name,
                     total   : itemList.total,
                     color   : itemList.color,
                     img     : itemList.img,
                   });
               }

               $('button.sort-by-amount').attr('disabled',false);
               $('button.sort-by-amount').removeClass('active');

               $('button.sort-by-sales').attr('disabled',true);
               $('button.sort-by-sales').addClass('active');
             }else {

               dataSet = dataSet.sort(function (a, b) {
                   return  b.qty - a.qty;
               });

               for(var i = 0; i < (dataSet.length>=20 ? 20:dataSet.length); i++){
                   var itemList = dataSet[i];
                   baseItemSale.push({
                     no      : itemList.no,
                     barcode : itemList.barcode,
                     qty     : itemList.qty,
                     name    : itemList.name,
                     total   : itemList.total,
                     color   : itemList.color,
                     img     : itemList.img,
                   });
               }

               $('button.sort-by-amount').attr('disabled',true);
               $('button.sort-by-amount').addClass('active');

               $('button.sort-by-sales').attr('disabled',false);
               $('button.sort-by-sales').removeClass('active');
             }


             chart2.dataProvider = baseItemSale;
             chart2.validateData();

          }


            $('body').on('click','button.sort-by-sales',function(){

                setDataSet('sales',chartData);

            })

            $('body').on('click','button.sort-by-amount',function(){

                setDataSet('amount',chartData);

            })
        




        chart.addListener("rendered", zoomChart);
        if(chart.zoomChart){
          chart.zoomChart();
        }

        function zoomChart(){
            chart.zoomToIndexes(Math.round(chart.dataProvider.length * 0.4), Math.round(chart.dataProvider.length * 0.55));
        }









        var chart3 = AmCharts.makeChart( "pieChartItemgroup", {
        	"hideCredits" : true,
          "type": "pie",
          "theme": "light",
          "dataProvider": chartData.groups,
          "titleField": "name",
          "valueField": "qty",
          "labelRadius": 5,

          "radius": "32%",
          "innerRadius": "60%",
          "labelText": "[[title]]",
          "export": {
            "enabled": false
          },
          "allLabels": [{
            "y": "45%",
            "align": "center",
            "size": 15,
            "bold": true,
            "color": "#555",
            "text": 'Quantity'
          }],
        });

        //Ajax Load data
        function setDataSet3(field,sendData) {


          var dataSet = sendData.groups;

           var groupItemSale = [];


           if(field=='sales'){

             dataSet = dataSet.sort(function (a, b) {
                 return  b.total - a.total;
             });

             for(var i = 0; i < dataSet.length; i++){
                 var itemList = dataSet[i];
                 groupItemSale.push({
                   name    : itemList.name,
                   qty     : itemList.total,

                 });
             }

             chart3.allLabels = [{
               "y": "45%",
               "align": "center",
               "size": 15,
               "bold": true,
               "color": "#555",
               "text": lang('common','Value')
             }];

           }else {

             dataSet = dataSet.sort(function (a, b) {
                 return  b.qty - a.qty;
             });

             for(var i = 0; i < dataSet.length; i++){
                 var itemList = dataSet[i];
                 groupItemSale.push({
                   name    : itemList.name,
                   qty     : itemList.qty,
                 });
             }

             chart3.allLabels = [{
               "y": "45%",
               "align": "center",
               "size": 15,
               "bold": true,
               "color": "#555",
               "text": lang('common','Quantity')
             }];

           }



           chart3.dataProvider = groupItemSale;
           chart3.validateData();
            
        }

        // Load text 3.8 second.
        setTimeout(function(){
          chart3.allLabels = [{
            "y": "45%",
            "align": "center",
            "size": 15,
            "bold": true,
            "color": "#555",
            "text": lang('common','Quantity')
          }];
          chart3.validateData();
        },3800);

          $('body').on('click','button.sort-by-sales',function(){

            setDataSet3('sales',chartData);

          })

          $('body').on('click','button.sort-by-amount',function(){

            setDataSet3('amount',chartData);

          })







      }
      initChart();
    }

  }
})


 

 