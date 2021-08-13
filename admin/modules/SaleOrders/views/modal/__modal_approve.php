<style type="text/css">

	/* Modal Center Screen*/
	#ew-modal-Approve {
	  text-align: center;
	  padding: 0!important;
	}

	#ew-modal-Approve:before {
	  content: '';
	  display: inline-block;
	  height: 100%;
	  vertical-align: middle;
	  margin-right: -4px;
	}

	.Approve-dialog {
	  display: inline-block;
	  text-align: left;
	  vertical-align: middle;
	}

	.blink {
	  animation: blinker 1s linear infinite;
	}

	@keyframes blinker {  
	  50% { opacity: 0; }
	}
</style>
<!-- pickitem index.php?r=SaleOrders/saleorder/viewitem-->
<div id="ew-modal-Approve" class="modal fade" role="dialog" >
  <div class="modal-dialog Approve-dialog">
	<form>  
	    <!-- Modal content-->
	    <div class="modal-content" >
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><?=Yii::t('common','Approval'); ?></h4>
	      </div>
	      <div class="Smooth-Ajax">
	      <div class="modal-body">
			<span id="ew-data-text" style="display: none;"></span>
	        <h4>ต้องการ <span id="ew-showText">!!</span> รายการหรือไม่ ? </h4>
	        <div class="reject-reason ew-approve-body"></div>
	      </div>
	      </div>
	      <div class="modal-footer">
	      	  	
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	        <input type="submit" class="btn btn-primary ew-confirm" name="btn-confirm" value="Confirm" >
	       
	      </div>
	    </div>
	</form>
  </div>
</div>

