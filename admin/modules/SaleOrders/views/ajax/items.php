<?php
//use admin\modules\SaleOrders\models\SalehearderSearch;
use common\models\Items;
use admin\modules\Manufacturing\models\KitbomHeader;

//$SaleHeader  = new SalehearderSearch();
$company     = Yii::$app->session->get('Rules')['comp_id'];

$pd = '<div class="row item-box" count="'.count($dataProvider->models).'">';

$countGroup = $dataProvider->getTotalCount();
if($countGroup > 0){
  foreach ($dataProvider->models as $model) {
        if($model->itemSet != null){
            $SetName    = $model->itemSet->name;
            $SetDetail  = $model->itemSet->detail;
        
            // หาว่า มี Set นี้ใน KitBom หรือไม่
            if(KitbomHeader::find()
            ->where([
              'item_set' => $model->itemset,
              'multiple' => 0,
              'status'=>1, 
              'comp_id' => $company
            ])
            ->andwhere(['not',['format_gen'=>null]])
            ->exists()){
                $bom = 'enabled';
            }else {
                $bom = 'disable';
            }

            // ---./ ------
            $count  = Items::find()
            //->rightJoin('item_mystore','item_mystore.item=items.id')
            ->joinwith(['item_mystore'])
            ->where(['items.itemset' => $model->itemset, 'item_mystore.status' => 1])
            ->andWhere(['item_mystore.comp_id' => $company])
            ->count();

            $colors = ($model->itemGroup->color)? $model->itemGroup->color : '#00a7d0';

            $pd.= '<div class="item-groups '.($countGroup == 1 ? 'col-xs-12' : 'col-xs-6').' col-sm-6 col-md-3" data-key="'.$model->id.'">
                    <div class="box box-widget widget-user ItemGrid">
                      <div class="widget-user-header" style="background-color:'.$colors.'; color:#fff;">
                        <h5 class="widget-user-username">'. mb_substr($SetName,0,50). '</h5>
                      </div>
                      <div class="widget-user-image">
                        <img class="btn ew-PickItem" src="'.$model->picture.'" itemno="'.$model->No.'" itemset="'.$model->itemset.'" ew-Set-Name="'. $SetName. '" ew-bom="'.$bom.'">
                      </div>
                      <div class="box-footer">
                        <div class="row">
                          <div class="col-xs-6 border-right">
                            <div class="description-block">
                              <h5 class="description-header">'.( $model->salePrice ? number_format($model->salePrice->avg) : 0).'</h5>
                              <span class="description-text">'.Yii::t('common','Price').'</span>
                            </div>
                          </div>
                          
                          <div class="col-xs-6">
                            <div class="description-block">
                              <h5 class="description-header">'.$count.'</h5>
                              <span class="description-text">'.Yii::t('common','Product').'</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>';

        }
  }
  
}else{
  $pd.= '<div class="col-md-3">
          <div class="col-xs-2 text-center"><i class="fas fa-search fa-3x"></i></div>
          <div class="col-xs-10 text-center">'.Yii::t('common','NO DATA FOUND').'<br/> ไม่พบข้อมูล</div>
        </div>';
}
$pd.= '</div>';
echo $pd;