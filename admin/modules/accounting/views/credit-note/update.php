<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\rcinvoiceheader */
app\assets\SweetalertAsset::register($this);

$this->title = $model->no_;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Invoice Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->document_no_, 'url' => ['view', 'id' => $model->document_no_]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>

<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>


<div class="sale-invoice-header-update" ng-init="Title='<?=$model->no_?>'">

 

    <?= $this->render('_form', [
        'model' => $model,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>
 
<!-- Right Click -->
<style type="text/css">
	#contextMenu {
	  position: absolute;
	  display:none;
	  z-index: 500;

	 
	}
	.dropdown-menu{
		box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
		background-color: rgb(250,250,250);
	}
	.data-body{
		overflow-x: hidden;
	}

	/*body.modal-open {
	    overflow: hidden;
	}*/
</style> 
<div id="contextMenu" class="dropdown clearfix" style="">
	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px; ">

		<li>
			<a tabindex="-1" href="#" class="ew-inv-re-no" id="ew-rename-btn"><i class="fa fa-pencil text-warning" aria-hidden="true"></i> <?=Yii::t('common','Rename')?></a>
		</li>

		<li class="divider"></li>

		<li>
			<a tabindex="-1" href="index.php?r=accounting/credit-note/update&id=<?=base64_encode($model->id)?>&no=<?=$model->no_?>"><i class="fa fa-refresh text-success" aria-hidden="true"></i> <?=Yii::t('common','Refresh')?></a>
		</li>



		<li><?= Html::a('<i class="fa fa-trash-o text-danger" aria-hidden="true"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => base64_encode($model->id),'no' => $model->no_], [
		    'class' => '',
		    'data' => [
		        'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
		        'method' => 'post',
		    ],
		]) ?></li>

		<li class="divider"></li>

		<li>
			<a tabindex="-1" href="index.php?r=accounting/credit-note/view&id=<?=base64_encode($model->id)?>&no=<?=$model->no_?>"><i class="fa fa-info text-info" aria-hidden="true"></i> <?=Yii::t('common','Properties')?></a>
		</li>

		<li class="divider"></li>

		<li>
			<?php 
				$GenSeries      = new admin\models\Generater(); // Generater::NextRuning('rc_invoice_header','no_','all',false);      
                $isVat       	= $GenSeries->InfoSeries('rc_invoice_header','no_','all',false);
				if($isVat):
			?>
			<a tabindex="-1" href="#id=<?=$isVat->id?>&code=IV" class="edit-Runing-Series"><i class="fa fa-info fa-sort-amount-desc text-danger" aria-hidden="true"></i> <?=Yii::t('common','No Series')?></a>
				<?php endif;?>
		</li>

	</ul>
</div>

<script type="text/javascript">
  
    $(function() {
      
      var $contextMenu = $("#contextMenu");
      
      $("body").on("contextmenu", ".ew-inv-no", function(e) {
        $contextMenu.css({
          display: "block",
          left: e.pageX-200,
          top: e.pageY
        });
        return false;
      });
      
      $contextMenu.on("click", "a", function() {
         $contextMenu.hide();
      });
      
    });


    $(document).click(function(e) {

	  // check that your clicked
	  // element has no id=info

	  if(e.target.id != 'contextMenu') {
	    $("#contextMenu").hide();

	     
	  }

	  //if(e.target.id != 'contextmenu') {

	    // var data = '<h4>' + $('.ew-inv-change').attr('ew-no_') + '<h4>';
     //    $('.ew-inv-change').html(data); 
     //    $('.ew-inv-change').attr('class','ew-inv-no');
	  //}
	});
</script>

<!-- /.Right Click -->

<script type="text/javascript">

 

    $('.edit-Runing-Series').click(function(){

        $('#RunNoSeries').modal('show');
        var link = $(this).attr('href').substring(1);         

        $.ajax({ 

            url:"index.php?r=series/ajax_noseries&"+ link,
            type: 'GET', 
            success:function(getData){
                 
                $('.data-body').html(getData); 
                $('.modal-title').html('<i class="fa fa-info fa-sort-amount-desc " aria-hidden="true"></i> <?=Yii::t('common','No Series')?>'); 
                $('.data-body').attr('style','height: 76vh;overflow-y: auto;'); 
               
            }
        })   
        

    });


     
</script>