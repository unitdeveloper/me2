<?php

namespace app\assets;

use yii\web\AssetBundle;
 



class SaleDashBoardAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/';
    
    public $js = [
         

       // 'plugins/jQuery/jquery-2.2.3.min.js',
       //'https://code.jquery.com/ui/1.11.4/jquery-ui.min.js',

        //'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js',

        //'plugins/daterangepicker/daterangepicker.js',

        'plugins/knob/jquery.knob.js',

        'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',

        'dist/js/pages/dashboard.js',

        
/*<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
 





<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/knob/jquery.knob.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>*/


         
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