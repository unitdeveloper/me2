<?php

namespace admin\modules\SaleOrders\controllers;
use Yii;
use common\models\SaleHeader;
use admin\modules\SaleOrders\models\SalehearderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use admin\modules\SaleOrders\models\OrderSearch;

use admin\modules\items\models\SearchPicItems;
use common\models\Items;


use common\models\TmpMenuGroup;
use common\models\VatType;

use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


class SaleinvController extends \yii\web\Controller
{
    public function actionDelete()
    {
        return $this->render('delete');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionUpdate()
    {
        return $this->render('update');
    }

    public function actionView()
    {
        return $this->render('view');
    }


    protected function findModel($id)
    {
        if (($model = SaleHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionPrint($id)
    {
         
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = $this->findModel($id);

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where(['order_no' => $model->no]);
        $dataProvider->query->andwhere(['comp_id' => $company]);
     
        

        $PageFooter = '';

         
        //$pdf->SetHTMLHeader('<img src="' . base_url() . 'custom/Hederinvoice.jpg"/>');

        //$pdf->SetHTMLFooter('xxxx'); 
    
    
        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('__print_sale_inv',[
                    'model' => $model,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                     
                ]);
    
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            //'cssFile' => '@admin/web/css/pdf.css',
            'cssFile' => 'css/pdf.css',
             
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css', 
            'filename' => 'Inv_'.$model->no,
            // any css to be embedded if required
            //'cssInline' => '.bd{border:1.5px solid; text-align: center;} .ar{text-align:right} .imgbd{border:1px solid}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Inv : '.$model->no.' '],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>[''],
                //'SetFooter'=>['{PAGENO}'],
                //'SetHTMLFooter' => ''.$PageFooter.'',
            ]
        ]);

        /* Thai Font */
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $pdf->options['fontDir'] = array_merge($fontDirs, [
            Yii::getAlias('@webroot').'/fonts'
        ]);

        $pdf->options['fontdata'] = $fontData + [
            'saraban' => [
                'R' => 'thsarabunnew-webfont.ttf',
            ],
            'freesiaupc' => [
                'R' => 'FreesiaUPC.ttf', 
            ]
        ];

        return $pdf->render();

        
        //return $this->renderpartial('__print_sale_order');
       // return $this->renderpartial('__print_so');
    }

}
