<?php 

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Unitofmeasure;

?>

<style>

@media print{
    @page {
        margin-top:21px !important;
        size: A4 portrait; 
    }
    body{
        font-family: 'saraban', 'roboto', sans-serif; 
        font-size:10px !important;
        height: 100% !important;
    }
    
    .search-box,
    #export_table_wrapper, 
    #modal-create-item,
    #search-for-add-child,
    #quantity-for-create,
    #item-title,
    #modal-craft-item .modal-footer{
      display: none !important;
    }

    #modal-craft-item{
      -ms-overflow-style: none;
    }



    #modal-craft-item::-webkit-scrollbar { width: 0 !important }

    .body-craft{
      height: 500px !important;
    }

    #modal-craft-item .modal-content{
      height: 100% !important;
      background-color: red;
    }
    
}


  table.detail-view th {
          width: 150px;
  }
  .item-a-hover:hover{
    background:#1796ab;
    color: #fff;
  }

  .bom-link-name {
    font-size:10px; 
    padding:10px;
  }

  .bom-image-tag{
    padding:20px 20px 20px 20px;
  }

  .search-control{
    max-width: 300px; 
  }

  @media (max-width: 767px) {
    .bom-image-tag{
      padding:20px 20px 0px 20px;
    }
  }

  @media (max-width: 375){
    #search-item {
      width:100px;
    }

    .search-control{
      max-width:150px !important;
    }
  }
      

  #item-source-render .minus-item{
    min-height: 100px;
    /* min-width: 80px; */
    
    font-family: 'roboto';
    border: 1px solid #ccc;
  }

  #item-source-render .minus-item .qty{
    font-size: 25px;              
    text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;
  }

  .minus-per-unit,
  .plus-per-unit{
    padding: 0px 10px 0px 10px;
    font-size:20px;
  }
</style>



