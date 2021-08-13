<?php 
use kartik\widgets\SwitchInput;

?>
<section class="content-header">
      <h1>
      Configuration System
        <small>ตรวจสอบการทำงาน</small>
      </h1>
      <?php 
        echo yii\widgets\Breadcrumbs::widget([
            'itemTemplate' => "<li><b>{link}</b></li>\n", // template for all links
            'links' => [
                //['label' => 'Configuration', 'url' => ['/config/default', 'id' => 1]],
                'Configuration'
                // [
                //     'label' => 'Post Category',
                //     'url' => ['post-category/view', 'id' => 10],
                //     'template' => "<li><b>{link}</b></li>\n", // template for this link only
                // ],
            
                
            ],
        ]);

        ?>
</section>
<style>
  .content-wrapper {
    background-color: #ecf0f5 !important;
}
</style>
<div class="config-default-index">
  <div class="box-body">
    <p>ระบบบริหารจัดการ และ ตรวจสอบการทำงาน ที่อาจจะเกิดกรณีขัดแยง</p>
    

    <div class="row" style="margin-bottom:200px;">
      <?= $this->render('_cpu_chart') ?>
      <div class="col-sm-12 text-right" style="margin-top:50px;">
        Status <i class="far fa-dot-circle blink  <?=($system->enabled ==1 ? 'text-green ': 'text-yellow')?>"></i>
        <h4 > <i class="fab fa-windows text-aqua"></i>  <?=Yii::t('common','Start System')?> </h4>
        <?php  
            // Label and input vertically stacked on separate lines
            echo SwitchInput::widget([
                'name' => 'system-status',
                'value' => $system->enabled,
                'inlineLabel'   => false,
                'pluginOptions' => [
                    'onColor'   => 'success',
                    'offColor'  => 'danger',
                    'size'      => 'small',
                    'onText'    => 'Started',
                    'offText'   => 'Stoped'
                ],
                'pluginEvents'  => [                    
                  "switchChange.bootstrapSwitch" => "function() { 
                      if($(this).is(':checked')){
                          switchStartSystem('1', res=> {
                            console.log(res);
                          })
                      }else{
                          switchStartSystem('0', res=> {
                            console.log(res);
                          })                           
                      }  
                  }",
                ]
            ]); 
         ?>        
      </div>
      
      <div class="col-sm-12 text-right"  >
      <hr class="style2"/>
        <h4 style="margin-top:50px;"><i class="fas fa-cubes"></i> <?=Yii::t('common','Lock Stock')?></h4>
        <?php  
            // Label and input vertically stacked on separate lines
            echo SwitchInput::widget([
                'name' => 'lock-stock',
                'value' => $stock->enabled,
                'inlineLabel'   => false,
                'pluginOptions' => [
                    'onColor'   => 'danger',
                    'offColor'  => 'success',
                    'size'      => 'small',
                    'onText'    => 'Locked',
                    'offText'   => 'Unlock'
                ],
                'pluginEvents'  => [                    
                  "switchChange.bootstrapSwitch" => "function() { 
                      if($(this).is(':checked')){
                          switchStockLocker('1', res=> {
                            console.log(res);
                          })
                      }else{
                          switchStockLocker('0', res=> {
                            console.log(res);
                          })                           
                      }  
                  }",
                ]
            ]); 
         ?>        
      </div>
    </div>

    <hr class="style2"/>
    <h3>List Image File</h3>
    <div class="row">            
        <div class="col-sm-3">
            <a class="info-box" href="index.php?r=config/default/list-image" target="_blank">
                <span class="info-box-icon bg-aqua"><i class="far fa-images"></i></span>

                <div class="info-box-content">
                <span class="info-box-text">Image Folder</span>
                <span class="info-box-number">Explorer</span>
                </div>
                <!-- /.info-box-content -->
            </a>
        </div>
        <div class="col-sm-3">
          <a class="info-box" href="index.php?r=Manufacturing%2Fdefault/validate-bom&word=CHONG-8" target="_blank">
              <span class="info-box-icon bg-black"><i class="fas fa-sitemap"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">Validate BOM</span>
              <span class="info-box-number">BOM</span>
              </div>
              <!-- /.info-box-content -->
          </a>
        </div>
    </div>

    <hr class="style2"/>
    <h3>Installation</h3>
    <div class="row">            
        <div class="col-sm-3">
            <a class="info-box" href="index.php?r=install/default">
                <span class="info-box-icon bg-yellow"><i class="fa fa-cogs"></i></span>

                <div class="info-box-content">
                <span class="info-box-text">Install</span>
                <span class="info-box-number">Number Series</span>
                </div>
                <!-- /.info-box-content -->
            </a>
        </div>
        <div class="col-sm-9">
         
        </div>
    </div>
    
    <h3>Configurations</h3>   
    <div class="row" style="margin-bottom:150px;">
      <div class="col-sm-3">
          <a class="info-box" href="index.php?r=language">
              <span class="info-box-icon bg-green"><i class="fa fa-flag"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">Config</span>
              <span class="info-box-number">Language</span>
              </div>
              <!-- /.info-box-content -->
          </a>
      </div>     
      <div class="col-sm-3">
          <a class="info-box" href="index.php?r=admin">
              <span class="info-box-icon bg-red-active"><i class="fas fa-unlock-alt"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">Administration</span>
              <span class="info-box-number">Rules</span>
              </div>
              <!-- /.info-box-content -->
          </a>
      </div>    
    </div>
    <hr class="style2"/>
    <h3>Data Validation</h3>
    <div class="row" style="margin-bottom:150px;">
      <div class="col-sm-3">
          <a class="info-box" href="index.php?r=Management%2Freport/inv-fixed">
              <span class="info-box-icon bg-aqua"><i class="fa fa-files-o"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">Case conflict</span>
              <span class="info-box-number">Posted Invoice</span>
              </div>
              <!-- /.info-box-content -->
          </a>
      </div>        
      <div class="col-sm-3">
            <a class="info-box" href="index.php?r=config/default/sale-header-check">
                <span class="info-box-icon bg-green-active"><i class="fas fa-shopping-bag"></i></span>
                <div class="info-box-content">
                <span class="info-box-text">Sale Header </span>
                <span class="info-box-number">Sales conflict</span>
                </div>
                <!-- /.info-box-content -->
            </a>
        </div>
        <div class="col-sm-3">
            <a class="info-box" href="index.php?r=config/default/sale-invoice-check">
                <span class="info-box-icon bg-orange-active"><i class="far fa-file-alt"></i></span>
                <div class="info-box-content">
                <span class="info-box-text">Sale Invoice </span>
                <span class="info-box-number">Sales conflict</span>
                </div>
                <!-- /.info-box-content -->
            </a>
        </div>

        <div class="col-sm-3">
            <a class="info-box" href="index.php?r=config/default/posted-invoice-check">
                <span class="info-box-icon bg-red"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                <span class="info-box-text">Posted Invoice </span>
                <span class="info-box-number">Sales conflict</span>
                </div>
                <!-- /.info-box-content -->
            </a>
        </div>

        <div class="col-sm-3">
            <a class="info-box" href="index.php?r=express/default/index">
                <span class="info-box-icon bg-aqua">Exp</span>
                <div class="info-box-content">
                <span class="info-box-text">EXPRESS </span>
                <span class="info-box-number">COMPARE</span>
                </div>
                <!-- /.info-box-content -->
            </a>
        </div>

        <div class="col-sm-3">
            <a class="info-box" href="index.php?r=items/report/low">
                <span class="info-box-icon bg-green">IT</span>
                <div class="info-box-content">
                <span class="info-box-text">สินค้าที่จำนวนติดลบ </span>
                <span class="info-box-number">ITEM LOW STOCK</span>
                </div>
                <!-- /.info-box-content -->
            </a>
        </div>
    </div>
 
    
    <hr class="style2"/>

    <h3>System Informations</h3>
    <div class="row">
      <div class="col-sm-6">
        <div class="panel panel-danger">
            <div class="panel-heading">
              <h3 class="panel-title">CPU</h3>
            </div>
            <div class="panel-body">
    
              <?php

              //header("Content-Type: text/plain");

              $cpuLoad = common\models\Systeminfo::getServerLoad();
              if (is_null($cpuLoad)) {
                  echo "CPU load not estimateable (maybe too old Windows or missing rights at Linux or Windows)";
              }
              else {
                  echo number_format($cpuLoad,2) . " %";
              }


              ?>
              </div>
        </div>
      </div>
    
      <div class="col-sm-6">
        <div class="panel panel-danger">
            <div class="panel-heading">
              <h3 class="panel-title">System Info</h3>
            </div>
            <div class="panel-body">
    

            <?php

            use ProcessMonitor\ProcessMonitor;

            $monitor = new ProcessMonitor();
            $process = $monitor->search("apache");
            $processList = $monitor->searchMultiple("rust-server|nginx");
            $result = $monitor->searchMultiple("rust-server|nginx", true);
            var_dump($processList);
            echo '<hr>';
            if ($result){
              var_dump($result->summary);
              foreach($result->summary as $key => $info){
                 // echo $key.' -> '.$info;
              }  
            }  
            echo '<hr>';
            if ($process) {
              echo "<div>Apache (PID: " . $process->pid . ") is using " . $process->cpu . "% CPU and " . $process->ram . " RAM </div>";
            }
                
            ?>
            </div>
        </div>
      </div>
    </div>


     



    <div class="row">
      <div class="col-sm-6">
        <div class="panel panel-default">
          <div class="panel-body">
              <?php 
                $totalDisk  = disk_total_space("/");
                $freeDisk   = disk_free_space("/");
              ?>
              Disk Total Space : <span id="disk-total" data-val="<?=formatSize($totalDisk);?>"><?=formatSizeUnits($totalDisk);?></span>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-body">
              Disk Free Space : <span id="disk-free" data-val="<?=formatSize($freeDisk);?>"><?=formatSizeUnits($freeDisk);?></span>       
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        
      </div>      
    </div>

  </div> 
