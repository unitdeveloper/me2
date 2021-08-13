<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\SaleHeader */

$this->title = $model->no;
 

?>

<style>
    .item-detail{
        width:100%;
        height:100%;
        position:fixed;
        background:#fff;
        top:0px;
        right:0px;
        z-index:1030;
        overflow:hidden;
        display:none;
    }

    .text-customer-name{
        margin:-20px 0 20px 0;
        z-index:1020;
    }

    @media screen and (max-width: 767px){

        .total-text-line{
            padding-right: 10px;
        }

        .item-description {
            overflow: hidden;
            text-overflow: ellipsis;
            width: 96%;
        }
    }

    @media screen and (max-width: 425px){
         
        .submit-btn-zone{
            position:fixed;
            bottom:0px;
            background-color: rgb(253,253,253);
            border-top:1px solid #eaeaea;
            padding:10px 0 10px 0;
            width:100%;
            z-index:1000;
        }
    }
    
    @media screen and (max-width: 375px){
        .table-responsive {
            overflow-x: hidden;
            border: none !important;
        }

        .rule-xs-mac{
            max-width:340px; 
            overflow-x:auto;
        }

        .add-product-service{
            color:#888;
        }

        input.no-border{
            background:none;
        }

        a#complete-btn:active{
            color:green;
        }

        .submit-btn-zone{
            position:fixed;
            bottom:0px;
            background-color: rgb(253,253,253);
            border-top:1px solid #eaeaea;
            padding:10px 0 10px 0;
            width:100%;
            z-index:1000;
        }
    
        .FilterResource{
            padding-bottom:50px;
        }

        .total-text-line{
            padding-right: 45px;
        }

        .item-description {
            overflow: hidden;
            text-overflow: ellipsis;
            width: 90%;
        }
       
    }

	@media (max-width: 768px) {
		.main-header {
			margin-right: -15px !important;
		}
	}
    
    @media screen and (max-width: 320px){
        .total-text-line{
            padding-right: 100px;
        }

        .item-description {
            overflow: hidden;
            text-overflow: ellipsis;
            width: 70%;
        }
    }
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-header-update" ng-init="Title='<?= Html::encode($this->title) ?>'">
	<?=$this->render('_form', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    ?>
</div>


