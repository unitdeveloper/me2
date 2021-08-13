<?php 

use yii\helpers\Html;

$updateDate     = new DateTime('2019-05-13');
$today          = new DateTime(date('Y-m-d',strtotime($head->PostingDate)));
$digit_stock    = Yii::$app->session->get('digit') ? Yii::$app->session->get('digit')->stock : 0;
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <title><?=Yii::t('common','Stock Checking')?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="images/icon/apple/favicon-32x32.png">
    <style> 
        @media print {
            @page {
                size: A4 landscape;
            }
 
            body{
                background-color:#fff !important;
            }
        }
    </style>
  </head>
  <body style="background-color:#ccc;">
    <div class="container" style="background-color:#fff; height:100%; min-width:900px;"> 
        <div class="row">
            <div class="col-12">
                <div class="row  d-flex justify-content-end">
                    <div class="col-4 mt-3">
                        <table class="table table-bordered ">
                            <thead>
                                <tr class="bg-light">
                                    <th colspan="2"><?=Yii::t('common','Product counting report')?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?=Yii::t('common','Document No')?></td>
                                    <td><?=$head->DocumentNo?></td>
                                </tr>
                                <tr>
                                    <td><?=Yii::t('common','Date')?></td>
                                    <td><?=$models? date('d/m/Y',strtotime($models[0]->PostingDate)) : '';?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12">
                
                <table class="table table-bordered renders-table">
                    <thead>
                        <tr class="bg-light">
                            <th width="50"><?=Yii::t('common','#')?></th>
                            <th width="170"><?=Yii::t('common','Code')?></th>
                            <th><?=Yii::t('common','Name')?></th>
                            <th class="text-right" <?=($today < $updateDate ? 'style="display:none;"' : ' ')?>><?=Yii::t('common','Before adjust')?></th>
                            <th class="text-right" width="100"><?=Yii::t('common','Amount adjust')?></th>
                            <th class="text-right" width="100"<?=($today < $updateDate ? 'style="display:none;"' : ' ')?>><?=Yii::t('common','After adjust')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        $i = 0;
                        $after  = 0;
                        $before = 0;
                        $differ = 0;
                        
                        foreach ($models as $key => $model) {
                            $i++;
                            $diff = $model->QtyMoved + $model->Quantity;
                            
                            
                            echo '<tr>
                                        <td>'.$i.'</td>
                                        <td>'.Html::a($model->items->master_code,['/items/items/view','id' => $model->items->id],['target' => '_blank']).'</td>
                                        <td>
                                            <div>'.$model->Description.'</div>
                                            <div>'.$model->items->description_th.'</div>
                                        </td>
                                        <td class="text-right font-roboto" '. ($today < $updateDate ? 'style="display:none;"' : ' ') .'>'.number_format($model->QtyMoved,$digit_stock).'</td> 
                                        <td class="text-right text-success font-roboto">'.Html::a(number_format($model->Quantity,$digit_stock),
                                                        [
                                                            '/warehousemoving/warehouse',
                                                            'WarehouseSearch[ItemId]'   => base64_encode($model->items->id), 
                                                            'WarehouseSearch[rowid]'    => $model->id
                                                        ],
                                                        ['target' => '_blank']).'</td>
                                        <td class="text-right text-warning font-roboto" '. ($today < $updateDate ? 'style="display:none;"' : ' ') .'>'.number_format($model->QtyToMove,$digit_stock).'</td>
                                    </tr>';
                            $after  +=  $model->Quantity;
                            $before +=  $model->QtyMoved;
                            $differ +=  $diff;
                        }
                    ?>
                                             
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right">รวม</td>
                            <td id="sum-remain" class="text-right" <?=($today < $updateDate ? 'style="display:none;"' : ' ')?>><?=number_format($before,$digit_stock)?></td>
                            <td id="sum-remain" class="text-right"><?=number_format($after,$digit_stock)?></td>
                            <td id="sum-remain" class="text-right" <?=($today < $updateDate ? 'style="display:none;"' : ' ')?>><?=number_format($differ,$digit_stock)?></td>
                        </tr>
                    </tfoot>
                </table>                
            </div>
             
        </div>
        <footer class="text-center">
            <div class="row text-left">
                <div class="col">
                        <label><?=Yii::t('common','Remark')?> : </label>
                        <div class="ml-5"><?=$head->remark?></div>
                </div>
            </div>
            <div class="row signature mt-5 mb-5 d-flex justify-content-end"> 
                 
                <div class="col-3" style="position:relative;">
                        <div style="position:absolute; left: 45%; top: -8%;"><?=$head->contact?></div>
                        <div>..............................</div>
                        <div>(<?=Yii::t('common','Product Counter')?>)</div>
                        <div style="position:absolute; left: 20%; top: 55px;">(....<?=date('d.../..m.../..Y',strtotime($head->PostingDate))?>....)</div>
                        <div style="position:absolute; right: 5%; bottom: -100%; font-size:10px;">REV02.13-05-2019</div>
                </div>
                
            </div>
        </footer>
        
    </div>

     

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>