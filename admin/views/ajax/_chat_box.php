<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use admin\models\AlertException;

 

                                     
/* @var $this yii\web\View */
/* @var $model common\models\BankList */
/* @var $form yii\widgets\ActiveForm */

 


$chat = '<div class="direct-chat-messages"  >';
 

foreach ($query as $key => $chatModel) {



	$chat.= '<div class="direct-chat-msg">
				<div class="direct-chat-info clearfix">
	                <span class="direct-chat-name pull-left">'.$chatModel->users->profile->name.'</span>
	                <span class="direct-chat-timestamp pull-right">'.$chatModel->event_date.'</span>
                </div>
                <img class="direct-chat-img" src="'.$chatModel->users->profile->getPhotoViewer().'" alt="message user image">';

    $chat.= '	<div class="direct-chat-text">
    				 '.$chatModel->remark.' <span class="pull-right">'.AlertException::DocStatus($chatModel->doc_status).'</span>
    			</div>';
    

    $chat.= '</div>';



}
$chat.= '</div>';



?>
 



<div class="row" style="margin-bottom: 7px;">
	<div class="col-xs-12">
		<span class="link pointer" data-toggle="collapse" data-target="#Tracking"><i class="fa fa-commenting" aria-hidden="true"></i> <?= Yii::t('common','Tracking')?></span>
	</div>

</div>

<div id="Tracking" class="collapse in">
	<!-- Tracking Options -->
	<div class=" " >
					
		<?=$chat;?>		                

		<div class="chat-bot"  data="<?=$_GET['id']?>">

		    <?php $form = ActiveForm::begin([
		    	'id' => 'form-posted-chat',
		    	'enableClientValidation' => false,
		        'enableAjaxValidation' => false,
		    	'options' => ['enctype' => 'multipart/form-data']
		    ]); ?>

			 
				
				
		 		<div class="col-xs-12" style="padding:0px;">
		 			<div class="icon-choice">
						<i class="fa fa-smile-o text-yellow " aria-hidden="true"></i>
					</div>
		 			<?= $form->field($model, 'remark')->textInput([
						 'maxlength' => true,
						 'style' => 'padding-left: 30px; ' ,
						 'class' => 'form-control send-message',
						 'placeholder' => Yii::t('common','Message')
						 ])->label(false) ?>

		 			<div class="emojicon">
		 				<div class="emojicon-heading"><i class="fa fa-smile-o text-warning" aria-hidden="true"></i></div>
		 				<a class="btn emojicon-click" data="0"><i class="fa fa-commenting-o text-danger" aria-hidden="true"></i></a>
		 				<a class="btn emojicon-click" data="1"><i class="fa fa-credit-card text-danger" aria-hidden="true"></i></a>
		 				<a class="btn emojicon-click" data="2"><i class="fa fa-exchange text-danger" aria-hidden="true"></i></a>
		 				<a class="btn emojicon-click" data="3"><i class="fa fa-question text-danger" aria-hidden="true"></i></a>
		 				<a class="btn emojicon-click" data="4"><i class="fa fa-clock-o text-danger" aria-hidden="true"></i></a>
		 				<a class="btn emojicon-click" data="5"><i class="fa fa-info-circle text-danger" aria-hidden="true"></i></a>
		 				<a class="btn emojicon-click" data="6"><i class="fa fa-hourglass-half text-danger" aria-hidden="true"></i></a>
		 			</div>
		 		</div>


		 		<div class="col-xs-3" style="padding: 0px;">			
					<?= $form->field($model, 'doc_status')->textInput(['class' => 'hidden text-emojicon'])->label(false) ?>
						
				</div>
			 
		 
		 	<div class="col-xs-3 hidden" style="padding-left: 1px;"><?= Html::submitButton('<i class="fa fa-bookmark-o text-info" aria-hidden="true"></i> ', ['class' => 'btn btn-default-ew btn-flat']) ?></div>
			

		 

		    <?php ActiveForm::end(); ?>

		</div>
		</div>
	<!-- /.Tracking Options -->

	

</div>


<script type="text/javascript">
	// ให้อยู่ข้างล่างเสมอ  
	$(document).ready(function(){
		$(".direct-chat-messages").scrollTop($(".direct-chat-messages")[0].scrollHeight);	
		$('input.send-message').focus().val('');

		disableSubmit('#form-posted-chat');

	});
	

	$('body').on('keyup','input.send-message',function(e){
		var keyCode = e.keyCode || e.which;
		if(keyCode === 13){

			if($.trim($('input.send-message').val())==''){
			 
				return false;

			}else {

				AjaxChatSubmit($('#form-posted-chat'),'div.chat-tracking');
				$('body').find('input.send-message').val('');

			}

		}
		

	})

	// //ไม่ให้ Submit 
	// $('#form-posted-chat').on('beforeSubmit', function(e) {

	// }).on('submit', function(e){
	//   e.preventDefault();
	// });


	
</script>
 