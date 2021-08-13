let delayID=null; /* ต้องอยู่นอก function (จะทำงานครั้งเดียว)*/


function loadFindBox($div){
  // clear box
  $('ew.find-items-box').remove();
	// ----- find-items -----
    var $findDiv =  '<ew class="find-items-box">'+
                    '<div class="find-item" >'+
                    '   <div class="find-item-render" > </div>'+
                    '   <div class="find-load">'+
                    '       <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw text-info"></i>'+
                    '       <span class="sr-only">Loading...</span>'+
                    '   </div>'+
                    '</div>'+
                    '</ew>';

    //$('.ew-item-insert').append($findDiv);
    $($div).append($findDiv);
}




function findItemTable($this){


    $('.find-item').show('fast');
    $('.find-load').fadeIn('fast');


    $.ajax({
        url:"index.php?r=items/ajax/find-items",
        type:'POST',
        data:{word:$this.val()},
        async:true,
        success:function(getData){
            $('.find-item-render').html(getData);

            setTimeout(function(e){
                $('.find-item').slideDown('slow');
                $('.find-load').fadeOut();
            },100);



        }

    })

}

createLine = (url,model) => {
  
}

showItemSearch = (items,callback) => {
  let html = '';
  $.each(items,(key,model) =>{
    let price = model.price;
    html+= '<div>'+
    '<a href="javascript:void(0)" class="pick-item" data-key="' + model.id + '" data-price="' + model.price + '" data-item="' + model.item + '" data-no="' + model.no + '" data-desc="' + model.desc_th + '" data-qty="1" data-cost="' + model.cost + '">'+
      '<div class="panel panel-info">'+
        '<div class="panel-body">'+
          '<div class="row">'+
              '<div class="col-md-1 col-sm-2">'+
                '<img src="' + model.img + '" alt="" class="img-responsive" style="max-height: 100px; margin-bottom:20px;">'+
              '</div>'+
              '<div class="col-md-11 col-sm-10">'+
                '<div class="row">'+
                  '<div class="col-md-10 col-xs-8 ">' + model.desc_th + '</div>'+
                  '<div class="col-md-2 col-xs-4 text-right">'+
                    '<span class="find-price"><p class="price">ราคา</p>' + number_format(price.toFixed(2)) + '</span>'+
                  '</div>'+
                '</div>'+
                '<div class="row">'+
                  '<div class="col-xs-12"><span class="text-sm text-gray ">' + model.desc_en + '</span></div>'+
                  '<div class="col-xs-12"><label class="text-black ">รหัส : ' + model.item + '</label></div>'+
                '</div>'+
                '<div class="row">'+
                  '<div class="col-xs-8"><label>Stock : </label></div>'+
                  '<div class="col-xs-4 text-right"><span class="text-gray">' + model.inven + '</span></div>'+
                '</div>'+
              '</div>'+
          '</div>'+
        '</div>'+
      '</div>'+
    '</a>'+
  '</div>';
  });
  
  callback(html)
}

function findItemTableJson(props,callback){


  $('.find-item').show('fast');
  $('.find-load').fadeIn('fast');


  $.ajax({
      url:"index.php?r=items/ajax/find-items-json&limit=20&word=" + props.val(),
      type:'GET',
      async:true,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      dataType:'JSON',
      success:function(response){

        if(response.length === 0){ 
          callback(response);
          $('.find-item').hide('fast');
          $('.find-load').fadeOut('fast');
        }else {

          if(response.length === 1){
            // NEW LINE
            callback(response);
            $('.find-item').hide('fast');
            $('.find-load').fadeOut('fast');
                   
          }else{
            // SHOW ITEM LIST           
            showItemSearch(response,(res) => {
              $('.find-item-render').html(res);
            });       

            setTimeout(function(e){
                $('.find-item').slideDown('slow');
                $('.find-load').fadeOut();
            },100);

          }
        }
      }
  })

}




$(document).click(function(e) {

  // check that your clicked
  // element has no id=find-items-box

  if($(e.target).closest('ew').attr('class') != 'find-items-box') {
    $(".find-item").fadeOut('fast');

  }


});


function ajaxUpdateField($this,$form){

  var tr        = $this.closest('tr');

  var form      = $($form);
  //var formData  = form.serialize();
  var $data = {
    ajax:true,
    key:tr.attr('data-key'),
    name:tr.find($this).attr('name'),
    data:tr.find($this).val(),
  };

  var action    = form.attr('action');

  //****Delay 0.5 Sec****
  if(delayID){ clearTimeout(delayID);}
    delayID=setTimeout(function(){

      $.ajax({
        url: action+'&_pjax=%23p0',
        type: form.attr("method"),
        data: $data,
        success: function (getData) {

            $('div.throw-status').html(getData);

        },
        error: function () {
            alert("Something went wrong");
        }
      });

  delayID=null;
},100);
  //****/. Delay 0.5 Sec****

}



function ajaxUpdateFieldActive($Object){

  var tr        = $Object.data.closest('tr');

  var form      = $($Object.form);
  //var formData  = form.serialize();
  var $data = {
    ajax:true,
    key:tr.attr('data-key'),
    name:tr.find($Object.data).attr('name'),
    data:tr.find($Object.data).val(),
  };

  var action    = form.attr('action');

  //****Delay 0.5 Sec****
  if(delayID){ clearTimeout(delayID);}
    delayID=setTimeout(function(){

      $.ajax({
        url: action+'&_pjax=%23p0',
        type: form.attr("method"),
        data: $data,
        success: function (getData) {

            $('div.throw-status').html(getData);

             
            var obj = jQuery.parseJSON(getData);
            if(obj.status === 200){

            }else{
              $.notify({
                // options
                icon: 'far fa-check-square',
                message: obj.message
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

        },
        error: function () {
            alert("Something went wrong");
        }
      });

  delayID=null;
},$Object.time);
  //****/. Delay 0.5 Sec****

}


$('body').on('change','input.ajax-update',function(){
  updateItems($(this).attr('name'),$(this).attr('data-key'),$(this).val());
})

function updateItems(field,id,value){
  $.ajax({
    url:'index.php?r=items/ajax/update&id='+id+'&field='+field,
    method:'POST',
    data:{id:id,value:value},
    success:function(getData){
      console.log(getData);
    }
  })
}
