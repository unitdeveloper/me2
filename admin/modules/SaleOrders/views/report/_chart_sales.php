 

<div id="saleIdLoad<?=$div?>" data="<?=$saleCode?>">
	<?php //echo $div.' - '; echo $saleCode; ?>
</div>
<!-- Chart code -->
<script>



var SalePeopleDetail<?=$div?> = [];


function SalePeopleContentDetail<?=$div?>()
{
    $.ajax({ 

        url:"index.php?r=SaleOrders/report/json-sale-people-column-chart",
        type: "GET", 
        data: {saleId:$('#saleIdLoad<?=$div?>').attr('data')},
        async:true,
        success:function(getData){
             
            var obj = jQuery.parseJSON(getData);

            $.each(obj, function (index, value) {
                SalePeopleDetail<?=$div?>.push({
                    nameMonth:value["month"], 
                    saleAmount:value["saleAmount"]/1000000,  
                    color:'#FF0F00'
                });


            });

        }

    }); 
}
SalePeopleContentDetail<?=$div?>();

console.log(SalePeopleDetail<?=$div?>);

var chart = AmCharts.makeChart("chartPersale", {
  "type": "serial",
  "theme": "light",
  "marginRight": 70,
  "dataProvider": [{
    "saleAmount": "USA",
    "nameMonth": 3025,
    "color": "#FF0F00"
  }, {
    "saleAmount": "China",
    "nameMonth": 1882,
    "color": "#FF6600"
  }, {
    "saleAmount": "Japan",
    "nameMonth": 1809,
    "color": "#FF9E01"
  }, {
    "saleAmount": "Germany",
    "nameMonth": 1322,
    "color": "#FCD202"
  }, {
    "saleAmount": "UK",
    "nameMonth": 1122,
    "color": "#F8FF01"
  }, {
    "saleAmount": "France",
    "nameMonth": 1114,
    "color": "#B0DE09"
  }, {
    "saleAmount": "India",
    "nameMonth": 984,
    "color": "#04D215"
  }, {
    "saleAmount": "Spain",
    "nameMonth": 711,
    "color": "#0D8ECF"
  }, {
    "saleAmount": "Netherlands",
    "nameMonth": 665,
    "color": "#0D52D1"
  }, {
    "saleAmount": "Russia",
    "nameMonth": 580,
    "color": "#2A0CD0"
  }, {
    "saleAmount": "South Korea",
    "nameMonth": 443,
    "color": "#8A0CCF"
  }, {
    "saleAmount": "Canada",
    "nameMonth": 441,
    "color": "#CD0D74"
  }],
  "valueAxes": [{
    "axisAlpha": 0,
    "position": "left",
    "title": "Visitors from country"
  }],
  "startDuration": 1,
  "graphs": [{
    "balloonText": "<b>[[category]]: [[value]]</b>",
    "fillColorsField": "color",
    "fillAlphas": 0.9,
    "lineAlpha": 0.2,
    "type": "column",
    "valueField": "nameMonth"
  }],
  "chartCursor": {
    "categoryBalloonEnabled": false,
    "cursorAlpha": 0,
    "zoomable": false
  },
  "categoryField": "saleAmount",
  "categoryAxis": {
    "gridPosition": "start",
    "labelRotation": 45
  },
  "export": {
    "enabled": true
  }

});

</script>
 

<div class="row">
     	<div class="col-sm-4">
     		<div class="col-sm-12">
				<div><h5><?=Yii::t('common','Information')?></h5></div>
				<div class="info-box">
				<span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

				<div class="info-box-content">
				  <span class="info-box-text">Sales</span>
				  <span class="info-box-number">-</span>
				</div>
				<!-- /.info-box-content -->
				</div>
				<!-- /.info-box -->
			</div>
     	</div>
         
        <div class="col-sm-8">
        	<div><h5>ยอด ​Sale Order รายปี</h5></div>
            <div id="chartPersale"></div>
        </div>
     
</div>
