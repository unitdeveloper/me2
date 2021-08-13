<?php
use yii\helpers\Html;



/*
* Setup workdate
*/
if(empty(Yii::$app->session->get('workdate'))) Yii::$app->session->set('workdate',date('Y-m-d'));
if(empty(Yii::$app->session->get('worktime'))) Yii::$app->session->set('worktime',date('H:i:s'));

if (Yii::$app->controller->action->id === 'login') {

    echo $this->render(
        'main-login',
        ['content' => $content]
    );

} else {

    admin\assets\BtfourAsset::register($this);
    app\assets\SweetalertAsset::register($this);
    
    $collapse = Yii::$app->session->get('collapse');
    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/bower/bootstrap/dist');

?>
<?php $this->beginPage() ?>
    
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" ng-app="ewApp">

<head>
  <meta charset="<?= Yii::$app->charset ?>"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?= Html::csrfMetaTags() ?>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title><?= Html::encode($this->title) ?></title>
 
   
  <?php $this->head() ?>
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
<?php $this->beginBody() ?>
  <!-- Navigation-->
  
  <?php
    echo $this->render(
        'left.php',
        ['directoryAsset' => $directoryAsset]
    );
  ?>
 
  



  <div class="content-wrapper">
         
  
        
    <?= $content ?>
 
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>
  </div>

  <?php $this->endBody() ?>
  </body>
  </html>
  <?php $this->endPage() ?>
<?php } ?>