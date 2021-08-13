<?php
use yii\helpers\Html;
 
 
 
      
 
 
?>

<style>
	.iv-no{
		position:relative;
		cursor: context-menu;
	}
	 
</style>

<?php $this->registerCssFile('css/billing-note.css');?>

<?= $this->render('__menu_filter'); ?>
 


<div class="billing-render"></div>

<?php $this->registerJsFile('js/slide-menu-right.js');?>

<?PHP

$js =<<<JS

 	

	$(document).ready(function(){

		 

		setTimeout(function(){
			// $('.menu-right-slide').show("slide", { direction: "right" }, 500);
			// $('body').attr('style','overflow: auto;');
			// $('.menu-right-slide').animate({"height": '100%'});

			// $('.menu-left-click').fadeIn('slow');
			// $(this).html('<i class="fa fa-refresh fa-spin text-info "></i>');

		},500);

		
		
		
		
         
		$('.custom-menu-print').hide();

		 

		if(getUrlVars('fdate')!=null){
			getRenderBilling();

			setTimeout(function(){
				$('.menu-right-slide').show("slide", { direction: "right" }, 500);
				$('body').attr('style','overflow: auto;');
				$('.menu-right-slide').animate({"height": '80%'});
			},500);

			//$('p#bill-box').attr('style','background-color : #f0ad4e; color:#fff;');
		}else {
			getCustomerStyle();
		}
		

	})

	$('body').on('click','button.save-billing',function(){
		
		// var $no = $(this).attr('data-file');
  //       window.location.replace("index.php?r=accounting/billing/update&no="+$no);

  		if($('.customer-billing').attr('data')!=''){

  			var data = {
				fdate:getUrlVars('fdate'),
				tdate:getUrlVars('tdate'),
				searchVat:getUrlVars('searchVat'),
				customer:getUrlVars('customer'),
				action:'update',			

			};

 
			$.ajax({
				url:'index.php?r=accounting/billing/render-table',
				type:'GET',
				data:data,
				async:true,
				success:function(getData){
					$('.Navi-Title').html(getData);
				}
			})
  		}else {
  			swal(
              'ดูเหมือนว่า "ยังไม่ได้ค้นหาลูกค้าเลย"',
              'กรุณาเลือกลูกค้า',
              'warning'
            );
            return false;
  		}
  		
		 
	});

	function getCustomerStyle(){

		$.ajax({
			url:'index.php?r=accounting/billing/customer-style',
			type:'GET',			 
			async:true,
			success:function(getData){
				$('.billing-render').html(getData);
			}
		})
		
	}

	function getRenderBilling(){
		var getUrl = {
				fdate:getUrlVars('fdate'),
				tdate:getUrlVars('tdate'),
				searchVat:getUrlVars('searchVat'),
				customer:getUrlVars('customer'),

		};

		$.ajax({
			url:'index.php?r=accounting/billing/render-table',
			type:'GET',
			data:getUrl,
			async:true,
			success:function(getData){
				$('.billing-render').html(getData);
			}
		})
	}

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

JS;
$this->registerJS($js);
?>

 