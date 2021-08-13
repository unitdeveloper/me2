<?php
namespace admin\assets;

use yii\web\AssetBundle;
use yii\web\View;

class AngularAsset extends AssetBundle
{
    //public $sourcePath = '@bower';
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
      'js/angular.min.js',
      'js/angular/angular-route.min.js',
      'js/angular-animate.min.js',
      'js/angular-sanitize.min.js',
      'js/app.js?v=4.03.27',
      'js/ng-barcode.js',

    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
        'type' => 'text/javascript',
    ];
}
