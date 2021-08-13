<?php
namespace admin\assets;

use yii\base\Exception;
use yii\web\AssetBundle;

/**
 * AdminLte AssetBundle
 * @since 0.1
 */
class BowerAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/remarkable-bootstrap-notify';
    public $css = [
         
    ];
    public $js = [
        'bootstrap-notify.min.js',

    ];
    public $depends = [

    ];

    
}
