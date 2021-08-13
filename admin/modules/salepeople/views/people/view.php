<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

use common\models\SaleGroup;

/* @var $this yii\web\View */
/* @var $model common\models\SalesPeople */

$this->title = $model->name.' '.$model->surname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sales Peoples'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .content{
        background:#edf0f5;
    }
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sales-people-view">



    <!-- Main content -->
    <section class="content">

      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?=$model->getPicture();?>" alt="User profile picture">

              <h3 class="profile-username text-center"><?=$model->name.' '.$model->surname ?></h3>

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

              <a href="#" class="btn btn-primary btn-block"><b>Summary</b></a>
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
                <?=Html::img($model->sign,['class'=>'img-responsive','style'=>'max-width:150px;margin-top:15px;'])?>
           
              </p>

              <hr>

              <strong><i class="fa fa-file-text-o margin-r-5"></i> <?=Yii::t('common','Notes')?></strong>

              <p>  
                <i class="fab fa-line text-success fa-2x"></i> <?=$model->line_id?>
              </p>
              <?= Html::a('<i class="fa fa-trash-o" ></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger-ew pull-right',
                        'data' => [
                            'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                ]) ?>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#infomation" data-toggle="tab"><?=Yii::t('common','Infomation')?></a></li>
              <li><a href="#customer" data-toggle="tab"><?=Yii::t('common','Customer')?></a></li>
              <li><a href="#settings" data-toggle="tab"><?=Yii::t('common','Settings')?></a></li>
              <li><a href="#timeline" data-toggle="tab"><?=Yii::t('common','Timeline')?></a></li>
              
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="infomation">
                <!-- DetailView -->
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        
                        [
                            'label' => Yii::t('common','Member Id'),
                            'value' => $model->id
                        ],
                        'code',
                         
                        [
                          'label' => Yii::t('common','Gender'),
                          'value' => Yii::t('common',$model->gender)
                      ],
                        'prefix',
                        'name',
                        'surname',
                        'nickname',
                        'salegroup:html',                             
                        'company.name',
                        'tax_id',
                        'position',
                        'address',
                        'address2',
                        'postcode',
                        'date_added',
                        
                    ],
                ]) ?>



                <!-- /.DetailView -->
              </div>
              <!-- /.tab-pane -->  
              <div class="tab-pane table-responsive" id="customer">
              <div class="col-sm-12 text-right">
              
              <?php
                echo ExportMenu::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => [
                              'customer.code',
                              'customer.name',
                              'customer.locations.province',
                              'customer.contact',
                              [
                                'label' => Yii::t('common','Phone'),
                                'format' => 'html',
                                'contentOptions' => ['style' => 'max-width:100px; overflow-x: auto;'],
                                'value' => 'customer.phone'
                              ],
                            ],
                            'columnSelectorOptions'=>[
                                'label' => 'Columns',
                                'class' => 'btn btn-success-ew'
                            ],
                            'fontAwesome' => true,
                            'dropdownOptions' => [
                                'label' => 'Export All',
                                'class' => 'btn btn-primary-ew'
                            ],
                            'exportConfig' => [
                                ExportMenu::FORMAT_HTML => false,
                            ],
                            'styleOptions' => [
                                ExportMenu::FORMAT_PDF => [
                                    'font' => [
                                        'family' => ['garuda'],
                                            //'bold' => true,
                                            'color' => [
                                                'argb' => 'FFFFFFFF',
                                        ],
                                    ],
                                ],
                            ],
                            'filename' => Yii::t('common','Customers'),
                            //'encoding' => 'utf8',
                        ]);
                ?>
              </div>
              <?= GridView::widget([
                  'dataProvider' => $dataProvider,
                  //'filterModel' => $searchModel,
                  'columns' => [
                      ['class' => 'yii\grid\SerialColumn'],

                      'customer.code',
                      'customer.name',
                      'customer.locations.province',
                      'customer.contact',
                      [
                        'format' => 'html',
                        'contentOptions' => ['style' => 'max-width:100px; overflow-x: auto;'],
                        'value' => 'customer.phone'
                      ],
                      ],
                  ]); ?>
              </div>
              <!-- /.tab-pane -->            
              
              <div class="tab-pane" id="settings">
                <form class="form-horizontal">
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Name</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputName" placeholder="Name">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Name</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputName" placeholder="Name">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputExperience" class="col-sm-2 control-label">Experience</label>

                    <div class="col-sm-10">
                      <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputSkills" class="col-sm-2 control-label">Skills</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                  
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-primary">Submit</button>
                      
                    </div>
                    
                  </div>
                </form>
                
              </div>
              <!-- /.tab-pane -->

              <div class="tab-pane" id="timeline">
                <!-- The timeline -->
                <ul class="timeline timeline-inverse">
                  <!-- timeline time label -->
                  <li class="time-label">
                        <span class="bg-red">
                          10 Feb. 2014
                        </span>
                  </li>
                  <!-- /.timeline-label -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-envelope bg-blue"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>

                      <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                      <div class="timeline-body">
                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                        weebly ning heekya handango imeem plugg dopplr jibjab, movity
                        jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                        quora plaxo ideeli hulu weebly balihoo...
                      </div>
                      <div class="timeline-footer">
                        <a class="btn btn-primary btn-xs">Read more</a>
                        <a class="btn btn-danger btn-xs">Delete</a>
                      </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-user bg-aqua"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>

                      <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request
                      </h3>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-comments bg-yellow"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>

                      <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>

                      <div class="timeline-body">
                        Take me to your leader!
                        Switzerland is small and neutral!
                        We are more like Germany, ambitious and misunderstood!
                      </div>
                      <div class="timeline-footer">
                        <a class="btn btn-warning btn-flat btn-xs">View comment</a>
                      </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <!-- timeline time label -->
                  <li class="time-label">
                        <span class="bg-green">
                          3 Jan. 2014
                        </span>
                  </li>
                  <!-- /.timeline-label -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-camera bg-purple"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> 2 days ago</span>

                      <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>

                      <div class="timeline-body">
                        <img src="http://placehold.it/150x100" alt="..." class="margin">
                        <img src="http://placehold.it/150x100" alt="..." class="margin">
                        <img src="http://placehold.it/150x100" alt="..." class="margin">
                        <img src="http://placehold.it/150x100" alt="..." class="margin">
                      </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <li>
                    <i class="fa fa-clock-o bg-gray"></i>
                  </li>
                </ul>
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
 
 
 

   

</div>
