<?php
namespace admin\controllers;

use Yii;
use yii\web\Controller;
use common\models\LineBot;

use yii\helpers\Url;
use ProcessMonitor\ProcessMonitor;
use common\models\Systeminfo;
use common\models\Siri;

class LineController extends Controller
{
    public function beforeAction($action)
    {
        if ($action->id == 'webhook') {
            $this->enableCsrfValidation = false; //ปิดการใช้งาน csrf
        }
    
        return parent::beforeAction($action);
    }

   
    public function actionWebhook(){
                    
        $bot            = LineBot::findOne(3);   
        $json_string    = file_get_contents('php://input');
        $jsonObj        = json_decode($json_string); 
        $to             = $jsonObj->events[0]->source->userId; 

        if($jsonObj->events[0]->message->type == 'image'){
            self::setSiri('other',$jsonObj);
        }else if($jsonObj->events[0]->message->type == 'file'){
            self::saveFile('file',$jsonObj);
        }else if($jsonObj->events[0]->message->type == 'sticker'){
            
            $res = $bot->bot([
                'to' => $to,
                'message' => [                    
                        [
                            "type" => "sticker",
                            "packageId"=> "1",
                            "stickerId"=> "2"
                        ]
                ]
            ]);
            return true; 
        }else{

            $textSource     = $jsonObj->events[0]->message->text;              
            $post_data      = [
                'to'=> $to,
                'messages' => self::Robot($textSource,$bot,$jsonObj)
            ];             
            $res            = $bot->bot($post_data);

            return true;
        }
    }

     
    static function Robot($word,$bot,$session){      

        
        
        

            if (strpos($word, 'งง') !== false) {  

                $message = [
                    [
                        "type"=>"text",
                        "text"=> 'งงทำไม ไม่มีอะไรให้งง',
                    ],
                    [
                        "type" => "sticker",
                        "packageId"=> "1",
                        "stickerId"=> "13"
                    ]
                ];

            }else if (strpos($word, 'คืออะไร') !== false) {  
                
                $result_text = $bot->wiki($word);


                $message = [
                    [
                        "type"=>"text",
                        "text"=> $result_text,
                    ]
                ];
            }else if (strpos($word, 'ค้นหา') !== false) {  
                
                $result_text = $bot->wiki($word);
                
                $message = [
                    [
                        "type"=>"text",
                        "text"=> $result_text,
                    ]
                ];
            }else if (strpos($word, 'คู่มือ') !== false) {
               
                if(self::setSiri('manual',$session)){
                    $text = '.';
                }else{
                    $text = '.-';
                }

                $message = [
                    [
                        "type"=>"text",
                        "text"=>"คุณกำลังมองหาคู่มืออยู่ ใช่หรือไม่? ".$text,
                    ],
                    // [
                    //     "type"=>"text",
                    //     "text"=> 'Register '.\Yii::$app->session->get('siri').' ...',
                    // ]
                ];

                
                

            }else if (strpos($word, 'เมื่อไหร่') !== false) {

                $message = [
                    [
                        "type"=>"text",
                        "text"=>"เมื่อไหร่? ก็ยังไม่แน่ใจ",
                    ]
                ];

            }else if (strpos($word, 'ฝนจะตก') !== false) {

                $message = [
                    [
                        "type"=>"text",
                        "text"=>"ฉันไม่ใช่ Siri คุณลองไปถาม Siri ดุสิ",
                    ]
                ];

            }else if (strpos($word, 'ร้อน') !== false || strpos($word, 'หนาว') !== false) {

                $res = $bot->weather('กรุงเทพมหานคร',$word);

                $message = [
                    [
                        "type"=>"text",
                        "text"=> 'อุณหภูมิ '.$res,
                    ]
                ]; 

            }else if (strpos($word, 'อากาศ') !== false) {

                $res = $bot->weather('กรุงเทพมหานคร',$word);

                $message = [
                    [
                        "type"=>"text",
                        "text"=> 'อุณหภูมิ '.$res,
                    ]
                ]; 

                // if (strpos($word, 'หนาว') !== false) {
                //     $message = [
                //         [
                //             "type"=>"text",
                //             "text"=>"ไม่ค่อยหนาว แค่สั่นๆ",
                //         ]
                //     ];                
                // }else if (strpos($word, 'ร้อน') !== false) {
                //     $message = [
                //         [
                //             "type"=>"text",
                //             "text"=>"ตับแตกเลย",
                //         ]
                //     ];                
                // }else {
                //     $message = [
                //         [
                //             "type"=>"text",
                //             "text"=>"กำลังจะหนาว",
                //         ]
                //     ]; 
                // }


            }else if (strpos($word, 'คุณเป็นใคร') !== false) {


                $message = [
                    [
                        'type'=>'image',
                        'originalContentUrl' => 'https://upload.wikimedia.org/wikipedia/commons/e/ee/Seal_of_the_Central_Intelligence_Agency.png',
                        'previewImageUrl' => 'https://kryeministri-ks.net/repository/images/CIA.svg.png',
                    ],
                    [
                        "type" => "sticker",
                        "packageId"=> "1",
                        "stickerId"=> "3"
                    ]
                ]; 

            }else if (strpos($word, 'คุณชื่ออะไร') !== false ) {


                $message = [
                    [
                        "type"=>"text",
                        "text"=> 'ฉันชื่อ E-WIN  (อี-วิน)',
                    ]
                ]; 

            }else if (strpos($word, 'ทำอะไรได้') !== false ) {


                $message = [
                    [
                        "type"=>"text",
                        "text"=> "ฉันตรวจสอบอุณหภูมิให้ได้นะ\r\nให้ฉันค้นหาอะไรให้ก็ได้ เพียงพิมพ์ ค้นหา วรรค อะไรก็ได้ที่อยากให้หา",
                    ]
                ]; 

            }else if (strpos($word, 'มึง') !== false || strpos($word, 'กู') !== false) {


                $message = [
                    [
                        "type"=>"text",
                        "text"=> "ทำไมต้องขึ้น มึง,กู ด้วย\r\nคุยกันดีๆก็ได้",
                    ],
                    [
                        "type" => "sticker",
                        "packageId"=> "2",
                        "stickerId"=> "520"
                    ],
                ]; 

            }else if (strpos($word, 'อี') !== false || strpos($word, 'ไอ้') !== false) {


                $message = [
                    [
                        "type"=>"text",
                        "text"=> "คุยกันดีๆก็ได้",
                    ],
                    [
                        "type" => "sticker",
                        "packageId"=> "2",
                        "stickerId"=> "518"
                    ],
                ]; 

            }else if (strpos($word, 'ควย') !== false || strpos($word, 'สัด') !== false || strpos($word, 'สัส') !== false) {


                $message = [
                    [
                        "type"=>"text",
                        "text"=> "ควยไร! \r\nคุยกันดีๆไม่ได้หรอ",
                    ],
                    [
                        "type" => "sticker",
                        "packageId"=> "2",
                        "stickerId"=> "518"
                    ],
                ]; 

            }else if ($word=='ควาย') {


                $result_text = $bot->wiki($word);
                
                $message = [
                    [
                        'type'=>'image',
                        'originalContentUrl' => 'https://i.ytimg.com/vi/uvLlJ--4CCg/hqdefault.jpg',
                        'previewImageUrl' => 'https://i.ytimg.com/vi/uvLlJ--4CCg/hqdefault.jpg',
                    ],
                    [
                        "type"=>"text",
                        "text"=> $result_text,
                    ]
                ];

            }else if (($word=='หอย') || ($word=='หี')) {


                $result_text = $bot->wiki($word);
                
                $message = [
                    [
                        'type'=>'image',
                        'originalContentUrl' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/Shell_Island_1985.jpg/250px-Shell_Island_1985.jpg',
                        'previewImageUrl' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/Shell_Island_1985.jpg/250px-Shell_Island_1985.jpg',
                    ],
                    [
                        "type"=>"text",
                        "text"=> $result_text,
                    ]
                ];

            }else if ($word=='หำ') {


                $result_text = $bot->wiki($word);
                
                $message = [
                    [
                        'type'=>'image',
                        'originalContentUrl' => 'https://i.ytimg.com/vi/omM9YE07ftA/hqdefault.jpg',
                        'previewImageUrl' => 'https://i.ytimg.com/vi/omM9YE07ftA/hqdefault.jpg',
                    ],
                    [
                        "type"=>"text",
                        "text"=> $result_text,
                    ]
                ];

            }else if (($word == 'เหี่ย') || ($word== 'เหี้ย') || ($word == 'ตะกวด')) {


                $result_text = $bot->wiki($word);
                
                $message = [
                    [
                        'type'=>'image',
                        'originalContentUrl' => 'https://www.matichon.co.th/gallery/fullimages/2013/04/1367219101.jpg',
                        'previewImageUrl' => 'https://www.matichon.co.th/gallery/fullimages/2013/04/1367219101.jpg',
                    ],
                    [
                        "type"=>"text",
                        "text"=> $result_text,
                    ]
                ];

            }else if (($word == 'สัตว์') || ($word== 'ตอแหล') || ($word == 'อีดอก') || ($word == 'ดอกทอง') || ($word == 'ดอก')) {


                $result_text = $bot->wiki($word);
                
                $message = [                
                    [
                        "type"=>"text",
                        "text"=> $result_text,
                    ]
                ];

            }else if (strpos(strtolower($word), 'cpu') !== false || strpos($word, 'ซีพียู') !== false) {

                $cpuLoad = Systeminfo::getServerLoad();
                if (is_null($cpuLoad)) {
                    $CPU = "CPU load not estimateable (maybe too old Windows or missing rights at Linux or Windows)";
                }
                else {
                    $CPU = 'CPU : '.number_format($cpuLoad,2) . " %";
                }

                $message = [
                    [
                        "type"=>"text",
                        "text"=> $CPU,
                    ]
                ]; 

            }else if (strpos(strtolower($word), 'ram') !== false || strpos($word, 'แรม') !== false) {

               

                $mems = Systeminfo::getMemoryInfo();
                $text = '';
                foreach ($mems as $key => $mem) {
                    $ram = ($mem / 1024) / 1024;
                    $text.= $key.' '.$ram."\r\n";
                }
                 
 
                $message = [
                    [
                        "type"=>"text",
                        "text"=> $text,
                    ]
                ]; 

            }else if (strpos(strtolower($word), 'system') !== false || strpos(strtolower($word), 'info') !== false) {

                

                // $monitor = new ProcessMonitor();
         
                // $process = $monitor->search("apache");
                // $processList = $monitor->searchMultiple("rust-server|nginx");
                // $result = $monitor->searchMultiple("rust-server|nginx", true);
                // $text = 'CPU : '.$processList[0]->cpu."\r\n";
                // $text.= 'Ram : '.$processList[0]->ram."\r\n";
                $df = disk_free_space("/");
                $df = (($df/1024)/1024)/1024;

                $dt = disk_total_space("/");
                $dt = (($dt/1024)/1024)/1024;

                $du = (($dt-$df)/$df)*100;

                $cpuLoad= Systeminfo::getServerLoad();
                $mems   = Systeminfo::getMemoryInfo();

                //$mems   = self::getMemoryInfo();
                $Total  = ($mems['MemTotal'] / 1024) / 1024;
                $Free   = ($mems['MemFree'] / 1024) / 1024;
                $Avai   = ($mems['MemAvailable'] / 1024) / 1024;
                $Buffer = ($mems['Buffers'] / 1024) / 1024;
                $Direct = ($mems['DirectMap4k'] / 1024) / 1024;
                $usage  = (($Free+$Direct)/$Total)*100 +1;  // +1 เพื่อให้่ตรงกับ webmin (เนื่องจากยังหาค่าที่ตรงกับ webmin ไม่ได้)
                $ramu   = (($Avai-$Free)/$Total)*100;   
                
                $text = 'CPU : '.number_format($cpuLoad,2)." % \r\n";
                $text.= 'Ram : '.number_format($ramu)." % \r\n";
                $text.= 'Disk Usage : '.number_format($du)." % \r\n \r\n";

                //$text.= 'IP Address : '.$_SERVER['SERVER_ADDR']." \r\n \r\n";

                $text.= 'Disk Total : '.self::formatSizeUnits(disk_total_space("/"))." \r\n";
                $text.= 'Disk Free :  '.self::formatSizeUnits(disk_free_space("/")) ." \r\n \r\n";

                $text.= 'Ram Total : '.number_format($Total,2)."\r\n";
                $text.= 'Ram Free : '.number_format($Free,2)."\r\n";
                $text.= 'Ram Available : '.number_format($Avai,2)."\r\n";
                //$text.= 'Ram Direct : '.number_format($Direct,2)."\r\n";
               
                // $text = '';
                // foreach ($mems as $key => $mem) {
                //     $ram = ($mem / 1024) / 1024;
                //     $text.= $key.' '.$ram."\r\n";
                // }

                $message = [
                    [
                        "type"=>"text",
                        "text"=> $text,
                    ]
                ]; 

            }else if (strpos($word, 'siri') !== false || strpos($word, 'สิริ') !== false) {
                 
                self::setSiri('Yes',$session);
                $message = [
                    [
                        "type"=>"text",
                        "text"=> 'ฉันไม่ใช่ สิริ',
                    ],
                    // [
                    //     "type"=>"text",
                    //     "text"=> 'Register '.self::getSiri($session->events[0]->source->userId).' ...',
                    // ]
                ]; 
                self::crearSiri($session->events[0]->source->userId);

            }else {
                
                if(self::getSiri($session->events[0]->source->userId)){
                    if (strpos($word, 'ใช่') !== false || strpos(strtolower($word), 'Yes') !== false ) {
                        $message = [
                                [
                                    "type"=>"text",
                                    "text"=> 'คู่มือสามารถดูได้จาก http://www.ewinl.com/manual',
                            ]
                        ];                     
                    }else if (strpos($word, 'ไม่') !== false ) {
                        $message = [
                                [
                                    "type" => "sticker",
                                    "packageId"=> "4",
                                    "stickerId"=> "286"
                            ]
                        ];
                        //\Yii::$app->session->set('siri','');                           
                    }else {
                        $message = [
                                [
                                "type" => "sticker",
                                "packageId"=> "1",
                                "stickerId"=> "1"
                                ],
                                [
                                    "type"=>"text",
                                    "text"=> '  '.self::getSiri($session->events[0]->source->userId),
                                ]
                        ];
                    }            
                }else {
            
                    $message = [
                            [
                            "type" => "sticker",
                            "packageId"=> "1",
                            "stickerId"=> "2"
                            ],
                            // [
                            //     "type"=>"text",
                            //     "text"=> ' '.self::getSiri($session->events[0]->source->userId),
                            // ]
                    ];
                }
                
            }
         

        
        return $message;
    }


