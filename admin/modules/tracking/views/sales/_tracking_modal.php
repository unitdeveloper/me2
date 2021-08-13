<?php 

/*
 <ul class="timeline">
	<li>
	  <div class="timeline-badge"><i class="glyphicon glyphicon-check"></i></div>
	  <div class="timeline-panel">
	    <div class="timeline-heading">
	      <h4 class="timeline-title">Mussum ipsum cacilds</h4>
	      <p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> 11 hours ago via Twitter</small></p>
	    </div>
	    <div class="timeline-body">
	      <p>Mussum ipsum cacilds, vidis   </p>
	    </div>
	  </div>
	</li>

	<li class="timeline-inverted">
	  <div class="timeline-badge warning"><i class="glyphicon glyphicon-credit-card"></i></div>
	  <div class="timeline-panel">
	    <div class="timeline-heading">
	      <h4 class="timeline-title">Mussum ipsum cacilds</h4>
	    </div>
	    <div class="timeline-body">
	      <p>Mussum ipsum cacilds, vidis litro  </p>
	      
	    </div>
	  </div>
	</li>

	<li>
	  <div class="timeline-badge danger"><i class="glyphicon glyphicon-credit-card"></i></div>
	  <div class="timeline-panel">
	    <div class="timeline-heading">
	      <h4 class="timeline-title">Mussum ipsum cacilds</h4>
	    </div>
	    <div class="timeline-body">
	      <p>Mussum ipsum cacilds, vidis litro  </p>
	    </div>
	  </div>
	</li>

	<li class="timeline-inverted">
	  <div class="timeline-panel">
	    <div class="timeline-heading">
	      <h4 class="timeline-title">Mussum ipsum cacilds</h4>
	    </div>
	    <div class="timeline-body">
	      <p>Mussum ipsum cacilds, vidis litro </p>
	    </div>
	  </div>
	</li>
 


</ul> */

