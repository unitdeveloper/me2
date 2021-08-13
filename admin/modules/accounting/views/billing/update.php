<?php
use yii\helpers\Html;
 
 
 
 
$GenSeries        = new admin\models\Generater();
$NoSeries         = $GenSeries->NextRuning('billing_note','vat_type','0',false);

            
 
 
?>
<style>
	.iv-no{
		position:relative;
		cursor: context-menu;
	}
	 
</style>
 

<?php $this->registerJsFile('https://code.jquery.com/jquery-3.3.1.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);?>


<?php $this->registerCssFile('css/billing-note.css?v=4.11');?>

<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>

<?= $this->render('__menu_filter',['dataProvider' => $dataProvider]); ?>


<?= $this->render('__billing_table',['dataProvider' => $dataProvider,'customer' => (@$_GET['customer'])? @$_GET['customer'] : $customer]); ?>


<div class="billing-render"></div>
 
 

<?php $this->registerJsFile('js/slide-menu-right.js?v=3.03.27');?>
 
 
<?php
$Yii = 'Yii';
$js =<<<JS
 

    $(document).ready(function(){
		
        setTimeout(function(){
            $('.menu-right-slide').show("slide", { direction: "right" }, 500);
            $('body').attr('style','overflow: auto;');
            $('.menu-right-slide').animate({"height": '80%'});
        },500);


        $('p.text-name').css('background-color','#00acd6').css('color','#fff');

		// Hidden Button save 
		$('.ew-save-common').css('visibility','hidden');
    })

    $('body').on('click','button.save-billing',function(){
		
		// var no = $(this).attr('data-file');
  //       window.location.replace("index.php?r=accounting/billing/update&no="+no);
  		var data = {
				action:'update',
				no:$(this).attr('data-file'),
		};

		 
		$.ajax({
			url:'index.php?r=accounting/billing/update',
			type:'GET',
			data:data,
			async:true,
			success:function(getData){
				$('.Navi-Title').html(getData);
			}
		})
		 
	})

	
	$('body').on('click','.edit-comment',function(){
		$(this).html('<input type="text" class="form-control print-comment" id="edit-comment">');
		$('input.print-comment').val($(this).data('text'));
		$(this).removeClass('edit-comment');
		$('#print-billing').attr('disabled',true);

	})

	$("body").on("click",".iv-no",function(e) {
		//e.preventDefault();
		$(this).select();
	});

	$("body").on("contextmenu", ".iv-no", function(e) {
		e.preventDefault();
		var key       = $(this).closest('tr').attr('data-key');
		 
		//console.log(e.pageX);
		$('.contextMenu').remove();
		var template = '<div id="contextMenu'+key+'" data-key="'+ key +'"  class="contextMenu" style="position: absolute;z-index: 500; top:-5px;" >'+
                    '<div  class="dropdown clearfix ">'+
                      '<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px; box-shadow: 5px 5px 5px rgba(0,0,0,.2);">'+
                        '<li data-no="{{itemno}}">'+
                          '<a href="javascript:void(0);" class="more"  ><i class="fas fa-ellipsis-h"></i> Infomations</a>'+
                        '</li>'+                  
                        '<li class="divider"></li>'+                  
                        '<li>'+
                          '<a href="javascript:void(0);" class="delete-inv" ><i class="fa fa-trash-o"></i> Delete</a>'+
                        '</li>'+                         
                      '</ul>'+
                    '</div>'+
				  '</div>';
		$(this).append(template);		  
		
	});

	$(document).click(function(e) {
		// check that your clicked
		// element has no id=info
		if(e.target.class != 'contextMenu') {
			$(".contextMenu").hide();
		}
	});

	$('body').on('click','.delete-inv',function(){
		var key = $(this).closest('div.contextMenu').attr('data-key');
		var row = $(this).closest('tr[data-key="'+key+'"]');
		if(confirm('{$Yii::t("common","Do you want to delete this document")}')){
			
			$.ajax({
				url:'index.php?r=accounting/billing/delete-line&id='+key,
				type:'POST',				 
				dataType:'JSON',
				success:function(response){
					if(response.status==200){
						row.remove();
						countNumberIndex();		
						$('body').find('.text-baht').html(response.textbaht);				 
					}
					
				}
			})
		
		}
	})

	function countNumberIndex(){
		var obj 	= $('tr.row-invoice');	
		var total 	= 0;
		if(obj.length<=0){
			window.location.href = 'index.php?r=accounting%2Fbilling%2Findex';
		}	
		$.each(obj,function(key,model){
			$('tr[data-key="'+$(model).attr('data-key')+'"]').find('td.key-index').html(key+1);
			total += parseFloat($('tr[data-key="'+$(model).attr('data-key')+'"]').find('td.sum-line').attr('data'));
		});
		$('.number-summary').html(number_format(total.toFixed(2)));
	}


	$('body').on('click','.btn-add-new-line',function(){
		let val = 0;
		let text= 'คืนของ';
		let no 	= "{$id}";
		createLine({val:val, text:text, no:no}, res => {
			if(res.status===200){	
				let balance = res.raw.val;
				let tr = `<tr style="" data-key="`+ res.raw.id +`" class="row-invoice">	
							<td class="key-index"></td>								 
							<td class="text-right" colspan="5" style="border-top:1px solid #000;"><input type="text" class="form-control text-right no-border discount-text" value="คืนของ"  style="padding: 0px; font-family: saraban; font-size: 14px;"/></td>	
							<td class="text-right sum-line" width="100px" data="`+ res.raw.val +`" style="position: relative;">
								<input type="text" class="form-control text-right no-border discount-line" style="padding: 0px; font-size: 14px; font-family: saraban;" value="`+  number_format(balance.toFixed(2)) +`"/>
								<div style="position:absolute;right: -15px; top: 18px;"><i class="fa fa-minus pointer text-red minus-line"></i></div>
							</td>
						</tr>`;
				$('.page-body-table tbody tr:last').after(tr);				
				countNumberIndex();
			}
		})
		
	});


	const createLine = (obj, callback) => {
		fetch("?r=accounting/billing/create-line&file=update", {
			method: "POST",
			body: JSON.stringify(obj),
			headers: {
			"Content-Type": "application/json",
			"X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
			}
		})
		.then(res => res.json())
		.then(response => {
			$('body').find('.text-baht').html(response.textbaht);
			callback(response);			
		}).catch(error => {
			console.log(error);
		});
	}

	const updateLine = (obj, callback) => {
		fetch("?r=accounting/billing/update-line", {
			method: "POST",
			body: JSON.stringify(obj),
			headers: {
			"Content-Type": "application/json",
			"X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
			}
		})
		.then(res => res.json())
		.then(response => {
			callback(response);
		}).catch(error => {
			console.log(error);
		});
	}
 


	$('body').on('keyup', '.discount-line', function(){

		let id 			= $(this).closest('tr').attr('data-key');
		var noCommas 	= $(this).val().replace(/,/g, ''),
    		asANumber 	= +noCommas;
						$(this).closest('td').attr('data', asANumber);
		
						countNumberIndex();
	})

	$('body').on('change', '.discount-line', function(){

		let id 			= $(this).closest('tr').attr('data-key');
		var noCommas 	= $(this).val().replace(/,/g, ''),
			asANumber 	= +noCommas;
						 

		updateLine({id:id, val:asANumber, field:'balance'}, res => {
			countNumberIndex();
		})

	})

	$('body').on('change', '.discount-text', function(){

		let id		= $(this).closest('tr').attr('data-key');
		var val 	= $(this).val()					

		updateLine({id:id, val:val, field:'description'}, res => {
			countNumberIndex();
		})

	})

	$('body').on('click', '.minus-line', function(){
		var key = $(this).closest('tr').attr('data-key');
		var row = $(this).closest('tr[data-key="'+key+'"]');
	 
		var row = $(this).closest('tr[data-key="'+key+'"]');
		if(confirm('{$Yii::t("common","Do you want to delete this line")}')){
			
			$.ajax({
				url:'index.php?r=accounting/billing/delete-line&id='+key,
				type:'POST',				 
				dataType:'JSON',
				success:function(response){
					if(response.status==200){
						row.remove();
						countNumberIndex();		
						$('body').find('.text-baht').html(response.textbaht);				 
					}
					
				}
			})
		
		}
		 
	})


	const changeNoSeries = (obj, callback) => {
		fetch("?r=accounting/billing/change-series", {
			method: "POST",
			body: JSON.stringify(obj),
			headers: {
			"Content-Type": "application/json",
			"X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
			}
		})
		.then(res => res.json())
		.then(response => {
			callback(response);
		}).catch(error => {
			console.log(error);
		});
	}

	$('body').on('change', 'input#no', function(){
		 let no = "{$id}";
		 let newNo = $(this).val();
		 let el = $(this);
		 if(confirm("Do you want to change "+ no +" to "+ newNo + " ?")){
			 
			changeNoSeries({no:no, val:newNo}, res => {
				el.val(res.no);
				window.location = '?r=accounting/billing/update&id=' + btoa(res.no);
			})
		 }
		  
	 });

	 
	// $("body").on("keypress", "input#no", function(e) {
	// 	var keyCode = e.keyCode || e.which;  
	// 	let no 		= "{$id}";
	// 	let newNo 	= $(this).val();
	// 	if (keyCode === 13) {
			
	// 		if(confirm("Do you want to change "+ no +" to "+ newNo + " ?")){
				
	// 			changeNoSeries({no:no, val:newNo}, res => {
	// 				console.log(res)
	// 			})
	// 		}
	// 	}
	// });


	 
   
JS;
$this->registerJS($js);
?>
