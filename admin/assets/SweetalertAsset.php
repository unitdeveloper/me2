<?php

namespace app\assets;

use yii\web\AssetBundle;
 



class SweetalertAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/sweetalert2/dist/';
    public $css = [
    	'sweetalert2.min.css',

    ];
    public $js = [
    	'sweetalert2.min.js',
        //'core-js.min.js',

    	//<!-- Include a polyfill for ES6 Promises (optional) for IE11 and Android browser -->
    	//'core.js',
    	//'https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js',

    ];
    public $jsOptions = [
        //'position' => View::POS_HEAD,
        'type' => 'text/javascript',
    ];
    
}