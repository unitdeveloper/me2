<!-- pickitem index.php?r=SaleOrders/saleorder/viewitem-->
 
<style type="text/css">
	.text-small{
		font-size: 15px;
	}
	.small-box:hover{
		/* box-shadow: 10px 10px 16px #DEDEDE;
		-moz-box-shadow: 10px 10px 16px #DEDEDE;*/
		color:#000;
		border:1px solid #ffd890 !important;
		/* transform: translateY(-2px);*/
		transition: all .3s;  
	} 

	/*.shadow:hover{
		color:#000;
	}*/
	#ewInput-txt{
		font-size:16px;
		min-width:50px;
		/*height:40px; color:#000;*/
	}
	.ew-Code{
		display:none;
	}

	#ew-Head {
		margin:0px 0px 19px -3px;
		padding: 10px;
    	border: 1px solid #f7f5f5;
	}

	#PickItem-Modal .modal-body{
		/* background-color:#f9fafc; */
	}

  #PickItem-Modal .names{
    display:none;
  }

	@media (max-width: 768px) {
		.Picking{
			margin: 27px -18px 0px -12px;
		}

		#PickItem-Modal .names{
      display: block;
			width: 100%;
			position: fixed;
			top: 45px;
			left: 0px;
			padding: 5px;
			background: rgb(0 0 0 / 77%);
			z-index: 1000;
      color:#29e1ff;
		}

    .modal-full .modal-body {
      padding:20px !important;
    }

    #PickItem-Modal .modal-footer{
      height: 65px;
      padding-top: 15px;
    }
	}

	@media (max-width: 425px) {
		.ew-box-click{
			margin-left: -10px !important;
			margin-right: 0px !important;
		}	

		div#show_master_code{
			position: fixed;
			bottom: 0;
			right: 0;
			width: 300px;
			border: 3px solid #73AD21;
		}	
	}

	@media (max-width: 320px) {
		.col-xs-6{
			width:100%;
		}
	}
</style>
<div id="PickItem-Modal" class="modal modal-full fade in" role="dialog" data-keyboard="false" data-backdrop="static" style="transform: translate3d(0,0,0);">
  <div class="modal-dialog  ">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header" style="background:#000;">
        <button type="button" class="close" data-dismiss="modal"  >&times;</button>
        <h4 class="modal-title">Product Infomation</h4>
        <div class="names ew-Validate-" ></div>
      </div>
      <div class="Smooth-Ajax">
      <div class="modal-body ew-create-item" style="background-color:#f9fafc;">

        <p>Loading...</p>
      </div>
      </div>
      <div class="modal-footer" style="background: #000000bd; z-index: 3;">
        <button type="button" class="btn btn-default close-ewItemInfoModal pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i>  
          <?=Yii::t('common','Close');?></button>

        <button type="button" name="Select" id="PickToSaleLine" style="display:none;" class="btn btn-info" ><i class="fa fa-check"></i> 
          <?=Yii::t('common','Select')?></button>

        <button class="btn btn-info" id="ewSelect" style="display:none;"><i class="fa fa-check"></i> 
          <?=Yii::t('common','Select');?></button>   
      </div>
    </div>

  </div>
</div>

