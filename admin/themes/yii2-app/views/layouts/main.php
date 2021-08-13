<?php
    use yii\helpers\Html;
    
    /* @var $this \yii\web\View */
    /* @var $content string */

    /*
    * Setup workdate
    */
    if(empty(Yii::$app->session->get('workdate'))) Yii::$app->session->set('workdate',date('Y-m-d'));
    if(empty(Yii::$app->session->get('worktime'))) Yii::$app->session->set('worktime',date('H:i:s'));


    /*
    * Font setup
    *  
    */
    $font = "'Kanit', sans-serif !important";
    if(empty(Yii::$app->session->get('systemfont'))) Yii::$app->session->set('systemfont','font-family:'.$font.';');

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

    $this->render('auth');
    admin\assets\AdminAsset::register($this);
    app\assets\PeneRarAsset::register($this);
    admin\assets\AdminLteAsset::register($this);
    app\assets\SweetalertAsset::register($this);
    


    $collapse = Yii::$app->session->get('collapse');

    

    ?>

    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" ng-app="ewApp">
    <head>
        
        <meta charset="<?= Yii::$app->charset ?>" data="charset"/>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0,minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
        <meta name="HandheldFriendly" content="true">
        <?= Html::csrfMetaTags() ?>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-title" content="EWIN">

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
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <style>html{-webkit-text-size-adjust: none;} body{ <?=Yii::$app->session->get('systemfont')?>} </style>
        <script>           
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            
            ga('create', 'UA-99888323-1', 'auto');
            ga('send', 'pageview');
        </script>
    </head>
    <body class="hold-transition skin-green sidebar-mini <?=$collapse?>" >
    <?php $this->beginBody() ?>
    <div class="wrapper">
    <?= \yii2mod\notify\BootstrapNotify::widget(); ?>
        <?= $this->render('header.php') ?>

        <?= $this->render('left.php')?>

        <?= $this->render(
            'content.php',
            ['content' => $content]
        ) ?>

    </div>
    <?php

        $this->registerJs("
            
            $(document).ajaxStart(function () {
                Pace.restart()
            })
            
            window.addEventListener('load',function() {

                setTimeout(function(){
            
                window.scrollTo(0, 0);
            
                }, 0);
            
            });

            
        ");
    ?>
    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>
    <?php } ?>

