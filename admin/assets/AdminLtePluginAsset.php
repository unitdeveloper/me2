<?php

namespace app\assets;

use yii\web\AssetBundle;




class AdminLtePluginAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/';

    public $js = [
        //'datatables/dataTables.bootstrap.min.js',

        //'plugins/datepicker/bootstrap-datepicker.js',
        //'dist/js/pages/dashboard.js',

        'bootstrap-toggle-master/js/bootstrap-toggle.min.js',

        #<!-- jQuery 2.2.3 -->
        //'plugins/jQuery/jquery-2.2.3.min.js',

        #<!-- jQuery UI 1.11.4 -->
        //'https://code.jquery.com/ui/1.11.4/jquery-ui.min.js',
        'plugins/jQueryUI/jquery-ui.min.js',

        #<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

        #<!-- Bootstrap 3.3.6 -->
        /*'bootstrap/js/bootstrap.min.js',*/





        #<!-- daterangepicker -->
        //'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js',
        'plugins/daterangepicker/moment.min.js',
        'plugins/daterangepicker/daterangepicker.js',

        #<!-- datepicker -->
        'plugins/datepicker/bootstrap-datepicker.js',

         'plugins/knob/jquery.knob.js',

         'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',

        // 'dist/js/pages/dashboard.js',

        #<!-- Slimscroll -->
        'plugins/slimScroll/jquery.slimscroll.min.js',


        #<!-- AdminLTE App --> ชนกัน กับ Jquery
        //'dist/js/app.min.js',


        'plugins/colorpicker/bootstrap-colorpicker.min.js',
        #<!-- AdminLTE for demo purposes -->
        //'dist/js/demo.js',
        // more plugin Js here

        'plugins/select2/select2.full.min.js',

        'plugins/iCheck/icheck.min.js',

    ];
    public $css = [
        'bootstrap-toggle-master/css/bootstrap-toggle.min.css',

        'plugins/select2/select2.min.css',
        //'datatables/dataTables.bootstrap.css',

        'plugins/datepicker/datepicker3.css',
        'plugins/daterangepicker/daterangepicker.css',

        'https://fonts.googleapis.com/css?family=Kanit:300',

        #<!-- Bootstrap 3.3.6 -->
        //'bootstrap/css/bootstrap.min.css',

        #<!-- Font Awesome -->
        //'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css',

        #<!-- Ionicons -->
        //'https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css',
        #<!-- Theme style -->
        //'dist/css/AdminLTE.min.css',
        #<!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
        /*'dist/css/skins/_all-skins.min.css',
        #<!-- iCheck -->
        'plugins/iCheck/flat/blue.css',*/
        'plugins/iCheck/all.css',

        /*#<!-- Morris chart -->
        'plugins/morris/morris.css',
        #<!-- jvectormap -->
        'plugins/jvectormap/jquery-jvectormap-1.2.2.css',
        #<!-- Date Picker -->
        'plugins/datepicker/datepicker3.css',
        #<!-- Daterange picker -->
        'plugins/daterangepicker/daterangepicker.css',
        #<!-- bootstrap wysihtml5 - text editor -->
        'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',*/

        'plugins/colorpicker/bootstrap-colorpicker.min.css',

        // more plugin CSS here
    ];
    public $depends = [
        //'dmstr\web\AdminLteAsset',
    ];
    public $jsOptions = [
        'type' => 'text/javascript',
    ];
}
