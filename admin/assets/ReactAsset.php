<?php
namespace admin\assets;

use yii\web\AssetBundle;
use yii\web\View;

class ReactAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
       
    ];
    public $js = [
         '//unpkg.com/react/umd/react.development.js',
         '//unpkg.com/react-dom/umd/react-dom.development.js',
         '//unpkg.com/babel-standalone@6.15.0/babel.min.js'
        
    ];
    public $jsOptions = [
         
    ];
    
}
