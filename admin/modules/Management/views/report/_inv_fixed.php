<?php
 
use common\models\RcInvoiceHeader;

?>
<section class="content-header">
      <h1>
      Case conflict
        <small>#Posted Invoice</small>
      </h1>
        <?php 
        echo yii\widgets\Breadcrumbs::widget([
            'itemTemplate' => "<li><b>{link}</b></li>\n", // template for all links
            'links' => [
                ['label' => 'Configuration', 'url' => ['/config/default'],'template' => "<li><b>{link}</b></li>\n"],
                 
                'Case conflict'


            ],
        ]);

        ?>
</section>
<br><br>
<div class="row">
    <div class="col-sm-12">
    
        <?php if(isset($_GET['table'])) : ?>
            <a href="index.php?r=config%2Fdefault" class="btn btn-primary-ew"><i class="far fa-arrow-alt-circle-left"></i> Back</a>
            <a href="index.php?r=Management%2Freport/inv-fixed"  class="btn btn-warning-ew pull-right"><i class="fa fa-files-o"></i> Check Posted Invoice</a>
        <?php endif;?>
        

        <?php if(!isset($_GET['table'])) : ?>
            <a href="index.php?r=config%2Fdefault" class="btn btn-primary-ew"><i class="far fa-arrow-alt-circle-left"></i> Back</a>
            <a href="index.php?r=Management%2Freport/inv-fixed&table=SaleInvoiceHeader"  class="btn btn-primary-ew pull-right"><i class="fa fa-file-o"></i> Check Sale Invoice</a>
            <a href="index.php?r=Management%2Freport/inv-fixed&update=200" onclick="return confirm('Are you sure, you want to calibrate it?')" class="btn btn-warning-ew"><i class="fa fa-download"></i> Calibrate Invoice</a>
            <a href="index.php?r=Management%2Freport/inv-fixed&update=error" onclick="return confirm('Are you sure, you want to calibrate it?')" class="btn btn-danger-ew"><i class="fa fa-download"></i> Calibrate Error Invoice</a>
        <?php endif;?>
    </div>
</div>
<div class="row margin-top">

    <?php
  
    // echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >';
    //         echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">';
    //         echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';

            $data = '<div class="col-lg-12">';
            $data.= '<table width="100%" class="table table-bordered">';
            $data.='<tr class="bg-gray">';
                $data.= '<th>#</th>';
                $data.= '<th>Posting Date</th>';
                $data.= '<th>ID</th>';
                $data.= '<th>No</th>';
                $data.= '<th>Sale People</th>';
                $data.= '<th>Sum Line</th>';
                $data.= '<th>Sum Total</th>';
                $data.= '<th>Discount</th>';
                $data.= '<th>Percent Discount</th>';
                $data.= '<th>Manual Discount</th>';
                $data.= '<th>Include Vat</th>';
            $data.= '</tr>';

            

            foreach($query->all() as $key => $model){
                $data.='<tr>';
                    $data.= '<td>';
                    $data.= $key+1;
                    $data.= '</td>';
                    $data.= '<td>';
                    $data.= $model->posting_date;
                    $data.= '</td>';
                    $data.= '<td>';
                    $data.= $model->id;
                    $data.= '</td>';
                    $data.= '<td>';
                    $data.= $model->no_;
                    $data.= '</td>';
                    $data.= '<td>';
                    $data.= $model->sales_people;
                    $data.= '</td>';
                    $data.= '<td>';
                    $data.= $model->sumLine;
                    $data.= '</td>';
                    $data.= '<td>';
                    $data.= $model->sumTotal;
                    $data.= '</td>';
                    $data.= '<td>';
                    $data.= $model->discount;
                    $data.= '</td>';
                    $data.= '<td>';
                    $data.= ($model->percent_discount)? $model->percent_discount : '<i class="fas fa-exclamation-triangle" alt="null" title="null"></i>';
                    $data.= '</td>'; 
                    $data.= '<td align=right>';

                    $percentDiscount    = ($model->discount /$model->sumLine)*100;
                    $Discount           = $model->sumLine * ($percentDiscount /100);
                    if($Discount != $model->discount){
                        $color = 'color:red;';
                        
                        if(isset($_GET['update'])){
                            \Yii::$app->getSession()->addFlash('danger',Yii::t('common','ยกเลิก! เนื่องจากไม่ใช่ข้อผิดพลาด (ต้องการไม่แสดง % ส่วนลด)'));
                            // if($_GET['update']=='error'){                        
                            //     $Inv   = RcInvoiceHeader::findOne($model->id);
                            //     $Inv->percent_discount = $Discount;
                            //     $Inv->save();
                            // }
                        }

                    }else {
                        $color = 'color:green;';
                        if(isset($_GET['update'])){
                            \Yii::$app->getSession()->addFlash('danger',Yii::t('common','ยกเลิก! เนื่องจากไม่ใช่ข้อผิดพลาด (ต้องการไม่แสดง % ส่วนลด)'));
                            if($_GET['update']==200){  
                                //$Inv   = RcInvoiceHeader::findOne($model->id);
                                //$Inv->percent_discount = $Discount;
                                
                                //$Inv->save();
                            }
                        }
                        
                    }



                    $data.=     '<div>'.$percentDiscount.' => <span style="'.$color.'">'.$Discount.'</span></div>';

                    $data.= '</td>'; 
                    $data.= '<td>';
                    $data.= $model->include_vat;
                    $data.= '</td>';
                $data.='</tr>';
            }
            if($query->count()<=0){
                \Yii::$app->getSession()->addFlash('danger',Yii::t('common','ยกเลิก! เนื่องจากไม่ใช่ข้อผิดพลาด (ไม่ต้องการแสดง % ส่วนลด)'));
                $data.='<tr><td colspan="11"><h2><i class="far fa-thumbs-up"></i> ไม่มีข้อมูลผิดพลาด</h2> No data conflict</td></tr>';
                //$data ='<div class="col-sm-12">  No data conflict </div>'; 
            }
            $data.= '</table>';
            $data.= '</div>';

            
            echo $data;
    ?>
</div>