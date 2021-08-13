<?php
namespace admin\assets;

use yii\web\AssetBundle;
use yii\web\View;

class JQueryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
        'plugins/bootstrap-toggle-master/css/bootstrap-toggle.min.css',
    ];
    public $js = [
        'js/jquery-2.1.3.min.js',
        'js/ui-bootstrap-tpls-2.5.0.min.js',
        'js/jquery.rippleria.min.js',
        'plugins/bootstrap-toggle-master/js/bootstrap-toggle.min.js',
        
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
        'type' => 'text/javascript',
    ];
    
}
