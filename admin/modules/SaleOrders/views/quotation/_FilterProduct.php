<?php
use kartik\widgets\ActiveForm;
use common\models\ItemCategory;
use common\models\Itemgroup;

use admin\models\FunctionCenter;

$Fnc = new FunctionCenter();
?>
 

<style type="text/css">
	.dropdown-large {
    position: static !important;
  }
  .dropdown-menu-large {
    margin: 20px 0 5px 0 ;
    padding: 20px 0px;
    width:100% !important;
  }
  .dropdown-menu-large > li > ul {
    padding: 0;
    margin: 0;
  }
  .dropdown-menu-large > li > ul > li {
    list-style: none;
  }
  .dropdown-menu-large > li > ul > li > a {
    /* display: block;
    padding: 3px 20px;
    clear: both;
    font-weight: normal;
    line-height: 1.428571429;
    color: #337ab7;
    white-space: normal; */
    font-size:16px;
  }
  .dropdown-menu-large > li ul > li > a:hover,
  .dropdown-menu-large > li ul > li > a:focus {
    text-decoration: none;
    color: #262626;
    /* background-color: #f5f5f5; */
  }
  .dropdown-menu-large .disabled > a,
  .dropdown-menu-large .disabled > a:hover,
  .dropdown-menu-large .disabled > a:focus {
    color: #999999;
  }
  .dropdown-menu-large .disabled > a:hover,
  .dropdown-menu-large .disabled > a:focus {
    text-decoration: none;
    background-color: transparent;
    background-image: none;
    filter: progid:DXImageTransform.Microsoft.gradient(enabled = false);
    cursor: not-allowed;
  }
  .dropdown-menu-large .dropdown-header {
    color: #428bca;
    font-size: 18px;
  }
  .navbar-default .navbar-nav > .open > a, 
  .navbar-default .navbar-nav > .open > a:hover, 
  .navbar-default .navbar-nav > .open > a:focus {
    /* background-color: rgb(52,180,159) !important; */
  }
  ul.ew-ul-sub > li{
    border:1px solid #fff;
    padding:5px 5px 5px 0px;
  }
  ul.ew-ul-sub > li > .ew-filter-onclick:hover{
    border:1px solid #ccc;
  }

  @media (max-width: 768px) {
    .dropdown-menu-large {
      margin-left: 0 ;
      margin-right: 0 ;
      background-color: #fff !important;
      width:100% !important;
    }
    .dropdown-menu-large > li {
      margin-bottom: 30px;
    }
    .dropdown-menu-large > li:last-child {
      margin-bottom: 0;
    }
    .dropdown-menu-large .dropdown-header {
      padding: 3px 15px !important;
    }
   
    .ew-ul-sub li
    {
      list-style-type: none;
      font-size: 18px;
      margin-bottom: 15px;
      margin-top: 15px;
      color:#337ab7 !important;

    }

    .navbar-brand {
      padding: 19px 15px;
    }

   
     
  }

</style>
 
<nav class="navbar  navbar-static">
   
	<div class="navbar-collapse- js-navbar-collapse-">
		<ul class="nav navbar-nav">
			<li class="dropdown dropdown-large">
				<a href="javascript:void(0);" class="dropdown-toggle add-product-service" data-toggle="dropdown">
        <i class="fa fa-plus text-primary"></i> <?= Yii::t('common','Add Product / Service');?> <b class="caret"></b></a>
				
				<ul class="dropdown-menu dropdown-menu-large row" style="border:5px solid rgba(111,52,196, .5); ">
          <?php                
           $menuList = Itemgroup::find()->where(['Child' => '00','status' => 1])
           ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
           ->orderBy(['Description_th' => SORT_DESC]);
           $dropdown = '';
					 foreach ($menuList->all() as  $model) {
              $count = Itemgroup::find()->where(['Child' => $model->GroupID,'status' => 1])->count();
              if($count>0){
                $dropdown.= '<li class="col-sm-3">
                  <ul class="ew-ul-sub">
                    <li class="dropdown-header"> 
                      <i class="fa fa-caret-down pull-left" aria-hidden="true"></i>
                      '. Yii::t('common',$model->Description_th) . '
                    </li>                  
                    '. $Fnc->MenuGroup($model->GroupID) .' 
                  </ul>
                </li>';
              }
            }
					  echo $dropdown;    
					?>					 
				</ul>
				
			</li>
		</ul>
		
	</div><!-- /.nav-collapse -->
</nav>

 
 