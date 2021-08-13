<?php

 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;

 
?>
<?php
 


$session = Yii::$app->session;
$Actions = $session->get('Method'); 

# actionIndex,actionView, actionUpdate, actionCreate
switch ($Actions) {
    case 'actionUpdate':
        $editUrl  = Url::toRoute(['update','id' => @$_GET['id']]);
        $printUrl  = Url::toRoute(['print-page','id' => @$_GET['id'],'footer'=>'all']);
        $btnSave = 'onclick="$(\'form\').submit();"';
        break;
    case 'actionCreate':
        $editUrl = '#';
        $printUrl = '#';
        $btnSave = 'onclick="$(\'form\').submit();"';
        break;
    case 'actionView':
        $editUrl  = Url::toRoute(['update','id' => @$_GET['id']]);
        $printUrl  = Url::toRoute(['print-page','id' => @$_GET['id'],'footer'=>'all']);
        $btnSave = 'style="visibility:hidden;"';
        break;  
        
    default:
        $editUrl = '#';
        $printUrl = '#';
        $btnSave = 'style="visibility:hidden;"';
        break;
}

?>


<div class="content-wrapper">

<!--
    <section class="content-header">
        <?php if (isset($this->blocks['content-header'])) { ?>
            <h1><?= $this->blocks['content-header'] ?></h1>
        <?php } else { ?>
            <h1>
                <?php
                if ($this->title !== null) {
                    echo \yii\helpers\Html::encode($this->title);
                } else {
                    echo \yii\helpers\Inflector::camel2words(
                        \yii\helpers\Inflector::id2camel($this->context->module->id)
                    );
                    echo ($this->context->module->id !== \Yii::$app->id) ? '<small>Module</small>' : '';
                } ?>
            </h1>
        <?php } ?>

        <?=
        Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
    </section>
-->
    <section class="content">
        
        <div class="load-ewin"></div> 

        <?= Alert::widget() ?>
        <?= $content ?>
    </section>
    
</div>

<style type="text/css">
    .throw-status{
        position: fixed;
        right: 20px;
        top: 20px;
    }
</style>
<div class="throw-status"></div> 

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        
        <!-- Home tab content -->
        <div class="active tab-pane" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading">Recent Activity</h3>
            <ul class='control-sidebar-menu'>

                <?php if(in_array(\Yii::$app->controller->Route,['SaleOrders/saleorder/update','SaleOrders/saleorder/view'])): ?>
                <li>
                    <a href='<?= $printUrl?>' target='_blank'>
                        <i class="menu-icon fa fa-print bg-green"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading"><?= Yii::t('common', 'Print') ?> </h4>

                            <p><?=Yii::t('common','Sale Order')?> </p>
                        </div>
                    </a>
                </li>


                
                <li>
                    <a href='index.php?r=SaleOrders/saleorder/print-page&id=<?=@$_GET['id']?>&footer=all&doc=ใบเสนอราคา&docEn=Sale Quotation' target='_blank'>
                         <i class="menu-icon fa fa-print bg-orange"></i>

                        <div class="menu-info">
                           <h4 class="control-sidebar-subheading"><?=Yii::t('common','Print Quotation')?></h4>
                           <p><?=Yii::t('common','Sale Quotation')?></p>
                        </div>
                    </a>
                </li>
                <?php endif; ?>


                <hr style="width:95%;">
                <!-- <li class="dropdown messages-menu ">
                    <?php
                    echo Html::a('<span id="CH"></span> &nbsp;&nbsp;  CH', Url::current(['language' => 'zh-CN']), ['class' => 'ch-CH']);
                   
                    ?>
                </li> -->
                <li class="dropdown messages-menu ">
                    <a href='<?= Url::current(['language' => 'en-EN']) ?>'>
                        <i class="menu-icon fa fa-flag bg-light-blue"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading"> English </h4>

                            <p>Change Language </p>
                        </div>
                    </a>
                     
                </li>

            </ul>
            <ul class='control-sidebar-menu'>
                <li class="dropdown messages-menu ">
                     <a href='<?= Url::current(['language' => 'th-TH']) ?>'>
                        <i class="menu-icon fa fa-flag bg-light-blue"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading"> ภาษาไทย </h4>

                            <p>เปลี่ยนภาษา</p>
                        </div>
                    </a>
                      
                </li>

                <li class="dropdown messages-menu ">
                     <a href='<?= Url::current(['language' => 'ch-CH']) ?>'>
                        <i class="menu-icon fa fa-flag bg-red"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading"> 简体中文 </h4>

                            <p>改变语言</p>
                        </div>
                    </a>
                     
                </li>

                <li class="dropdown messages-menu ">
                     <a href='<?= Url::current(['language' => 'la-LA']) ?>'>
                        <i class="menu-icon fa fa-flag bg-light-blue"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading"> ພາສາລາວ </h4>

                            <p>ປ່ຽນພາສາ</p>
                        </div>
                    </a>
                      
                </li>
                <!-- <li>
                    <a href='javascript::;'>
                        <i class="menu-icon fa fa-user bg-yellow"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                            <p>New phone +1(800)555-1234</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href='javascript::;'>
                        <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                            <p>nora@example.com</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href='javascript::;'>
                        <i class="menu-icon fa fa-file-code-o bg-green"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                            <p>Execution time 5 seconds</p>
                        </div>
                    </a>
                </li> -->
            </ul>
             

        </div>
        <!-- /.tab-pane -->

        <!-- Settings tab content -->
        <div class="tab-pane" id="control-sidebar-settings-tab">
            <form method="post">
                <h3 class="control-sidebar-heading">History Update</h3>

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Last Update. <?=date('Y-m-d'); ?>
                        <input type="checkbox" class="pull-right" disabled="true" checked/>
                    </label> 

                    <p>
                        <a href="index.php?r=site/update">ประวัติการ update ของระบบ</a>
                    </p>
                </div>
                <!-- /.form-group -->

                 
            </form>
        </div>
        <!-- /.tab-pane -->
    </div>
</aside><!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class='control-sidebar-bg'></div>
