<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use yii\grid\GridView;
?>

<style type="text/css">
	h1, h2, h3, h4, h5, h6{
		font-family: 'Kanit', sans-serif !important;
	}
    .cust-info{
        position: relative;
        
    }

    .bg-content{
        position: absolute;
        width:80px;
        min-height: 100px;
        background-size: 80px auto;
        background-repeat:no-repeat;
        background-position:right top;
        opacity: 0.1;
        right: 0px;
    }
    .bg-content span{
        color: red;
        background-color: #fff;
        padding: 5px 5px 0px 5px;

    }

    .show-dialog-click{
        margin-top: 10px;
    }

    body{
        /*overflow: hidden;*/
    }
</style>
<div class="row">
    <div class=" col-xs-12">
        
        <?php 
                $gridColumns = [
                    //['class' => 'yii\grid\SerialColumn'],

                    //'id',
                    //'customer.name',
                    //'banklist.name',
                    // [
                    //    // 'attribute' => 'bank',
                    //     'label' => Yii::t('common','Bank'),
                    //     'format' => 'raw',
                    //     'headerOptions' => ['class' => 'hidden-xs'],
                    //     'filterOptions' =>  ['class' => 'hidden-xs'],
                    //     'contentOptions' => ['class' => 'img hidden-xs','style' => 'width:80px;'],
                    //     'value' => function($model){
                    //         return Html::img('uploads/'.$model->banklist->imageFile,['class'=>'img-responsive']);
                    //     }
                    // ], 

                    [
                        'attribute' => 'cust_name_',
                        'label' => Yii::t('common','Customer'),
                        'contentOptions' => [
                                            'style'=>'max-width:200px; height:150px; overflow: auto; word-wrap: break-word; ',
                                          ],
                        'format' => 'raw',
                        'value' => function($model){
                            $app = '<div class="cust-info">';
                            $app.= '<div class="bg-content text-center" style="background-image:url(uploads/'.$model->banklist->imageFile.');"><span>
                            '.Yii::t('common',$model->type).'</span></div>';
                            $app.= "<div><h4>{$model->customer->code}</h4></div>";
                            $app.= "<div><h4>{$model->customer->name}</h4></div>";
                            $app.= "<div>{$model->post_date_cheque} {$model->transfer_time}</div>";
                            $app.= "<div class='hidden-sm hidden-md hidden-lg'>{$model->banklist->name}</div>";
                            //$app.= "<div class='hidden-sm hidden-md hidden-lg'>{$model->post_date_cheque}</div>";
                            $app.= "<div>{$model->remark}</div>";
                            $app.= "</div>";

                            if($model->type=='Cheque')
                            $app.= "<div class='hidden-sm hiddem-md hidden-lg'>{$model->bank_id}</div>";

                            return $app;
                        }
                    ], 
                    // [
                    //    // 'attribute' => 'bank',
                    //     'label' => Yii::t('common','Bank'),
                    //     'headerOptions' => ['class' => 'hidden-xs'],
                    //     'filterOptions' =>  ['class' => 'hidden-xs'],
                    //     'contentOptions' => ['class' => 'hidden-xs'],
                    //     'format' => 'raw',
                    //     'value' => function($model){
                    //         return $model->banklist->name;
                    //     }
                    // ], 
                    //'bank_account',
                    //'bank_branch',
                    //'bank_id',
                    [
                        //'attribute' => 'bank_id',
                        'label' => Yii::t('common','Bank ID'),
                        'headerOptions' => ['class' => 'hidden-xs'],
                        'filterOptions' =>  ['class' => 'hidden-xs'],
                        'contentOptions' => ['class' => 'hidden-xs'],
                        'format' => 'raw',
                        'value' => function($model){
                            if($model->type=='Cheque')
                                return $model->bank_id;
                            else
                                return '';
                        }
                    ], 
                    // 'create_date',
                    // 'posting_date',
                    // 'tranfer_to',
                     //'balance',
                    [
                        'attribute' => 'balance',
                        'label' => Yii::t('common','Balance'),
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right','style' => 'width:80px;'],
                        'format' => 'raw',
                        'value' => function($model){
                            //return number_format($model->balance,2);
                            $Check = \common\models\Cheque::find()->where(['source_id' => $model->source_id])->andwhere(['not', ['source_id' => null]]);
                            $sumBl = $Check->sum('balance');
                            $app = '<div class="show-dialog-click btn btn-info col-xs-12 btn-flat" style="font-size:16px;">'.number_format($sumBl,2).'</div>';
                           // $app.= '<div >'.Html::img('uploads/'.$model->banklist->imageFile,['class'=>'img-responsive']).'</div>';

                            return $app;
                        }
                    ], 
                    // 'post_date_cheque',

                    // [
                    // 	'label' => Yii::t('common','Approve'),
                    //     'headerOptions' => ['class' => 'hidden-xs'],
                    //     'contentOptions' => ['class' => 'hidden-xs'],
                    // 	'format' => 'raw',
                    // 	'value' => function($model){
                    // 		return "<div  >".Yii::t('common','Approve')."</div>";
                    // 	}
                    // ],


                    
                ]; ?>



         <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                //'showFooter' => true,
                'tableOptions' => ['class' => 'table table-striped table-bordered','id' => 'approve-table'],
                'columns' => $gridColumns,
                'pager' => [
                    'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
                    'prevPageLabel' => '«',   // Set the label for the "previous" page button
                    'nextPageLabel' => '»',   // Set the label for the "next" page button
                    'firstPageLabel'=> '<i class="fa fa-fast-backward" aria-hidden="true"></i>',   // Set the label for the "first" page button
                    'lastPageLabel'=>'<i class="fa fa-fast-forward" aria-hidden="true"></i>',    // Set the label for the "last" page button
                    'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
                    'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
                    'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
                    'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
                    'maxButtonCount'=>15,    // Set maximum number of page buttons that can be displayed
                    ],
                 
            ]); ?>

    </div>

</div>

<?=$this->render('../approve/dialog')?>

<?php $this->registerJsFile('js/dialog.js');?>
<script type="text/javascript">
	$(document).ready(function(){

		

	});

	$('body').on('click','.show-dialog-click',function(){
		
        var $data = {
            key:$(this).closest('tr').attr('data-key'),
        };

        // Sent data key to Close button.
        $('.close-dialog').attr('data',$(this).closest('tr').attr('data-key'));


        // If dialog now showing.
        // Provide hide dialog.
        // Else running content again.
		if($('.panel-dialog').is(':visible')){

			 
			$('.panel-dialog').toggle("slide", { direction: "right" }, 500);
             

			return false;
		}else {

            $('body').attr('style','overflow: hidden; position: relative; height: 100%;');

			$('.panel-dialog').toggle("slide", { direction: "right" }, 500);
            
			ApproveClick($(this),$data);

            

            $('.approve-btn').hide();
			return false;
		}


	})



	function ApproveClick($this,$data)
	{
		var $key = $this.closest('tr').attr('data-key');
		
		$('.panel-body').hide();
        $('.loading').show();

		$.ajax({ 

		  url:"index.php?r=approval/app/create",
		  type: "GET", 
          data: $data,
		  success:function(getData){

		  	$('.ew-dialog-body').html(getData);

            setTimeout(function(){
                
                $('.panel-body').slideDown();


            },500);
            $('.loading').hide();
		  	
		  }
		});
	}


     
 

    
</script>