<div class="modal fade" id="modal-create-item">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Infomation')?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4 mt-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fab fa-codepen"></i></span>
                            <input type="text" name="code" id="item-code" class="form-control input-lg" placeholder="<?=Yii::t('common','Code')?>" />
                        </div>
                        <div class="mt-10">
                            <?= Yii::t('common','Image')?>
                            <div class="item-image-change">
                                <input type="file" id="item-image" name="image" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8 mt-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-text-height"></i></span>
                            <input type="text" name="name" id="item-name" class="form-control input-lg" placeholder="<?=Yii::t('common','Name')?>" />
                        </div>

                        <div class="mt-10 input-group">
                            <span class="input-group-addon"><i class="fas fa-align-left"></i></span>
                            <input type="text" name="detail" id="item-detail" class="form-control input-lg" placeholder="<?=Yii::t('common','Model/Detail')?>" />
                        </div>

                        <div class="row">
                            <div class="col-xs-8 mt-10">
                                <div class="mt-10 input-group">
                                    <span class="input-group-addon"><i class="fas fa-ruler-combined"></i></span>
                                    <input type="text" name="size" id="item-size" class="form-control input-md" placeholder="<?=Yii::t('common','Size')?> (2x2, 4x4)" />
                                </div>
                            </div>
                            <div class="col-xs-4 mt-10 ">
                                <div class="mt-10 input-group">  
                                     
                                    <?= Html::dropDownList('item-measure', null,
                                        ArrayHelper::map(Unitofmeasure::find()->all(),
                                                            'id','UnitCode'
                                                        ),
                                                        [
                                                            'class'=>'form-control input-md',
                                                            'id' => 'item-measure',
                                                            //'prompt' => Yii::t('common','Measure'),
                                                            // 'options' => [                        
                                                            //     @$_GET['item-measure'] => ['selected' => 'selected']
                                                            //     ],
                                                        ] 
                                                        
                                        ) 
                                    ?>
                                     
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                <a href="#" class="btn btn-primary-ew pull-left" id="craft-item" ><i class="fa fa-cogs"></i> <?=Yii::t('common','Setting Produce')?></a>
                <a href="#" class="btn btn-warning pull-left btn-make-produce" target="_blank" ><i class="fa fa-cogs"></i> <?=Yii::t('common','Produce')?></a>                
                <a href="#" class="btn btn-info-ew " id="btn-modal-openlink" target="_blank"><i class="fa fa-link"></i> <?=Yii::t('common','View Detail')?></a>
                <button type="button" class="btn btn-success btn-save-item" id="btn-modal-action"><i class="fa fa-save"></i> <?=Yii::t('common','Save')?></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade modal-full" id="modal-craft-item">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=Yii::t('common','Produce')?> : <span id="item-name-description"></span></h4>
      </div>
      <div class="modal-body body-craft" style="background-color: rgb(241, 241, 241);margin-top: -5px; padding-bottom:10px; padding-top: 15px; color:#fff;">
         
        <div class="row">
          <div class="col-sm-7 mb-10">
            <div class=" " id="item-source-render"></div>
          </div>
          <div class="col-sm-2  text-center align-middle mt-10 mb-10">    
            <span class="hidden-xs">         
              <a href="#" class="btn btn-default-ew make-craft-item" style="margin-top:200px; color:#fff;"><i class="fas fa-arrow-right fa-4x" ></i></a>
            </span> 

            <span class="hidden-sm hidden-md hidden-lg">         
              <a href="#" class="btn btn-default-ew make-craft-item mt-10" style="color:#fff;"><i class="fas fa-arrow-down fa-4x btn"></i></a>
            </span> 
          </div>
          <div class="col-sm-3 text-center">
            
            <div class="input-group" style="margin: 10px auto 10px auto;" id="quantity-for-create">
              <span class="input-group-btn">
                <button type="button" class="btn btn-number btn-default" data-type="minus" data-field="craft-qty" data-rippleria="">
                  <span class="glyphicon glyphicon-minus"></span>
                </button>
              </span>
              <input type="number" step="any" class="form-control input-number text-center" name="craft-qty" value="1"/>
              <span class="input-group-btn">
                <button type="button" class="btn btn-number btn-default" data-type="plus" data-field="craft-qty" data-rippleria="">
                  <span class="glyphicon glyphicon-plus"></span>
                </button>
              </span>
            </div>
            
            <img src="" class="img-item-destination img-responsive img-thumbnail" style="width: 100%;" />
            <div class="sort-name" style="position: absolute; left: 30%;top: 60px; color: #000;"> </div>
            
          </div>
        </div>

        <div class="row" id="search-for-add-child" >
           
          <div class="col-xs-12 mt-10" style="min-height:150px;">

          <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><?=Yii::t('common','Items')?></h3>
                <div class="pull-right search-control" style="margin-top: -25px; margin-right: -13px;">
                    
                    <div class="input-group">
                      <input type="text" class="form-control" id="search-item" placeholder="Search">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-default"><i class="fa fa-search"></i></button>
                      </span>
                    </div>
                   
                </div>
              </div>
              <div class="panel-body bg-gray">
                 <div class="row render-item-search"></div>
              </div>
          </div>
          
            
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>        
        <a href="#" class="btn btn-warning make-craft-item hidden"><i class="fas fa-cogs"></i> <?=Yii::t('common','Produce')?></a>
        <a href="#" class="btn btn-warning make-prod-request" ><i class="fa fa-cogs"></i> <?=Yii::t('common','Produce')?></a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-showitem" style="z-index:1050">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">ITEM</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-6">
            <div class="name hidden"></div>
              <img src="" class="img-responsive"/>
            <div class="fullname mt-5"></div>
          </div>
          <div class="col-xs-6">
              <label for="item-alias" class="mr-5"><?=Yii::t('common','Name')?></label>
              <input type="text" id="item-alias" name="alias" class="form-control" />
              <div class="mt-5 item-description"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
      </div>
    </div>
  </div>
</div>

