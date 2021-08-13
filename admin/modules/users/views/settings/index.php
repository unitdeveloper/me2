<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use yii\widgets\ActiveForm;

use yii\helpers\ArrayHelper;

$Rules = Yii::$app->session->get('Rules');
//var_dump($Rules);
/* @var $this yii\web\View */
/* @var $model common\models\SalesPeople */

$this->title = $model->firstname.' '.$model->lastname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sales Peoples'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
 <style>
    .content{
        background:#edf0f5;
    }
    .align-cener{
      margin:auto;
      text-align:center;
    }
    .btn-file:active{
      border:0px;
    }
    .error{
      color:red;
    }
    .item-info:hover {
        background: #3fbbea !important;
        color: #fff !important;
    }
</style>
 
 
  <!-- Content Header (Page header) -->
  <div class="row">
    <div class="col-xs-12">        
          <h4>
            <?=Yii::t('common','User Profile')?>
          </h4>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> <?=Yii::t('common','Home')?></a></li>
            <li class="active"><?=Yii::t('common','Settings')?></li>
            <!-- <li class="active">User profile</li> -->
          </ol>
        
    </div>
  </div>
 
  <div class="sales-people-view">
 
  <?php $form = ActiveForm::begin([
    'id' => 'profile-user'
  ]); ?>
      <!-- Main content -->
      <section class=" ">


        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="box box-primary">
              <div class="box-body box-profile">
                 
                  <!-- <div class="btn btn-file-">                                     
                      <img class=" img-responsive " src="<?=$model->picture;?>" id='img-preview-photo'>                            
                      <input type="file" id="profile-photo" name="Profile[photo]" value="c4ca4238a0b923820dcc509a6f75849b_thumb.jpg">                 
                  </div> -->
                 
                 
                <?=  $form->field($model,'photo',['options' => ['class' => 'btn btn-file']])->fileInput(['id' => 'profile-photo'])->label(false);?>



                <h3 class="profile-username text-center"><?=$model->firstname.' '.$model->lastname ?></h3>

                <p class="text-muted text-center"><?=$model->position?></p>

                <?php if($Rules->sale_id != ''){ ?>
                  <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                      <b><?=Yii::t('common','Sale Order')?></b> <a class="pull-right">-</a>
                    </li>
                    <li class="list-group-item">
                      <b><?=Yii::t('common','Pending')?></b> <a class="pull-right">-</a>
                    </li>
                    <li class="list-group-item">
                      <b><?=Yii::t('common','Invoice')?></b> <a class="pull-right">-</a>
                    </li>
                  </ul>
                <?php } ?>
                
                <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : '<i class="fa fa-floppy-o"></i> '.Yii::t('common', 'Save'), 
                ['class' => $model->isNewRecord ? 'btn btn-success btn-block' : 'btn btn-primary btn-block']) ?>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->

            <!-- About Me Box -->
            <div class="box box-primary hidden-xs">
              <div class="box-header with-border">
                <h3 class="box-title"><?=Yii::t('common','Information')?></h3>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <strong><i class="fa fa-book margin-r-5"></i> <?=Yii::t('common','Address')?></strong>

                <p class="text-muted">
                  <?=$model->address?>
                </p>

                <hr>

                <strong><i class="fa fa-map-marker margin-r-5"></i> <?=Yii::t('common','Location')?></strong>

                <p class="text-muted">-</p>

                <hr>

                <strong><i class="fa fa-pencil margin-r-5"></i> <?=Yii::t('common','Signature')?></strong>

                <p>
                  -
            
                </p>

                <hr>

                <strong><i class="fa fa-file-text-o margin-r-5"></i> <?=Yii::t('common','Notes')?></strong>

                <p>  -</p>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="nav-tabs-custom">
              <ul class="nav nav-tabs">

                <li class="active"><a href="#profiletab" data-toggle="tab"><i class="fas fa-info text-info"></i> <?=Yii::t('common','Profile')?></a></li>

                <li><a href="#company" data-toggle="tab"><i class="far fa-address-card"></i> <?=Yii::t('common','Company')?></a></li>

                <li><a href="#settings" data-toggle="tab"><i class="fas fa-cog"></i> <?=Yii::t('common','Settings')?></a></li>

                <?php
                  if($Rules->sale_id != ''){
                    echo '<li><a href="#salepeople" data-toggle="tab"><i class="fas fa-user text-orange"></i> '.Yii::t('common','Sale People').'</a></li>';
                    echo '<li><a href="#my-item-sales" data-toggle="tab"><i class="fas fa-list"></i> '.Yii::t('common','My item sales').'</a></li>';
                  }
                ?>

                

              </ul>
              <div class="tab-content">
                <div class="active tab-pane" id="profiletab">
                  <!-- DetailView -->
                  <?= DetailView::widget([
                      'model' => $model,
                      'attributes' => [
                          'user_id',
                          'name',
                          'public_email:email',                           
                          'location',
                          'website',                           
                          'timezone',
                          'address:ntext',
                          'province:ntext',
                          'district:ntext',
                          'amphur:ntext',
                          'postcode:ntext',
                          'city:ntext',
                          'mobile_phone:ntext',
                          'tax_id',
                          'user_birthday',
                          'country'
                      ],
                  ]) ?>

                  <!-- /.DetailView -->
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="company">
                   <?php
                    $company = \common\models\Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
                   ?>
                  <div class="row">
                      <div class="col-sm-2">
                        <img src="<?=$company->logoViewer;?>" class="img-responsive">
                      </div>
                      <div class="col-sm-10">
                          <div class="panel panel-default">
                            <!-- Default panel contents -->
                            
                            <div class="panel-heading"><?=$company->name;?></div>
                              <!-- Table -->
                              <table class="table">
                                <tbody>
                                  <tr><td><?= Yii::t('common','Vat Registration')?> : <?=$company->vat_register;?></td></tr>
                                  <tr><td><?=Yii::t('common','Address')?> : <?=$company->address;?></td></tr>
                                  <tr>
                                    <td>
                                      <?= $company->maps ? Html::img($company->mapsViewer,[
                                                                  'alt'       => Yii::t('common','Maps'),       
                                                                  'id'        => 'maps',                          
                                                                  'class'     => 'img-responsive',                                
                                                                  'data-zoom-image' => $company->mapsViewer
                                                              ]) : '';?>
                                      <?= Html::a('<i class="fa fa-download"></i> '.Yii::t('common','Download'),"images/company/maps/{$company->maps}"); ?>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                          </div>
                      
                      </div>
                  </div>
                   
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="settings">
                  <div class="row">
                      <div class="col-sm-6">
                        <?= $form->field($model,'firstname')?>
                      </div>
                      <div class="col-sm-6">
                        <?= $form->field($model,'lastname')?>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <?= $form->field($model,'address')?>
                      </div>
                      <div class="col-sm-6">
                        <?= $form->field($model,'tax_id')?>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <?= $form->field($model,'mobile_phone')->widget(yii\widgets\MaskedInput::className(),[
                          'name' => 'input-1',
                          'mask' => '999-999-9999'
                        ])?>
                      </div>
                      <div class="col-sm-6">
                        <?= $form->field($model,'postcode')?>
                      </div>
                    </div>

                    <div class="row">
                       
                      <div class="col-sm-6">
                      
                        <?php
                          if($model->country=='') $model->country = '213';

                          echo $form->field($model, 'country')->dropDownList(
                              ArrayHelper::map(\common\models\Countries::find()->orderBy(['country_name' => SORT_ASC])->all(),
                                                          'id',
                                                          'country_name'),[
                                                            'data-live-search'=> "true",
                                                            'class' => 'selectpicker',
                                                            'prompt'=>Yii::t('common','Select'). ' ' .Yii::t('common','country')
                                                          ]
                          )
                        ?>
                      </div>
                    </div>
                        

                    <div class="row">
                      <div class="col-sm-6">
                        <div class="box box-danger">
      
                          <div class="box-body">
                            <h4 class="box-heading"><?= Yii::t('common','Change password')?></h4>
                            <?=Yii::t('common','User Name')?> : <?= Yii::$app->user->identity->username; ?>
                            <p class="box-text">
                              <?= $form->field($model, 'password')->passwordInput([
                                  'id' => 'password',
                                  'name' => 'password',
                                  'placeholder' => '******'
                                ]) ?>
                               
                              <?= $form->field($model, 'confirm_password')->passwordInput([
                                  'id' => 'confirm_password',
                                  'name' => 'confirm_password',
                                  'placeholder' => '******'
                                ]) ?>
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                    
 
                
                </div>

                <!-- /.tab-pane -->

                <?php if($Rules->sale_id != ''){ ?>
                  <div class="tab-pane" id="salepeople">
                      <div class="panel panel-default">
                        <!-- Default panel contents -->                        
                        <div class="panel-heading"><?=$model->salepeople->name;?> <?=$model->salepeople->surname;?></div>                          
                          <!-- Table -->
                          <table class="table font-roboto">
                            <tbody>
                              <tr>
                                <td><?=Yii::t('common','Code')?> : <?=$model->salepeople->code;?></td>
                              </tr>
                              <tr>
                                <td><?=Yii::t('common','ID')?> : <?=$model->salepeople->id;?></td>
                              </tr>
                              <tr>
                                <td><?=Yii::t('common','Mobile')?> : <?=$model->salepeople->mobile_phone;?></td>
                              </tr>
                            </tbody>                              
                          </table>
                      </div>                    
                  </div>
                  <div class="tab-pane" id="my-item-sales">
                      <h4><?=Yii::t('common','My item sales')?></h4>
                      <div id="table-item-sales" class="mt-10"></div>               
                  </div>
                <?php } ?>
                <!-- /.tab-pane -->
                
                

              </div>
              <!-- /.tab-content -->
            </div>
            <!-- /.nav-tabs-custom -->

          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
 
 
        </section>
        <!-- /.content -->               

        <?php ActiveForm::end(); ?>  
          
    </div>
 
    
