<?php

namespace admin\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 * http://blog.neattutorials.com/angularjs-yii2-part-1-routing/
 */
class BtfourAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/sb-admin.css',
        'vendor/font-awesome/css/font-awesome.min.css',
        'vendor/bootstrap/css/bootstrap.min.css'
         
        
    ];
    public $js = [
        
        'js/bootstrap-select.min.js',
        //'js/action_script.min.js?v=2.12.27.2',
        'js/action_script.js?v=2.12.27.2',


    ];
    public $depends = [
        'yii\web\YiiAsset',
        // 'yii\bootstrap\BootstrapAsset',
        'admin\assets\AngularAsset',
        'admin\assets\JQueryAsset'
    ];
}
