<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use admin\modules\SaleOrders\models\FunctionSaleOrder;
?> 

<?= GridView::widget([
      'dataProvider' => $dataProvider,
      //'filterModel' => $searchModel,
      'tableOptions' => ['class' => 'table table-bordered  table-hover', 'id' => 'modern-trade-table'],
      'rowOptions' => function($model){
          return [
            'class' => (($model->confirm*1) > 0)? 'confirm-modal' : 'bg-pink confirm-modal',
            'data-no'   => $model->no
          ];
      },
      'columns' => [
          [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['class' => 'bg-primary'],
            'contentOptions' => function($model){
              return ['class' => (($model->confirm*1) > 0)? 'bg-gray serial-column' : 'bg-yellow blink serial-column',];
            }
          ],

          [
              'attribute' => 'head_date',
              'label' => Yii::t('common','Date'),
              'format' => 'raw',
              'headerOptions' => ['class' => 'hidden-xs bg-primary'],
              'filterOptions' => ['class' => 'hidden-xs'],
              'contentOptions' => function($model){
                return ['class' => 'hidden-xs pointer','style' => 'position:relative;', 'data-no' => $model->no, 'data-key' => $model->id];
              },            
              'value' => function ($model) {
                  $link = '<div>'.Yii::t('common','Date').' : '.date('d/m/Y',strtotime($model->order_date)).'</div>'."\r";
                  if($model->update_date != ''){
                      $link.= '<div>'.Yii::t('common','Time').' : '.date('H:i:s',strtotime($model->update_date)).'</div>'."\r";
                  }else {
                      $link.= '<div>'.Yii::t('common','Time').' : '.date('H:i:s',strtotime($model->create_date)).'</div>'."\r";
                  }
                  return $link;
              },
          ],

          [
              'attribute' => 'no',
              'format' => 'raw',
              'headerOptions' => ['class' => 'bg-primary'],
              'contentOptions' => function($model){
                return ['class' => 'text-info pointer','style' => 'position:relative;',  'data-no' => $model->no, 'data-key' => $model->id];
              },
              'value' => function($model){

                  // ตัดตัวอักษร ถ้ามากกว่า 35 ตัว
                  $count_char = utf8_strlen($model->customer['name']);
                  if($count_char >=32 )
                  {
                      $cust_name = iconv_substr($model->customer['name'],0,32,'UTF-8').'...';
                  }else {
                      $cust_name = $model->customer['name'];
                  }


                  if($model->vat_type==1)
                  {
                      $vat_color =  'text-success';
                  }else {
                      $vat_color =  'text-primary';
                  }
                  if(date('Ymd') == date('Ymd', strtotime($model->create_date )))
                  {
                      $Showdate = date('H:i',strtotime($model->create_date));
                  }else {
                      $Showdate = date('d/m/Y',strtotime($model->create_date));
                  }

                  $cus = '<div class="text-customer-info">
                                    '.$cust_name.'
                                  </div>';

                  $cus.= '<div class="'.$vat_color.' text-order-number">
                                    '.$model->no.'
                                  </div>';


                  $cus.= '<div class="hidden-sm hidden-md hidden-lg text-right" style="position:absolute; right:15px; top:10px; color:#ccc;">
                            <div class="text-aqua text-balance">
                              <span  style="background-color:#fff; padding-left:5px;padding-right:5px;">'.number_format($model->balance,2).'</span>
                            </div>
                            <small class="hidden-sm hidden-md hidden-lg " style="padding-left:5px;padding-right:5px;">  '.$Showdate.'</small>
                          </div>'."\r";


                  $Fnc = new FunctionSaleOrder();

                  $JobStatus = $Fnc->OrderStatus($model);

                  $cus.= '<div class="hidden-sm hidden-md hidden-lg text-ship-status">'.Yii::t('common',$JobStatus).'</div>'."\r";

                  if($model->status=='Shiped')
                  {
                      $cus.='<div class="hidden-sm hidden-md hidden-lg text-ship-status">
                              <i class="fa fa-calendar" aria-hidden="true"></i> '.date('d/m/Y',strtotime($model->ship_date)).'
                            </div>'."\r";
                  }

                  return $cus;
              },
          ],
       
          [

              'format' => 'raw',
              'filterOptions' => ['class' => 'hidden-xs'],
              'headerOptions' => ['class' => 'hidden-xs bg-primary'],
              'contentOptions' => function($model){
                return ['class' => 'hidden-xs pointer modern-status','style' => 'position:relative;',  'data-no' => $model->no, 'data-key' => $model->id];
              },            
              'value' => function($model){

                $confirmed = '';
                $waitting  = '';

                if(($model->confirm*1) > 0){
                  $status = '<div class="pull-left status"><i class="fa fa-check-square-o text-success"></i> '.Yii::t('common','Confirmed').'</div>';
                  $status.= '<div class="pull-right" style="color:#ccc;font-family:tahoma; font-size:10px;">'.date('Y-m-d H:i',strtotime($model->confirm_date)).'</div>';
                }else {
                  $status = '<div class="status"><i class="fa fa-square text-danger"></i> '.Yii::t('common','Waitting confirm').'</div>';
                }

                $Html = '<div class="col-xs-12">'.$status.'</div>';

                return $Html;

              },

          ],



      ],
      'pager' => [
        'options'=>['class' => 'pagination'],   // set clas name used in ui list of pagination
        'prevPageLabel'     => '«',   // Set the label for the "previous" page button
        'nextPageLabel'     => '»',   // Set the label for the "next" page button
        'firstPageLabel'    => Yii::t('common','First'),   // Set the label for the "first" page button
        'lastPageLabel'     => Yii::t('common','Last'),    // Set the label for the "last" page button
        'nextPageCssClass'  => Yii::t('common','next'),    // Set CSS class for the "next" page button
        'prevPageCssClass'  => Yii::t('common','prev'),    // Set CSS class for the "previous" page button
        'firstPageCssClass' => Yii::t('common','first'),    // Set CSS class for the "first" page button
        'lastPageCssClass'  => Yii::t('common','last'),    // Set CSS class for the "last" page button
        'maxButtonCount'    => 6,    // Set maximum number of page buttons that can be displayed
        ],
  ]); ?>


