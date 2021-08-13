<div class="col-sm-6">
    <div id="chartdiv"></div>
</div>
<!-- Styles -->
<style>
#chartdiv {
  width: 100%;
  height: 500px;
}

</style>

<!-- Resources -->
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

<!-- Chart code -->
<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("chartdiv", am4charts.PieChart3D);

var colorSet = new am4core.ColorSet();
colorSet.list = ["#00c0ef", "#74f174"].map(function(color) {
  return new am4core.color(color);
});


chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

chart.legend = new am4charts.Legend();

chart.data = [
  {
    country: "Usage Disk",
    litres: $('#disk-total').attr('data-val') - $('#disk-free').attr('data-val')
  },
  {
    country: "Free Space",
    litres: $('#disk-free').attr('data-val')
  } 
];

var series = chart.series.push(new am4charts.PieSeries3D());
series.colors = colorSet;
series.dataFields.value = "litres";
series.dataFields.category = "country";

}); // end am4core.ready()
</script>