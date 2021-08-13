<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\models\SetupNoSeries;
use common\models\NumberSeries;

use common\models\RuningNoseries;

use yii\web\NotFoundHttpException;
use yii\helpers\Url;
/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class Generater extends NumberSeries
{
protected static function notifyError($data,$series){

  //$model = \common\models\
  $char   = '0';
  $digit  = '000';

  if($data == NULL){
    $newSerie = self::newGenerate($series);
    return $newSerie;
    exit();
    

  // $js =<<<JS
  //   jQuery.ajax({
  //     url:"index.php?r=series/ajax_autogenseries",
  //     type: 'GET',
  //     data: {code:01,char:{$char},digit:{$digit},NoSeries:{$series}},
  //     async:false,     
  //   });
  // JS;

  // echo '<script src="js/jquery-2.1.3.min.js"></script>
  //       <script>
  //         alert("(Number Series ไม่มีอยู่) \r\n ระบบกำลังทำการสร้าง Number Series ให้อัตโนมัติ");
          
  //         location.reload();
  //       </script>';

  //   if(self::newGenerate($series)){
  //     Yii::$app->$getController->redirect(Yii::$app->request->url);
  //   }   
  


  }
}
public function GenNumber($table_name,$field_name,$cond,$close)
    {
        $SDate = date('Y-m-').'01';
        $EDate = date("Y-m-t", strtotime($SDate));
        $comp_id = Yii::$app->session->get('Rules')['comp_id'];

        // Find structure.
        // $model = new SetupNoSeries();
        // $modelSeries = $model->find()
        //               ->where(['form_id' => $id])
        //               ->andWhere(['comp_id' => $comp_id])
        //               ->one();

        // Generate This model
        $NoSeries = NumberSeries::find()
                    ->where(['table_name' => $table_name])
                    ->andWhere(['field_name' => $field_name])
                    ->andWhere(['cond' => $cond])
                    ->andWhere(['comp_id' => $comp_id])
                    ->one();

        // Find relation number series.
        $NewNoSeries = RuningNoseries::find()
                      ->where(['between', 'start_date', $SDate, $EDate])
                      ->andWhere(['no_series' =>$NoSeries->id])
                      ->andWhere(['comp_id' => $comp_id])
                      ->one();

        self::notifyError($NewNoSeries,$NoSeries->id);

        if($NewNoSeries->last_no != '')
        {

          // ดึงรายการเก่ามาคำนวน
          //$LasRuning = explode('-',$NewNoSeries->last_no);
          $LasNo  = explode('-',$NewNoSeries->last_no);

          // ค้นหาหลักแรก
          //$SubFormat    = explode('-',$NewNoSeries->start_no);
          //$StartFormat  = strlen($SubFormat[0]);


          if($close!= true)
          {

            $LasNoX = explode('-',$NewNoSeries->start_no);
            $LasNoY = substr($NewNoSeries->last_no, strlen($LasNoX[0]));
            $LasNo[0]  = $LasNoX[0];
            $LasNo[1] = $LasNoY;

          }

          //var_dump($LasNoY);
            //exit();

        }else {
          $LasNo = explode('-',$NewNoSeries->start_no);

          // Update Stating No.
          $starNox = $LasNo[0];
          $RunNox = $this->CheckNumber($LasNo[1]);

          if($close== true)
          {
            $RuningNumberx = $starNox.'-'.str_pad($RunNox, strlen($LasNo[1]), "0", STR_PAD_LEFT);
          }else {
            $RuningNumberx = $starNox.''.str_pad($RunNox, strlen($LasNo[1]), "0", STR_PAD_LEFT);


          }



          $NoSeries->starting_no = $RuningNumberx;
          // End Starting No.
        }

        // Find runing no.
        // By explode XX1701-001
        $starNo = $LasNo[0];

        // Sprit runing + 1.
        $RunNo = $this->CheckNumber($LasNo[1]);

        // Define runing charector nex to number.
        // And count the lenge of runing number. For add Zeros(0)  befor number.
        //

        if($close== true)
        {
          $RuningNumber = $starNo.'-'.str_pad($RunNo, strlen($LasNo[1]), "0", STR_PAD_LEFT);
        }else {
          $RuningNumber = $starNo.''.str_pad($RunNo, strlen($LasNo[1]), "0", STR_PAD_LEFT);
        }




        $NoSeries->last_no = $RuningNumber;

        $NoSeries->last_date = date('Y-m-d');
        $NoSeries->save();

        $NewNoSeries->last_no = $RuningNumber;
        $NewNoSeries->save();
        // return XX1701-00runing
        return $RuningNumber;
    }

public function GenerateNoseries($id,$close)
    {
    	  $SDate = date('Y-m-').'01';
        $EDate = date("Y-m-t", strtotime($SDate));
        $comp_id = Yii::$app->session->get('Rules')['comp_id'];

        // Find structure.
        //$model = new SetupNoSeries();
        $modelSeries = SetupNoSeries::find()
                      ->where(['form_id' => $id])
                      ->andWhere(['comp_id' => $comp_id])
                      ->one();
        if(!$modelSeries){
          return 'Error';
          exit();
        } 
        // Generate This model
        $NoSeries = NumberSeries::find()
                    ->where(['id' => $modelSeries->no_series])
                    ->andWhere(['comp_id' => $comp_id])
                    ->one();
        if(!$NoSeries){
          return 'Error';
          exit();
        }
        // Find relation number series.
        $NewNoSeries = RuningNoseries::find()
                      ->where(['between', 'start_date', $SDate, $EDate])
                      ->andWhere(['no_series' =>$modelSeries->no_series])
                      ->andWhere(['comp_id' => $comp_id])
                      ->one();

        self::notifyError($NewNoSeries,$NoSeries->id);

        if($NewNoSeries->last_no != '')
        {

          // ดึงรายการเก่ามาคำนวน
        	//$LasRuning = explode('-',$NewNoSeries->last_no);
          $LasNo  = explode('-',$NewNoSeries->last_no);

          // ค้นหาหลักแรก
          //$SubFormat    = explode('-',$NewNoSeries->start_no);
          //$StartFormat  = strlen($SubFormat[0]);


          if($close!= true)
          {

            $LasNoX = explode('-',$NewNoSeries->start_no);
            $LasNoY = substr($NewNoSeries->last_no, strlen($LasNoX[0]));
            $LasNo[0]  = $LasNoX[0];
            $LasNo[1] = $LasNoY;

          }

          //var_dump($LasNoY);
            //exit();

       	}else {
       		$LasNo = explode('-',$NewNoSeries->start_no);

       		// Update Stating No.
       		$starNox = $LasNo[0];
       		$RunNox = $this->CheckNumber($LasNo[1]);

          if($close== true)
          {
            $RuningNumberx = $starNox.'-'.str_pad($RunNox, strlen($LasNo[1]), "0", STR_PAD_LEFT);
          }else {
            $RuningNumberx = $starNox.''.str_pad($RunNox, strlen($LasNo[1]), "0", STR_PAD_LEFT);


          }



       		$NoSeries->starting_no = $RuningNumberx;
       		// End Starting No.
       	}

        // Find runing no.
        // By explode XX1701-001
        $starNo = $LasNo[0];

        // Sprit runing + 1.
        $RunNo = $this->CheckNumber($LasNo[1]);

        // Define runing charector nex to number.
        // And count the lenge of runing number. For add Zeros(0)  befor number.
        //

        if($close== true)
        {
          $RuningNumber = $starNo.'-'.str_pad($RunNo, strlen($LasNo[1]), "0", STR_PAD_LEFT);
        }else {
          $RuningNumber = $starNo.''.str_pad($RunNo, strlen($LasNo[1]), "0", STR_PAD_LEFT);
        }




        $NoSeries->last_no = $RuningNumber;

        $NoSeries->last_date = date('Y-m-d');
        $NoSeries->save();

        $NewNoSeries->last_no = $RuningNumber;
        $NewNoSeries->save();
        // return XX1701-00runing
        return $RuningNumber;
    }

    public static function CheckNumber($value)
    {

      $str = $value;
      $arr = preg_split('/(?<=[0-9])(?=[a-z]+)/i',$str);

      // Check Number Or Charector ?
      if(ctype_digit($arr[0])== true){

        return $arr[0]+1;

      }else {
        // throw new NotFoundHttpException('The requested page does not exist.');
        //echo "<script>alert('Number Series ไม่ถูกต้อง \r\n ระบบกำลังพยายามสร้าง Runing No.');</script>";
        return 1;
      }


    }

    public function AutoGenerate($Char)
    {
      return $Char;
    }



    public static function NextRuning($table_name,$field_name,$cond,$close){
      $LasNo        = [];
      $transaction  = Yii::$app->db->beginTransaction();
      try {
 
        $SDate = date('Y-m-').'01';
        $EDate = date("Y-m-t", strtotime($SDate));
        $comp_id = Yii::$app->session->get('Rules')['comp_id'];



        // Generate This model
        $NoSeries = NumberSeries::find()
                    ->where(['table_name'     => trim($table_name)])
                    ->andWhere(['field_name'  => trim($field_name)])
                    ->andWhere(['cond'        => trim($cond)])
                    ->andWhere(['comp_id'     => $comp_id])
                    ->one();

        if(!$NoSeries){
          return 'Error';
          exit();
        }

        

        // Find relation number series.
        $NewNoSeries = RuningNoseries::find()
                      ->where(['between', 'DATE(start_date)', $SDate, $EDate])
                      ->andWhere(['no_series' =>$NoSeries->id])
                      ->andWhere(['comp_id' => $comp_id])
                      ->one();

        

        self::notifyError($NewNoSeries,$NoSeries->id);

        if($NewNoSeries->last_no != ''){
          //var_dump($NewNoSeries->last_no); exit;
          // ดึงรายการเก่ามาคำนวน
          //$LasRuning = explode('-',$NewNoSeries->last_no);
          $LasNo  = explode('-',$NewNoSeries->last_no);

          // ค้นหาหลักแรก
          //$SubFormat    = explode('-',$NewNoSeries->start_no);
          //$StartFormat  = strlen($SubFormat[0]);
          if($close!= true){

            $LasNoX = explode('-',$NewNoSeries->start_no);
            $LasNoY = substr($NewNoSeries->last_no, strlen($LasNoX[0]));
            $LasNo[0]  = $LasNoX[0];
            $LasNo[1] = $LasNoY;

          }

        }else {
          // มีขีดกลาง
          if(strpos( $NewNoSeries->start_no, "-" )){
            $LasNo    = explode('-',$NewNoSeries->start_no);

            // Update Stating No.
            $starNox  = $LasNo[0];
            $RunNox   = self::CheckNumber($LasNo[1]);

            $close = true;
            // End Starting No.
            
          }else{ // ไม่มีขีดกลาง
            $close    = false;            
            $LasNo[1] = $NoSeries->format_gen;
            $RunNox   = self::CheckNumber($NewNoSeries->last_no);
            $starNox  = $NoSeries->starting_char;
            $LasNo[0] = $starNox;
          }

          if($close== true){
            $RuningNumberx = $starNox.'-'.str_pad($RunNox, strlen($LasNo[1]), "0", STR_PAD_LEFT);
          }else {
            $RuningNumberx = $starNox.''.str_pad($RunNox, strlen($NewNoSeries->last_no), "0", STR_PAD_LEFT);
          }
          $NoSeries->starting_no = $RuningNumberx;
          return $RuningNumberx;
          exit;
        }

        // Find runing no.
        // By explode XX1701-001
        $starNo = $LasNo[0];

        // Sprit runing + 1.
        $RunNo = self::CheckNumber($LasNo[1]);

        // Define runing charector nex to number.
        // And count the lenge of runing number. For add Zeros(0)  befor number.
        if($close== true){
          $RuningNumber = $starNo.'-'.str_pad($RunNo, strlen($LasNo[1]), "0", STR_PAD_LEFT);
        }else {
          $RuningNumber = $starNo.''.str_pad($RunNo, strlen($LasNo[1]), "0", STR_PAD_LEFT);
        }

        
        $transaction->commit();
                
      } catch (\Exception $e) {
        
          $transaction->rollBack();
          //Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
          return (Object)[
            'status' => 200,
            'message' => Yii::t('common','{:e}',[':e' => $e])
          ];
          //throw $e;
      }
        // return XX1701-00runing
        return $RuningNumber;
    }



    public static function CreateNextNumber($table_name,$field_name,$cond,$NewRuningNumber)
    {
        $SDate = date('Y-m-').'01';
        $EDate = date("Y-m-t", strtotime($SDate));
        $comp_id = Yii::$app->session->get('Rules')['comp_id'];



        // Generate This model
        $NoSeries = NumberSeries::find()
                    ->where(['table_name' => $table_name])
                    ->andWhere(['field_name' => $field_name])
                    ->andWhere(['cond' => $cond])
                    ->andWhere(['comp_id' => $comp_id])
                    ->one();

        // Find relation number series.
        $NewNoSeries = RuningNoseries::find()
                      ->where(['between', 'DATE(start_date)', $SDate, $EDate])
                      ->andWhere(['no_series' =>$NoSeries->id])
                      ->andWhere(['comp_id' => $comp_id])
                      ->one();

        self::notifyError($NewNoSeries,$NoSeries->id);

        $NoSeries->last_no = $NewRuningNumber;

        $NoSeries->last_date = date('Y-m-d');
        $NoSeries->save();

        $NewNoSeries->last_no = $NewRuningNumber;
        if($NewNoSeries->save()){
          return $NewRuningNumber;
        }else{
          return (Object)[
            'status' => 500,
            'message' => json_encode($NewNoSeries->getErrors(),JSON_UNESCAPED_UNICODE)
          ];
        }
        // return XX1701-00runing
        
    }

    public function updateNextNumber($id,$NewRuningNumber)
    {
        $SDate = date('Y-m-').'01';
        $EDate = date("Y-m-t", strtotime($SDate));
        $comp_id = Yii::$app->session->get('Rules')['comp_id'];



        // Generate This model
        $NoSeries = NumberSeries::findOne($id);

        // Find relation number series.
        $NewNoSeries = RuningNoseries::find()
                      ->where(['between', 'start_date', $SDate, $EDate])
                      ->andWhere(['no_series' =>$NoSeries->id])
                      ->andWhere(['comp_id' => $comp_id])
                      ->one();

        self::notifyError($NewNoSeries,$NoSeries->id);

        $NoSeries->last_no = $NewRuningNumber;

        $NoSeries->last_date = date('Y-m-d');
        $NoSeries->save();

        $NewNoSeries->last_no = $NewRuningNumber;
        $NewNoSeries->save();
        // return XX1701-00runing
        return $NewRuningNumber;
    }

    public function LastNumber($table_name,$field_name,$cond)
    {
        $SDate = date('Y-m-').'01';
        $EDate = date("Y-m-t", strtotime($SDate));
        $comp_id = Yii::$app->session->get('Rules')['comp_id'];


        $NoSeries = NumberSeries::find()
                    ->where(['table_name' => $table_name])
                    ->andWhere(['field_name' => $field_name])
                    ->andWhere(['cond' => $cond])
                    ->andWhere(['comp_id' => $comp_id])
                    ->one();

        // Find relation number series.
        $NewNoSeries = RuningNoseries::find()
                      ->where(['between', 'start_date', $SDate, $EDate])
                      ->andWhere(['no_series' =>$NoSeries->id])
                      ->andWhere(['comp_id' => $comp_id])
                      ->one();

        self::notifyError($NewNoSeries,$NoSeries->id);

        if($NewNoSeries->last_no==''){
          //$NewNoSeries->last_no = $this->GenerateNoseries($NewNoSeries->id,null);
          $NewNoSeries->last_no = $this->getRuning($table_name,$field_name,$cond);
        }


        return $NewNoSeries->last_no;
    }



    public function InfoSeries($table_name,$field_name,$cond,$close)
    {
        $SDate = date('Y-m-').'01';
        $EDate = date("Y-m-t", strtotime($SDate));
        $comp_id = Yii::$app->session->get('Rules')['comp_id'];



        // Generate This model
        $NoSeries = NumberSeries::find()
                    ->where(['table_name' => $table_name])
                    ->andWhere(['field_name' => $field_name])
                    ->andWhere(['cond' => $cond])
                    ->andWhere(['comp_id' => $comp_id])
                    ->one();

        return $NoSeries;
    }




    public static function newGenerate($id){

      $NoSeries   = \common\models\NumberSeries::findOne($id);

      if($NoSeries->separate=='NULL') $NoSeries->separate = '';
      //$Format     = explode('-',$NoSeries->format_gen);

      switch ($NoSeries->format_type) {

        case 'ONCE':
            $model = new RuningNoseries();

            $model->no_series     = $NoSeries->id;
            $model->start_date    = date('Y-')."01-01";


            $model->start_no      = $NoSeries->starting_char.$NoSeries->separate.$NoSeries->format_gen;
            $model->comp_id       = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();
          break;


        case '12M':

            for ($i=1; $i < 13; $i++) {

              $model = new RuningNoseries();

              $model->no_series     = $NoSeries->id;
              $model->start_date    = date('Y-').sprintf("%02d",$i)."-01";
              $model->start_no      = Generater::Separator($NoSeries,sprintf("%02d",$i));
              $model->comp_id       = Yii::$app->session->get('Rules')['comp_id'];
              $model->save();

            }

          break;



        case '1Y':

            $model = new RuningNoseries();

            $model->no_series     = $NoSeries->id;
            $model->start_date    = date('Y-')."01-01";
            $model->start_no      = $NoSeries->starting_char.$NoSeries->separate.$NoSeries->format_gen;
            $model->comp_id       = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();
          break;


        default:

            $model = new RuningNoseries();

            $model->no_series     = $NoSeries->id;
            $model->start_date    = date('Y-')."01-01";
            $model->start_no      = $NoSeries->starting_char.$NoSeries->separate.$NoSeries->format_gen;
            $model->comp_id       = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();


          break;
      }

      return $model;



    }

    public static function Separator($model,$runing){

      switch ($model->separate) {
        case 'YYYY':
          return $model->starting_char.date('Y').$model->format_gen;
          break;

        case 'YYYY-':
          return $model->starting_char.date('Y').'-'.$model->format_gen;
          break;

        case 'YY':
          return $model->starting_char.date('y').$model->format_gen;
          break;

        case 'YY-':
          return $model->starting_char.date('y').'-'.$model->format_gen;
          break;

        case 'YYTH-':
          return $model->starting_char.date('y',strtotime(date('Y')+543)).'-'.$model->format_gen;
          break;

        case 'YYTH':
          return $model->starting_char.date('y',strtotime(date('Y')+543)).$model->format_gen;
          break;

        case 'YYMM':
          return $model->starting_char.date('y').$runing.$model->format_gen;
          break;

        case 'YY-MM':
          return $model->starting_char.date('y').'-'.$runing.$model->format_gen;
          break; 
          
        case 'YY-MM-':
          return $model->starting_char.date('y').'-'.$runing.'-'.$model->format_gen;
          break;

        case 'YYYYTH':
          return $model->starting_char.date('Y',strtotime(date('Y')+543)).$model->format_gen;
          break;

        case 'YYYYTH-':
          return $model->starting_char.date('Y',strtotime(date('Y')+543)).'-'.$model->format_gen;
          break;

        case 'YYMMTH':
          return $model->starting_char.date('y',strtotime(date('Y')+543)).$runing.$model->format_gen;
          break;

        case 'YYMMTH-':
          return $model->starting_char.date('y',strtotime(date('Y')+543)).$runing.'-'.$model->format_gen;
          break;

        case 'YYMM-TH':
          return $model->starting_char.date('y',strtotime(date('Y')+543)).$runing.$model->format_gen;
          break;

        case 'YYMM-TH-':
          return $model->starting_char.date('y',strtotime(date('Y')+543)).$runing.'-'.$model->format_gen;
          break;

        case 'YY-MM-TH':
          return $model->starting_char.date('y',strtotime(date('Y')+543)).'-'.$runing.$model->format_gen;
          break;

        case 'YY-MM-TH-':
          return $model->starting_char.date('y',strtotime(date('Y')+543)).'-'.$runing.'-'.$model->format_gen;
          break;

        case 'YYMM-':
          return $model->starting_char.date('y').$runing.'-'.$model->format_gen;
          break;

        default:
          return $model->starting_char.$model->separate.$model->format_gen;
          break;
      }


    }



    public static function getRuning($table_name,$field_name,$cond)
    {
        $SDate      = date('Y-m-').'01';
        $EDate      = date("Y-m-t", strtotime($SDate));
        $comp_id    = Yii::$app->session->get('Rules')['comp_id'];
        $LastDigit  = [];
        $data       = '';


        // Generate This model
        $NoSeries = NumberSeries::find()
                    ->where(['table_name' => $table_name])
                    ->andWhere(['field_name' => $field_name])
                    ->andWhere(['cond' => $cond])
                    ->andWhere(['comp_id' => $comp_id])
                    ->one() ;
        
        if($NoSeries==null){
          // ติดตั้งเองทั้งหมด โดยไม่ต้องไปที่หน้า install
          $getSeries = \admin\modules\install\models\Install::createSeries('all');
          if($getSeries->status==200){
            // Run this function again
            return self::getRuning($table_name,$field_name,$cond);
          }else{
            var_dump($getSeries);
            exit;
          }
        }           

        // if(!$NoSeries){
        //   return 'Error';
        //   exit();
        // }
        // Find relation number series.
        $findDate = RuningNoseries::find()
                      ->where(['between', 'start_date', $SDate, $EDate])
                      ->andWhere(['no_series' =>$NoSeries->id])
                      ->andWhere(['comp_id' => $comp_id]);

        if($findDate->count() > 0){

          $NewNoSeries = $findDate->one();
          self::notifyError($NewNoSeries,$NoSeries->id);

        }else {

          // $NewNoSeries = RuningNoseries::find()
          //             ->andWhere(['no_series' =>$NoSeries->id])
          //             ->andWhere(['comp_id' => $comp_id])
          //             ->orderBy(['start_date' => SORT_DESC])
          //             ->one();

          $NewNoSeries = self::notifyError(null,$NoSeries->id);
  
        }

        # รันแล้ว
        if($NewNoSeries->last_no != ''){

          $last_no = $NewNoSeries->last_no;

        }else {
        # ยังไม่รันแล้ว
          $last_no = $NewNoSeries->start_no;
        }


        # แยกตั้วขึ้น ด้วย -,_,.,space
        $DocNo = preg_split('/[-_.:\s]+/', $last_no);

        # มีตัวขั้น
        if(count($DocNo) > 1){

          $NewRuningEnd   = (intval(end($DocNo)));

          $genDocNo = array();
          for ($i=0; $i < count($DocNo)-1; $i++) {
            $genDocNo[] = $DocNo[$i];
          }


          $NewRuning      = self::getSeparate($last_no,$genDocNo).'-'.str_pad(self::CheckNumber($NewRuningEnd), strlen($NoSeries->format_gen), "0", STR_PAD_LEFT);

        }else {
        # ไม่มีตัวขั้น
        # ต้องแยกใหม่ โดยการแยกตัวอักษรกับตัวเลขออกจากกัน
        # จากนั้น นำ Array ตัวสุดท้ายที่เป็นตัวเลขมา +1
          preg_match('/[^0-9]*([0-9]+)[^0-9]*/', $last_no, $DocNo);


          if(count($DocNo) > 1){

            //$NewRuningEnd   = (intval(end($DocNo)));
            $NewRuningEnd   = end($DocNo);
            $NewDocument    = Generater::getNewBySeparator($NewNoSeries,$NoSeries,$NewRuningEnd);
            $NewRuning      = $NewDocument->runing;



          }else {
            return 'Error';
          }

        }



        return $NewRuning;


    }

    public static function getSeparate($sep,$str){

      if(strpos( $sep, "-" )){
        $newSep = '-';
      }else if(strpos( $sep, "_" )){
        $newSep =  '_';
      }else if(strpos( $sep, "." )){
        $newSep =  '.';
      }else if(strpos( $sep, ":" )){
        $newSep =  ':';
      }else if(strpos( $sep, " " )){
        $newSep =  ' ';
      }else {
        $newSep =  $sep;
      }

      return implode($newSep,$str);

    }

    public static function getNewBySeparator($model,$template,$runing){
      /**
      * เอา Start_no ที่ได้จากการ Generate มาตัดออก ตามจำนวนที่ตั้งไว้
      */

      $CutOff     = mb_strlen($template->format_gen) + mb_strlen($template->separate,'utf8');
      $StartUp    = mb_substr($model->start_no,0, - $CutOff,'UTF-8');

      $NewNumber  = self::CheckNumber($runing);
      $newDoc     = $StartUp.str_pad($NewNumber, mb_strlen($template->format_gen), "0", STR_PAD_LEFT);
      return (object)[
              'runing' => $template->starting_char.$newDoc,
      ];
    }

    public static function UpdateSeries($table_name,$field_name,$cond,$NewRuningNumber)
    {
        $SDate = date('Y-m-').'01';
        $EDate = date("Y-m-t", strtotime($SDate));
        $comp_id = Yii::$app->session->get('Rules')['comp_id'];



        // Generate This model
        $NoSeries = NumberSeries::find()
                    ->where(['table_name' => $table_name])
                    ->andWhere(['field_name' => $field_name])
                    ->andWhere(['cond' => $cond])
                    ->andWhere(['comp_id' => $comp_id])
                    ->one();

        if($NoSeries==null){
          echo '<script> window.location.href="index.php?r=install/default"; </script>';
          exit();
        }
        // Find relation number series.
        $findDate = RuningNoseries::find()
                      ->where(['between', 'start_date', $SDate, $EDate])
                      ->andWhere(['no_series' =>$NoSeries->id])
                      ->andWhere(['comp_id' => $comp_id]);

        if($findDate->count() > 0){

          $NewNoSeries = $findDate->one();

        }else {

          // $NewNoSeries = RuningNoseries::find()
          //             ->andWhere(['no_series' =>$NoSeries->id])
          //             ->andWhere(['comp_id' => $comp_id])
          //             ->orderBy(['start_date' => SORT_DESC])
          //             ->one();

          $NewNoSeries = self::notifyError(null,$NoSeries->id);
        }


        $NoSeries->last_no = $NewRuningNumber;

        $NoSeries->last_date = date('Y-m-d');
        $NoSeries->save();

        $NewNoSeries->last_no = $NewRuningNumber;
        $NewNoSeries->save();

        return $NewRuningNumber;
    }


    

}