<?php $this->registerJsFile(Yii::getAlias('@web').'/js/jquery.validate.js', ['depends' => [\yii\web\JqueryAsset::className()]]);?>

<?php 
// https://igorlino.github.io/elevatezoom-plus/examples.htm#
$this->registerJsFile('https://cdn.rawgit.com/igorlino/elevatezoom-plus/1.1.6/src/jquery.ez-plus.js',['depends' => [\yii\web\JqueryAsset::className()]]); 
?>

<?php
$Yii = 'Yii';
$js =<<<JS

  $(document).ready(function(){

    $('div.btn-file').fadeOut(400, function() {
      $('div.btn-file').prepend('<img class="img-responsive" src="{$model->picture}" id="img-preview-photo">');    
    }).fadeIn(400);  

  });

  $('#gender-man').on('click',function(){
    $(this).attr('class','btn btn-info');
    $('#gender-woman').attr('class','btn btn-default');
    $('#salespeople-gender').val($(this).attr('data-key'));
  });

  $('#gender-woman').on('click',function(){
    $(this).attr('class','btn btn-info');
    $('#gender-man').attr('class','btn btn-default');
    $('#salespeople-gender').val($(this).attr('data-key'));
  });


  function readURL(input,div) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(div)
            .fadeOut(400, function() {
                $(div).attr('src', e.target.result);
                        
            })
            .fadeIn(400);
            
        }
        reader.readAsDataURL(input.files[0]);
    }
  }



  $("#profile-photo").change(function(){
    readURL(this,'#img-preview-photo');
  });



  $('body').on('change','#confirm_password',function(){

  var validator = $("#profile-user").validate({
      rules: {
        password: 'required',
        confirm_password: {
              equalTo: '#password'
          }
      },
      messages: {
          password: '{$Yii::t('common','Enter Password')}',
          confirm_password: '{$Yii::t('common','Enter Confirm Password Same as Password')}'
      }
  });
  if (validator.form()) {
      console.log('validate');
  }
  });


  $("#maps").ezPlus({
      zoomType: 'lens',
      lensShape: 'round',
      lensSize: 200,
  });