?>
<?php

	$liClass 	= NULL;
	$classIcon 	= 'timeline-inverted';
	$icon 		= '<i class="glyphicon glyphicon-credit-card"></i>';
	$DocStatus 	= NULL;
	$href		= 'href="#"';









	$li = '<ul class="timeline">';

	foreach ($model as $value) {


		
		if($value->doc_type == 'Sale-Order') {


			$liClass 	= NULL;
			$classIcon 	= NULL;
			$icon 		= '<i class="glyphicon glyphicon-check"></i>';
			$DocStatus 	= convDocStatus($value->doc_status);
			$href		= 'href="#"';

			switch ($value->doc_status) {
				case 'Create':
					$classIcon 	= NULL;
					$icon 		= '<i class="fa fa-first-order" aria-hidden="true"></i>';
					break;

				case 'Open':
					$classIcon 	= 'info';
					$icon 		= '<i class="glyphicon glyphicon glyphicon-check"></i>';
					break;

				case 'Release':
					$classIcon 	= 'danger';
					$liClass 	= 'timeline-inverted';
					$icon 		= '<i class="fa fa-envelope-o" aria-hidden="true"></i>';
					break;

				case 'Confirm':
					$liClass 	= 'timeline-inverted';
					$classIcon 	= 'warning';
					$icon 		= '<i class="fa fa-hourglass-half" aria-hidden="true"></i>';
					break;	

				case 'Checking':
					$liClass 	= 'timeline-inverted';
					$classIcon 	= 'warning';
					$icon 		= '<i class="fa fa-hourglass-half" aria-hidden="true"></i>';
					break;	

				case 'Reject':
					$liClass 	= NULL;
					$classIcon 	= NULL;
					$icon 		= '<i class="fa fa-undo text-danger" aria-hidden="true"></i>';
					break;	

				case 'Shiped':
					$liClass 	= NULL;
					$classIcon 	= 'success';
					$icon 		= '<i class="fa fa-truck" aria-hidden="true"></i>';
					break;	

				case 'Reject-Pre-Cancel':
					$liClass 	= 'timeline-inverted';
					$classIcon 	= 'dark';
					$icon 		= '<i class="fa fa-times" aria-hidden="true"></i>';
					break;	
				
				default:
					$classIcon 	= NULL;
					$liClass 	= NULL;
					break;
			}
				 
			 

			 

		}else if($value->doc_type == 'Sale-Ship'){

		 
			$liClass 	= 'timeline-inverted';
			$classIcon 	= 'info';
			$icon 		= '<i class="fa fa-cubes" aria-hidden="true"></i>';
			$DocStatus 	= ShipmentStatus($value->doc_status);
			$href		= 'href="index.php?r=warehousemoving%2Fheader%2Fview&id='.$value->doc_id.'" target="_blank"';
			

			switch ($value->doc_status) {

				case 'Undo':
					$liClass 		= NULL;
					$classIcon 		= NULL;
					$icon 			= '<i class="fa fa-undo text-danger" aria-hidden="true"></i>';
					$DocStatus 		= ShipmentStatus($value->doc_status);
					$value->doc_no 	= '<i class="glyphicon glyphicon-glass" aria-hidden="true"></i> '.$value->doc_no;
					break;
				

				default:
					$liClass 		= 'timeline-inverted';
					$classIcon 		= 'info';
					$icon 			= '<i class="fa fa-cubes" aria-hidden="true"></i>';
					$DocStatus 		= ShipmentStatus($value->doc_status);
					$value->doc_no 	= '<i class="fa fa-cube" aria-hidden="true"></i> '.$value->doc_no;
					break;
			}
				 

		}else if($value->doc_type == 'Sale-Inv'){

			$liClass 	= 'timeline-inverted';
			$classIcon 	= 'warning';
			$icon 		= '<i class="fa fa-tasks" aria-hidden="true"></i>';
			$DocStatus 	= InvoiceStatus($value->doc_status);
			$href		= 'href="#"';

		}else if($value->doc_type == 'Rc-Inv'){

			$liClass 	= NULL;
			$classIcon 	= 'danger';
			$icon 		= '<i class="fa fa-file-text" aria-hidden="true"></i>';
			$DocStatus 	= InvoiceStatus($value->doc_status);
			$href 		= 'href="?r=accounting%2Fposted%2Fprint-inv&footer=1&id='.base64_encode($value->doc_id).'&api='.base64_encode($value->id).'" target="_blank"';
			$value->doc_no = '<i class="fa fa-file-text-o" aria-hidden="true"></i> '.$value->doc_no;
			 

		}else if($value->doc_type == 'Credit-Note'){

			$liClass 	= 'timeline-inverted';
			$classIcon 	= 'danger';
			$icon 		= '<i class="fab fa-chrome fa-spin text-aqua"></i>';
			$DocStatus 	= InvoiceStatus($value->doc_status);
			$href		= 'href="index.php?r=accounting%2Fposted%2Fview-credit&id='.base64_encode($value->doc_id).'" target="_blank"';

		}else if($value->doc_type == 'Payment'){

			$liClass 		= NULL;
			$classIcon 		= 'danger';
			$icon 			= '<i class="glyphicon glyphicon-credit-card"></i>';
			$DocStatus 		= '';
			$href			= 'href="#"';
			$value->doc_no 	= NULL;

			switch ($value->doc_status) {
				case 'Pending':
					$liClass 		= 'timeline-inverted';
					$classIcon 		= 'danger';
					$DocStatus 		= Yii::t('common','Payment').' : '.Yii::t('common','Pending');
					$icon 			= '<i class="fa fa-credit-card" aria-hidden="true"></i>';
					break;

				case 'Delete':
					$liClass 		= NULL;
					$classIcon 		= NULL;
					$DocStatus 		= Yii::t('common','Payment').' : '.Yii::t('common','Delete');
					$icon 			= '<i class="fa fa-trash-o text-danger" aria-hidden="true"></i>';
					break;


				case 'Reject':

					$liClass 		= 'timeline-inverted';
					$classIcon 		= 'warning';
					$DocStatus 		= Yii::t('common','Payment').' : '.Yii::t('common','Reject');
					$icon 			= '<i class="fa fa-undo" aria-hidden="true"></i>';
					break;

				 


				case 'Approved':

					$liClass 		= NULL;
					$classIcon 		= 'success';
					$DocStatus 		= Yii::t('common','Payment').' : '.Yii::t('common','Approved');
					$icon 			= '<i class="fa fa-check" aria-hidden="true"></i>';
					break;

				}

				
			 

		} 




		$li.= '<li class="'.$liClass.'">
				  <div class="timeline-badge '.$classIcon.'">'.$icon.'</div>
				  <div class="timeline-panel bg-'.$classIcon.'">
				    <div class="timeline-heading">
						<h4 class="timeline-title ew-expand-wrapper"><a href="#" >'.$DocStatus.'</a></h4>
						<p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> '.$value->event_date.'</small></p>
						
				    </div>
				    <div class="timeline-body">
				      	<p><a '.$href.'>'.$value->doc_no.'</a></p>
				      	<div class="ew-wrapper" style="colur:#ccc">
							<p>'.RemarkTranslate($value).' </p>   
							          
						</div>
				    </div>
				  </div>
				</li>';

	}

	$li.= '</ul>';

	echo $li;






















	function RemarkTranslate($model)
	{

		$remark = explode(',', $model->remark);

		$text = '<div class="well">';
		foreach ($remark as $value) {
			 $text.= "<p>{$value}</p>";
		}
		$text.= '<p>'.Yii::t('common','Value').' : '.number_format(abs($model->amount)).' </p>';
		$text.= '<p>'.Yii::t('common','User').' : ('.$model->create_by.') '.$model->users->username.' : '.$model->profile->name.' </p>';
		$text.= '</div>';
		return $text;
	}


	function convDocStatus($status)
	{
		switch ($status) {

			case 'Create':
				return Yii::t('common','สร้างเอกสาร (ใบงาน)');
				break;


			case 'Open':
				return Yii::t('common','ปรับปรุง/แก้ไขเอกสาร (ใบงาน)');
				break;
			
			case 'Release':
				return Yii::t('common','ส่งใบงาน');
				break;

			case 'Confirm':
				return Yii::t('common','กำลังดำเนินการ');
				break;

			case 'Checking':
				return Yii::t('common','กำลังดำเนินการ');
				break;

			case 'Shiped':
				return Yii::t('common','จัดส่งแล้ว');
				break;

			case 'Reject':
				return Yii::t('common','เอกสารถูกตีกลับ');
				break;

			case 'Invoiced':
				return Yii::t('common','ออกบิลแล้ว');
				break;

			case 'Pre-Cancel':
				return  Yii::t('common','ขอแจ้งยกเลิก');
				break;

			case 'Confirm-Cancel':
				return  Yii::t('common','อนุมัติ ให้ยกเลิก');
				break;	

			case 'Reject-Pre-Cancel':
				return  Yii::t('common','ไม่อนุญาตให้ยกเลิก');
				break;

			case 'Cancel':
				return Yii::t('common','Cancel');
				break;

			default:
				return $status;
				break;
		}
                    
	}


	function ShipmentStatus($status)
	{
		switch ($status) {
			case 'Shiped':
				return Yii::t('common','บรรจุหีบห่อ');
				break;
			
			case 'Undo':
				return Yii::t('common','ยกเลิก การบรรจุหีบห่อ');
				break;

			default:
				return $status;
				break;
		}
	}


	function InvoiceStatus($status)
	{
		switch ($status) {
			case 'Open':
				return Yii::t('common','เตรียมจัดส่ง');
				break;
			
			case 'Posted':
				return Yii::t('common','บันทึกบิล เข้าสู่ระบบ');
				break;

			default:
				return $status;
				break;
		}
	}


?>