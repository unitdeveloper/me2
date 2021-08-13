<?php 
use yii\helpers\Html;
$fav = \common\models\FavoriteMenu::find()->where(['user_id' => Yii::$app->user->identity->id])->all();
?>
<style>
    .favorite-link:hover{
        background: #00a65a !important;
        color: #fff !important;
    }
</style>
<div class="row">
    <div class="col-xs-12 my-10">
        <div class="row">        
            <?php 
                foreach ($fav as $key => $value) {
                    echo '<div class="col-md-3 col-sm-6 col-xs-12">
                            '.Html::a(' 
                                <span class="info-box-icon bg-yellow"><i class="fa fa-star-o"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><h4>'.($value->name ? $value->name : Yii::t('common','Empty')).'</h4></span>
                                </div>
                            </div>',
                            $value->url,[
                                'class' => "info-box favorite-link",
                                'style' => "background: #ececec;"
                            ]).'
                         ';
                }
            ?>
        </div>
    </div>
</div>