    public function actionCallback()
    {
        $json = '{
            "events": [
              {
                "replyToken": "00000000000000000000000000000000",
                "type": "message",
                "timestamp": 1512031596540,
                "source": {
                  "type": "user",
                  "userId": "Udeadbeefdeadbeefdeadbeefdeadbeef"
                },
                "message": {
                  "id": "100001",
                  "type": "text",
                  "text": "Hello, world"
                }
              },
              {
                "replyToken": "ffffffffffffffffffffffffffffffff",
                "type": "message",
                "timestamp": 1512031596540,
                "source": {
                  "type": "user",
                  "userId": "Udeadbeefdeadbeefdeadbeefdeadbeef"
                },
                "message": {
                  "id": "100002",
                  "type": "sticker",
                  "packageId": "1",
                  "stickerId": "1"
                }
              }
            ]
          }';
        
 
        $jsonObj = json_decode($json); //รับ JSON มา decode เป็น StdObj
        // $to = $jsonObj->{"result"}[0]->{"content"}->{"from"}; //หาผู้ส่ง
        // $text = $jsonObj->events[0]->message->text; //หาข้อความที่โพสมา
        
        //$text_ex = explode(':', $text); //เอาข้อความมาแยก : ได้เป็น Array

        var_dump($jsonObj->events[0]->source->userId);

    } 

    public function saveFile($data,$session){
 
        $model = new Siri();
        $model->name = $data;
        $model->cond = 1;
        $model->type = $session->events[0]->message->type;
        $model->file = $session->events[0]->message->fileName;
        $model->user = $session->events[0]->source->userId;
        $model->sid  = json_encode($session);
        if($model->save(false)){
            return true;
        }else{
            return false;
        }
        
    }


    public function setSiri($data,$session){
 
        $model = new Siri();
        $model->name = $data;
        $model->cond = 1;
        $model->type = $session->events[0]->message->type;
        $model->user = $session->events[0]->source->userId;
        $model->sid  = json_encode($session);
        if($model->save(false)){
            return true;
        }else{
            return false;
        }
        
    }

    public function getSiri($sid){
        $model = Siri::find()->where(['user' => $sid,'type' => 'text'])->one();
        if($model){
            self::crearSiri($sid);
            return true;
        }else{
            return false;
        }
    }

    public function crearSiri($sid){
        if(Siri::deleteAll(['user' => $sid,'type' => 'text'])){
            return true;
        }else{
            return false;
        }
    }

    static function formatSizeUnits($bytes)
    {
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
 
     
}
?>
