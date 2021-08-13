<?php
 use common\models\Items;
 use admin\modules\Manufacturing\models\KitbomHeader;

 
 $pd = '<div class="row item-box" count="'.count($dataProvider->models).'">';
 foreach ($dataProvider->models as $model) {

     
       if(isset($model->itemSet->name))
       {
          $SetName = $model->itemSet->name;
          $SetDetail = $model->itemSet->detail;
       }else {
          $SetName = NULL;
          $SetDetail = NULL;
       }

       // หาว่า มี Set นี้ใน KitBom หรือไม่

       if(KitbomHeader::find()->where(['item_set' => $model->itemset,'multiple' => 0,'status'=>1])->andwhere(['not',['format_gen'=>null]])->exists())
       {
          $bom = 'enabled';
       }else {
          $bom = 'disable';
       }

       $count = Items::find()->where(['itemset' => $model->itemset])->count();

       $pd.= '<div class="col-md-3">
          <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user ItemGrid">
            <!-- Add the bg color to the header using any of the bg-* classes -->
 
            <div class="widget-user-header bg-aqua-active">
              <h3 class="widget-user-username">'. mb_substr($SetName,0,50). '</h3>
              
            </div>

            <div class="widget-user-image">

              <img class="btn ew-PickItem" src="'.$model->getPicture().'" data-toggle="modal" data-target="#PickItem-Modal" itemno="'.$model->No.'" itemset="'.$model->itemset.'" ew-Set-Name="'. $SetName. '" ew-bom="'.$bom.'">
            </div>
            <div class="box-footer">
              <div class="row">
                <div class="col-xs-6 border-right">
                  <div class="description-block">
                    <h5 class="description-header">'.$model->StandardCost.'</h5>
                    <span class="description-text">'.Yii::t('common','Price').'</span>
                  </div>
                  <!-- /.description-block -->
                </div>
                 
                <div class="col-xs-6">
                  <div class="description-block">
                    <h5 class="description-header">'.$count.'</h5>
                    <span class="description-text">'.Yii::t('common','Product').'</span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
          </div>
          <!-- /.widget-user -->
        </div>';

 	  
 	 
 }
 $pd.= '</div>';

 echo $pd;


 