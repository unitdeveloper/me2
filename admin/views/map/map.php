<?php
use common\models\SalesPeople;

use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\overlays\InfoWindow;
use dosamigos\google\maps\overlays\Marker;
use dosamigos\google\maps\Map;

use dosamigos\google\maps\services\DirectionsWayPoint;
use dosamigos\google\maps\services\TravelMode;
use dosamigos\google\maps\overlays\PolylineOptions;
use dosamigos\google\maps\services\DirectionsRenderer;
use dosamigos\google\maps\services\DirectionsService;
use dosamigos\google\maps\services\DirectionsRequest;
use dosamigos\google\maps\overlays\Polygon;
use dosamigos\google\maps\layers\BicyclingLayer;
 
$height     = '600';
$zoom       = 6;
$direct     = '';
$directSub  = 0;
if(isset($_POST['height']))     $height       = $_POST['height'];
if(isset($_POST['zoom']))       $zoom         = $_POST['zoom'];
if(isset($_POST['direct']))     $direct       = $_POST['direct'];
if(isset($_POST['directsub']))  $directSub    = $_POST['directsub'];


switch ($direct) {
  case 'North':
      $coord = new LatLng(['lat'=>18.380435,'lng'=>99.831485]);

      if($directSub==1) $coord = new LatLng(['lat'=>18.380435,'lng'=>99.831485]);
      if($directSub==3) $coord = new LatLng(['lat'=>16.269234,'lng'=>100.654369]);

    break;


  case 'East':
      $coord = new LatLng(['lat'=>13.216934,'lng'=>101.837480]);
    break;

  case 'South':
      $coord = new LatLng(['lat'=>8.429067,'lng'=>100.363157]);
      if($directSub==1) $coord = new LatLng(['lat'=>11.429067,'lng'=>100.363157]);
      if($directSub==3) $coord = new LatLng(['lat'=>7.429067,'lng'=>100.363157]);
    break;

  case 'Northeast':
      $coord = new LatLng(['lat'=>16.000869,'lng'=>102.491760]);
      if($directSub==1) $coord = new LatLng(['lat'=>16.464661,'lng'=>102.491760]);
      if($directSub==3) $coord = new LatLng(['lat'=>15.514370,'lng'=>102.491760]);
    break;

    
      
  case 'All':
      $coord = new LatLng(['lat'=>14.513698,'lng'=>101.305129]);
    break;
  
  default:
      $coord = new LatLng(['lat'=>13.612698,'lng'=>100.305129]);
    break;
}

//$coord = new LatLng(['lat'=>13.612698,'lng'=>100.305129]);
$map = new Map([
    'center'=>$coord,
    'zoom'=>$zoom,
    'width'=>'100%',
    'height'=>$height,
]);


foreach($contacts as $model){

  $district = '';
  if($model->district!='') $district = $model->districttb->DISTRICT_NAME;

  $province = '';
  if($model->province!='') $province = $model->provincetb->PROVINCE_NAME;

  $city     = '';
  if($model->city!='')      $city       = $model->citytb->AMPHUR_NAME;

  if($model->latitude=='')  $model->latitude  = substr($model->zipcode->latitude, 0,6).rand(10,100);
  if($model->longitude=='') $model->longitude = substr($model->zipcode->longitude, 0,6).rand(10,100);


  $coords = new LatLng(['lat'=>$model->latitude,'lng'=>$model->longitude]);  
  $marker = new Marker(['position'=>$coords]);


  $Balance = number_format(\common\models\SaleHeader::find()->where(['customer_id' => $model->id])->sum('balance'),2);
                      
 if(SalesPeople::find()->where(['code' => explode(',',$model->owner_sales)])->count()>0)
                                {
                                    $sales = SalesPeople::find()
                                    ->where(['code' => explode(',',$model->owner_sales)])
                                    ->all();
                                    $salpeople = '';

                                    foreach ($sales as $people) {
                                        $salpeople.= '<div>['.$people->code.'] '.$people->name.'</div>'; 
                                    }
                                     

                                }else {
                                    $salpeople = '-';
                                }

  $marker->attachInfoWindow(
    new InfoWindow([
        'content'=>"
             
               <h4><a href='index.php?r=customers%2Fcustomer%2Fview&id={$model->id}' target='_blank'>({$model->code})  {$model->name} </a></h4>
                  <table class='table table-striped table-bordered table-hover'>
                    <tr>
                        <td>ที่อยู่</td>
                        <td>{$model->address} </td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td>ต.{$district} อ.{$city} </td>
                    </tr>
                     
                    <tr>
                        <td> </td>
                        <td>จ.{$province} {$model->postcode} </td>
                    </tr>
                    <tr>
                        <td>โทร</td>
                        <td>{$model->phone} </td>
                    </tr>
                    <tr>
                        <td>ยอดขาย</td>
                        <td><a href='index.php?r=SaleOrders/order' target='_blank'>{$Balance} บาท</a></td>
                    </tr>
                    <tr>
                        <td>ผู้ดูแล</td>
                        <td>{$salpeople} </td>
                    </tr>
                  </table>",
        
        
    ])
  );
   
  
  
  $map->addOverlay($marker);  
  
  
     
}

// ขึดเส้น รอบที่ต้องการ

// $coords = [
//     new LatLng(['lat' => 25.774252, 'lng' => -80.190262]),
//     new LatLng(['lat' => 18.466465, 'lng' => -66.118292]),
//     new LatLng(['lat' => 32.321384, 'lng' => -64.75737]),
//     new LatLng(['lat' => 25.774252, 'lng' => -80.190262])
// ];
 
// $polygon = new Polygon([
//     'paths' => $coords
// ]);
// $map->addOverlay($polygon);

// -/. ขึดเส้น รอบที่ต้องการ
?>
 
<?php
echo $map->display();
?>
 