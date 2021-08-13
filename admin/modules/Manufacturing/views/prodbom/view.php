<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model common\models\BomHeader */

use admin\modules\Itemset\models\FunctionItemset;
$Fnc = new FunctionItemset;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Bom Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php

        
// $CalInven = $Fnc->ProMinBomLine($model->id);
// var_dump($CalInven);

?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>



<div class="bom-header-view">
    <div  class="row">
        <div class="col-sm-12 mt-10">
            <h1><?= Html::encode($this->title) ?></h1>
            <p>                
                <?= Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger pull-right',
                    'style' => 'margin-bottom:5px;',
                    'data' => [
                        'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
        </div>    
    </div>
    <div class="row">
        <div class="col-sm-2 mt-10">
            <img src="<?=$model->items->picture;?>" class="img-responsive"/>
        </div>    
        <div class="col-sm-10 mt-10">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [                
                'code',
                'name',
                'description:ntext',
                [
                    'label' => Yii::t('common','Inventory'),
                    'format' => 'raw',
                    'value' => function($model){
                        $items = \common\models\Items::find()->where(['ProductionBom' => $model->id])->all();
                        $data = '<ul style="  margin-left:-20px;">';
                        foreach ($items as $key => $item) {
                            $i = $key +1;
                            $data.= '<li ><a href="index.php?r=items/items/view&id='.$item->id.'" target="_blank">'.$i.'. '.$item->master_code.'</a></li>'."\r\n";
                            $data.= '<ul style="list-style-type:none;">';
                                $data.= '<li>  <label class="label label-warning">'.Yii::t('common','Stock').' : '.$item->last_stock.' '.$item->UnitOfMeasure.' </label></li>'."\r\n";
                                $data.= '<li class=" mt-5">  <label class="label label-warning">'.Yii::t('common','Can Produce').' : '.$item->last_possible.' '.$item->UnitOfMeasure.' </label></li>'."\r\n";
                            $data.= '</ul>';                            
                        }
                        $data.= '</ul>';

                        return $data;
                    }
                ],
                 
                'create_date',

                ]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 ">
            <div class="well">
                <?php 
                $profit = $model->items->salePrice->avg ? $model->items->salePrice->avg - $model->unitcost : 0;
        
                $html = '<div class="row">
                            <div class="col-md-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-aqua"><i class="fab fa-creative-commons-remix"></i></span>                        
                                    <div class="info-box-content">
                                    <span class="info-box-text">'.Yii::t('common','Real Cost').'</span>
                                    <span class="info-box-number">'.number_format($model->realCost,2).'<small></small></span>
                                    </div> 
                                </div>
                                <div class="info-box">
                                    <span class="info-box-icon bg-purple"><i class="far fa-square"></i></span>                            
                                    <div class="info-box-content">
                                    <span class="info-box-text">'.Yii::t('common','Standard Cost').'</span>
                                    <span class="info-box-number">'.number_format($model->items->myItems ? $model->items->myItems->StandardCost : 0,2).'</span>
                                    </div>    
                                </div>
                            </div>

                            
                            <div class="clearfix visible-sm-block"></div>                        
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-orange"><i class="fas fa-dollar-sign"></i></span>                            
                                    <div class="info-box-content">
                                    <span class="info-box-text">'.Yii::t('common','Sale Price').'</span>
                                    <span class="info-box-number">'.number_format($model->items->salePrice->avg,2).'</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box">
                                    <span class="info-box-icon '.($profit < 0 ? "bg-red" : "bg-green").'"><i class="fas fa-wallet"></i></span>                        
                                    <div class="info-box-content">
                                    <span class="info-box-text">'.Yii::t('common','Profit').'</span>
                                    <span class="info-box-number '.($profit < 0 ? "text-red" : "text-green").'">'.($profit < 0 ? "-" : "+").' '.number_format($profit,2).'</span>
                                    </div>
                                </div>
                            </div>
                        </div>';
                
                echo $html;

                ?>
            </div>
        </div>
    </div>
</div>
<hr class="style2">
<div class="row">
    <div class="col-md-12">
    <label><?=Yii::t('common','Bom Line') ?></label>
   
    <div class="BomLine">
        <?=$this->render('__bom_line',['model' =>$model,'dataProvider' => $dataProvider]); ?>
    </div>
    </div>
</div>
<script type="text/javascript">
    $('body').on('keydown','.InsertItem',function(e){

     if (e.which == 9 || e.which == 13) {

                if($('#InsertDesc').attr('ew-item-code') != 'eWinl')
                    {
                         
                        $('#InsertDesc').first().focus();
                    }

            

            ValidateItem();

         }
      
    });

    $('body').on('change','input.InsertItem',function(e){
         
        ValidateItem($(this));
      
    });

    $('body').on('click','.RemoveBomLine',function(){
     
      if (confirm('ต้องการลบรายการ "' + $(this).attr('alt') + '" ?')) {  

         RemoveBomLine($(this));

      }
      return false;

    }); 

    function RemoveBomLine(e)
    {
        var itemno = e.attr('href');
        var id = itemno.substring(1);

        var data = { param:{
                lineno:id, 
                bomid:<?=$model->id?>,
        }};

        route('index.php?r=Manufacturing/prodbom/delete-bom-line','POST',data,'BomLine');
    }

    function ValidateItem()
    {
        
             var inputItem = $.trim($('.InsertItem').val());  
            $('.InsertItem').val(inputItem);  

             $.ajax({ 

                url:"index.php?r=Manufacturing/ajax/json-find-item",
                type: "POST", 
                data: {param:{item:inputItem}},
                async:false,
                success:function(getData){
                     
                    
                    var obj = jQuery.parseJSON(getData);
                    //alert( obj.name === "John" );
                    $('.ew-desc').show();
                    $('#InsertDesc').val(obj.desc);

                    $('#InsertDesc').attr('ew-item-code',obj.item);
                    $('#InsertDesc').attr('data-key',obj.id);
                    $('.measure').html(obj.measure);

                    $('.ew-qty').show();
                    $('#InsertQty').val(1);

                    $('.ew-price').show();
                    $('#InsertPrice').val(obj.std);

                    if(obj.code != 'eWinl')
                    {
                        $('.ew-add').show();
                    }else {
                        $('.ew-add').hide();
                    }
                    
                    
                }
            });

            //alert(inputItem); 
            
                 
       
    }

    // Add to BOM Line.  
    // ---------->
    $('body').on('click','.ew-add',function(e){
        CreateBOMLine();
             
    });

    $('body').on('keydown','.ew-add',function(e){

        if (e.which == 13) {
            CreateBOMLine();

        }
    });


    $('body').on('keydown','#InsertPrice',function(e){

        if (e.which == 13) {
            CreateBOMLine();
        }
    });

    function CreateBOMLine() {
        

        if($('#InsertDesc').attr('ew-item-code') === 'eWinl')
        {
            if($('#InsertType').val()=='G/L')
            {
                alert('ขณะนี้ ยังไม่เปิดให้ใช้งาน G/L' );
            }else {
                alert('ไม่มี Item "'+ $('.InsertItem').val() +'"' );
            }
            
        }else {

            if($('#InsertPrice').val()==='' || $('#InsertPrice').val()==='0'){ alert('คุณกำลังพยายามใส่ "ราคา 0 บาท"'); }

            var data = {param:{ 
                itemno:$('#InsertDesc').attr('ew-item-code'), 
                bomid:<?=$model->id?>,
                amount:$('#InsertQty').val(),
                desc:$('#InsertDesc').val(),
                measure:$('.measure').html(),
                id:$('#InsertDesc').attr('data-key')
            }};

            route("index.php?r=Manufacturing/prodbom/create-bom-line",'POST',data,'BomLine');
            //LoadAjax();  
        }

        

    }
    // <--------
    // End add to BOM Line  
</script>
