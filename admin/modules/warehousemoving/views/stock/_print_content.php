<p id="a">ddd</p>

<?php
$js=<<<JS

    $('body').find('p#a').html('eeeeee');

JS;

$this->registerJs('https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js',Yii\web\View::POS_END);

?>