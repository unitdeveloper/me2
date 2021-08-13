<?php

namespace admin\components;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * This is just an example.
 */
class GoogleURLShortner extends Widget
{
    public $url;
    public $result;
    public $api_key = 'AIzaSyBAZro9s1h-cAGKhEySPR2lFD93XtdHlw4';
    
    public function init(){
         parent::init();
         if($this->url===null) {
                $this->result= 'Please provide a URL!';
         }else{
                
                $curl_obj = curl_init(sprintf('%s/url?key=%s', 'https://www.googleapis.com/urlshortener/v1', $this->api_key));

                curl_setopt($curl_obj, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_obj, CURLOPT_POST, true);
                curl_setopt($curl_obj, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
                curl_setopt($curl_obj, CURLOPT_POSTFIELDS, json_encode(['longUrl' => $this->url,'key' => $this->api_key]));
                curl_setopt($curl_obj, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl_obj, CURLOPT_SSL_VERIFYHOST, false);
                
                $response = curl_exec($curl_obj);
                $json = json_decode($response, true);
                curl_close($curl_obj);
                 
                $this->result= $response;
                //json_decode($response,JSON_UNESCAPED_UNICODE);
         }
    }

    public function run()
    {
        return Html::encode($this->result);
    }
}