</div>

<?php
function getMemory(){
 $fh = fopen('/proc/meminfo','r');
 $mem = 0;
 while ($line = fgets($fh)) {
   $pieces = array();
   if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
     $mem = $pieces[1];
     break;
   }
 }
 fclose($fh);
 return $mem;
}
 ///echo getMemory()." kB RAM found"; 


function getSystemMemInfo() 
{  
  $fh = fopen('/proc/meminfo','r');
  $meminfo = array();
  while ($line = fgets($fh)) {
    $pieces = array();

    list($key, $val) = explode(":", $line);
    $meminfo[$key] = trim($val);
  }
  fclose($fh);     

  return $meminfo;
}

//var_dump(getSystemMemInfo());
 
?>
 

 
<?php
// Snippet from PHP Share: http://www.phpshare.org

function formatSizeUnits($bytes){
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}

function formatSize($bytes){
  if ($bytes >= 1073741824)
  {
      $bytes = number_format($bytes / 1073741824, 2);
  }
  elseif ($bytes >= 1048576)
  {
      $bytes = number_format($bytes / 1048576, 2);
  }
  elseif ($bytes >= 1024)
  {
      $bytes = number_format($bytes / 1024, 2);
  }
  elseif ($bytes > 1)
  {
      $bytes = $bytes;
  }
  elseif ($bytes == 1)
  {
      $bytes = $bytes;
  }
  else
  {
      $bytes = '0';
  }

  return $bytes;
}
 

?>

 



<!-- HTML -->

<?php 
 

$JS=<<<JS
 


let switchStockLocker = (sw, callback) => {
  fetch("?r=config/default/stock-locker", {
    method: "POST",
    body: JSON.stringify({enabled:sw}),
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
    }
  })
    .then(res => res.json())
    .then(response => {
      callback(response);
    })
    .catch(error => {
      console.log(error);
    });
};

let switchStartSystem = (sw, callback) => {
  fetch("?r=config/default/start-system", {
    method: "POST",
    body: JSON.stringify({enabled:sw}),
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
    }
  })
    .then(res => res.json())
    .then(response => {
      callback(response);
    })
    .catch(error => {
      console.log(error);
    });
};


JS;

$this->registerJS($JS,\yii\web\View::POS_HEAD) ;

?>
