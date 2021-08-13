<?php

namespace admin\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class FontAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        
        '//fonts.googleapis.com/css?family=Kanit|Open+Sans|Oswald|Prompt|Roboto|Ubuntu'

        ### https://fonts.google.com/
        // font-family: 'Kanit', sans-serif;
        // font-family: 'Roboto', sans-serif;
        // font-family: 'Open Sans', sans-serif;
        // font-family: 'Ubuntu', sans-serif;
        // font-family: 'Oswald', sans-serif;
        // font-family: 'Prompt', sans-serif;
    ];
    
    public $js = [
        //'https://use.fontawesome.com/releases/v5.0.8/js/all.js',
    ];
    public $depends = [
       // 'yii\web\YiiAsset',
       // 'yii\bootstrap\BootstrapAsset',
    ];
}
