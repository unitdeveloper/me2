<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;


// usage without model


use kartik\widgets\DatePicker;
use admin\modules\apps_rules\models\SysRuleModels;


$workDate = Yii::$app->session->get('workdate');

?>
<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fas fa-home"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fas fa-cogs"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="active tab-pane" id="control-sidebar-home-tab">

        <div class="form-group" ng-controller="workdateCtrl">
            <label class="control-sidebar-subheading" >
              <div class="dateToday" data-date="{{dateToday|date:'yyyy-M-dd'}}"></div>
                <div>
                <?= Yii::t('common','AUTO WORKDATE')?>

                  <input type="checkbox" class="pull-right workdate-option" ng-click="workDate($event)" ng-model="setDate" ng-init="setDate=<?=(Yii::$app->session->get('workdate')==date('Y-m-d'))? 'true': 'false';?>"  />
                </div>

            </label>
            <div class="text-right">
              <div class="col-xs-8 no-padding">
                <?php
                echo DatePicker::widget([
                  'name' => 'workingdate',
                  'value' => $workDate,
                  'type' => DatePicker::TYPE_COMPONENT_APPEND,
                  'options' => ['placeholder' => 'Select work date ...','ng-disabled' => 'setDate',],
                  'pluginOptions' => [
                    'format' => 'yyyy-m-dd',
                    //'todayHighlight' => true,
                    'autoclose'=>true,
                  ],
                  'removeButton' => false,
                  'pickerButton' => [
                    'icon'=>'th',
                  ],
                ]);
                ?>
              </div>
              <div class="col-xs-4 no-padding">
                <button type="button"
                class="btn <?=(Yii::$app->session->get('workdate')==date('Y-m-d'))? 'btn-success': 'btn-primary';?> btn-flat save-workdate"
                ng-disabled="setDate"
                ng-click="newWorkdate($event);"
                ng-model="workdateInput"><?=(Yii::$app->session->get('workdate')==date('Y-m-d'))? 'Auto': 'Save';?></button>
              </div>
            </div>
            <br />



            <?php if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','Finance','report','common','menu'))): ?>
              <label class="control-sidebar-subheading" style="margin-top:50px;"><?=Yii::t('common','Sale People')?></label>
              <div class="row">
                <div class="col-xs-12">
                  <?php
                                      
                        $Sales = \common\models\SalesPeople::find()
                        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->andWhere(['status' => 1])
                        ->orderBy(['code' => SORT_ASC])
                        ->all();

                        $salespeople = arrayHelper::map($Sales,'id', function ($element) {
                                        return '['.$element['code'] .']  ' .$element['name'];
                                    });

                    echo Html::dropDownList('sale_people_on_right_menu',
                        [
                          'value' => Yii::$app->session->get('Rules')['sale_id']
                        ],
                        $salespeople,
                        [
                          'class' => 'form-control',                                                 
                          'prompt'=> Yii::t('common','- เลือก Sales -'),
                      ]
                    );

                    ?>
                </div>                
              </div> 
            <?php endif; ?>


            <?php if(in_array(\Yii::$app->controller->Route,['SaleOrders/saleorder/update','SaleOrders/saleorder/view'])): ?>
              <label class="control-sidebar-subheading" style="margin-top:50px;"><?=Yii::t('common','Print')?></label>
              <div class="row " >
                <div class="col-xs-6">
                  <a href='index.php?r=SaleOrders/saleorder/print-page&id=<?=@$_GET['id']?>&footer=all&doc=ใบเสนอราคา&docEn=Sale Quotation' target='_blank' class="btn btn-success-ew btn-flat">
                    <i class="menu-icon fa fa-print text-warning"></i> <?=Yii::t('common','Print Quotation')?>
                  </a>
                </div>
                <div class="col-xs-6">
                  <a href='<?= Url::toRoute(['print-page','id' => @$_GET['id'],'footer'=>'all'])?>' target='_blank' class="btn btn-success-ew btn-flat">
                    <i class="menu-icon fa fa-print text-warning"></i> <?=Yii::t('common','Sale Order')?>
                  </a>
                </div>
              </div>                    
            

            <label class="control-sidebar-subheading theme-selector" style="margin-top:50px;"><?=Yii::t('common','Themes')?></label>
              <div class="row " >
                <div class="col-xs-6">
                  <a href="javascript:void(0);" id="change-theme" class="btn btn-primary" data-id="0"><i class="fas fa-laptop"></i> Laptop</a>
                </div>
                <div class="col-xs-6">
                  <a href="javascript:void(0);" id="change-theme" class="btn btn-success" data-id="1"><i class="fas fa-tablet-alt"></i> Mobile</a>
                </div>
              </div>    
              
              <?php endif; ?>
        </div>
        <!-- /.form-group -->

        


      </div>
      <!-- /.tab-pane -->

      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-settings-tab">
          <form method="post">
              <h3 class="control-sidebar-heading"><?=Yii::t('common','History Update')?></h3>

              <div class="form-group">
                  <label class="control-sidebar-subheading">
                      <?= Yii::t('common','Last Update.')?> <?=date('Y-m-d'); ?>
                      <input type="checkbox" class="pull-right" disabled="true" checked/>
                  </label>

                  <p>
                      <a href="index.php?r=site/update"><?=Yii::t('common','System update')?></a>
                      V4.1:21042020
                  </p>
              </div>
              <!-- /.form-group -->


          </form>
      </div>
      <!-- /.tab-pane -->
    </div>
</aside><!-- /.control-sidebar -->