JS;

$this->registerJS($js,\yii\web\View::POS_END);
?>



<div class="modal fade" id="modal-item-info">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-green">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Item Info</h4>
            </div>
            <div class="modal-body table-responsive">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
            </div>
        </div>
    </div>
</div>

<?php 
 
$Yii = 'Yii';
$id  = $Rules->sale_id;
 
$jss=<<<JS

let state = {
    progress : false,
    data : []
};

const loadingDiv = `
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> {$Yii::t("common","Calculating data please wait a minute")} .... </div>
            <h4 class="years-callulate"></h4>
            <img src="images/icon/loader2.gif" height="122"/>             
            <h4 class="count-time"></h4>
        </div>`;

$('body').on('click','#myBtn',function(){
    topFunction();
});



const getApi  = (id, callback) => {
    fetch("?r=customers/responsible/get-sale-items", {
        method: "POST",
        body: JSON.stringify({id:id}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {
        callback(res);
    })
    .catch(error => {
        console.log(error);        
    });
}
 
 
const renderTable = () => {
    let id = '{$id}';
    
    getApi(id, res => {
        let html = `<table class="table" id="export_table">
                        <thead>
                            <tr class="bg-black">
                                <th class="text-center  hidden-xs" style="width:30px;">{$Yii::t('common','#')}</th>
                                <th class="text-center">{$Yii::t('common','Image')}</th>
                                <th >{$Yii::t('common','Product Name')}</th>                         
                                <th class="text-right  hidden-xs">{$Yii::t('common','Quantity')}</th>
                            </tr>
                        </thead>
                        <tbody>
                    `;
            res.items.map((model, key) => {
                html+= `<tr data-key="` + model.id + `" data-name="` + model.name + `">
                            <td class="hidden-xs">  ` + (key + 1) + ` </td>
                            <td  class="item-info pointer text-center" style="width:100px;">
                                    <div><img src="` + model.img + `" class="img-responsive" /></div>
                                    <div style="font-size:9px;">` + model.code + `</div>
                            </td>
                            <td>
                                <div class="mb-10">` + model.name + `</div>
                                <div class="hidden-sm hidden-md hidden-lg pull-right mt-10"> {$Yii::t('common','Quantity')} : ` + model.qty + `</div> 
                            </td>
                            <td class="text-right hidden-xs"> ` + model.qty + ` </td>
                        </tr>`;
            })

        html+= '</tbody></table>';       
        $('#table-item-sales').html(html);
        var table = $('body').find('#export_table').DataTable({
                        "paging": true,
                        "searching": true
                    }); 
    });
}

$(document).ready(function(){
    renderTable();
})

let getInvApi = (id, callback) => {
    fetch("?r=items/ajax/get-item-in-inv-by-sale", {
        method: "POST",
        body: JSON.stringify({id:id, sale:'{$id}'}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {
        callback(res);
    })
    .catch(error => {
        console.log(error);
    });
}


let renderTableInv = (data, callback) => {
    let html = `<table class="table table-bordered font-roboto" id="export_table_detail">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th style="min-width:75px;">{$Yii::t('common','Date')}</th>
                            <th style="min-width:90px;">{$Yii::t('common','Sale Order')}</th>
                            <th style="min-width:90px;">{$Yii::t('common','Tax Invoice')}</th>
                            <th class="text-right">{$Yii::t('common','Quantity')}</th>
                            <th class="text-right">{$Yii::t('common','Price')}</th>
                            <th class="text-right" style="min-width:70px;">{$Yii::t('common','Total')}</th>
                        </tr>
                    </thead>
    
    `;
    function compare( a, b ) {
        if ( a.no > b.no ){
            return -1;
        }
        return 0;
    }

    data.sort( compare );   
    data.map((model, key) => {
        html+= `<tr>
                    <td>` + (key + 1) + `</td>
                    <td>` + model.date + `</td>
                    <td><a href="?r=SaleOrders%2Fsaleorder%2Fview&id=` + model.soId + `" target="_blank">` + model.so + `</a></td>
                    <td><a href="?r=accounting%2Fposted%2Fread-only&id=` + model.id + `" target="_blank">` + model.no + `</td>
                    <td class="text-right bg-yellow">` + number_format((model.qty).toFixed(0)) + `</td>
                    <td class="text-right">` + number_format((model.price).toFixed(0)) + `</td>
                    <td class="text-right">` + number_format((model.qty * model.price).toFixed(0)) + `</td>
                </tr>`;
    })
        html+= '</table>';

    callback({html:html});
}

$('body').on('click','.item-info',function(){
    let id      = $(this).closest('tr').data('key');
    let name    = $(this).closest('tr').attr('data-name');
    $('#modal-item-info .modal-body').html(loadingDiv);
    getInvApi(id, res => {        
        renderTableInv(res, render => {     
            $('#modal-item-info .modal-title').html(name);                      
            $('#modal-item-info .modal-body').html(render.html);
            var table = $('body').find('#export_table_detail').DataTable({
                        "paging": false,
                        "searching": false
                }); 
        });
    })
    $('#modal-item-info').modal('show');
})


JS;
$this->registerJS($jss,\yii\web\View::POS_END);
?>
<?php $this->registerCssFile('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');?>
<?php $this->registerJsFile('//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
