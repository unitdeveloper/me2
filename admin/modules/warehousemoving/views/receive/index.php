<?php
/* @var $this yii\web\View */
?>
<h1><?=Yii::t('common','Purchase Order (PO)')?></h1>

<div class="row">
    <div class="col-xs-4">
        
        <div class="input-group">
            <input type="text" class="form-control input-lg" id="search" name="search" placeholder="เลขที่ PO (PO<?=date('Y')?>-0001)" />
            <div class="input-group-addon btn btn-search"><i class="fa fa-search"></i></div>
        </div>

    </div>
    <div class="col-xs-4"></div>
    <div class="col-xs-4"></div>
</div>
<div class="row">
    <div class="col-xs-12 mt-10">
        <div id="render-table">
            <h4>ค้นหาใบ PO เพื่อรับสินค้า</h4>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-Purchase-Receive" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg"  style="width:80%;">
      <div class="modal-content">
        <div class="modal-header bg-orange">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><i class="fas fa-tasks"></i> <?=Yii::t('common','Purchase Receive')?></h4>
        </div>

        <div class="modal-body" style="max-height:77vh; overflow-y:auto;">
        
        </div>

        <div class="modal-footer">
        <a type="button" class="btn btn-default-ew pull-left  close-modal" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></a>
        </div>
      </div>
    </div>
  </div>


<div class="loading-div" style="position:fixed; top:30%; left:50%; z-index:2000;">
  <i class="fas fa-sync fa-spin fa-4x" ></i>
</div>

<?php

$RC     = Yii::t('common','Receive');
$FULL   = Yii::t('common', 'Received');
$REC    = Yii::t('common','RECEIVED');

