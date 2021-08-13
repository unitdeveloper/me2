<style>
	.panel-dialog{
		position: fixed;
		right: 0px;
		top: 0px;
		display: none;
		height: 100%;
		width: 70%;
		z-index: 3000;

		background-color: rgb(254,254,254);
		border:1px solid #999;
		box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);
 
		
	}

	.maximize{
		margin-top: -3px;
	}
 
	.panel-heading{
		height: 50px;
	}
	.panel-body{
		overflow-x: auto;
		height: 85%;
		display: none;
		
	}
	.panel-footer{
		position: absolute;
		width: 100%;
		bottom: 0px;
		right: 0px;
	}
	.loading{
		position: absolute;
		left: 45%;
		top: 40%;
	}
</style>





<div class="panel panel-dialog ">
	<div class="panel-heading bg-dark  heading-dialog">
	<a href="#" class="close-dialog"><i class="fa fa-times-circle text-red" aria-hidden="true" ></i> </a>
	<a href="#" class="hide-dialog"><i class="fa fa-minus-circle text-yellow" aria-hidden="true"></i> </a>
	<a href="#"><img src="images/icon/maximize.png" width="14px;" class="maximize"> </a>

	</div>
	<div class="panel-body">
		<div class="ew-dialog-body"></div>
		
	</div>
	<div class="loading"><i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i><span class="sr-only">Loading...</span></div>
	<div class="panel-footer">
		<button type="button" class="btn btn-default-ew  pull-left close-dialog">
        <i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></button>  

        <button type="button" class="btn btn-success-ew  pull-right approve-btn">
        <i class="fa fa-check-square-o" aria-hidden="true"></i> <?=Yii::t('common','Approve')?></button>  
	</div>
</div>