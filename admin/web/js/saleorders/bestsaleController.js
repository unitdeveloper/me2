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
                "filter": [
                    {
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
                    }
                ]
            },
            "dataProvider": chartData.data,
            "valueField": "qty",
            "titleField": "name",
            "colorField": "color",
            "export": {
                "enabled": true
            }
            });

 

            chart.addListener("init", handleInit);

            chart.addListener("rollOverSlice", function(e) {
              handleRollOver(e);
            });
            
            function handleInit(){
              chart.legend.addListener("rollOverItem", handleRollOver);
            }
            
            function handleRollOver(e){
              var wedge = e.dataItem.wedge.node;
              wedge.parentNode.appendChild(wedge);
            }


      }
      initChart();
    }

  }
})


 

 