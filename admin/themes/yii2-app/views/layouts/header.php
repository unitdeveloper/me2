<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Register;
use kartik\icons\Icon;
use common\models\Company;
$user = Yii::$app->user->identity;
use admin\models\FunctionCenter;
$Fnc = new FunctionCenter();
// $Fnc->RegisterRule();
// if (Yii::$app->session->get('Rules')['rules_id'] == '') {
//     $Fnc->RegisterRule();
// }

// if (Yii::$app->session->get('Rules')['comp_id']){
//    $company = Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
// }

// ถ้าไม่มี 'index.php?r=xxx/xxx' ให้ใส่เข้าไป (เพื่อใช้สำหรับเปลี่ยนภาษา)
(isset($_GET['r']))?: Yii::$app->request->url = 'index.php?r=site%2Findex/';


?>

<header class="main-header" ng-controller="headingCtrl">

    <?php echo Html::a('<span class="logo-mini"><i class="fas fa-home" aria-hidden="true"></i></span><span class="logo-lg">' . Yii::$app->name . '</span>', ['/site/index'], ['class' => 'logo hidden-xs']) ?>
     
    <nav class="navbar navbar-static-top" role="navigation" >
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" id="offcanvas">
            <span class="sr-only">eWiNL Navigation</span>
        </a>
        <div class="Navi-Title" ng-bind="Title"></div>
        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                 <li class="dropdown messages-menu hidden-xs">
                    <a href="#" id="minimize" class="nav-link-expand">
                    <i class="fa fa-window-restore" aria-hidden="true" ></i>
                    </a>
                </li>
                <div class="pull-left hidden-xs show-workdate" ng-init="workdate='<?=Yii::$app->session->get('workdate')?>'">
                    <span id="date" ng-bind="workdate"></span> <span id="time" ng-bind="clock | date:'HH:mm:ss'"></span>
                </div>

                <select class="selectpicker language-picker pull-left hidden-sm hidden-md hidden-lg" data-width="fit">
                    <option data-content='<span class="flag-icon flag-icon-th"></span>  ไทย'      <?=(Yii::$app->language=='th-TH')? 'selected':''; ?> >th-TH</option>
                    <option data-content='<span class="flag-icon flag-icon-gb"></span>  English'  <?=(Yii::$app->language=='en-EN')? 'selected':''; ?> >en-EN</option>
                    <option data-content='<span class="flag-icon flag-icon-cn"></span>  中文'     <?=(Yii::$app->language=='zh-CN')? 'selected':''; ?> >zh-CN</option>
                    <option data-content='<span class="flag-icon flag-icon-la"></span>  ພາສາລາວ'  <?=(Yii::$app->language=='la-LA')? 'selected':''; ?> >la-LA</option>
                </select>

                <li class="dropdown messages-menu hidden-xs">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="far fa-envelope"></i>
                        <span class="label label-success warning-message"><!></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have <span class="warning-message">?</span> messages</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                            </ul>
                        </li>
                        <li class="footer"><a href="#">See All Messages</a></li>
                    </ul>
                </li>

                <li class="dropdown notifications-menu hidden-xs">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="far fa-bell"></i>
                        <span class="label label-danger warning-amount"><!></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header ">You have <span class="warning-amount">?</span> notifications</li>
                        <li class="notice-body">
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu font-roboto">
                                <?php
                                    // $ulWarn = '';  // Disabled 24/09/2020 จะไม่มีการรับสมัครจากหน้าเว็บแล้ว
                                    // foreach (Register::find()->where(['status' => 'pending'])->all() as $value) {
                                    //     # code...

                                    //     $ulWarn.= '<li>
                                    //                     <a href="index.php?r=company/company/pending&id='.$value->id.'">
                                    //                         <i class="'.$value->regis->icon.'"></i>
                                    //                             '.$value->regis_name.' / '.$value->regis->name.'

                                    //                     </a>
                                    //                 </li>'."\r\n";
                                    // }

                                    // echo $ulWarn;
                                ?>
                                 
                            </ul>
                        </li>
                        <li class="footer"><a href="#">View all</a></li>
                    </ul>
                </li>
                <!-- Tasks: style can be found in dropdown.less -->
                <li class="dropdown tasks-menu hidden-xs">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-flag-checkered"></i>
                        <span class="label label-danger warning-task"><!></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have <span class="warning-task"><img src="images/icon/mini-loader.gif"></span> tasks</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Design some buttons
                                            <small class="pull-right">20%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-aqua" style="width: 20%"
                                                 role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                 aria-valuemax="100">
                                                <span class="sr-only">20% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                               
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="#">View all tasks</a>
                        </li>
                    </ul>
                </li>
                <!-- User Account: style can be found in dropdown.less -->
 
                <li class="dropdown messages-menu hidden-xs">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?=(Yii::$app->language=='th-TH')? '<span class="flag-icon flag-icon-th"></span>':'';?>
                        <?=(Yii::$app->language=='en-EN')? '<span class="flag-icon flag-icon-gb"></span>':'';?>
                        <?=(Yii::$app->language=='zh-CN')? '<span class="flag-icon flag-icon-cn"></span>':'';?>
                        <?=(Yii::$app->language=='la-LA')? '<span class="flag-icon flag-icon-la"></span>':'';?>
                        
                        <span class="label label-success warning-message"><!></span>
                    </a>
                    <ul class="dropdown-menu ">
                        <li class="header"> Language</li>
                        <li>
                            <ul class="menu">
                                
                                <li><!-- Task item -->
                                    <a href="<?=Yii::$app->request->url?>&language=th-TH">
                                        <span class="flag-icon flag-icon-th"></span>  ไทย 
                                    </a>
                                </li>
                                <li><!-- Task item -->
                                    <a href="<?=Yii::$app->request->url?>&language=en-EN">
                                        <span class="flag-icon flag-icon-gb"></span>  English
                                    </a>
                                </li>
                                <li><!-- Task item -->
                                    <a href="<?=Yii::$app->request->url?>&language=zh-CN">
                                        <span class="flag-icon flag-icon-cn"></span>  中文
                                    </a>
                                </li>
                                <li><!-- Task item -->
                                    <a href="<?=Yii::$app->request->url?>&language=la-LA">
                                        <span class="flag-icon flag-icon-la"></span>  ພາສາລາວ
                                    </a>
                                </li>
                               
                            </ul>
                        </li>
                         
                    </ul>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <li>
                    <?php 
                        $current_url = \Yii\helpers\Url::current();
                        if(\Yii::$app->user->identity){
                            $fav = \common\models\FavoriteMenu::findOne(['url' => $current_url, 'user_id' => Yii::$app->user->identity->id]);
                            echo Html::a($fav != null ? '<i class="fas fa-star text-yellow"></i>' : '<i class="far fa-star"></i>','#',[
                                'class' => 'set-favorite-menu',
                                'data-url' => $current_url,
                                'data-status' => $fav != null ? 'on' : 'off'
                            ]);
                        }
                    ?>                     
                </li>

                <li>
                    <a href="#" data-toggle="control-sidebar" class="open-slide-right"><i class="fas fa-cogs"></i></a>
                </li>

            </ul>
        </div>
    </nav>

</header>
<div class="modal fade" id="modal-show-duplicate-document">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Duplicate Document')?></h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=Yii::t('common','Close')?></button>
                 
            </div>
        </div>
    </div>
</div>