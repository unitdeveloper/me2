<!DOCTYPE html>
<html >
<head>
	<title>Sale Order</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
	<style type="text/css">
		.panel-body {
		  position: relative;
		}
		#showBarcode {
		  position: absolute;
		  right: 4px;
		  top: 4px;
		}
	</style>
	<?php

	use \barcode\barcode\BarcodeGenerator; 

	$optionsArray = array(
	'elementId'=> 'showBarcode', /* div or canvas id*/
	'value'=> 'SO1705-0001',/* value for EAN 13 be careful to set right values for each barcode type */
	'type'=>'code39',/*supported types  ean8, ean13, upc, std25, int25, code11, code39, code93, code128, codabar, msi, datamatrix*/
	 
	);
	 

	?>
	<?= BarcodeGenerator::widget($optionsArray); ?>
</head>
<body>
	<div class="print-content">
		
		<div id="showBarcode"></div>


		<div ng-app="myApp" ng-controller="myCtrl">

		First Name: <input type="text" ng-model="firstName"><br>
		Last Name: <input type="text" ng-model="lastName"><br>
		<br>
		Full Name: {{firstName + " " + lastName}}


			<button ng-click="printDiv('printableArea');">Print</button>
			<div id="printable">
				Print this div
			</div>
			 
		</div>




	</div>
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>

	<script>
	var app = angular.module('myApp', []);
	app.controller('myCtrl', function($scope) {
	    $scope.firstName = "John";
	    $scope.lastName = "Doe";

	    $scope.printDiv = function(printable) {
		  var printContents = document.getElementById('printable').innerHTML;
		  var popupWin = window.open('', '_blank', 'width=300,height=300');
		  popupWin.document.open();
		  popupWin.document.write('<html><head><link rel="stylesheet" type="text/css" href="style.css" /></head><body onload="window.print()">' + printContents + '</body></html>');
		  popupWin.document.close();
		} 

	});
	</script>
</body>
</html>
 
