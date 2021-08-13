<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use \common\models\Items;
use \common\models\ItemMystore;
use \common\models\PackageControl;
/**
 * This is the model class for table "sale_group".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $comp_id
 */
class PosFilter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function showItems($limit){
        $model = Items::find()->where(['No'=> self::getMyitem(Yii::$app->session->get('Rules')['comp_id'])])
        ->orderBy(['ItemGroup' => SORT_ASC])
        ->limit($limit)
        ->all();
        if($model){
            return $model;
        }
    }

    public function renderItemList($product){

        $html = '<div class="row">';

        if ($product){
        
            foreach ($product as $key => $item) {

            //-----Background Color---
            $bg_style   = ($item->color=='')? ' ': 'background-color:'.$item->color.';';
            $bg_class   = ($item->color!='')? ' ': 'bg-aqua-active';
            //---/.Background Color---
            
            $Yii = 'Yii';
            $number = 'number_format';
            $html.=<<<HTML


                <div class="col-lg-3 col-md-4">
                <!-- Widget: user widget style 1 -->
                <a href="javascript:void(0)" class="product" >
                <div class="box box-widget widget-user item-picker" data-key="{$item->No}" data-code="{$item->barcode}" ng-click="pickProduct(\$event)">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header {$bg_class} " style="{$bg_style}">
                    <h3 class="widget-user-username">{$item->description_th}</h3>
                    <h5 class="widget-user-desc">{$item->brand}</h5>
                    </div>
                    <div class="widget-user-image">
                    <img class="img-responsive" src="{$item->getPicture()}" alt="User Avatar">
                    </div>
                    <div class="box-footer">
                    <div class="row">
                        <div class="col-xs-6 border-right">
                            <div class="description-block">
                                <span class="description-header">{$Yii::t('common','Price')}</span>                           
                            </div>
                        <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        
                        <!-- /.col -->
                        <div class="col-xs-6">
                            <div class="description-block">
                                <span class="description-header">{$item->iteminfo->price}</span>                           
                            </div>
                        <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    </div>
                </div>
                </a>
                <!-- /.widget-user -->
                </div>

HTML;
            }

        }else {
            $html.='<div></div>';
        }
        
        $html.= '</div>';
        return $html;

    }

  

    public function getMyitem($company)
    {


        if(ItemMystore::find()->where(['comp_id' => $company])->count() > 0 )
        {
            $model = ItemMystore::find()->where(['comp_id' => $company])->all();
            foreach ($model as $value) {
                $itemArr[]= $value->item_no;
            }

            return $itemArr;
        } else {
            return '0';
            //throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function findPermission(){
        $model = PackageControl::findOne(Yii::$app->session->get('Rules')['comp_id']);
        if($model){
            return $model;
        }else {
            return (Object)['id' => 'Error'];
        }
        

    }

    

}