<?php
$Yii = "Yii"; 
$js =<<<JS
  
  const renderBomTable = (data, callback) => {
    let tbody = ``;

    var i;
    var x;

    data.map((model,i) => {
      tbody+= `
          
        <div class="col-xs-3 ` +( model.id ? 'child-craft' : ' ')+ ` minus-item" 
              data-img="` + model.img + `" 
              data-key="` + model.id + `" 
              data-item="` + model.item + `"
              data-name="`+ model.name +`"
              data-nameTh="` + model.nameTh + `"
              data-code="`+ model.code +`"
              data-alias="`+ (model.alias ? model.alias : ' ') + `"
              data-qty="`+ model.qty + `"
              data-prio="` +model.prio+ `"
              style="cursor: move; position: relative; ` +(model.id ?  'background: url('+ model.img + ');  background-repeat: no-repeat; background-size: auto 100%; background-position: center; ' : '' )+ `  ` +(model.id ? 'background-color: #7c7c7c;' : 'background-color: #fff;')+ `">
          <div style="color: #404040; position: absolute; right: 6px;" >`+ (i+1) +`</div>
          <div style="margin: 0px -10px 0px 10px;" class="qty"><span class="qty-per-unit pointer">`+ model.qty + `</span></div>
          <div style="position: absolute; bottom:6px; left: 0px; padding: 0px 2px 0px 2px;background: rgba(204, 204, 204, 0.47);" class="name pointer item-detail">`+ (model.alias ? model.alias : ' ') + `</div>
          <div class="` + (model.id ? '' : 'hidden') + `" style="position:absolute; bottom:5px; right:5px; color: #696969;"><span class="stock">`+ model.qty + `</span> / <span class="remain">`+ model.stock + `</span></div>
        </div>
          
      `;
    })
    callback(tbody);
  }
  
  const sortElement = () => {
    let raw = [];
    let id  = $('#modal-create-item').attr('data-key');
    $( "#item-source-render .child-craft" ).each((key,el) => {
      raw.push({
        id: $(el).attr('data-key'),
        priority: key
      });
 
    })

    fetch("?r=items/item-craft/update-item-craft-proirity", {
            method: "POST",
            body: JSON.stringify({data:raw}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
      })
      .then(res => res.json())
      .then(response => { 
     
        if(response.status===200){
          renderBomTable(response.raws, html => {
            $('#item-source-render').html(html);
          });
        }
        
      })
      .catch(error => {
          console.log(error);
      });
  }
  
 
  $(document).ready(function(){
    
    $( function() {

      $( "#item-source-render" ).sortable({
        update: function( event, ui ) {
          sortElement();
        }
      });

      $( "#item-source-render" ).disableSelection();

     

    });

    $('.myclass').mousedown(function(event) {
      switch (event.which) {
          case 1:
              alert('Left mouse button is pressed');
              break;
          case 2:
              alert('Middle mouse button is pressed');
              break;
          case 3:
              alert('Right mouse button is pressed');
              break;
          default:
              alert('Nothing');
        }
    });
  });
  
  const getItemCraft = (obj, callback) => {
    $('.loading-div').show();
    fetch("?r=items/item-craft/get-item-craft", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
      })
      .then(res => res.json())
      .then(response => {            
          callback(response);    
          $('.loading-div').hide();        
      })
      .catch(error => {
          console.log(error);
      });
  }

  const addToTable = (obj, callback) => {
    $('.loading-div').show();
    fetch("?r=items/item-craft/add-item-to-bom-table", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {            
            callback(response); 
            $('.loading-div').hide();           
        })
        .catch(error => {
            console.log(error);
        });
  }

  const minusFoTable = (obj, callback) => {
    $('.loading-div').show();
    fetch("?r=items/item-craft/minus-from-table", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
    })
    .then(res => res.json())
    .then(response => {            
        callback(response);   
        $('.loading-div').hide();         
    })
    .catch(error => {
        console.log(error);
    });
  }

  const craftItem = (obj, callback) => {
    $('.loading-div').show();
    fetch("?r=items/item-craft/carft-item", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
    })
    .then(res => res.json())
    .then(response => {            
        callback(response);      
        $('.loading-div').hide();      
    })
    .catch(error => {
        console.log(error);
    });
  }

  


  $('body').on('click','a.item-inventory',function(){
      var modal = $('#item-inventory');
      var body  = modal.find('.modal-body').find('.locations');
      var id    = $(this).data('key');           
      $.ajax({
        url:'index.php?r=warehousemoving/inventory/inven-by-location',
        type:'GET',
        data:{id:id},
        dataType:'JSON',
        success:function(response){          
          body.html(response.html);
          body.fadeIn('slow');
          modal.find('.modal-body').find('.inven').html(number_format(response.inven));
        }
      });    
  });


  $(document).ready(function(){   
    $('.loading-div').hide();
  });

  $('#modal-craft-item').on('shown.bs.modal', function() {
      
  });      

  $('#modal-create-item').on('shown.bs.modal', function() {
    let id = $(this).attr('data-key');
      $('body').find('.btn-make-produce').attr('href', '?r=Manufacturing%2Fproduction-request%2Fcreate&item=' + id + '&qty=1');
  });      
    
  
  
  $('body').on('click', 'a#craft-item', function(){
 
    let el  = $(this).closest('div#modal-create-item');
    let id  = parseInt(el.attr('data-key'));
    let img = $('body').find('.item-image-change .item-img').attr('src');
    
    $('#modal-craft-item').modal('show');
    $('#modal-craft-item .img-item-destination').attr('src', img);


    let data = localStorage.getItem('item:'+id)
          ? JSON.parse(localStorage.getItem('item:'+id))
          : [];

    if(data.length > 0){
      renderBomTable(data, html => {
        $('#item-source-render').html(html);
      });
    }else{
      $('#item-source-render').html('Loading...');
      getItemCraft({id:id}, res => {
        $('#item-name-description').text(res.description);
        localStorage.setItem('item:'+id,JSON.stringify(res));
        renderBomTable(res.raws, html => {
          $('#item-source-render').html(html);
        });
      });
    }
    
  });


  $('body').on('click', '.add-to-table', function(){
    let el      = $('body').find('div#modal-create-item');
    let id      = parseInt($(this).attr('data-key'));
    let source  = parseInt(el.attr('data-key'));

    
    addToTable({id:id, source:source}, res => {
      if(res.status===200){
        // render table
        renderBomTable(res.raws, html => {
          $('#item-source-render').html(html);

        });

      }else{
        $.notify({
          // options
          icon: "fas fa-box-open",
          message: res.message
        },{
            // settings
            placement: {
              from: "top",
              align: "center"
            },
            type: "error",
            delay: 4000,
            z_index: 3000
        });
      }
    });
     

  });
 

  $('body').on('click', '.qty-per-unit', function(){
    let id  = $(this).closest('.minus-item').attr('data-key');
    let val = $(this).closest('.minus-item').attr('data-qty');

    let html = `
        <span class="minus-per-unit pointer" style="position: absolute; left: -11px; top: 4px;"> <i class="fas fa-minus text-red"></i> </span> 
        <span class="qty-per-unit pointer">` +val+ `</span> 
        <span class="plus-per-unit pointer"> <i class="fas fa-plus text-primary"></i> </span>       
      `;
  
 
    $(this).closest('div').html(html);
    
     
  }); 

  $('body').on('click', '.minus-per-unit', function(){
    let id = $(this).closest('.minus-item').attr('data-key');
    if (confirm('{$Yii::t("common","Delete")} -1 ?')) {
      minusFoTable({id:id}, res => {
        if(res.status===200){
          // render table
          renderBomTable(res.raws, html => {
            $('#item-source-render').html(html);
          });

        }else{
          $.notify({
            // options
            icon: "fas fa-box-open",
            message: res.message
          },{
              // settings
              placement: {
                from: "top",
                align: "center"
              },
              type: "error",
              delay: 4000,
              z_index: 3000
          }); 

        }

      });
    }
  })

  $('body').on('click', '.plus-per-unit', function(){
    let id      = parseInt($(this).closest('.minus-item').attr('data-item'));
    let el      = $('body').find('div#modal-create-item');
    let source  = parseInt(el.attr('data-key'));

    if (confirm('{$Yii::t("common","Add")} +1 ?')) {
      addToTable({id:id, source:source}, res => {
        if(res.status===200){
          // render table
          renderBomTable(res.raws, html => {
            $('#item-source-render').html(html);
          });

        }else{
          $.notify({
            // options
            icon: "fas fa-box-open",
            message: res.message
          },{
              // settings
              placement: {
                from: "top",
                align: "center"
              },
              type: "error",
              delay: 4000,
              z_index: 3000
          });
        }
      });
    }
  })

  $('body').on('mousedown', '.item-detail', function(event){
    let id      = parseInt($(this).closest('.minus-item').attr('data-key'));
    let img     = $(this).closest('.minus-item').attr('data-img');
    let code    = $(this).closest('.minus-item').attr('data-code');
    let name    = $(this).closest('.minus-item').attr('data-alias');
    let fullname= $(this).closest('.minus-item').attr('data-name');
    let nameTh  = $(this).closest('.minus-item').attr('data-nameTh');
    let itemId  = parseInt($(this).closest('.minus-item').attr('data-item'));
    
    switch (event.which) {
          case 1:
             // alert('Left mouse button is pressed');  
              if(id > 0){      
                $('#modal-showitem').modal('show').attr('data-key', itemId);
                $('body').find('#modal-showitem .modal-title').html(`<a href="?r=items%2Fitems%2Fview&id=`+itemId+`" target="_blank">` +code + `</a>`);
                $('body').find('#modal-showitem div.name').html(name);
                $('body').find('#modal-showitem div.fullname').html(fullname);
                $('body').find('#modal-showitem img').attr('src',img);
                $('body').find('#modal-showitem input[name="alias"]').val(name);
                $('body').find('#modal-showitem div.item-description').html(nameTh);
              }
              break;
          case 2:
             //alert('Middle mouse button is pressed');
              break;
          case 3:
                    
              event.preventDefault();
              
              if(id > 0){

                if (confirm('{$Yii::t("common","Delete")} -1 ?')) {
                  minusFoTable({id:id}, res => {
                    if(res.status===200){
                      // render table
                      renderBomTable(res.raws, html => {
                        $('#item-source-render').html(html);
                      });

                    }else{
                      $.notify({
                        // options
                        icon: "fas fa-box-open",
                        message: res.message
                      },{
                          // settings
                          placement: {
                            from: "top",
                            align: "center"
                          },
                          type: "error",
                          delay: 4000,
                          z_index: 3000
                      }); 

                    }

                  });
                }
              }
            return false;
            break;
          default:
              alert('Nothing');
        }
     

  });

  const updateAlias = (obj, callback) => {
    $('.loading-div').show();
    fetch("?r=items/items/update-alias", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
    })
    .then(res => res.json())
    .then(response => {            
        callback(response);     
    })
    .catch(error => {
        console.log(error);
    });
  }

  $('body').on('change','input[name="alias"]', function(){
      let id  = $(this).closest('#modal-showitem').attr('data-key');
      let val = $(this).val();
      let el  = $('body').find('div[data-item="'+id+'"]');

      updateAlias({id:id, val:val.trim(), field:'name'},res=> {
        if(res.status===200){
          $('#modal-showitem').modal('hide');
          $('.loading-div').hide();  
          
          el.find('div.name').html(val.trim());
          el.attr('data-alias', val.trim());
        }
      })
  })


  $('body').on('click', 'a.make-craft-item', function(){
    let el      = $('body').find('div#modal-create-item');
    let source  = parseInt(el.attr('data-key'));

    let qty     = parseInt($('body').find('input[name="craft-qty"]').val());
 
    let bom     = [];

    $('#item-source-render .minus-item').each((key, el) => {
        bom.push(parseInt($(el).attr('data-key')));
    })

    
    if(bom.length > 0 && qty != 0){
  
      if (confirm('Confirm ?')) {
        
        craftItem({source:source, qty:qty}, res => {

          if(res.status===200){
            $('a.remaining span').html(res.stock)
            $('#modal-craft-item').modal('hide');
          }else{

            $.notify({
              // options
              icon: "fas fa-box-open",
              message: res.message
            },{
                // settings
                placement: {
                  from: "top",
                  align: "center"
                },
                type: "error",
                delay: 4000,
                z_index: 3000
            }); 
            
          }
          
        });
      }

    }else{

      alert('Please add member of item. Or Quantity must not be 0.');
      
    }
      
  });

  const searchItem = (search, callback) => {
    $('#modal-craft-item .render-item-search').html('<i class="fas fa-sync fa-spin text-center"></i>');
    setTimeout(() => {
      
      fetch("?r=items/ajax/search-items-json", {
          method: "POST",
          body: JSON.stringify({search:search}),
          headers: {
              "Content-Type": "application/json",
              "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
          },
      })
      .then(res => res.json())
      .then(response => {            
          callback(response);            
      })
      .catch(error => {
          console.log(error);
      });
    }, 500);
  }

  let renderTableSeach = (data) => {
    var html = ``;

    data.map(model => {
      html+= `<div class="col-lg-1 col-md-2 col-sm-3 col-xs-4 mt-5 font-roboto">
                <img src="` + model.img +`" class="img-responsive img-thumbnail add-to-table pointer" data-key="` + model.id +`" title="` + model.name +`"   />
                <div style="position:absolute; top:5px; right:26px; font-size:18px;">
                  <span style="padding: 0px 5px 0px 5px; background: #d7dad9b8;">` + model.stock + `</span>
                </div>
                <div style="position:absolute; bottom:5px; left:26px; font-size:18px;">
                  <span style="padding: 0px 5px 0px 5px; background: #d7dad9b8;">` + model.alias + `</span>
                </div>
              </div>`;
    });
      
    $('body').find(".render-item-search").html(html);
  }
  
  $('body').on('change', 'input#search-item', function(){
    let search = $(this).val();

    searchItem(search, res => {
      renderTableSeach(res.raws);
    });
    
  });

  $('body').on('keypress', 'input#search-item', function(e){
    let search = $(this).val();
    if (e.which == 13) {
      searchItem(search, res => {
      renderTableSeach(res.raws);
    });
    }

  });


  const calulateQty = (qty) => {
 
    $('body').find('#item-source-render .minus-item').each((key, el) => {
        let myQty   = parseInt($(el).find('.qty').text());
        let remain  = parseInt($(el).find('.remain').text());

        let total   = qty * myQty;
        let img     = $(el).attr('data-img');

        if(total > remain){
          $(el).find('.stock').attr('style', 'color:red;').html(total);
          $(el).attr('style',`position: relative; background: url(` +img + `); background-repeat: no-repeat; background-size: auto 100%; background-color: rgb(243, 243, 243); background-position: center;`);
        }else{
          $(el).find('.stock').attr('style', 'color:#50dfff;').html(total);
          $(el).attr('style',`position: relative; background: url(` +img + `); background-repeat: no-repeat; background-size: auto 100%; background-position: center;`);
        }

        
    })
  }

  $('body').on('change', 'input[name="craft-qty"]', function(){
    let qty     = parseInt($(this).val());
    calulateQty(qty);
  })

  $('body').on('click', 'button[data-type="plus"]', function(){
    let qty     = parseInt($('input[name="craft-qty"]').val());
    let newQty  = parseInt(qty) + 1;
    $('input[name="craft-qty"]').val(newQty);
    
    calulateQty(newQty);
    
  })

  $('body').on('click', 'button[data-type="minus"]', function(){
    let qty     = parseInt($('input[name="craft-qty"]').val());
    let newQty  = parseInt(qty) - 1;
    $('input[name="craft-qty"]').val(newQty);
    
    calulateQty(newQty);
    
  });


  $('body').on('click', 'a.make-prod-request', function(){
    let item = $('body').find('#modal-create-item').attr('data-key');
    let qty  = $('body').find('input[name="craft-qty"]').val();
    window.open('index.php?r=Manufacturing%2Fproduction-request%2Fcreate&item='+item+'&qty=' +qty );
  })


JS;
$this->registerJS($js);
?>