$js =<<<JS
  
  
  const searchOrder = (search, callback) => {
    $('.loading-div').show();
    fetch("?r=Purchase/order/search", {
            method: "POST",
            body: JSON.stringify({search:search}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
      })
      .then(res => res.json())
      .then(response => {   
        $('.loading-div').hide();              
        callback(response);    
      })
      .catch(error => {
          console.log(error);
      });
  }


  $(document).ready(function(){   
    $('.loading-div').hide();
    $('input#search').focus();
  });


  const renderTableSeach = (data) => {
      let body = ``;
      data.map((model, keys) => {
            body+= `
                <tr class="` + (model.complete ? 'bg-success' : (model.received ? 'bg-warning' : 'bg-white')) + `" data-key="` + model.id + `">
                    <td class="bg-gray"> ` + (keys + 1) + ` </td>
                    <td> ` + (model.date) + ` </td>
                    <td> <a href="?r=Purchase%2Forder%2Fview&id=` + (model.id) + `" target="_blank" >` + (model.no) + `</a> </td>
                    <td> <a href="index.php?r=Purchase/order/receive&id=` + (model.id) + `" target="_blank" class="btn btn-warning-ew"> <i class="fas fa-truck-loading"></i>  ${RC}  </a> </td>
                    <td> ` + (model.name) + ` </td>
                    <td class="text-center"> ` + (model.complete
                                ? '<i class="fas fa-check"></i>'
                                : ' '
                            ) + ` </td>

                    <td><a href='#' class="open-modal-receive"> <i class="fas fa-tasks"></i> ${REC} </a></td>                    
                </tr>
            `;
      })
      let table= `
                <table class="table table-bordered font-roboto">
                    <thead>
                        <tr>
                            <th class="bg-primary" style="width:20px;">#</th>
                            <th class="bg-gray" style="width:120px;">Date</th>
                            <th class="bg-gray" style="width:150px;">Po</th>
                            <th class="bg-gray" style="width:30px;">Receive</th>
                            <th class="bg-gray">Vendors</th>                            
                            <th class="bg-gray" style="width:70px;">${FULL}</th>
                            <th class="bg-gray"  style="width:120px;">Detail</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        ` + body + `
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="bg-primary"></th>
                            <th class="bg-gray"></th>
                            <th class="bg-gray"></th>
                            <th class="bg-gray"></th>
                            <th class="bg-gray"></th>
                            <th class="bg-gray"></th>
                            <th class="bg-gray"></th>
                        </tr>
                    </tfoot>
                </table>
        `;
   
    $('#render-table').html(table);
  }

  $('body').on('keypress', 'input#search', function(e){
    let search = $(this).val();
    if (e.which == 13) {
        searchOrder(search, res => {
            renderTableSeach(res.raws);
        });
    }
  });

  $('body').on('click', 'div.btn-search', function(e){
    let search = $('input#search').val();
    
    searchOrder(search, res => {
        renderTableSeach(res.raws);
    });
     
  });


    const renderTableOnModal = (data) => {

        let body =  ``;

        if(data){
        
            data.map((model,keys) => {

                let lineItem = ``;
                model.line.map((line, key) => {
                    let qty = line.qty * 1;
                    lineItem+= `
                        <tr >
                            <td> <img src="` + line.img + `" class="img-responsive pull-left " style="width:20px; margin-right:5px;">` + line.item_no + `</td>
                            <td>` + line.desc + `</td>
                            <td class="text-right" >` + number_format(qty) + `</td>
                            <td class="text-right" >` + line.measure + `</td>
                        </tr>
                    `;
                });

            body+= `
                    <tr data-key="` + model.id + `">
                        <td class="bg-gray">` + (keys + 1) + `</td>
                        <td >` + model.date + `</td>
                        <td ><a href="index.php?r=warehousemoving%2Freceive%2Fview&id=` + model.id + `&po=` + model.no + `" target="_blank">` + model.no + `</a></td>
                        <td >
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-gray">
                                        <th class=" " style="width:180px;">Product</th>
                                        <th class=" " >Description</th>
                                        <th class="text-right" style="width:80px;">Quantity</th>
                                        <th class="text-right" style="width:80px;">Measure</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ` + lineItem + `
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"> </td>
                                    </tr>
                                </tfoot>
                            </table>                
                        </td>
                    </tr>
                        
                `; 
            });

        }else{
            body+= `<tr><td></td><td colspan="4">Not receive</td></tr>`;
        }

        

        let table = `
                    <table class="table table-hover font-roboto">
                        <thead>
                            <tr class="bg-dark">
                                <th style="width:10px;" class="text-center">#</th>
                                <th style="width:150px;">Date</th>
                                <th style="width:110px;">No</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            ` + body + `
                        </tbody>                              
                    </table>`;

        $('#modal-Purchase-Receive .modal-body').html(table);

    }

    const getReceive = (obj, callback) => {
        $('.loading-div').show();
        $.ajax({ 
            url:"?r=Purchase/order/get-receive&id=" + obj.id,
            type: 'GET', 
            data:{reload:true},
            async:true,
            dataType:'JSON',
            success:function(response){
                       
                callback(response);      
            }
        });  
        // fetch("?r=Purchase/order/get-receive&id=" + obj.id, {
        //     method: "GET",
        //     body: JSON.stringify(obj),
        //     headers: {
        //         "Content-Type": "application/json",
        //         "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        //     },
        // })
        // .then(res => res.json())
        // .then(response => {   
        //     $('.loading-div').hide();              
        //     callback(response);    
        // })
        // .catch(error => {
        //     console.log(error);
        // });
    };


    $('body').on('click', 'a.open-modal-receive', function(){
        $('#modal-Purchase-Receive').modal('show');
        $('#modal-Purchase-Receive .modal-body').html('');
        let id = $(this).closest('tr').attr('data-key');
        getReceive({id:id}, res => {
            setTimeout(() => {
                renderTableOnModal(res.header);
                $('.loading-div').hide();       
            }, 500);
            
        })
    });

JS;
$this->registerJS($js);
?>
