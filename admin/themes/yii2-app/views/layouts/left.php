<?php
use yii\helpers\Html;
use common\models\User;

//$user = Yii::$app->user->identity;
if(isset(Yii::$app->user->identity->id)){
  
  $user = User::findOne(Yii::$app->user->identity->id);
 
?>
<style media="screen">
  .dropdown-menu-left{
    list-style-type:none;
    margin:15px 0px 0px -40px;

  }
  .user-panel>.image>img {
      max-width: 65px;
  }
  .user-panel>.info {
    padding: 5px 5px 5px 25px;
  }
  .home-menu{
    height:60px;
    margin:20px 0px 10px -5px;
    border-bottom:1px solid #ccc;
  }
  .home-menu a:active{
    color:red;
  }

  .sidebar .sidebar-background, .off-canvas-sidebar .sidebar-background {
      position: absolute;
      z-index: -1;
      height: 100%;
      width: 100%;
      display: block;
      top: 0;
      left: 0;
      background-size: cover;
      background-position: center center;
      opacity:0.05;
  }
 
</style>
<aside class="main-sidebar" >

    <section class="sidebar" >
    <div class="sidebar-background" ></div>
        <!-- Sidebar user panel -->
        <div class="row hidden-sm hidden-md hidden-lg home-menu">
          <div class="col-sm-12">            
            <a href="?r=site/index"  class="btn btn-info-ew btn-flat" data-rippleria><i class="fas fa-home" aria-hidden="true"></i> <?=Yii::t('common','Home')?></a>
          </div>

        </div>


        
 

        <?php
            // $modules = $this->context->module->id.'/'.Yii::$app->controller->id;

            // if($modules=='SaleOrders/event'){

            //   echo $this->render('menu_promotions');

            // }else{
              //var_dump(Yii::$app->session->get('PACKAGE'));
              
              if(Yii::$app->user->identity->id ==1){

                echo $this->render('menu_admin');
  
              }else{ 
                
                switch (Yii::$app->session->get('PACKAGE')) {
                  case 'FULL':
                    echo $this->render('menu');
                    break;

                  case 'POS':
                    echo $this->render('menu_promotions');
                    break;
                  
                  default:
                    echo $this->render('menu');
                    //Yii::$app->response->redirect(['site/index']);
                    break;
                }

              }
           // } 

        ?>
        
        
        <div class="user-panel" style="display:none;">
            <hr />
            <div class="pull-left image">
                <img src="<?=$user->profile->getPicture()?>" class="img-responsive" alt="<?=$user->username?>"/>
            </div>
            <div class="pull-left info">
                <p><?=($user->profile->name != NULL)? $user->profile->name : $user->username; ?></p>

                <a href="#"><i class="far fa-circle text-success"></i> <?=Yii::t('common','Online')?></a>
            </div>
            
            <div class="row">
              
              <div class="col-xs-12">
                <ul class="dropdown-menu-left">
                  
                  <!-- user Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="index.php?r=users/settings" class="btn btn-success-ew btn-flat" data-rippleria>
                        <i class="fas fa-user"></i>
                        <?=Yii::t('common','Profile')?>
                      </a>
                    </div>
                    <div class="pull-right">
                      <?= Html::a(
                        '<i class="fas fa-sign-out-alt" ></i> '.Yii::t('common','Sign out'),
                        ['/site/logout'],
                        ['data-method' => 'post', 'class' => 'btn btn-warning-ew btn-flat' ,'data-rippleria' => ' ']
                        ) ?>
                    </div>
                  </li>

                  
                </ul>
              </div>
            </div>
        </div>


    </section>

</aside>
<?php } ?>