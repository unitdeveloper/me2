<div id="itemGroup"></div>
<?php $this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js',['depends' => [\admin\assets\ReactAsset::className()]]); ?>


<?php
	$Options =  ['depends' => [\admin\assets\ReactAsset::className()],'type'=>'text/jsx'];
	$this->registerJsFile('@web/js/items/group.jsx?v=3.06.26.1', $Options);
?>