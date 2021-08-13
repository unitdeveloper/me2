<?php
use yii\grid\GridView;
use yii\helpers\Html;
?>
<style media="screen">
	#sortable tr{
		cursor: move;
	}
</style>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<div class="ew-actionCreate row">

	<?php
	$div = '<table class="table table-striped table-bordered" >';
	$div.= '<tbody id="sortable">';
	$i = 0;
	foreach ($dataProvider->models as $model) {
		$i++;
		$div.= '<tr data-key="'.$model->id.'" class="row" style="margin-bottom:2px;" data-priority="'.$model->priority.'">';
		$div.= '<td class="col-sm-5 col-xs-4">'.$model->propertytb->name.'</td> <td class="col-sm-5 col-xs-5">'.$model->propertytb->description.'</td>';
		$div.= '<td class="col-sm-2 col-xs-3">'.Html::a('<span class="glyphicon glyphicon-trash"></span>',['/itemgroup/property/delete/',
			'id'=>$model->id,
			'itemgroup' => $model->itemgroup],
			[
				'class'=>'btn btn-default col-xs-12',
				'data'=>[
					'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
					'method'=>'POST'
					]
			]).'</td>';
		$div.= '</tr>';
	}
	$div.='	</tbody>';
	$div.='	</table>';
	echo $div;

	?>

	<?php
	// echo GridView::widget([
  //       'dataProvider' => $dataProvider,
	// 			'summary' => false,
  //       'columns' => [
  //           ['class' => 'yii\grid\SerialColumn'],
	//
	//
  //           'propertytb.name',
  //           'propertytb.description',
  //           'status',
  //           [
  //             'class' => 'yii\grid\ActionColumn',
  //             'options'=>['style'=>'width:150px;'],
  //             'buttonOptions'=>['class'=>'btn btn-info'],
  //             'template'=>'<div class="btn-group btn-group-sm text-center" role="group">  {delete} </div>'
  //           ],
	//
  //       ],
  //   ]);

    ?>
</div>
<script type="text/javascript">
  $('#sortable').sortable({
		update: function(e,ui){
           var lis = $("#sortable tr");
           var ids = lis.map(function(i,el){
           		return {id:el.dataset.key, priority:el.dataset.priority}
						}).get();
						
           //console.log(JSON.stringify(ids));
					 $.ajax({
						 url:'index.php?r=itemgroup%2Fitemgroup%2Fview&id='+$('div.itemgroup-view').attr('data-key'),
						 type:'POST',
						 data:{ids:ids},
						 dataType:'JSON',
						 success:function(response){
							 if(response.status==200){
									$.notify({
											// options
											icon: 'far fa-save',
											message: '<?=Yii::t('common','Saved')?>'
									},{
											// settings
											placement: {
													from: "top",
													align: "center"
											},
											type: 'info',
											delay: 3000,
											z_index:3000,
									});
							 }else{
									$.notify({
											// options
											icon: 'fas fa-exclamation-triangle',
											message: response.message
									},{
											// settings
											placement: {
													from: "top",
													align: "center"
											},
											type: 'warning',
											delay: 3000,
											z_index:3000,
									});
							 }
							
						 }
					 });


         }
	});

</script>
