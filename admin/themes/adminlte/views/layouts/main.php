
<?php

//use app\assets\FontAsset;
//FontAsset::register($this);

//use app\assets\AdminLtePluginAsset;
//AdminLtePluginAsset::register($this);

use yii\helpers\Html;
use richardfan\widget\JSRegister;
/* @var $this \yii\web\View */
/* @var $content string */


if (Yii::$app->controller->action->id === 'login') {
/**
 * Do not use this code in your template. Remove it.
 * Instead, use the code  $this->layout = '//main-login'; in your controller.
 */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    $user = Yii::$app->user->identity;
    if(empty($user)){

        return Yii::$app->response->redirect('../../frontend/web/index.php?r=user/recovery/request');
        //var_dump($user);
        exit();
    }

    $Profile = $user->profile;



    if (class_exists('app\assets\AppAsset')) {
        app\assets\AppAsset::register($this);
    } else {
        admin\assets\AppAsset::register($this);
    }

    app\assets\PeneRarAsset::register($this);
    dmstr\web\AdminLteAsset::register($this);
    app\assets\AdminLtePluginAsset::register($this);
    app\assets\SweetalertAsset::register($this);


    $session = Yii::$app->session;
    $collapse = $session->get('collapse');


    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');


    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" ng-app="ewApp">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />


        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-title" content="EIL">

        <link rel="apple-touch-icon" sizes="57x57" href="images/icon/apple/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="images/icon/apple/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="images/icon/apple/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="images/icon/apple/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="images/icon/apple/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="images/icon/apple/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="images/icon/apple/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="images/icon/apple/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="images/icon/apple/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="images/icon/apple/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="144x144"  href="images/icon/apple/android-icon-144x144.png">
        <link rel="icon" type="image/png" sizes="32x32" href="images/icon/apple/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="images/icon/apple/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="images/icon/apple/favicon-16x16.png">
        <link rel="manifest" href="images/icon/apple/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="images/icon/apple/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">

         <link rel="shortcut icon" type="image/png" href="images/icon/apple/favicon-16x16.png"/>

         <link rel="apple-touch-icon-precomposed" href="images/icon/apple/favicon-16x16.png">
         <link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/icon/apple/apple-icon-72x72.png">
         <link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/icon/apple/apple-icon-114x114.png">
         <link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/icon/apple/apple-icon-144x144.png">





        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>



        <style type="text/css">
            body{
                font-family: 'Kanit', sans-serif;
            }
            .content-wrapper, .right-side {
                min-height: 100%;
                background-color: #fff;
                z-index: 800;
            }


            .toggle.android { border-radius: 0px;}
            .toggle.android .toggle-handle { border-radius: 0px; }
        </style>


        <script src="js/jquery-2.1.3.min.js" type="text/javascript"></script>
        <script src="js/jquery.cookie.js" type="text/javascript"></script>

        <script src="js/angular.min.js"></script>
        <script src="js/angular/angular-route.min.js"></script>
        <script src="js/angular-animate.min.js"></script>
        <script src="js/angular-sanitize.min.js"></script>
        <script src="js/ui-bootstrap-tpls-2.5.0.min.js"></script>
        <script src="plugins/nprogress-master/nprogress.js"></script>

        <link href='plugins/nprogress-master/nprogress.css' rel='stylesheet' />

        <script>
          $(function(){
              NProgress.start();
          })
          var app = angular.module("ewApp", ['ngRoute','ngAnimate', 'ngSanitize', 'ui.bootstrap']);
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
          
          ga('create', 'UA-99888323-1', 'auto');
          ga('send', 'pageview');

        </script>

    </head>
    <body class="hold-transition <?= \dmstr\helpers\AdminLteHelper::skinClass() ?> <?=$collapse?>" >



    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset,'Profile' => $Profile]
        ) ?>

        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset,'Profile' => $Profile]
        )
        ?>

        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset,'Profile' => $Profile]
        ) ?>

    </div>


    <?php $this->endBody() ?>

    </body>

    <?php JSRegister::begin(); ?>
    <script>//$.widget.bridge('uibutton', $.ui.button);
    $(function(){ setTimeout(function() {  NProgress.done();  $('.fade').removeClass('out');  }, 1000); });
    //if(location.protocol != 'https:'){location.href = 'https:' + window.location.href.substring(window.location.protocol.length);}
    </script>
    <?php JSRegister::end(); ?>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
