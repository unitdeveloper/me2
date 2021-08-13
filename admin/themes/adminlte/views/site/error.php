<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<style type="text/css">
    .error-img{
        
        width:100%;
        height:200px;
        overflow:hidden;
        background-image: url('images/rio2_jewel.png');
        background-repeat:no-repeat;
        background-size:contain;
        background-position:center;
    }
    .t-footer{
        position: absolute;
        bottom: 50px;
        width: 100%;
        height: 456px;
        right: 10px;
        background-image: url('images/christmas-tree-png-28.png');
        overflow:hidden;    
        background-repeat:no-repeat;
        background-size:contain;
        background-position:right;
    }
</style>
<section class="content">

    
        <div class="col-md-4 text-center">
            <div class="col-md-12">
                <h3><?= $name ?></h3>

           

            </div>
            <div class="error-img"></div>
            <br>
             <p>
                <?= nl2br(Html::encode($message)) ?>
            </p>
            <form class='search-form'>
                <div class='input-group'>
                    <input type="text" name="search" class='form-control' placeholder="Search"/>

                    <div class="input-group-btn">
                        <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        <!-- <img src="images/rio2_jewel.png" class="img-responsive"> -->
        </div>
        <div class="col-md-8">
        <br><br>
        <div class="error-content">
            
            <p>
                The above error occurred while the Web server was processing your request.
                Please contact us if you think this is a server error. Thank you.
                Meanwhile, you may <a href='<?= Yii::$app->homeUrl ?>'>return to dashboard</a> or try using the search
                form.
            </p>

            
        </div>
        </div>

</section>
 
<div class="t-footer hidden-xs"></div>