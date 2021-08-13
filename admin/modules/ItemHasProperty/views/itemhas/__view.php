<?php
use common\models\Property;



$data = '<table class="table">
		<thead>
		<tr><th>#</th><th>Property</th><th>Value</th><th></th></tr>
		</thead>';

$i = 0;
foreach ($dataProvider as $key => $value) {
	# code..
	$i ++;
	$data.= ' <tr id="'.$value->id.'">';

	$Property = Property::find()->where(['id'=> $value->property_id ])->one();
	$data.= '<td>'.$i.'.</td>';
	$data.= '<td>'.$Property->description.'</td>';
	$data.= '<td>'.$value->values.'</td>';
	$data.= '<td>
			<a href="#" type="button" class="btn btn-danger text-right"   id="del-prop'.$i.'">
			<i class="fa fa-trash-o" aria-hidden="true"></i> Delete
			</a>

			

			</td>';
	$data.= '</tr> ';

}
$data.= '</table>';
echo $data;

// var_dump($_GET['pid']).'<br>';
// var_dump($_GET['pval']);
?>
<div class="delete-prop"></div>


<!-- <script type="text/javascript">
				    $("#del-prop'.$i.'").click(function(){
				    	if (confirm("Are you sure?")) {
					        

					    	 $.ajax({ 

						            url:"index.php?r=ItemHasProperty/itemhas/ptdelete&id='.$value->id.'",
						            type: "POST", 
						            data:"",
						            async:false,
						            success:function(getData){
						                 
						                 
						                $(".delete-prop").html(getData);
						                
						               
						            }
						    	});
						    $( "#'.$value->id.'" ).remove();
					    }
					    return false;
			    	
			    });
			</script> -->