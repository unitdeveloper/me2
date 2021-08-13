<?php

namespace app\assets;

use yii\web\AssetBundle;
 



class DashBoardAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/';
    
    public $js = [
         

         #<!-- Sparkline -->
        'plugins/sparkline/jquery.sparkline.min.js',

        #<!-- jvectormap -->
        'plugins/jvectormap/jquery-jvectormap-1.2.2.min.js',
        'plugins/jvectormap/jquery-jvectormap-world-mill-en.js',

        #<!-- jQuery Knob Chart -->
        'plugins/knob/jquery.knob.js',

        #<!-- FastClick -->
        'plugins/fastclick/fastclick.js',

        #<!-- Morris.js charts -->
        'https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js',
        //'plugins/morris/morris.min.js',

        #<!-- Bootstrap WYSIHTML5 -->
        'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',
         
    ];
    public $css = [
        
    ];
    public $depends = [
        //'dmstr\web\AdminLteAsset',
    ];
    public $jsOptions = [
        'type' => 'text/javascript',
    ];
}