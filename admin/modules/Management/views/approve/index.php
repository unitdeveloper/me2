<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use yii\grid\GridView;

use common\models\Cheque;
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
                            $app.= '<div class="bg-content text-center" style="background-image:url('.$model->banklist->picture.');"><span>'.Yii::t('common',$model->type).'</span></div>';
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
                            $Check = Cheque::find()->where(['source_id' => $model->source_id])->andwhere(['not', ['source_id' => null]]);
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
                // 'rowOptions' => function($model){
                //          return '';
                // },
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

<?=$this->render('dialog')?>

<?php $this->registerJsFile('js/dialog.js');?>
<?php $this->registerJsFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);?>


<?php
$Yii = 'Yii';
$js =<<<JS
    
    $(document).ready(function(){

	});
	$('body').on('click','.show-dialog-click',function(){
        var data = {
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
			ApproveClick($(this),data);
            $('.approve-btn')
            .attr('class','btn btn-success approve-btn pull-right')
            .attr('data',$(this).closest('tr').data('key'))
            .html('<i class="fa fa-check-square-o" aria-hidden="true"></i> {$Yii::t("common","Approve")}');
            $('.approve-btn').fadeIn('slow');
			return false;
		}
	})



	function ApproveClick(toAttr,data)
	{
		var key = toAttr.closest('tr').attr('data-key');
		$('.loading').show();
		$.ajax({ 
		  url:"index.php?r=approval/app/create",
		  type: "GET", 
          data: data,
          async:true,
		  success:function(getData){
		  	$('.ew-dialog-body').html(getData);
            setTimeout(function(){
                $('.panel-body').slideDown();
                $('.loading').fadeOut('slow');
            },600);
		  }
		});
	}


    // Approve. [Create]
    $('body').on('click','button.approve-btn',function(){
        if (confirm('{$Yii::t("common","Do you want to approve this?")}')) { 
            $('form#form-approve-cheque').submit();
            $('.approve-btn').fadeOut('slow');
            $('.close-dialog').addClass('close-dialog-remove');
            removeRow($(this));
            setTimeout(function(){
                $('.panel-dialog').hide("slide", { direction: "right" }, 500);
                $('body').attr('style','overflow: auto;');
            },500);
            
        }
        return false;
    });

    // Update Button
    $('body').on('click','a.update-btn',function(){
        $('.approve-btn').hide();
        var id = $(this).attr('data');
        $.ajax({ 
          url:'index.php?r=approval/app/update&id='+id,
          type: 'GET', 
          async:true,
          success:function(getData){
            $('div.ew-dialog-body').html(getData).fadeIn('slow');
          }
        });
        $('.approve-btn')
        .attr('class','btn btn-warning edit-btn pull-right')
        .html('<i class="fa fa-save" aria-hidden="true"></i> {$Yii::t("common","Save")}');
        $('.edit-btn').fadeIn('slow');
    });

    // Edit
    $('body').on('click','button.edit-btn',function(){
        $('form#form-approve-cheque').submit();
        $('.edit-btn').fadeOut('slow');
    });


    // Delete
    $('body').on('click','button.delete-btn',function(){

    });


    //
    $('body').on('click','.close-dialog-remove',function(){
        removeRow($(this));
        $('.close-dialog').removeClass('close-dialog-remove');
    });

    function removeRow(toAttr)
    {
        var id = toAttr.attr('data');
        $('table#approve-table tr[data-key="'+id+'"]').attr('style','background-color:#000;'); 
        $('table#approve-table tr[data-key="'+id+'"]').hide('slow');
        setTimeout(function(){
            $('table#approve-table tr[data-key="'+id+'"]').remove();
        },500);
    }
JS;

$this->registerJs($js,\yii\web\View::POS_END);
?>