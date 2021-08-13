<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use common\models\Company;
use common\models\SaleGroup;

 
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
</style>
<div class="sales-people-form">
<?php $form = ActiveForm::begin(); ?>

    <!-- Main content -->
    <section class="content">

      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <div class="align-cener">
                <span class="btn btn-file">                                     
                    <img class="profile-user-img img-responsive img-circle" src="<?=$model->getPicture();?>" id='img-preview-photo'>                            
                    <input type="file" name="SalesPeople[photo]" id="salespeople-photo" >
                </span>
              </div>

              <h3 class="profile-username text-center"><?=$model->name.' '.$model->surname?></h3>

              <p class="text-muted text-center"><?=$model->position?></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Sale</b> <a class="pull-right"><?=$model->saleorder?></a>
                </li>
                <li class="list-group-item">
                  <b>Pending</b> <a class="pull-right"><?=$model->getSaleorder(['Checking','Release','Reject'])?></a>
                </li>
                <li class="list-group-item">
                  <b>Invoice</b> <a class="pull-right"><?=$model->getSaleorder(['Shiped','Invoiced','Close'])?></a>
                </li>
              </ul>

              <a href="#" class="btn btn-primary-ew btn-block"><b>Summary</b></a>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary">
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

              <p class="text-muted"><?=$model->salegroup?></p>

              <hr>

              <strong><i class="fa fa-pencil margin-r-5"></i> <?=Yii::t('common','Signature')?></strong>

              <p>
                <?= $form->field($model, 'sign_img')->fileInput(['maxlength' => true])->label(false); ?> 
           
              </p>
              <?=Html::img($model->sign,['class'=>'img-responsive','id' => 'img-preview-sign','style'=>'max-width:150px;margin-top:15px;'])?>

              <hr>

              <strong><i class="fa fa-file-text-o margin-r-5"></i> <?=Yii::t('common','Notes')?></strong>

              <p>  <i class="fab fa-line text-success fa-2x"></i> <span >{{lineid}}</span></p>
              <p class="sing-preview"></p>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#Info" data-toggle="tab"><?=Yii::t('common','Information')?></a></li>
              <li><a href="#settings" data-toggle="tab">Settings</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="Info">
                <!-- DetailView -->
                
                <div class="row">
                    <div class="col-sm-2">
                        <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-5">
                      <?= $form->field($model, 'tax_id')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-xs-5">
                        <label>Gender</label><br>
                        <div class="btn-group">
                          <label type="text" class="btn <?=($model->gender=='Man')?   'btn-info':'btn-default' ;?> " id="gender-man" data-key="Man"><i class="fas fa-male"></i> Man</label>
                          <label type="text" class="btn <?=($model->gender=='Woman')? 'btn-info':'btn-default' ;?> " id="gender-woman" data-key="Woman"><i class="fas fa-female"></i> Woman</label>
                        </div>
                        <?= $form->field($model, 'gender')->hiddenInput()->label(false) ?>

                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-2"><?= $form->field($model, 'prefix')->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Mr.')]) ?></div>
                    <div class="col-xs-4"><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></div>
                    <div class="col-xs-4"><?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?></div>
                    <div class="col-xs-2"><?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?></div>
                </div>
                

              

                
                

                <div class="row">
                  <div class="col-sm-6">
                  <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
                  </div>
                  <div class="col-sm-4">
                    <?= $form->field($model, 'mobile_phone')->textInput(['maxlength' => true]) ?>
                  </div>
                  <div class="col-sm-2" ng-init="lineid='<?=$model->line_id?>'">
                    <?= $form->field($model, 'line_id')->textInput(['maxlength' => true,'ng-model' => 'lineid']) ?>
                  </div>
                </div>  

                

                <div class="row">
                  <div class="col-sm-6">
                    <?= $form->field($model, 'address2')->textInput(['maxlength' => true]) ?>
                  </div>
                  <div class="col-sm-6">
                    <?= $form->field($model, 'postcode')->textInput(['maxlength' => true]) ?>
                  </div>
                </div>      
 



                <!-- /.DetailView -->
              </div>

              <div class="tab-pane" id="settings">
                 
                <div class="row">
                    <div class="col-sm-6">
                      <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-6">
                      <?php $model->sale_group = explode(',',$model->sale_group); ?>
                      <?= $form->field($model, 'sale_group')->dropDownList(
                                    ArrayHelper::map(SaleGroup::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->orderBy(['name' => SORT_ASC])->all(),'id','name'),
                                    [
                                        'data-live-search'=> "true",
                                        'class' => 'selectpicker form-control',
                                        'multiple'=>"multiple",
                                    ] 
                                ) ?>
                    </div>
                  </div>

                  
                

                    <div class="row">
                      <div class="col-sm-6">
                        <?php 

                          if(Yii::$app->user->identity->id==1)
                          {
                              $CompanyList = Company::find()
                                          ->orderBy(['name' => SORT_ASC])
                                          ->all();

                          }else {
                                  $CompanyList = Company::find()
                                                              ->where(['id' => Yii::$app->session->get('Rules')['comp_id']])
                                                              ->orderBy(['name' => SORT_ASC])
                                                              ->all();
                          }

                          if($model->user_id == '') $model->user_id = Yii::$app->user->identity->id;

                          ?>
                          <?= $form->field($model, 'comp_id')->dropDownList(
                                      ArrayHelper::map($CompanyList,'id','name'),
                                      [

                                          'data-live-search'=> "true",
                                          'class' => 'selectpicker form-control',
                                              
                                              
                                              
                                      ] 
                                  ) ?>
                          <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>
                      </div>
                      <div class="col-sm-6">
                        <label> </label>
                        <?php if($model->status=='') $model->status = 1; ?>
                        <?= $form->field($model, 'status')->checkBox(['class'=>'input-md','data-toggle'=>"toggle",
                        'data-style'=>"android", 'data-onstyle'=>"info"]) ?>
                      </div>
                    </div>
              </div>


              <div class="form-group text-right">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : '<i class="fa fa-save"></i> '.Yii::t('common', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
 
   

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJS("
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

              //$('#img-preview').attr('src', e.target.result).fadeIn('slow');

          }

          reader.readAsDataURL(input.files[0]);
      }
  }

  $(\"#salespeople-sign_img\").change(function(){
      readURL(this,'#img-preview-sign');
  });

  $(\"#salespeople-photo\").change(function(){
      readURL(this,'#img-preview-photo');
  });
");