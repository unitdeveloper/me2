<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
 
?>

<div class="panel panel-info">
    <div class="panel-body">
        

            

    


    <?php

        $renders  = '';
    
        if(Yii::$app->request->post('data')){
            $raws     = Yii::$app->request->post('data');              
        }else{              
            $raws     = \common\models\Cheque::find()->where(['source_id' => $model->source_id])->all();
        }
        
        $balance_cheque     = 0;
        $totals             = 0;
        $i                  = 0;
        foreach ($raws as $key => $value) {

            if(Yii::$app->request->post('data')){
                $status = $value['status'];
                $id     = $value['id'];
                $no     = '';
            }else{
                $status             = $value->apply_to_status;
                $id                 = $value->apply_to;
                $balance_cheque     = $value->balance;
                $cheque_date        = $value->posting_date;
                $no                 = $value->apply_to_no;
            }

            
            $i++;
            
            if($status == 'Posted'){
                $source = \common\models\RcInvoiceHeader::findOne($id);
            }else{
                $source = \common\models\SaleInvoiceHeader::findOne($id);
            }

            $id         = '';
            $date       = '';
            $no         = $no;
            $balance    = $balance_cheque * 1;
            $ivDate     = '';
            
            
            if($source != null){
                $id      = $source->id;
                $date    = Yii::$app->request->post('data') ? $source->posting_date : $cheque_date;
                $no      = $source->no_;
                $balance = Yii::$app->request->post('data') ? $source->sumtotals->total : $balance_cheque;
                $totals += $value->balance;
                $ivDate  = $source->posting_date;
            }else{
                $totals += Yii::$app->request->post('data') ? 0 : $balance_cheque;
            }

            $renders.= '<tr data-key="'.$id.'">
                            <td>'.$i. '</td>
                            <td>'.date('Y-m-d', strtotime($date)).'</td>
                            <td >'.$no.' '.$ivDate.'</td>
                            <td class="text-right">'.number_format($balance,2).'</td>                              
                        </tr>';

            
        }

        $renders.= ' <tfoot>
                <tr>           
                <th class="bg-gray"># </th>
                <th class="bg-gray" colspan="2"></th>
                <th class="bg-gray text-right"><span class="total-balance">'. number_format($totals,2) .'</span></th>
                </tr>
            </tfoot>
        ';

        ?>

        <table class="table table-bordered" id="export_table">
            <thead>
                <tr>
                    <th class="bg-primary" style="width:30px;">#</th>
                    <th class="bg-primary" style="width:100px;"><?=Yii::t('common','Date')?></th>
                    <th class="bg-primary"><?=Yii::t('common','No')?></th>
                    <th class="bg-primary text-right"><?=Yii::t('common','Balance')?></th> 
                </tr>
            </thead>
            <?=$renders?>
        </table>

        <div class="mt-10">
            <a href="index.php?r=accounting/cheque/print&id=<?=$model->source_id;?>" target="_blank" class="btn btn-info pull-right mr-2" >
            <i class="fa fa-print" aria-hidden="true"></i> <?=Yii::t('common','Print');?> </a>
        </div>

        <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'banklist.name',
                    'bank_account',
                    'bank_branch',
                    'bank_id',
                    'create_date',
                    'posting_date',
                    //'bankaccount.name',
                    [
                        'label' => Yii::t('common','To account'),
                        'value' => function($model){
                            return $model->bankaccount->name.' #'.$model->bankaccount->bank_no.' #'.$model->bankaccount->banklist->name;
                        }
                    ],
                    [
                        'label' => Yii::t('common','Balance'),
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'bg-success text-red'],
                        'value' => function($model){
                            return '<div style="font-size:25px;">'.number_format($model->balance_cheque,2).'</div>';
                        }
                    ],
                    //'balance',
                    'post_date_cheque',
                    'transfer_time',
                    'apply_to',
                    'remark',
                    [
                        'label' => Yii::t('common',' '),
                        'format' => 'raw',
                        'value' => function($model){
                           
                            $html = '<a href="#" class="btn btn-danger ew-delete-cheque pull-right mr-2" data="'.$model->id.'">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i> '.Yii::t('common','Delete').'</a>';

                            $html.= '<a href="index.php?r=accounting/cheque/print&id='.$model->source_id.'" target="_blank" class="btn btn-info pull-right mr-2" >
                                    <i class="fa fa-print" aria-hidden="true"></i> '.Yii::t('common','Print').'</a>';


                            return $html;
                        }
                    ],
                ],
            ]) ?>
    </div>
</div>
