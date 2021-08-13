<?php

namespace admin\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 * http://blog.neattutorials.com/angularjs-yii2-part-1-routing/
 */
class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'//use.fontawesome.com/releases/v5.0.9/css/all.css',
        '//use.fontawesome.com/releases/v5.1.0/css/all.css',
        'css/bootstrap-select.min.css',
        'css/ewin_load.css?v=5.6.22',
        'css/animate.css?v=2.12.27.1',
        'css/date-picker-custom.css?v=4.04.12',
        '//cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css',
        //'fontawesome/css/fontawesome-all.css',  
        '//cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css',       
        'css/jquery.rippleria.min.css',
        'plugins/pace/pace.min.css',
        'css/bs4.css?v=4.5.08'
    ];
    public $js = [
        'js/bootstrap-select.min.js',
        //'js/action_script.min.js?v=4.09.18',
        //'js/ghost.js?v=4',
        'js/action_script.js?v=4.09.19',
        'js/left.js?v=5.09.24.1',
        'plugins/pace/pace.min.js',    
        'js/easyNotify.js',
        //'core-js.min.js'
        //'js/screenfull.min.js',
    ];
    public $jsOptions = [
        'type' => 'text/javascript',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'admin\assets\AngularAsset',
        'admin\assets\JQueryAsset',
        'admin\assets\FontAsset',
        'admin\assets\BowerAsset',
        //'admin\assets\FontawesomeAsset'
    ];
}
