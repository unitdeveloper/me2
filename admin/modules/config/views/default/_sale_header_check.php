<section class="content-header">
      <h1>
      Sale Header
        <small>#Salepeople conflict</small>
      </h1>
        <?php 
        echo yii\widgets\Breadcrumbs::widget([
            'itemTemplate' => "<li><b>{link}</b></li>\n", // template for all links
            'links' => [
                ['label' => 'Configuration', 'url' => ['/config/default'],'template' => "<li><b>{link}</b></li>\n"],
                 
                'Salepeople conflict'


            ],
        ]);

        ?>
</section>
<br><br>
<div class="row">
    <div class="col-sm-12">
    
        <?php if(isset($_GET['table'])) : ?>
            <a href="index.php?r=Management%2Freport/inv-fixed"  class="btn btn-warning pull-right"><i class="fa fa-files-o"></i> Check Posted Invoice</a>
        <?php endif;?>
        

        <?php if(!isset($_GET['table'])) : ?>
            <a href="index.php?r=config%2Fdefault" class="btn btn-primary-ew"><i class="far fa-arrow-alt-circle-left"></i> Back</a>
            <a href="index.php?r=config/default/sale-header-fixed&session=<?=base64_encode(date('His'))?>" onclick="return confirm('Are you sure, you want to calibrate it?')" class="btn btn-warning-ew">
            <i class="fa fa-gavel"></i> Calibrate Sale id</a>
        <?php endif;?>
    </div>
</div>
<div class="row margin-top">

    <?php

            $data = '<div class="col-lg-12">';
            $data.= '<table width="100%" class="table table-bordered">';
            $data.='<tr class="bg-gray">';
                $data.= '<th>#</th>';
                $data.= '<th>Posting Date</th>';
                $data.= '<th>ID</th>';
                $data.= '<th>No</th>';
                $data.= '<th>Sale code</th>';
                $data.= '<th>name</th>';
                $data.= '<th class="bg-success text-center">Sale id</th>';   
                $data.= '<th class="bg-warning text-center">Conflict</th>';                             
                
            $data.= '</tr>';

            

            foreach($query as $key => $model){
                $data.='<tr>';
                    $data.= '<td>';
                    $data.= $key+1;
                    $data.= '</td>';
                    $data.= '<td>';
                    $data.= $model['order_date'];
                    $data.= '</td>';
                    $data.= '<td>';
                    $data.= $model['id'];
                    $data.= '</td>';
                    $data.= '<td>';
                    $data.= $model['no'];
                    $data.= '</td>';                    
                    $data.= '<td >';
                    $data.= $model['sales_people'];                    
                    $data.= '</td>';
                    $data.= '<td>';                   
                    $data.= $model['name'].'('.$model['sale'].')';
                    $data.= '</td>';     
                    $data.= '<td  class="bg-success text-center">';
                    $data.= $model['sale'];              
                    $data.= '</td>';
                    $data.= '<td class="bg-warning text-center">';                   
                    $data.= $model['sale_id'];
                    $data.= '</td>';  
                             
                $data.='</tr>';
            }
            if(count($query)<=0){
                $data.='<tr><td colspan="11"><h2><i class="far fa-thumbs-up"></i> ไม่มีข้อมูลผิดพลาด</h2> No data conflict</td></tr>';
                //$data ='<div class="col-sm-12">  No data conflict </div>'; 
            }
            $data.= '</table>';
            $data.= '</div>';

            
            echo $data;
    ?>
</div>