<div class="modal fade modal-full" id="modal-confirm-checklist">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                <button type="button" class="btn btn-warning" id="confirm-item-checked" style="display:none;"><i class="fa fa-save"></i> <?=Yii::t('common','Confirm')?></button>
            </div>
        </div>
    </div>
</div>

<?php
$LABEL_IMG          = Yii::t('common','Image');
$LABEL_CODE         = Yii::t('common','Code');
$LABEL_NAME         = Yii::t('common','Name');
$LABEL_QTY          = Yii::t('common','Quantity');
$LABEL_ALL          = Yii::t('common','Check All');
$LABEL_STOCK        = Yii::t('common','Stock');
$LABEL_CONFIRM_LIST = Yii::t('common','Confirm List');
$LABEL_TABLE_DETAIL = Yii::t('common','Choose items that are ready for delivery.');

$jsx =<<<JS

const getOrderDetail = (obj,callback) => {
    $('.loading').show();
    fetch("?r=warehousemoving/shipment/load-data", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
    })
    .then(res => res.json())
    .then(response => { callback(response); $('.loading').hide(); })
    .catch(e => { swal("Fail!", e +' '+ new Date().toTimeString().slice(0, 8), "error"); });
}

let renderTable = (obj, callback) => {

    let body    = '';
    let i       = 0;
    let data    = obj.raws;

    data.length > 0
    ? data.map(model => {
        i++;
        body += `<tr data-key="` + model.id + `" class="` + (model.stock >= model.qty ? '' : 'bg-danger' ) + `">
                    <td class=" " style="font-family:roboto;">` + i + `</td>
                    <td class=" " style="font-family:roboto;">
                        <label class="pointer" for="check-stock-` + model.id + `">
                        <img src="`+ model.img +`" class="img-responsive img-thumbnail" />
                        </label>
                    </td>
                    <td class="item-code" style="font-family:roboto;"><a href="?r=items%2Fitems%2Fview&id=` + model.item + `" target="_blank">`+ model.code +`</a> </td>
                    <td class="item-desc"><label class="pointer" for="check-stock-` + model.id + `">`+ model.desc_th +`</label></td>
                    <td class="text-right ` + (model.stock >= model.qty ? 'text-green' : 'text-red' ) + `" style="font-family:roboto;">`+ number_format(model.stock) +`</td>
                    <td class="text-right bg-yellow" style="font-family:roboto;"><input type="text" class="form-control text-right" name="qty" value="`+ (model.qty * 1) +`" /></td>
                    <td class="text-center">
                        <label style="width:100%; height:30px;" class="pointer" for="check-stock-` + model.id + `">
                            <input type="checkbox" ` + (model.stock >= model.qty ? 'checked' : '' ) + ` id="check-stock-` + model.id + `" data-key="` + model.id + `" name="check-item"/> 
                        </label>
                    </td>
                </tr> \r\n`;
      })
    : (body += `<tr><td colspan="7" class="text-center" ><h2 style="margin-top:100px; margin-bottom:50px;"><i class="fas fa-exclamation-triangle"></i> No Data</h2></td></tr>`);

    let html = `<table class="table table-bordered" id="data-items" data-key="` + obj.id + `">
                    <thead>
                        <tr>
                            <th class="bg-gray text-center" colspan="6" >{$LABEL_TABLE_DETAIL}</th>                            
                            <th class="bg-gray text-center" >{$LABEL_CONFIRM_LIST}</th>
                        </tr>
                        <tr>
                            <th class="bg-dark" width="10">#</th>
                            <th class="bg-dark" width="20">{$LABEL_IMG}</th>
                            <th class="bg-dark" width="150">{$LABEL_CODE}</th>
                            <th class="bg-dark">{$LABEL_NAME}</th>
                            <th class="bg-dark text-right" width="80">{$LABEL_STOCK}</th>
                            <th class="bg-yellow text-right" width="100">{$LABEL_QTY}</th>
                            <th class="bg-dark text-center" width="100">
                                <input type="checkbox" id="check-all" name="checked"/>
                                <label class="pointer" for="check-all">  {$LABEL_ALL} </label>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        `+ body +`
                    </tbody>
                </table>`;

        html+= `<div class="well text-primary">
                    <small>
                        <div >หมายเหตุ :</div>
                        <div>*** เลือกรายการที่พร้อมส่ง </div>
                        <div>*** เมื่อคอนเฟิร์มแล้ว สินค้านั้นจะถูกจอง(ระบบจะยกเลิกจอง หลังจากเปิดบิลแล้วโดยอัตโนมัติ)</div>  
                    </small>                  
                </div>`;

    if(obj.confirm > 0){
        $('#confirm-item-checked').hide();
    }else{
        $('#confirm-item-checked').show(); 
    }
    callback({
        html:html
    });
}

