<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use kartik\widgets\DateTimePicker;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\warehousemoving\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Item Adjust');
$this->params['breadcrumbs'][] = $this->title;


function getItemGroup($obj){
    $models = \common\models\ItemgroupCommon::find()->where([
        "child"     => $obj->id,
        'status'    => "1", 
        'group_for' => "inv", 
        'comp_id'   => Yii::$app->session->get('Rules')['comp_id']
    ])->orderBy('sequent')->all();

    $data = [];
    foreach ($models as $key => $model) {
        $data[]= (Object)[
            'child'     => getChildGroup($model),
            'id'        => $model->id,
            'name'      => $model->name,
            'name_en'   => $model->name_en
        ];
    }

    return $data;
}


function getChildGroup($obj){

    $models = \common\models\ItemgroupCommon::find()->where([
        "child"     => $obj->id,
        'status'    => "1", 
        'group_for' => "inv", 
        'comp_id'   => Yii::$app->session->get('Rules')['comp_id']
    ])->orderBy('sequent')->all();

    $data = [];
    foreach ($models as $key => $model) {
        $data[]= (Object)[
            'child'     => getChildGroup($model),
            'id'        => $model->id,
            'name'      => $model->name,
            'name_en'   => $model->name_en
        ];
    }

    return $data;
}

// if((Yii::$app->user->identity->id !==1) && (Yii::$app->session->get('Rules')['comp_id']==1)){
//     echo '<h3>ปิดการใช้งาน (ชั่วคราว)</h3><br /> 31/10/2019';
    
// }else{
?>
 
<div class="item-journal-index" ng-init="Title='<?= Html::encode($this->title) ?>'">

    <div class="row">
        <div class="col-md-5">

            <?php 

                $html = '<div class="row">';
                $models = $dataProvider->getModels();
                foreach ($models as $key => $model) {
                    $html.='<div class="col-sm-6">
                                <div class="panel panel-default">                             
                                    <div class="panel-body '.($model->countChild > 0 ? 'itemgroup-menu pointer': ' bann').'" id="itemgroup-menu" data-id="'.$model->id.'" style="margin:-15px 0 -15px 0;">
                                        <div class="row">
                                            <div class="col-xs-2" style="background-color:#ddd; height:70px; padding:10px; border-right:1px solid #ccc;">                                        
                                            '.($key +1).'. <img src="'.$model->photo.'" class="img-responsive" /></div>
                                            <div class="col-xs-10 text-name '.(
                                                $model->countChild > 0 
                                                    ? $model->group_for =='inv' 
                                                        ? 'bg-info' 
                                                        : 'bg-navy' 
                                                    : 'bg-gray'
                                                ).'"  style="padding:10px;  height:70px;">'.$model->name_en.'</div>
                                        </div>
                                    </div>';
                                    
                        // $G = getItemGroup($model);
                        // foreach($G as $group) {
                        //     $html.= '<div class="text-left popup-list-" id="popup-list" style="">
                        //                 <ul class="menu row">
                        //                     <div class="col-xs-6" id="child">
                        //                         <li>
                        //                             <span id="count"> </span>
                        //                             <a href="javascript:void(0)" class="click-item-in-group" data-key="'.$group->id.'" data-name="'.$group->name.'">'.$group->name.'</a>
                        //                         </li>
                        //                     </div>
                        //                 </ul>
                        //             </div>';
                        // } 

                    $html.='    </div>
                            </div>';

                    
                   
                }

                $html.= '</div>';

                
            
            ?>

            <?=$html;?>
        </div>
        <div class="col-md-7" id="renders">
            <div id="renders-header">
                <h3>มูลค่าสินค้าคงคลัง</h3>
                <h3>库存价值</h3>
                <div class="text-right">
                    <?php 
                        if(Yii::$app->user->identity->id ==1){
                            echo Html::a(Yii::t('common','Show All'),['index','for' => 'all'],['class' => 'btn btn-default-ew']);
                        }
                    ?>
                </div>
                <br />
                <p>มูลค่าสินค้าในแต่ละเดือน</p>
                <p>每月产品价值</p>
                
            </div>
            <div class="panel panel-primary" id="renders-panel">
                <div class="panel-heading" id="renders-panel-heading">
                        <h3 class="panel-title">每月产品价值</h3>                        
                </div>
                <div class="table-responsive"  id="renders-panel-body">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-7 col-sm-8"><img src="https://www.idashboards.com/wp-content/uploads/2017/10/line-graph.png" class="img-responsive"/></div>
                            <div class="col-md-5 col-sm-4 hidden">
                                <div class="hidden-sm hidden-md hidden-lg" style="margin-top:25px;"></div>
                                <div class="row" style="border:1px solid #ccc; margin:1px;">
                                    <div class="col-xs-8" style="border-right:1px solid #ccc; height:60px;">
                                        <div style="margin:10px 0 0 10px;">มูลค่ารวม
                                            <p>总价值</p></div>
                                    </div>
                                    <div class="col-xs-4"><h3>X</h3></div>
                                </div>
                                <div style="margin-top:25px;"></div>
                                <div class="row" style="border:1px solid #ccc; margin:1px;">
                                    <div class="col-xs-8" style="border-right:1px solid #ccc; height:60px;">
                                        <div style="margin:10px 0 0 10px ;">กำลังขนส่ง
                                            <p>运输价值</p></div>
                                    </div>
                                    <div class="col-xs-4"><h3>X</h3></div>
                                </div>
                            </div>
                        </div>
                            
                    </div>
                </div>
                <div class="panel-footer bg-primary text-right"  id="renders-panel-footer" style="background-color: #337ab7;">
                    <a href="#" style="font-size:20px;" class="text-white add-to-group" target="_blank">+</a>
                </div>
            </div>
            <div id="text-remark" style="display:none;">
                <label for="remark"><?=Yii::t('common','Remark')?></label>
                <textarea type="text" name="" id="remark" class="form-control" rows="3" required="required" style="margin-bottom:70px;"></textarea>
            </div>
        </div>
        <div class="active-bottom" style="display:none;">   
            <div class="btn-group pull-right">
                <?=Html::a('<i class="fas fa-print"></i> '.Yii::t('common','Print'),'#',['class'=> 'btn btn-info click-btn-print','style' => 'margin-right:20px;'])?>   
                <?=Html::a('<i class="fas fa-save"></i> '.Yii::t('common','Save'),'#',['class'=> 'btn btn-success click-btn-save'])?>       
            </div>
        </div>
    </div>
