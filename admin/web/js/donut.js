            var chart;
            var legend;

            var chartData = [
                {
                    "country": "Cz",
                    "litres": 156.9
                },
                {
                    "country": "Ir",
                    "litres": 131.1
                },
                {
                    "country": "Ger",
                    "litres": 115.8
                },
                {
                    "country": "Aus",
                    "litres": 109.9
                },
                {
                    "country": "Aus",
                    "litres": 108.3
                },
                {
                    "country": "UK",
                    "litres": 65
                },
                {
                    "country": "Bel",
                    "litres": 50
                }
            ];

            AmCharts.ready(function () {
                // PIE CHART
                chart = new AmCharts.AmPieChart();
                chart.dataProvider = chartData;
                chart.titleField = "country";
                chart.valueField = "litres";
                chart.gradientRatio = [0, 0, 0 ,-0.2, -0.4];
                chart.gradientType = "radial";
                chart.hideCredits = true;

                // LEGEND
                legend = new AmCharts.AmLegend();
                legend.align = "center";
                legend.markerType = "circle";
                chart.balloonText = "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>";
                chart.addLegend(legend);

                // WRITE
                chart.write("chartdivdonut");
            });

            // changes label position (labelRadius)
            function setLabelPosition() {
                if (document.getElementById("rb1").checked) {
                    chart.labelRadius = 30;
                    chart.labelText = "[[title]]: [[value]]";
                } else {
                    chart.labelRadius = -30;
                    chart.labelText = "[[percents]]%";
                }
                chart.validateNow();
            }


            // makes chart 2D/3D
            function set3D() {
                if (document.getElementById("rb3").checked) {
                    chart.depth3D = 10;
                    chart.angle = 10;
                } else {
                    chart.depth3D = 0;
                    chart.angle = 0;
                }
                chart.validateNow();
            }

            // changes switch of the legend (x or v)
            function setSwitch() {
                if (document.getElementById("rb5").checked) {
                    legend.switchType = "x";
                } else {
                    legend.switchType = "v";
                }
                legend.validateNow();
            }