$('body').on('click', 'tr.confirm-modal', function(){
    let id = parseInt($(this).attr('data-key'));
    let no = $(this).attr('data-no');
    console.log(no);
    getOrderDetail({id:id, no:no}, response => {
        if(response.status===200){
            $('#modal-confirm-checklist').modal('show');
            
            renderTable(response, res => {
                $('#modal-confirm-checklist .modal-body').html(res.html);
                $('#modal-confirm-checklist .modal-title').html(no);
            });
        }else{
            $('#modal-confirm-checklist').modal('close');
        }
    })
    
})


$('body').on('click', '#confirm-item-checked', function(){
    let so      = parseInt($('#data-items').attr('data-key'));
    let raws    = [];
    $('#data-items tr').each(function(){
        let row = $(this).find('input[name="check-item"]:checked');
        let id  = row.attr('data-key');
        let val = row.closest('tr').find('input[name="qty"]').val();
        let code= row.closest('tr').find('.item-code').text();
        
        if(id!==undefined){
            raws.push({
                id: id,
                code: code,
                qty: val
            });
        }       
    })

    if(confirm('Confirm ?')){

        if(raws.length > 0){
            
            fetch("?r=warehousemoving/default/confirm", {
                method: "POST",
                body: JSON.stringify({id:so, raw:raws}),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
                },
            })
            .then(res => res.json())
            .then(response => { 
                if(response.status===200){
                    swal("Success", 'OK', "success");
                    setTimeout(() => {
                        $('#modal-confirm-checklist').modal('hide');    
                        // ปิดการยืนยัน
                        $('body').find('#modern-trade-table tr[data-key="'+so+'"]').removeClass('bg-pink'); 
                        $('body').find('#modern-trade-table tr[data-key="'+so+'"]').find('td.serial-column').attr('class','bg-gray serial-column');    
                        let htm = `<div class="col-xs-12">
                                    <div class="pull-left status">
                                    <i class="fa fa-check-square-o text-success"></i> Confirmed</div>
                                    <div class="pull-right" style="color:#ccc;font-family:tahoma; font-size:10px;"> </div>
                                </div>`;  
                        $('body').find('#modern-trade-table tr[data-key="'+so+'"]').find('td.modern-status').html(htm);             
                    }, 1000);
                    
                }
            })
            .catch(e => { swal("Fail!", e +' '+ new Date().toTimeString().slice(0, 8), "error"); });
        }else{
            console.log(raws);
        }
    }else{
        return false;
    }

})


$('body').on('click', '#check-all', function(){
    $('input[name="check-item"]').not(this).prop('checked', this.checked);
})

JS;

$this->registerJs($jsx,\yii\web\View::POS_END);

?>