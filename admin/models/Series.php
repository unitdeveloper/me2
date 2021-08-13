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
class Series extends Model
{

    public static function gen($table_name,$field_name,$cond){
        $comp_id    = Yii::$app->session->get('Rules')['comp_id'];
        $NoSeries   = NumberSeries::find()
                        ->where(['table_name' => $table_name])
                        ->andWhere(['field_name' => $field_name])
                        ->andWhere(['cond' => $cond])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->one();

        $no = '*Not Work*';

        if($NoSeries != null){
            // หาตัวล่าสุด ถ้าไม่มีก็ + 1 ไปเลย


            // ตัวเลขที่รัน
            $countFormat= mb_strlen($NoSeries->format_gen);

            $sqlLast    = "SELECT `no` FROM $table_name WHERE `comp_id` =  $comp_id ORDER BY `no` DESC  limit 1" ;
            //echo $sqlLast;
            $getLast    = \Yii::$app->db->createCommand($sqlLast)->queryOne();

            if($getLast != null){ // ถ้าหาตัวรันล่าสุดจากตาราง
                // [[ เอาตัวรัน ไป + 1 ]]

                // หาตัวเลขล่าสุด 
                // นับจำนวนเพื่อให้รู้ว่า จะตัดตัวอักษรออกกี่ตัว *** strlen('Hellow Wold', - 10 ,4)
            
                $Text   = substr($getLast['no'],0, (int)strlen($getLast['no']) - (int)$countFormat); 
                $qtyToCut =(int)strlen($Text);

                // ตัดจำนวนเพื่อมา +1 
                $last   = mb_substr($getLast['no'],  $qtyToCut , $countFormat, 'UTF-8');  
                 
                $LastNo = (int)($last + 1);
                
                $no     = $Text.str_pad($LastNo, $countFormat, "0", STR_PAD_LEFT);                
               
                
            }else{ // ไม่เจอข้อมูลในตาราง
                $formatGen = str_pad(1, strlen($NoSeries->format_gen), "0", STR_PAD_LEFT);  
                $no = $NoSeries->starting_char.self::Separator($NoSeries->separate).$formatGen;
                //$no = $NoSeries->starting_char.$NoSeries->separate.$formatGen;
                //$no='not found';
            }

            return $no;
        }else{
            // Create New;
            $model                  = new NumberSeries();
            $model->name            = 'Production';
            $model->starting_char   = 'PDR';
            $model->separate        = 'YYMM-';
            $model->format_gen      = '000';
            $model->table_name      = $table_name;
            $model->field_name      = $field_name;
            $model->description     = '--';
            $model->format_type     = '12M';
            $model->cond            = $cond;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();

                         
            return self::gen($table_name, $field_name, $cond);
        }
    }


    public static function invoice($table_name,$field_name,$cond, $type, $vat){
        $comp_id    = Yii::$app->session->get('Rules')['comp_id'];
        $NoSeries   = NumberSeries::find()
                        ->where(['table_name' => $table_name])
                        ->andWhere(['field_name' => $field_name])
                        ->andWhere(['cond' => $cond])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->one();

        $no = '*Not Work*';

        if($NoSeries != null){
            // หาตัวล่าสุด ถ้าไม่มีก็ + 1 ไปเลย


            // ตัวเลขที่รัน
            $countFormat= mb_strlen($NoSeries->format_gen);

            $thisYear   = date('Y'); 
            $sqlLast    = "SELECT $field_name FROM $table_name WHERE
                            `comp_id` =  $comp_id 
                            AND  YEAR(`posting_date`) = $thisYear  
                            AND `doc_type` = '$type'
                            AND `vat_percent` = $vat
                            ORDER BY $field_name DESC  limit 1" ;
            //echo $sqlLast;
            $getLast    = \Yii::$app->db->createCommand($sqlLast)->queryOne();

            if($getLast != null){ // ถ้าหาตัวรันล่าสุดจากตาราง
                // [[ เอาตัวรัน ไป + 1 ]]

                // หาตัวเลขล่าสุด 
                // นับจำนวนเพื่อให้รู้ว่า จะตัดตัวอักษรออกกี่ตัว *** strlen('Hellow Wold', - 10 ,4)
            
                $Text       = substr($getLast[$field_name],0, (int)strlen($getLast[$field_name]) - (int)$countFormat); 
                $qtyToCut   =(int)strlen($Text);

                // ตัดจำนวนเพื่อมา +1 
                $last       = mb_substr($getLast[$field_name],  $qtyToCut , $countFormat, 'UTF-8');  
                 
                $LastNo     = (int)($last + 1);
                
                $no         = $Text.str_pad($LastNo, $countFormat, "0", STR_PAD_LEFT);                
               
                
            }else{ // ไม่เจอข้อมูลในตาราง
                $formatGen = str_pad(1, strlen($NoSeries->format_gen), "0", STR_PAD_LEFT);  
                $no = $NoSeries->starting_char.self::Separator($NoSeries->separate).$formatGen;
                //$no = $NoSeries->starting_char.$NoSeries->separate.$formatGen;
                //$no='not found';
            }

            return $no;
        }else{
            // Create New;
            $model                  = new NumberSeries();
            $model->name            = 'Production';
            $model->starting_char   = 'IV';
            $model->separate        = 'YYMM-';
            $model->format_gen      = '000';
            $model->table_name      = $table_name;
            $model->field_name      = $field_name;
            $model->description     = '--';
            $model->format_type     = '12M';
            $model->cond            = $cond;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();

                         
            return self::gen($table_name, $field_name, $cond);
        }
    }