</div>

 
<div class="modal fade" id="modal-pick-date">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Posting date')?></h4>
            </div>
            <div class="modal-body">
                <div class="well well-sm posting-date-picker" style="background-color: #fff; width:100%">
                    <?=DatePicker::widget([
                        'name' => 'posting_date',
                        'type' => DatePicker::TYPE_INLINE,
                        'value' => date('d-m-Y'), //'23-Feb-2019'
                        'pluginOptions' => [
                            'format' => 'dd-mm-yyyy',
                            'multidate' => false
                        ],
                        'options' => [
                            // you can hide the input by setting the following
                             'style' => 'display:none'
                        ]
                    ]);?>
                </div>
                <div class="row">
                    <div class="col-xs-8 text-right">
                        <label class="mt-8" for="document-time"><?=Yii::t('common','Time')?></label>
                    </div>
                    <div class="col-xs-4">   
                        <div class="input-group">                     
                            <input type="time" name="document-time" class="form-control" value="<?=date('H:i:s')?>" id="document-time" />
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="button" id="reset-time">
                                    <i class="fas fa-history"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?=Html::a('<i class="fas fa-power-off"></i> '.Yii::t('common','Close'),'#',['class'=> 'btn btn-default pull-left ','data-dismiss' => 'modal'])?>   
                <?=Html::a(Yii::t('common','Next').' <i class="fas fa-forward"></i>','#',['class'=> 'btn btn-success btn-save-adjust'])?>      
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-inspector" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','ผู้ตรวจนับ')?></h4>
            </div>
            <div class="modal-body">
                <div><?=Yii::t('common','Date')?>: <span id="date"></span></div>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon" id="sizing-addon1" style="background-color: #d2d6de;">ระบุชื่อ ผู้ตรวจนับ</span>
                    <input type="text" class="form-control inspector" placeholder="<?=Yii::t('common','Name')?>" aria-describedby="sizing-addon1" name="inspector">
                </div>
                
            </div>
            <div class="modal-footer">
                <?=Html::a('<i class="fas fa-backward"></i> '.Yii::t('common','Back'),'#',['class'=> 'btn btn-default pull-left btn-back-to-date'])?>      
                <?=Html::a('<i class="fas fa-save "></i> '.Yii::t('common','Save'),'#',['class'=> 'btn btn-success btn-save-inspector'])?>      
            </div>
        </div>
    </div>
</div>


 
<div class="modal fade" id="modal-loading"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog text-center" style="margin-top: 25%;">
        <div><i class="fa fa-refresh fa-spin fa-4x text-white"></i></div>
    </div>
</div>

<?php $this->registerCssFile('css/stock.css',['rel' => 'stylesheet','type' => 'text/css']);?>
<?=$this->render('index-script'); ?>

<?php //} ?>