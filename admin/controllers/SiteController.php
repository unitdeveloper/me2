<?php
namespace admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

use common\models\AppsRules;
use common\models\Authentication;

use Spatie\PdfToText\Pdf;


use NcJoes\PopplerPhp\PdfInfo;
use NcJoes\PopplerPhp\Config;
use NcJoes\PopplerPhp\PdfToCairo;
use NcJoes\PopplerPhp\PdfToHtml;
use NcJoes\PopplerPhp\Constants as C;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error','help','update'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index','indexs', 'print','dashboard','api-login','feature'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    static function Permission(){
        $rules      = \common\models\AppsRules::findOne(['user_id' => Yii::$app->user->identity->id]);
        $permission = \common\models\AuthAssignment::findOne(['user_id' => Yii::$app->user->identity->id]);

        if($permission ==null){          
            return true;
        }else if($rules ==null){          
            return true;
        }else {
            return false;
        }
    }

    static function Authentications($obj){

        $model = Authentication::findOne(['user_id' => Yii::$app->user->identity->id]);
        if ($model != null){
            $model->token      = Yii::$app->session->getId();
            $model->X_CSRF_Token = base64_encode(Yii::$app->session->getId());
            $model->user_id    = \Yii::$app->user->identity->id;
            $model->comp_id    =  $obj->comp_id;
            $model->date       =  date('Y-m-d H:i:s');
            $model->ip          = Yii::$app->getRequest()->getUserIP();
            $model->save();
        }else{
            $Authen = new Authentication();
            $Authen->token      = Yii::$app->session->getId();
            $Authen->X_CSRF_Token = base64_encode(Yii::$app->session->getId());
            $Authen->user_id    = \Yii::$app->user->identity->id;
            $Authen->comp_id    =  $obj->comp_id;
            $Authen->date       =  date('Y-m-d H:i:s');
            $Authen->ip         = Yii::$app->getRequest()->getUserIP();
            $Authen->save();
        }
        

        
    }

    public function actionIndex()
    {
        return $this->render('loading');   
    }

    public function actionIndexs()
    {

        if(\common\models\Options::getSystemStatus()){

        
            $cookies = Yii::$app->response->cookies;
            

            if(self::Permission()){
                return $this->redirect(['install/default']);
            }
    
            // Register Zone

            // +---[EW 2017®]----+
            // |                 |
            // |                 |
            // |     Welcome     |
            // |      https      |
            // |                 |
            // |    eWiN Live    |
            // |                 |
            // |                 |
            // |                 |
            // +-----[eWiNL]-----+

            if(!Yii::$app->session->get('Rules')){

        

                $session = \Yii::$app->session;

                $AppsRules = AppsRules::find()
                        ->where(['user_id' => \Yii::$app->user->identity->id])
                        ->one();


                if($AppsRules != null)
                {

                    
                    if($AppsRules->status != 1){
                        Yii::$app->user->logout();
                        Yii::$app->session->setFlash('warning', Yii::t('common','Your username is locked.'));
                        return $this->redirect(['user/security/login']);
                        exit;
                    }
                    
                    $this->cookieregister('myCompany',$AppsRules->comp_id);

                
                    $session->set('sales_id',  $AppsRules->sales_id);
                    $session->set('user.sales_id',  $AppsRules->sales_id);
                    
                    $session->set('myCompany', $AppsRules->comp_id);
                    $session->set('user.myCompany', $AppsRules->comp_id);

                    $session->set('Rules',     $AppsRules);
                    $session->set('user.Rules',     $AppsRules);

                    $company = \common\models\Company::findOne($AppsRules->comp_id);
                    $session->set('company',$company->name);
                    $session->set('brand',$company->brand);
                    $session->set('logo',$company->logoViewer);
                    /**
                     * SET PACKAGE
                     */
                    if(!$session->get('PACKAGE')){                
                        $session->set('PACKAGE',$company->package);                
                    }

                
                    self::Authentications($AppsRules);

                    if((Yii::$app->session->get('workyears')==NULL) || (Yii::$app->session->get('workyears')== 1970)) { 
                        Yii::$app->session->set('workyears',date('Y')); 
                    }

                    if(Yii::$app->session->get('workdate')==NULL) { 
                        Yii::$app->session->set('workdate',date('Y-m-d')); 
                    }

                }


                
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'myCompany',
                    'value' => Yii::$app->session->get('myCompany'),               
                ]));

            
                // 1 = Administrator
                // 3 = Sales & Marketing
                // 4 = Sales Admin
                // 5 = Business-Monitoring
                // 7 = Sales Director
                // 12= Store
                // 13= Admin Office

                // switch (Yii::$app->session->get('Rules')['rules_id']) {
                //     case 1:
                //         return $this->render('index');
                //         break;

                //     case 5:
                //         return $this->render('index');
                //         break;

                //     case 8:
                //         return $this->render('index-office');
                //         break;

                //     case 12:
                //         return $this->render('index-store');
                //         break;
                    
                //     case 13:
                //         return $this->render('index-office');
                //         break;

                //     default:
                //         return $this->render('index-sale-report');
                //         break;
                // }
            } else{
                if(Yii::$app->session->get('Rules')['status'] != 1){
                    Yii::$app->user->logout();
                    Yii::$app->session->setFlash('warning', Yii::t('common','Your username is locked.'));
                    return $this->redirect(['user/security/login']);
                    exit;
                }
                
            }
            // if((Yii::$app->session->get('Rules')['rules_id']==1) || (Yii::$app->session->get('Rules')['rules_id']==5)){
            //     return $this->render('index');
            // }else{
            //     return $this->render('index-sale-report');
            // }
            // https://admin.ewinl.com/index.php?r=apps_rules%2Fsetup
            if(Yii::$app->session->get('Rules')['comp_id']==68){
                return $this->render('index-stock');
            }else{
                switch (Yii::$app->session->get('Rules')['rules_id']) {
                    case 1:
                        return $this->render('index');
                        break;

                    case 5:
                        return $this->render('index');
                        break;

                    case 8:
                        return $this->render('index-office');
                        break;

                    case 12:
                        return $this->render('index-store');
                        break;
                    
                    case 13:
                        return $this->render('index-office');
                        break;

                    default:
                        return $this->render('index-sale-report');
                        break;
                }
            }

        }else{
            return $this->renderpartial('system-off');
        }
        
    }

    public function actionDashboard(){
        return $this->render('dashboard-sale');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionHelp()
    {



        

        

        $file           = Yii::getAlias('@webroot').'/uploads/file.pdf';
        $source         = Yii::getAlias('@webroot').'/uploads/file/temp';

       

        $text           = '';
        $countPages     = 1;

        
        if (isset($_POST['submit'])) {

            if(isset($_FILES["file"])){
                
                if($_FILES["file"]['size'] > 0){


                    // Config::setBinDirectory('/usr/local/bin/pdftohtml');

                    // Config::setOutputDirectory($source);
                    // $pdfToHtml = new PdfToHtml($_FILES["file"]["tmp_name"]);
                    // $pdfToHtml->setZoomRatio(1.8);
                    // $pdfToHtml->exchangePdfLinks();
                    // $pdfToHtml->startFromPage(1)->stopAtPage(5);
                    // $pdfToHtml->generateSingleDocument();
                    // $pdfToHtml->noFrames();
                    // $pdfToHtml->oddPagesOnly();
                    // $pdfToHtml->outputToConsole();
                    // $text = $pdfToHtml->generate();

                                        
                    // // change pdftohtml bin location
                    // \Gufy\PdfToHtml\Config::set('pdftohtml.bin', '/usr/local/bin/pdftohtml');

                    // // change pdfinfo bin location
                    // \Gufy\PdfToHtml\Config::set('pdfinfo.bin', '/usr/local/bin/pdfinfo');

                    // \Gufy\PdfToHtml\Config::set('pdftohtml.output', $source.'/html');
                    // // initiate
                    // $pdf = new \Gufy\PdfToHtml\Pdf($_FILES["file"]["tmp_name"]);

                    // // convert to html and return it as [Dom Object](https://github.com/paquettg/php-html-parser)
                    // $text = $pdf->html();





                    $pdf = new \TonchikTm\PdfToHtml\Pdf($_FILES["file"]["tmp_name"], [
                        'pdftohtml_path'    => '/usr/local/bin/pdftohtml',
                        'pdfinfo_path'      => '/usr/local/bin/pdfinfo',                        
                        'generate' => [     // settings for generating html
                            'singlePage'    => false,    // we want separate pages
                            'imageJpeg'     => false,   // we want png image
                            'ignoreImages'  => false,   // we need images
                            'zoom'          => 1.5,     // scale pdf
                            'noFrames'      => true,    // we want separate pages
                        ],
                        'clearAfter'        => true,    // auto clear output dir (if removeOutputDir==false then output dir will remain)
                        'removeOutputDir'   => true,    // remove output dir
                        'outputDir'         => $source, // output dir
                        'html' => [         // settings for processing html
                            'inlineCss'     => true,    // replaces css classes to inline css rules
                            'inlineImages'  => true,   // looks for images in html and replaces the src attribute to base64 hash
                            'onlyContent'   => true,    // takes from html body content only
                             
                        ]
                    ]);
                    
                    
                    
                    // get pdf info
                    $pdfInfo = $pdf->getInfo();
                    
                    // get count pages
                    $countPages = $pdf->countPages();
                    
                    // get content from one page
                    $contentFirstPage = $pdf->getHtml()->getPage(1);
                    
                     
                    // get content from all pages and loop for they
                    foreach ($pdf->getHtml()->getAllPages() as $page) {
                        $text.= $page . '<br/>';
                    }



                    // $file = $_FILES["file"]["tmp_name"];
                    // // $text = (new Pdf('/usr/local/bin/pdftotext'))
                    // // ->setPdf($file)
                    // // ->text();
                   
                    
                    // $text = Pdf::getText($file,'/usr/local/bin/pdftotext');
                    //$text = json_encode($text,JSON_UNESCAPED_UNICODE);
                    
                }
            } 
            

            

        }
        

        //var_dump($text);

        //Pdf::getText($file, $source);
        return $this->render('help',['text' => $text,'page' => $countPages]);
    }

    public function actionUpdate()
    {
        return $this->render('update');
    }

    public function cookieregister($cookies_name,$value)
    {
        $cookies = \Yii::$app->response->cookies;    //Enable cookie editing permissions.
        $cookies->add(new \yii\web\Cookie([
            'name' => $cookies_name,
            'value' => $value,              //set "CartSessionID" = encryp value
        ]));
    }

    public function actionError(){
        echo 'Error';
    }

    public function actionPrint(){
        // แค่ทดสอบ escpos (DIRECT PRINT)
        // print no dialog
        // ยังไม่ได้นำไปใช้งาน
        // ทำงานที่เครื่อง Server ได้ (แต่ไม่สามารถทำงานที่ client ได้)
        return $this->render('print');
    }


   public function actionFeature(){
       return $this->render('feature');
   }
}