    public static function invoiceNo($table_name,$field_name,$cond, $type, $doc){
        $comp_id    = Yii::$app->session->get('Rules')['comp_id'];
        $NoSeries   = NumberSeries::find()
                        ->where(['table_name' => $table_name])
                        ->andWhere(['field_name' => $field_name])
                        ->andWhere(['cond' => $cond])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->one();

        $no = '*Not Work*';

        if($NoSeries != null){
            // หาตัวล่าสุด ถ้าไม่มีก็ + 1 ไปเลย


            // ตัวเลขที่รัน
            $countFormat= mb_strlen($NoSeries->format_gen);

            $thisYear   = date('Y'); 
            $sqlLast    = "SELECT $field_name FROM $table_name WHERE
                            `comp_id` =  $comp_id 
                            AND  YEAR(`posting_date`) = $thisYear  
                            AND `doc_type` = '$type'
                            AND `no_` LIKE ('$doc%')
                            ORDER BY $field_name DESC  limit 1" ;
            //echo $sqlLast;
            $getLast    = \Yii::$app->db->createCommand($sqlLast)->queryOne();

            if($getLast != null){ // ถ้าหาตัวรันล่าสุดจากตาราง
                // [[ เอาตัวรัน ไป + 1 ]]

                // หาตัวเลขล่าสุด 
                // นับจำนวนเพื่อให้รู้ว่า จะตัดตัวอักษรออกกี่ตัว *** strlen('Hellow Wold', - 10 ,4)
            
                $Text       = substr($getLast[$field_name],0, (int)strlen($getLast[$field_name]) - (int)$countFormat); 
                $qtyToCut   =(int)strlen($Text);

                // ตัดจำนวนเพื่อมา +1 
                $last       = mb_substr($getLast[$field_name],  $qtyToCut , $countFormat, 'UTF-8');  
                 
                $LastNo     = (int)$last > 0 ? (int)($last + 1) : 1;
                
                $no         = $Text.str_pad($LastNo, $countFormat, "0", STR_PAD_LEFT);                
               
                
            }else{ // ไม่เจอข้อมูลในตาราง
                $formatGen = str_pad(1, strlen($NoSeries->format_gen), "0", STR_PAD_LEFT);  
                $no = $NoSeries->starting_char.self::Separator($NoSeries->separate).$formatGen;
                //$no = $NoSeries->starting_char.$NoSeries->separate.$formatGen;
                //$no='not found';
            }

            return $no;
        }else{
            // Create New;
            $model                  = new NumberSeries();
            $model->name            = 'Production';
            $model->starting_char   = $doc;
            $model->separate        = 'YYMM-';
            $model->format_gen      = '000';
            $model->table_name      = $table_name;
            $model->field_name      = $field_name;
            $model->description     = '--';
            $model->format_type     = '12M';
            $model->cond            = $cond;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();

                         
            return self::gen($table_name, $field_name, $cond);
        }
    }

    public static function Separator($separate){
        list($y,$m,$d)  = explode("-",date('Y-m-d'));
        $thY            = ($y+543);
        $thy            = substr(($y+543),2);

        switch ($separate) {

            case 'YYYY':
                return date('Y');
                break;

            case 'YYYY-':
                return date('Y').'-';
                break;

            case 'YY':
            return date('y');
            break;

            case 'YY-':
            return  date('y').'-';
            break;          

            case 'YYMM':
            return  date('ym');
            break;

            case 'YYMM-':
            return date('ym-');
            break;

            case 'YY-MM':
            return date('y-m');
            break; 
            
            case 'YY-MM-':
            return date('y-m').'-';
            break;

            case 'YYYYTH':
            return $thY;
            break;

            case 'YYYYTH-':
            return $thY.'-';
            break;

            case 'YYMMTH':
            return $thy.date('m');
            break;
            case 'YYMM-TH':
            return $thy.date('m');
            break;

            case 'YYMMTH-':
            return $thy.date('m').'-';
            break;
            case 'YYMM-TH-':
            return $thy.date('m').'-';
            break;

            case 'YYTH':
            return $thy;
            break;

            case 'YYTH-':
            return $thy.'-';
            break;   
            
            case 'YY-MM-TH':
            return $thy.'-'.date('m');
            break;

            case 'YY-MM-TH-':
            return $thy.'-'.date('m').'-';
            break;


            default:
            return $separate;
            break;
        }
  
  
      }
}