
<div class="modal fade " id="modal-pick-vendors-wizard" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Vendors')?></h4>
            </div>
           
            <div class="modal-body">
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-sm-6 pull-right">
                        <form name="search">
                            <div class="input-group"  >
                                <input type="text" name="search" class="form-control" autocomplate="off" placeholder="<?=Yii::t('common','Search')?>" />                 
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-default s-click"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12" id="renderVendors"></div>
                </div>
            </div>
             
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <i class="fas fa-power-off"></i> Close</button>
                 
            </div>
        </div>
    </div>
</div>

<?php
$Yii    = 'Yii';
$js     =<<<JS
  
const rendersVendors = (data, div) => {
  let rows = ``;
  data.length > 0
    ? data.map(model => {
      rows += `<tr data-key="` + model.id +`" data-address="` + model.address +`"   data-term="` + model.term  +`">
                    <td class="code" style="font-family:roboto;">` + model.code  +`</td>
                    <td class="name">` + model.name  +`</td>
                    <td class="text-center"><button type="button" class="selected-vendors btn btn-primary btn-flat"><i class="fas fa-check"></i> {$Yii::t('common','Select')}</button></td>
                </tr>`;
      })
    : rows += `<tr><td colspan="3" class="text-center">{$Yii::t('common','No data')}</td></tr>`;


  let html = `<table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{$Yii::t('common','Code')}</th>
                        <th>{$Yii::t('common','Name')}</th>
                        <th style="width:95px;" class="text-center">{$Yii::t('common','Select')}</th>
                    </tr>
                </thead>
                <tbody>
                  ` + rows + `
                </tbody>
              </table>`;

  $("body").find(div).html(html);
  $('body').find('button.selected-vendors:first').focus();
};

const searchVendor = search => {
  fetch("?r=accounting/payment/find-vendors", {
    method: "POST",
    body: JSON.stringify({ search: search }),
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
    }
  })
    .then(res => res.json())
    .then(response => {
      rendersVendors(response.data, "#renderVendors");
    })
    .catch(error => {
      console.log(error);
    });
};



$("body").on("keypress", '#modal-pick-vendors-wizard input[name="search"]', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    let words = $('#modal-pick-vendors-wizard input[name="search"]').val();
    searchVendor(words);
  }
});


// Select Vendors
$("body").on("click", "button.selected-vendors", function() {
  let term    = $(this).closest("tr").attr("data-term");
  let vendor = {
    id: parseInt($(this).closest("tr").attr("data-key")),
    name: $(this).closest("tr").find("td.name").text(),
    code: $(this).closest("tr").find("td.code").text(),
    term: term ? term : 0
  };

  localStorage.setItem("vendors", JSON.stringify(vendor));
  $('.vendor-code').html(vendor.code);
  $('a.vendor-code').attr('href', `index.php?r=vendors%2Fvendors%2Fview&id=` +vendor.id);
  $('.vendor-name').html(vendor.name);
  $('.next-to-create-line').show(); // Show button Next
  
  localStorage.removeItem('payment-header'); // Clear Heading
  localStorage.removeItem('payment-line'); // Clear Cache
  
  
  $('body').find('#Payment-Line').html(renderLineTable([])); // Load line from empty cache  

  setTimeout(() => {
      $('.content-step-vendor').hide();
      $('.content-step-edit').show();
      $('body').find('input[name="search-code"]').select().focus();
  }, 500);
  $("#modal-pick-vendors-wizard").modal("hide");  
   
});

$('#modal-pick-vendors-wizard').on('show.bs.modal',function(){ 
  setTimeout(() => {
    $('body').find('input[name="search"]').select().focus();   
  }, 500);    
})

$('#modal-pick-vendors-wizard').on('hidden.bs.modal',function(){   
  $('body').find('#renderVendors').html(' ');
})




JS;
$this->registerJs($js,\yii\web\View::POS_END);
?>
  