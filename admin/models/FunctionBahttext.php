<?php

namespace admin\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Model;

/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class FunctionBahttext extends Model 
{
	 
	public function ThaiBaht($number){

		if($number == 0){
			return '';
		}

		$numberformat = number_format($number,2);

		// $explode = explode('.' , $numberformat);
		// $baht = $explode[0];
		// $stang = $explode[1];
		list($baht, $stang) = explode('.', $numberformat);

		$minus = '';

		if($baht < 0 )
		{
			$baht = abs($baht);
			$minus = '(ลบ) ';
		}

		if($stang == '00'){
			return $minus.$this->thai($baht).'บาทถ้วน';
		}else{
			return $minus.$this->thai($baht).'บาท'.$this->thai($stang).'สตางค์';
		}


	 }
		 
	 public function thai($num){  
		$returnNumWord = NULL; 
		$num = str_replace(',','',$num);
		$num_decimal = explode('.',$num);
		$num = $num_decimal[0];
		 

		$returnNumWord;   
		$lenNumber = strlen($num);   
		$lenNumber2 = $lenNumber - 1;   

		$ewGroup = ['' , 'สิบ' ,  'ร้อย' , 'พัน' , 'หมื่น' , 'แสน' , 'ล้าน' , 'สิบ' , 'ร้อย' , 'พัน' , 'หมื่น' , 'แสน' , 'ล้าน'];   
		$ewDigit = ['' , 'หนึ่ง' , 'สอง' , 'สาม' , 'สี่' , 'ห้า' , 'หก' , 'เจ็ด' , 'แปด' , 'เก้า'];   
		 

		$ii = 0;   

		for($i = $lenNumber2;$i >= 0;$i--){   
			$ewNumWord[$i] = substr($num,$ii,1);   
			$ii++;   
		}   

		$ii = 0;   
		for($i = $lenNumber2; $i >= 0;$i--){   
			if(($ewNumWord[$i] == 2 && $i ==1) || ($ewNumWord[$i] == 2 && $i == 7)){   
			    $ewDigit[$ewNumWord[$i]]='ยี่';   
			}else{   
			    if($ewNumWord[$i] == 2){   
			        $ewDigit[$ewNumWord[$i]] = 'สอง';        
			    }   
			    if(($ewNumWord[$i] == 1 && $i <= 2 && $i == 0) || ($ewNumWord[$i] == 1 && $lenNumber > 6 && $i == 6)){   
			    				    	
			        if(@$ewNumWord[$i + 1] == 0){   
			            $ewDigit[$ewNumWord[$i]] = 'หนึ่ง';      
			        }else{   
			            $ewDigit[$ewNumWord[$i]] = 'เอ็ด';       
			        }   
			    }else if(($ewNumWord[$i] == 1 && $i <= 2 && $i == 1) || ($ewNumWord[$i] == 1 && $lenNumber >6 && $i == 7)){   
			        $ewDigit[$ewNumWord[$i]] = '';   
			    }else{   
			        if($ewNumWord[$i] == 1){   
						$ewDigit[$ewNumWord[$i]] = 'หนึ่ง';   
			        }   
			    }   
			}   
			if($ewNumWord[$i] == 0){   
				if($i != 6){
					$ewGroup[$i] = '';   
				 }
			}
			$ewNumWord[$i] = substr($num,$ii,1);   
			$ii++;   
			$returnNumWord.=$ewDigit[$ewNumWord[$i]].$ewGroup[$i];   
		} 
		return $returnNumWord;   
	}

}
