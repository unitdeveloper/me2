<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class PeneRarAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    	 

    ];
    public $js = [
    	'js/speech_google_api.js',
 
    ];
    public $jsOptions = [
        'type' => 'text/javascript',
    ];

}