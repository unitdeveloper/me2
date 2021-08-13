<?php

namespace admin\modules\warehousemoving\controllers;

use Yii;
use yii\helpers\Html;
use common\models\WarehouseMoving;
use common\models\WarehouseHeader;
use common\models\PurchaseLine;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

use common\models\PrintPage;
use common\models\Company;
use yii\data\ActiveDataProvider;


class ReceiveController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionView($id)
    {
        $query = WarehouseMoving::find()
        ->where(['source_id' => $id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 100],
            
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = WarehouseHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionPrint($id){

        $model    = $this->findModel($id);
        $query    = WarehouseMoving::find()
        ->where(['source_id' => $model->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            //'sort'=> ['defaultOrder' => ['id'=>SORT_ASC]],
        ]);

        $template   = PrintPage::findOne(PrintPage::findCustomPrint('receive'));
        $Company    = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

      
        $header = (Object)[
            'height'    => $template->header_height,
            'top'       => $template->margin_top,
            'fontsize'  => $template->font_size,
            'title'     => $model->DocumentNo,
            'watermark' => (Object)[
                'text'      => $template->water_mark,
                'left'      => $template->water_mark_left,
                'top'       => $template->water_mark_top,
                'color'     => $template->water_mark_color,
                'size'      => $template->water_mark_size,
                'radius'    => $template->water_mark_radius,
                'padding'   => $template->water_mark_padding,
                'border'    => $template->water_mark_border,
                'border_color' => $template->water_mark_border_color,
                'css'       => $template->water_mark_css,
                'switch'    => $template->water_mark_switch,
                'img'       => $template->watermark,
                'img_alpha' => $template->water_mark_img_alpha,
                'img_width' => $template->water_mark_img_width
            ],
        ];

        $pageSize   = $template->pagination ?: 15;
        if(isset($_GET['pagesize']))    $pageSize   = $_GET['pagesize'];
        
        $body   = (Object)[
            'height'    => $template->body_height,
            'pagesize'  => $pageSize,
            'fontsize'  => $template->font_size
        ];

        $Bahttext   = new \admin\models\FunctionBahttext();

        $defineHeader = [           
            '{COMPANY_LOGO}'        => '<img src="'.$Company->logoViewer.'" style="width: 110px;">',
            '{COMPANY_NAME}'        => $Company->name,
            '{COMPANY_NAME_EN}'     => $Company->name_en,
            '{COMPANY_ADDRESS}'     => $Company->vat_address.' อ.'.$Company->vat_city.' จ.'.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_ADDRESS_EN}'  => $Company->vat_address.' '.$Company->vat_city.' '.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_PHONE}'       => $Company->phone,
            '{COMPANY_FAX}'         => $Company->fax,
            '{COMPANY_MOBILE}'      => $Company->mobile,
            '{COMPANY_EMAIL}'       => $Company->email,
            '{DOCUMENT_NO}'         => $query->count() > 0 ? $model->DocumentNo : '',
            '{ORDER_NO}'            => $model->SourceDoc,
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->PostingDate)),
            '{ORDER_DATE_EN}'       => $query->count() > 0 ? date('d/m/y',strtotime($model->PostingDate)) : '',
            '{ORDER_DATE_TH}'       => $query->count() > 0 ? date('d/m/y',strtotime($model->PostingDate.' + 543 Years')) : '',
            '{COMPANY_TAX}'         => $Company->vat_register,
            '{COMPANY_HEAD}'        => $Company->headofficetb->data_char,
            '{PAGE_TITLE}'          => 'PO : '.$model->DocumentNo,
            '{HEAD_OFFICE}'         => $Company->headofficetb->data_char,     
        
            '{APPLY_FOR}'           => $model->Description,
            //'{CREATER}'             => $model->users->profile->name,
        
            '{VENDOR_CODE}'         => $query->count() > 0 
                                        ? $model->vendor
                                            ? $model->vendor->code 
                                            : ''
                                        : '',
            '{VENDOR_NAME}'         => $query->count() > 0 
                                        ? $model->vendor 
                                            ? $model->vendor->name 
                                            : ''
                                        : '',
            '{VENDOR_ADDRESS}'      => $model->vendor ? $model->vendor->address : '',
            '{VENDOR_TAX}'          => $model->vendor ? $model->vendor->vat_regis : '',
            '{VENDOR_PHONE}'        => $model->vendor ? $model->vendor->phone : '',
            '{VENDOR_FAX}'          => $model->vendor ? $model->vendor->fax : '',
        
            //'{REF_NO}'              => $model->ref_no,
            '{REF_TO}'              => $model->ext_document,
            //'{PURCHASE_REQUEST}'    => $model->ref_pr,
            '{REF_TERM_OF_PAYMENT}' => $model->purchase
                                        ? $model->purchase->payment_term > 0 
                                            ?  $model->purchase->payment_term . ' ' . Yii::t('common','Day')
                                            : Yii::t('common','Cash')
                                        : '',
            '{REF_TERM_OF_PAYMENT_TH}' => $model->purchase
                                            ? $model->purchase->payment_term > 0 
                                                ? date('d/m/y',strtotime($model->purchase->payment_term.' + 543 Years'))
                                                : Yii::t('common','Cash')
                                            : '',
            '{DELIVERY_SCHEDULE}'   => $model->purchase ? $model->purchase->delivery_date : '<p style="color:#fff;">00</p>',
            '{DELIVERY_SCHEDULE_TH}'=> $model->purchase ? date('d/m/y',strtotime($model->purchase->delivery_date.' + 543 Years')) : '',
            '{DELIVERY_ADDRESS}'    => $model->purchase ? $model->purchase->delivery_address : '',
        
            '{LABEL_REMARK}'        => 'หมายเหตุ : ',
            '{VALUE_REMARK}'        => $model->remark, //wordwrap($model->remark, 350, "<br/>\n", false),
            '{LABEL_TOTAL}'         => 'รวมเป็นเงิน',
            '{VALUE_TOTAL}'         => number_format($model->totals->beforediscount,2),
            '{LABEL_DISCOUNT}'      => 'ส่วนลด',
            '{VALUE_DISCOUNT}'      => number_format($model->totals->discount,2),
            '{LABEL_SUBTOTAL}'      => Yii::t('common','Total after discount'),
            '{VALUE_SUBTOTAL}'      => number_format($model->totals->subtotal,2),
            '{LABEL_VAT}'           => 'ภาษีมูลค่าเพิ่ม VAT',
            '{VALUE_VAT}'           => $model->purchase ? (($model->purchase->vat_percent * 1).' %')  : '',
            '{VALUE_INCLUDEVAT}'    => number_format($model->totals->includevat,2),
            '{LABEL_GRANDTOTAL}'    => Yii::t('common','Grand total'),
            '{VALUE_GRANDTOTAL}'    => number_format($model->totals->total,2),
            '{VALUE_BAHTTEXT}'      => $model->purchase ? $Bahttext->ThaiBaht($model->totals->total - (($model->purchase->withholdTax * $model->totals->subtotal)/100)) : '',

            '<!--IF_WHT-->'         => $model->purchase ? (($model->purchase->withholdTaxSwitch===1)? ' ': '<!--')  : '',
            '<!--IF_WHT_END-->'     => $model->purchase ? (($model->purchase->withholdTaxSwitch===1)? ' ': '-->')  : '',
            '<!--IF_NOT_WHT-->'     => $model->purchase ? ($model->purchase->withholdTax <= 0 ? ' ': '<!--')  : '',
            '<!--IF_NOT_WHT_END-->' => $model->purchase ? ($model->purchase->withholdTax <= 0 ? ' ': '-->')  : '',

            '{LABEL_WHT_PERCENT}'   => 'หักภาษี ณ ที่จ่าย',
            '{VALUE_WHT_PERCENT}'   => $model->purchase ? ($model->purchase->withholdTax * 1)  : '',
            '{VALUE_WHT}'           => $model->purchase ? ($model->purchase->withholdTax * $model->totals->subtotal)/100  : '',
            '{LABEL_TOTAL_WHT}'     => 'ยอดชำระ',
            '{VALUE_TOTAL_WHT}'     => $model->purchase ? number_format($model->totals->total - (($model->purchase->withholdTax * $model->totals->subtotal)/100), 2)  : ''
        ];

 

        $PrintTemplate = $this->renderPartial('@admin/views/setting/printer-template',[
            'model'         => $model,
            //'dataProvider'  => $dataProvider,
            'print'         => $template,
            'Company'       => $Company,
            'header'        => $header,
            'body'          => $body,
            'defineHeader'  => $defineHeader
        ]);


        // get your HTML raw content without any layouts or scripts

        $content = $this->renderPartial('_print_body',[
                  'model'           => $model,
                  'dataProvider'    => $dataProvider,
                  'header'          => $header,
                  'print'           => $template,
                  'body'            => $body
        ]);

      // setup kartik\mpdf\Pdf component
      $pdf = new Pdf([
            'mode'        => Pdf::MODE_UTF8,
            'format'      => $template->paper_size,  
            'orientation' => $template->paper_orientation,
            'destination' => Pdf::DEST_BROWSER,
            'content'     => $content,
            'cssFile'     => 'css/pdf.css',
            'filename'    => $model->DocumentNo.'.pdf',
            'cssInline'   => '@page {margin: 0; } body{font-family: saraban,  sans-serif, freesiaupc,  tahoma; font-size:11px;} ',
            'options'     => [
                'title'             => 'PO : '.$model->DocumentNo, 
                'autoScriptToLang'  => true, 
                'autoLangToFont'    => true,
                'languageToFont' => new CustomLanguageToFontImplementation(), //แสดงภาษาจีน และภาษาไทย โดยกำหนด Font-family เอง
            ],           
            'methods' => [
                'WriteHTML' => $PrintTemplate,
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
    }


        
    public function actionPrintLetter($id){

        $model    = $this->findModel($id);
        $query    = WarehouseMoving::find()
        ->where(['source_id' => $model->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            //'sort'=> ['defaultOrder' => ['id'=>SORT_ASC]],
        ]);

        //$template   = PrintPage::findOne(PrintPage::findCustomPrint('receive_letter'));
        $Company    = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

       
        $content = $this->renderPartial('_print_body_haft',[
                  'model'           => $model,
                  'dataProvider'    => $dataProvider,
                  'company'         => $Company
                  //'header'          => $header,
                  //'print'           => $template,
                  //'body'            => $body
        ]);

      // setup kartik\mpdf\Pdf component
      $pdf = new Pdf([
            'mode'        => Pdf::MODE_UTF8,
            //'format'      => $template->paper_size, 
            'format'      =>  "A4",  // [148,210]
            'orientation'   => Pdf::ORIENT_PORTRAIT,
            'destination'   => Pdf::DEST_BROWSER,
            'content'     => $content, 
            'filename'    => $model->DocumentNo.'.pdf',
            'cssInline'   => '@page {margin: 0; } body{font-family: roboto, saraban, garuda;}',
            'options'     => [
                'title'             => 'RC : '.$model->DocumentNo, 
                //'autoScriptToLang'  => true, 
                //'autoLangToFont'    => true,
                //'languageToFont' => new CustomLanguageToFontImplementation(), //แสดงภาษาจีน และภาษาไทย โดยกำหนด Font-family เอง
            ],           
            'methods' => [
                //'WriteHTML' => $PrintTemplate,
            ]
      ]);

 

      return $pdf->render();

        
          
    }

}


 

class CustomLanguageToFontImplementation extends \Mpdf\Language\LanguageToFont{

    public function getLanguageOptions($llcc, $adobeCJK)
    {
        if ($llcc === 'th') {
            return [false, 'saraban']; // for thai language, font is not core suitable and the font is Frutiger
        }

        return parent::getLanguageOptions($llcc, $adobeCJK);
    }

}