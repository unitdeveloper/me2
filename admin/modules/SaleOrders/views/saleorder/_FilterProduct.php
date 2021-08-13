<?php
//use kartik\widgets\ActiveForm;
//use common\models\ItemCategory;
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
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19) !important;
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
    padding:5px;
    transform: translateY(-2px);
		transition: all .3s;
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
      padding: 9px 9px !important;
      border:1px solid #ccc;
      background-color:#47a8d1;
      color: #fff !important;
      margin: 0px -16px 0px 12px  !important;
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

   .navbar{
     margin-left:15px;
     margin-right:15px;
     
   }
     
  }
  .ItemGrid:hover{
        box-shadow: 10px 10px 16px #DEDEDE;
        -moz-box-shadow: 10px 10px 16px #DEDEDE;
        transform: translateY(-2px);
        transition: all .3s;
    }
</style>
 
<nav class="navbar navbar-static">
   
	<div class="navbar-collapse- js-navbar-collapse-">
		<ul class="nav navbar-nav">
			<li class="dropdown dropdown-large">
				<a href="javascript:void(0);" class="dropdown-toggle add-product-service btn-info-ew" data-toggle="dropdown">
        <i class="fas fa-bars text-aqua"></i> <?= Yii::t('common','Product / Service');?> <b class="caret"></b></a>
				
				<ul class="dropdown-menu dropdown-menu-large row">
          <?php                
           $menuList = Itemgroup::find()->where(['Child' => '00','status' => 1])
           ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
           ->orderBy(['sequent' => SORT_ASC]);
           $dropdown = '';
					 foreach ($menuList->all() as  $model) {
              $count = Itemgroup::find()->where(['Child' => $model->GroupID,'status' => 1, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->count();
              if($count>0){
                $dropdown.= '<li class="col-xs-6 col-sm-6 col-md-4">
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

 
 <script>
  $(document).ready(function(){

    $(".dropdown").on("show.bs.dropdown", function(event){
      var x = $(event.relatedTarget).text(); // Get the text of the element
      $('.ResourceItemSearch').hide();       
      $('.FilterResource').css('height','500'); 
    });

    $(".dropdown").on("hide.bs.dropdown", function(){
      $('.ResourceItemSearch').show();
      $('.FilterResource').css('height','100%');
    });

  });
</script>