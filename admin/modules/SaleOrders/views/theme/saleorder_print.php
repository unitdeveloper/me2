<!DOCTYPE html>
<html>
<head>
	<title>Sale Order</title>
 
	<?php
	$optionsArray = array(
	'elementId'=> 'showBarcode', /* div or canvas id*/
	'value'=> 'SO1705-0001',/* value for EAN 13 be careful to set right values for each barcode type */
	'type'=>'code39',/*supported types  ean8, ean13, upc, std25, int25, code11, code39, code93, code128, codabar, msi, datamatrix*/
	 
	);
	 

	?>
	<?= \barcode\barcode\BarcodeGenerator::widget($optionsArray); ?>
</head>
<body onload="window.print()">
	<div class="print-content">
		Print
		<div id="showBarcode" class="hidden-xs hidden-sm"></div>
	</div>
</body>
</html>

