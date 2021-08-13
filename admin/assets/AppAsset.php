<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'css/site.css',
        'css/bootstrap-select.min.css',
        //'css/ewin_load.min.css?update=171017',
        'css/ewin_load.css?v=1.21.10',



    ];
    public $js = [


        'js/bootstrap-select.min.js',
        //'js/action_script.min.js?v=1.30.10',
        'js/action_script.js?v=101017',
        'js/left-menu.js?update=101017',







    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
