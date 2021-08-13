<?php
use yii\helpers\Html;
?>
<style>
body .content-wrapper {
 background:#000000 !important;
}

/*

*/

h3 {
  color: rgba(31,181,172,.9);
}
.text{
	color: rgba(31,181,172,.9);
	text-align: center;
}


.folded-corner:hover .text{
	visibility: visible;
	color: #000000;;
}
.Services-tab{
	margin-top:20px;
	

}

/*
  nav link items
*/
.folded-corner{
  padding: 25px 25px;
  position: relative;
  font-size: 90%;
  text-decoration: none;
  color: #999; 
  background: transparent;
  transition: all ease .5s;
  border: 1px solid rgba(31,181,172,.9);
}
.folded-corner:hover{
	background-color: rgba(31,181,172,.9);
}

/*
  paper fold corner
*/

.folded-corner:before {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  border-style: solid;
  border-width: 0 0px 0px 0;
  border-color: #ddd #000;
  transition: all ease .3s;
}

/*
  on li hover make paper fold larger
*/
.folded-corner:hover:before {
	background-color: #D00003;
  border-width: 0 50px 50px 0;
  border-color: #eee #000;
  
}

.service_tab_1{
	background-color: #000;
}
.service_tab_1:hover .fa-icon-image{
    color: #000;
    transform: rotate(360deg) scale(1.5);
}


.fa-icon-image{
	color: rgba(31,181,172,.9);
	display: inline-block;
    font-style: normal;
    font-variant: normal;
    font-weight: normal;
    line-height: 1;
    font-size-adjust: none;
    font-stretch: normal;
    -moz-font-feature-settings: normal;
    -moz-font-language-override: normal;
    text-rendering: auto;
    transition: all .65s linear 0s;
    text-align: center;
    transition: all 1s cubic-bezier(.99,.82,.11,1.41);
}

 
</style>


<div class="content">
<?=$this->render('widget')?>
<div class="row" style="margin-left:-55px !important;">
	<ul>
		<a class="col-lg-6 col-md-6 col-sm-12 col-xs-12 Services-tab  item" href="?r=items%2Fstock%2Findex">
			<div class="folded-corner service_tab_1">
				<div class="text">
				<i class="fa fa-chart-pie fa-5x fa-icon-image"></i>
						<p class="item-title">
								<h3> <?=Yii::t('common','Stock')?></h3>
							</p><!-- /.item-title -->
					<p>
					</p>
				</div>
			</div>
		</a>
		<a class="col-lg-3 col-md-3 col-sm-12 col-xs-12 Services-tab item" href="?r=customers%2Fcustomer%2Freadonly">
			<div class="folded-corner service_tab_1">
				<div class="text">
					<i class="fa fa-id-card fa-5x fa-icon-image"></i>
						<p class="item-title">
							<h3> <?=Yii::t('common','Customer')?></h3>
						</p><!-- /.item-title -->
					<p>
						
					</p>
				</div>
			</div>
		</a>

		<a class="col-lg-3 col-md-3 col-sm-12 col-xs-12 Services-tab item" href="?r=salepeople%2Fpeople%2Fread-only">
			<div class="folded-corner service_tab_1">
				<div class="text">
					<i class="fa fa-users fa-5x fa-icon-image"></i>
						<p class="item-title">
							<h3> <?=Yii::t('common','Sales')?></h3>
						</p><!-- /.item-title -->
					<p>
						
					</p>
				</div>
			</div>
		</a>

		
		<a class="col-lg-3 col-md-3 col-sm-12 col-xs-12 Services-tab item" href="?r=SaleOrders%2Fsaleorder">
			<div class="folded-corner service_tab_1">
				<div class="text">
					<i class="fa fa-dollar-sign fa-5x fa-icon-image"></i>
						<p class="item-title">
							<h3><?=Yii::t('common','Sale Order')?></h3>
						</p><!-- /.item-title -->
					<p>
						
					</p>
				</div>
			</div>
		</a>
		
		
		<a class="col-lg-3 col-md-3 col-sm-12 col-xs-12 Services-tab item" href="?r=SaleOrders%2Freport%2Fbest-sale" target="_blank">
			<div class="folded-corner service_tab_1">
			<div class="text">
				<i class="fa fa-line-chart fa-5x fa-icon-image"></i>
					<p class="item-title">
						<h3><?=Yii::t('common','Best Sale')?></h3>
					</p><!-- /.item-title -->
					<p>
						
					</p>
				</div>
			</div>
		</a>
	
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 Services-tab item">
				<div class="folded-corner service_tab_1 ">
					<div class="text">
						<i class="fa fa-tv fa-5x "></i>
							<p class="item-title">
								<h3><?=Yii::t('common','POS')?></h3>
							</p><!-- /.item-title -->
						<p>
							
						</p>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 Services-tab item">
			<div class="folded-corner service_tab_1 ">
				<div class="text">
					<i class="fa fa-bullhorn fa-5x "></i>
						<p class="item-title">
							<h3> Support</h3>
						</p><!-- /.item-title -->
					<p>
						
					</p>
				</div>
			</div>
		</div>
	   </ul>
	</div>
</div>