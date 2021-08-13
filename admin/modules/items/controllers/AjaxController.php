<?php

namespace admin\modules\items\controllers;

use Yii;
use yii\helpers\Html;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


use common\models\Items;
use common\models\PropertyHasGroup;
use common\models\Property;
use admin\modules\items\models\SearchItems;
use common\models\ItemCrossReference;

use common\models\ItemMystore;

class AjaxController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'ajax-pick-items',
                            'find-items',
                            'find-items-json',
                            'find-items-my-store-json',
                            'find-items-json-limit',
                            'update-priority',
                            'auto-master-code',
                            'item-of-measure',
                            'update-item-of-measure',
                            'create-item-of-measure',
                            'delete-item-of-measure',
                            'set-default-of-measure',
                            'search-items-json',
                            'recalculate'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout'                    => ['post'],
                    'update-priority'           => ['post'],
                    'find-items-json'           => ['get'],
                    'items-update'              => ['post'],
                    'update'                    => ['post'],
                    'create-reference'          => ['POST'],
                    'delete-reference'          => ['POST'],
                    'auto-master-code'          => ['POST'],
                    'update-item-of-measure'    => ['POST'],
                    'create-item-of-measure'    => ['POST'],
                    'delete-item-of-measure'    => ['POST'],
                    'set-default-of-measure'    => ['POST'],
                    'search-items-json'         => ['POST'],
                    'recalculate'               => ['POST'],
                    'get-item-in-inv'           => ['POST']
                ],
            ],
        ];
    }

    public function actionUpdatePriority(){

      if(Yii::$app->request->isAjax) {

        if(Yii::$app->request->post('ids')){
          $array = $_POST['ids'];

           

          $i = 0;
          foreach ($array as  $value) {
            $i++;
            /*
            * Update both
            * 1. Update Item has property for sort priority.
            * 2. Update Property has group for setup default priority this group.
            */
            $ItemsHas = \common\models\ItemsHasProperty::find()->where(['Items_No' => $_GET['id']])->andWhere(['property_id' => $value['id']])->one();
            $prop     = \common\models\PropertyHasGroup::findOne($value['propId']);

            if($ItemsHas){           
                $ItemsHas->priority   = $i;
                $ItemsHas->update(false);
            }

            if($prop){ 
                $prop->priority       = $i;
                $prop->update(false);
            }

          }



        }


      }

    }

    public function actionAjaxPickItems()
    {

        $search     = '';
        $OrderId    = '';


        if(isset($_GET['search']))     $search     = $_GET['search'];
        if(isset($_GET['SaleOrder']))  $id         = $_GET['id'];

    	$searchModel = new SearchItems();

        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider = $searchModel->search(Yii::$app->request->post());
        $dataProvider->query->andWhere(['<>','status',0]);
        //$dataProvider->pagination->pageSize=10;


        // $this->company = Yii::$app->session->get('Rules')['comp_id'];
        if($search!='')
        {
            $dataProvider->pagination->pageSize=50;
            // $dataProvider->query->andFilterWhere(['or',
            //     ['like', 'No', $_POST['search']],
            //     ['like', 'Description', $_POST['search']],
            //     ['like', 'barcode', $_POST['search']],
            //     ['like', 'description_th', $_POST['search']]
            //     ]);

            $dataProvider->query->andFilterWhere(['or',
                ['like','No',$search],
                ['like','Description',$search],
                ['like','barcode',$search],
                ['like', 'description_th', $search]]);


        }else {
            $dataProvider->pagination->pageSize=10;
        }

        if(Yii::$app->request->isAjax) {
            return $this->renderAjax('_pick_items', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

            //$data   =   $this->renderAjax('_pick_items',['searchModel' => $searchModel,'dataProvider' => $dataProvider]);

            //Yii::$app->response->format = Yii\web\Response::FORMAT_JSON;

            //return array("status"=>'success','data'=>$data);exit;

        }else {
            // return $this->render('_pick_items', [
            //     'searchModel' => $searchModel,
            //     'dataProvider' => $dataProvider,
            // ]);
        }


    }

    public function actionFindItems(){

        $words = explode(" ",$_POST['word']);

         
        // $query = Items::find()
        // ->where(['id' => self::getMyitem(Yii::$app->session->get('Rules')['comp_id'])])
        // ->andWhere(['or',
        //     ['like','barcode', $words],
        //     ['like','Description', $words],
        //     ['like','description_th', $words],
        //     ['like','master_code', $words]            
        // ])->orderBy(['StandardCost' => SORT_DESC]);

        $query = ItemMystore::find()->where(['or',
            ['like','name', $words],
            ['like','name_en', $words],
            ['like','master_code', $words],
            ['like','detail', $words],
            ['like','size', $words],
            ['like','barcode', $words]
        ])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->orderBy(['name' => SORT_DESC])
        ->limit((isset($_GET['limit']))? $_GET['limit'] : 20);

        // '.wordwrap($value->description_th, 20, "<br/>\r\n").'
        // '.mb_substr($value->Description, 0,40).'
        
        $htmlPrint = '';

        if($query->exists()){

            foreach ($query->all() as $model) {



                $Yii          = 'Yii';
                $StdCost      = $model->items->StandardCost > 0 ? number_format($model->items->StandardCost,2) : $model->items->CostGP;
                $unit_price   = $model->items->StandardCost > 0 ? number_format($model->items->StandardCost,2) : $model->items->CostGP;
                $Inventory    = $model->items->ProductionBom > 0 
                                    ? number_format($model->items->last_possible,2)
                                    : number_format($model->items->last_stock,2);
                $itemImg      = Html::img($model->items->picture,['class' => 'img-responsive','style' => 'max-width:100px; margin-bottom:20px;']);

        $htmlPrint.=
<<<HTML
<a href="#true" itemno="{$model->master_code}" data-key="{$model->item}" desc="{$model->name} {$model->detail} {$model->size}" price="{$unit_price}"  class="pick-item-to-createline" >
  <div class="panel panel-info">
    <div class="panel-body">


      <div class="row">

          <div class="col-md-1 col-sm-2">{$itemImg}</div>

          <div class="col-md-11 col-sm-10">

            <div class="row">
              <div class="col-md-10 col-xs-8">{$model->name} {$model->detail} {$model->size}</div>
              <div class="col-md-2 col-xs-4 text-right">
                <span class="find-price hidden"><p class="price">{$Yii::t('common','Price')}</p>{$StdCost}</span>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12"><span class="text-sm text-gray">{$model->name_en}</span></div>
              <div class="col-xs-12"><label class="text-black">{$Yii::t('common','Code')} : {$model->master_code}</label></div>
            </div>

            <div class="row">
              <div class="col-xs-8"><label>{$Yii::t('common','Stock')}</label></div>
              <div class="col-xs-4 text-right"><span class="text-gray">{$Inventory}</span></div>
            </div>

          </div>

      </div>

    </div>
  </div>
</a>
HTML;

            }
        }else {
            $htmlPrint.='<div><i class="fa fa-search fa-4x text-warning" aria-hidden="true"></i> Sory! No results found. '.$_POST['word'].'</div>';

        }

        return $htmlPrint;
    }


    public function actionFindItemsMyStoreJson($word)
    {
  
  
        $words = explode(" ",$_GET['word']);
  
  
        $query = ItemMystore::find()->where(['or',
            ['like','name', $words],
            ['like','name_en', $words],
            ['like','master_code', $words],
            ['like','detail', $words],
            ['like','size', $words],
            ['like','barcode', $words]
        ])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->andWhere(['status' => 1])
        ->orderBy(['name' => SORT_DESC])
        ->limit((isset($_GET['limit']))? $_GET['limit'] : 20);
  
        // '.wordwrap($value->description_th, 20, "<br/>\r\n").'
        // '.mb_substr($value->Description, 0,40).'

 
        $htmlPrint =[];
  
        if($words){
  
            foreach ($query->all() as $model) {
  
              $htmlPrint[]= [
                'no'      => $model->item_no,
                'img'     => $model->items->picture,
                'desc_th' => $model->name,
                'desc_en' => $model->name_en,
                'cost'    => $model->items->StandardCost,
                'item'    => $model->master_code,
                'inven'   => $model->items->ProductionBom > 0
                                ? $model->items->last_stock
                                : $model->items->last_possible,
                'barcode' => $model->items->barcode,
                'price'   => $model->items->CostGP,
                'id'      => $model->item,
                'detail'  => $model->detail ? $model->detail : '',
                'size'    => $model->size ? $model->size : ''
              ];
  
            }
  
        }else {
            $htmlPrint[]= [];
  
        }
  
          return json_encode($htmlPrint);
  
  }

  public function actionFindItemsJson($word)
  {


      $words = explode(" ",$_GET['word']);


      $query = Items::find()->where(['or',
          ['like','Description', $words],
          ['like','description_th', $words],
          ['like','master_code', $words],
          ['like','barcode', $words]
      ])->orderBy(['StandardCost' => SORT_DESC])
      ->limit((isset($_GET['limit']))? $_GET['limit'] : 20);

      // '.wordwrap($value->description_th, 20, "<br/>\r\n").'
      // '.mb_substr($value->Description, 0,40).'
      $htmlPrint =[];

      if($words){

          foreach ($query->all() as $model) {

            $htmlPrint[]= [
              'no'      => $model->No,
              'img'     => $model->picture,
              'desc_th' => $model->description_th,
              'desc_en' => $model->Description,
              'cost'    => $model->StandardCost,
              'item'    => $model->master_code,
              'inven'   => $model->inven,
              'barcode' => $model->barcode,
              'price'   => $model->lastPrice * 1,
              'id'      => $model->id
            ];

          }

      }else {
          $htmlPrint[]= [];

      }

        return json_encode($htmlPrint);

  }

  public function actionFindItemsJsonLimit($word,$limit=10)
  {


      $words = explode(" ",$_GET['word']);


      $query = Items::find()
            ->joinWith('mystore')
            ->where(['or',
          ['like','items.Description', $words],
          ['like','items.description_th', $words],
          ['like','items.master_code', $words],
          ['like','items.barcode', $words]
      ])
      ->andWhere(['item_mystore.comp_id'=> Yii::$app->session->get('Rules')['comp_id']])
      ->andWhere(['items.Status' => 1])
      ->orderBy(['items.StandardCost' => SORT_DESC])
      ->limit($limit);

      // '.wordwrap($value->description_th, 20, "<br/>\r\n").'
      // '.mb_substr($value->Description, 0,40).'
      $htmlPrint =[];

      if($query->count()>=1){

          foreach ($query->all() as $model) {

            $htmlPrint[]= [
                'id'        => $model->id,
                'no'        => $model->No,
                'img'       => $model->picture,
                'desc_th'   => $model->description_th,
                'desc_en'   => $model->Description,
                'cost'      => ($model->StandardCost)? $model->StandardCost : 0,
                'item'      => $model->master_code,
                'inven'     => $model->ProductionBom > 0
                                    ? $model->last_possible 
                                    : $model->last_stock,//$model->getInven($model),
                'barcode'   => $model->barcode,
                'price'     => (($model->CostGP)? $model->CostGP : 0) * 1,
                'active'    => false,
                'count'     => (int)(($query->count())? $query->count() : 0),
                'size'      => $model->size ? $model->size : '',
                'detail'    => $model->detail ? $model->detail : ''
            ];

          }

      }else {
          $htmlPrint[]= [
              'count' => 0
          ];

      }

        return json_encode($htmlPrint);

  }


  public function actionFindItemsInfo($No)
  {


      $model = Items::find()->where(['No' => $No])->one();

      $htmlPrint =[];

      if($model){


            $htmlPrint[]= [
              'no'      => $model->No,
              'img'     => $model->getPicture(),
              'desc_th' => $model->description_th,
              'desc_en' => $model->Description,
              'cost'    => $model->StandardCost,
              'item'    => $model->master_code,
              'inven'   => $model->getInven($model),
              'barcode' => Yii::$app->session->get('barcode'),
              'price'   => $model->CostGP,
              'newbar'  => Yii::$app->session->get('barcode'),
              'active'  => true,
            ];



      }else {
          $htmlPrint[]= [];

      }

        return json_encode($htmlPrint);

  }

  public function actionFindItemsForClone($word,$limit=10)
  {
    $words = explode(" ",$_GET['word']);
    $query = Items::find()
    ->where(['or',
        ['like','Description', $words],
        ['like','description_th', $words],
        ['like','master_code', $words],
        ['like','barcode', $words]
    ])
    ->orderBy(['Description' => SORT_ASC])
    ->limit($limit);

    $htmlPrint =[];
    if($query->count()>=1){

        foreach ($query->all() as $model) {

        $htmlPrint[]= [
            'id'        => $model->id,
            'no'        => $model->No,
            'img'       => $model->getPicture(),
            'desc_th'   => $model->description_th,
            'desc_en'   => $model->Description,
            'cost'      => $model->StandardCost,
            'item'      => $model->master_code,
            'inven'     => $model->getInven($model),
            'barcode'   => $model->barcode,
            'price'     => $model->CostGP,
            'active'    => false,
            'count'     => $query->count()
        ];
        }
    }else {
        $htmlPrint[]= [
            'count' => 0
        ];
    }
    return json_encode($htmlPrint);
  }

  function actionItemsUpdate(){
    $model = Items::find()->where(['No' => $_POST['id']])->one();
    $model->barcode = Yii::$app->session->get('barcode');
    if($model->save())
    {
      return true;
    }else {
      \Yii::$app->session->setFlash('error', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
      return json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
      
    }

  }

  function actionUpdate($id,$field){

    $model = Items::find()->where(['No' => $_POST['id']])->one();

    $model->$field = $_POST['value'];

    if($model->save())
    {
      return true;
    }else {
      return json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
    }

  }



  public function actionListMenu(){
      $JSON = [];


      //$Items = Items::find()->where(['itemGroup' => $_GET['group']])->all();

      $group = PropertyHasGroup::find()
      ->where(['itemgroup' => $_GET['group']])
      ->all();
      foreach ($group as $key => $model) {
        $prop = Property::findOne($model->property);
        $JSON[] = [
            'id' => $model->itemgroup,
            'name' => $prop->name,
            'desc' => $prop->description
            //'No'    => $model->No,
            //'code'  => $model->master_code,
            //'img'   => $model->picture,
            //'desc'  => $model->Description,
           // 'inven' => $model->inven,
        ];
      }

      return json_encode($JSON);
  }


  public function actionItemCrossReference(){
    $items  = Items::findOne(Yii::$app->request->post('id')); //$items  = Items::findOne(base64_decode($_POST['no']));
    if($items){
        $models = ItemCrossReference::find()
                ->where(['item' => $items->id])
                ->all();
    
        $html = '';
        foreach($models as $model){

            if($model->reference_type==1){
                $crossRef =  \common\models\Customer::findOne($model->reference_no);
                
            }else {
                $crossRef =  \common\models\Vendors::findOne($model->reference_no);
            }

            $cust   = ($model->reference_type==1)? 'selected="selected"':'';
            $vender = ($model->reference_type==2)? 'selected="selected"':'';

            $html.= '<tr data-key="'.$model->id.'">
                        <td>
                            <select name="refer_type" class="form-control">
                                <option value="1" '.$cust.'>'.Yii::t('common','Customer').'</option>
                                <option value="2" '.$vender.'>'.Yii::t('common','Vendor').'</option>
                            </sclect>
                        </td>
                        <td>
                            <input type="hidden" class="form-control"  name="source_id" value="'.$model->reference_no.'">
                            <div class="ew-type input-group">
                                <input type="text" class="form-control" name="source" readonly="readonly" value="'.$crossRef->name.'">
                                <span class="btn-info input-group-addon btn-source-picker" style="cursor:pointer;"><i class="fa fa-caret-square-o-up"></i></span>
                            </div>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="ref-barcode" value="'.$model->barcode.'">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="ref-item" value="'.$model->item_no.'">
                        </td>
                        
                        <td>
                            <input type="text" class="form-control" name="ref-description" value="'.$model->description.'">
                            <input type="hidden" class="form-control"  name="measure_code" value="'.$model->unit_of_measure.'">
                        </td>
                        
                        <td class="text-center">
                            <button type="button" class="btn btn-danger-ew delete-ref-line"><i class="fa fa-minus"></i> '.Yii::t('common','Delete').'</button>
                        </td>
                    </tr>';
        }
        $html.= '<tr data-key="0">
                        <td>
                            <select name="refer_type" class="form-control">
                                <option value="1">'.Yii::t('common','Customer').'</option>
                                <option value="2">'.Yii::t('common','Vendor').'</option>
                            </sclect>
                        </td>
                        <td>
                            <input type="hidden" class="form-control"  name="source_id">
                            <div class="ew-type input-group">
                                <input type="text" class="form-control" name="source" readonly="readonly" placeholder="เลือกลูกค้า...">
                                <span class="btn-info input-group-addon btn-source-picker" style="cursor:pointer;"><i class="fa fa-caret-square-o-up"></i></span>
                            </div>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="ref-barcode" value="'.$items->barcode.'">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="ref-item" value="'.$items->master_code.'">                                    
                        </td>
                        <td>
                            <input type="text" class="form-control" name="ref-description" value="'.$items->description_th.'">
                            <input type="hidden" class="form-control"  name="measure_code" value="'.$items->UnitOfMeasure.'">
                        </td>                        
                        <td class="text-center"> </td>
                    </tr>';
        if($models){
            return json_encode([
                'status' => 200,
                'message' => 'done',
                'value'     => [
                    'id'    => '1',
                    'no'    => $items->master_code,
                    'html'  => $html
                ]
            ]);
        }else{
            
            return json_encode([
                'status' => 404,
                'message' => Yii::t('common','Not found'),
                'value'     => [
                    'id'    => '1',
                    'no'    => $items->master_code,
                    'html'  => $html
                ]
            ]);
        }
    }else{
        $html = ' <tr>
                        <td colspan="6">Save before</td>
                    </tr>';
        return json_encode([
            'status' => 404,
            'message' => 'done',
            'value'     => [
                'id'    => '1',
                'no'    => 'New item',
                'html'  => $html
            ]
        ]);
    }
   
  }


  public function actionPickMeasure(){
    $query = \common\models\Unitofmeasure::find()->all();
    $html = '<table class="table table-hover">';
    foreach($query as $model){
        $html.= '<tr data-key="'.$model->UnitCode.'" data-desc="'.$model->Description.'" class="pointer event-measure-selected">';
        $html.= '<td>'.$model->UnitCode.'</td>';
        //$html.= '<td>'.$model->Description.'</td>';
        $html.= '</tr>';
    }
    $html.='</table>';
    
    return json_encode([
        'status' => 200,
        'message' => 'done',
        'html' => $html
    ]);
  }

  public function actionCreateReference(){
    $query = ItemCrossReference::find()
             ->where(['reference_type' => $_POST['type']])
             ->andWhere(['reference_no' => $_POST['ref']])
             ->andWhere(['reference_item' => base64_decode($_POST['no'])]);

    
    if($query->exists()){
        // ถ้าบันทัดใหม่ แต่เลือกลูกค้า/ผู้ขาย ที่มีอยู่แล้ว
        // จะไม่ต้องทำอะไร 
        // ตอบกลับเป็น Exists

        if($_POST['line']==0){   
            return json_encode([
                'status' => 201,
                'message' => 'Exists',
                'value' => [
                    'data' => $_POST['ref'],                        
                ]
            ]); 
        }else{
            // ถ้าเป็นบันทัดเดิม  
            // แต่เป็นลูกค้าใหม่ ให้แก้บันทัดเดิม
            $validate               = $query->one();
            $update                 = ItemCrossReference::findOne($_POST['line']);    
            $update->barcode        = $_POST['barcode'];
            $update->item_no        = $_POST['mastercode'];
            $update->description    = $_POST['desc'];
            $update->item           = $_POST['item'];
             
        
             
                       
            if($update->save()){
                if($update->reference_no==$validate->reference_no){  
                    return json_encode([
                        'status' => 202,
                        'message' => 'Update',
                        'value' => [
                            'data' => $update->description,                        
                        ]
                    ]);   
                }else{
                    return json_encode([
                        'status' => 201,
                        'message' => 'Update',
                        'value' => [
                            'data' => $update->description,                        
                        ]
                    ]);  
                }
                           
            }else {
                return json_encode([
                    'status' => 500,
                    'message' => json_encode($update->getErrors(),JSON_UNESCAPED_UNICODE),
                    'value' => [
                        'data' => $update->description
                    ]
                ]);
            }
        }

    }else {     
        if($_POST['line']==0){      
            $model                  = new ItemCrossReference();
            $model->reference_type  = $_POST['type'];        
            $model->reference_no    = $_POST['ref'];  
            $model->reference_item  = base64_decode($_POST['no']);   
            $model->barcode         = $_POST['barcode'];           
            $model->item_no         = $_POST['mastercode'];
            $model->description     = $_POST['desc'];
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];       
            $model->unit_of_measure = $_POST['unit'];     
            $model->item            = $_POST['item'];       
            if($model->save()){
                return json_encode([
                    'status' => 200,
                    'message' => 'Create',
                    'value' => [
                        'data' => $model,                        
                    ]
                ]);
            }else {
                return json_encode([
                    'status' => 500,
                    'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                    'value' => [
                        'data' => $model
                    ]
                ]);
            }
        }else{
            $update = ItemCrossReference::findOne($_POST['line']);
            $update->reference_no   = $_POST['ref'];          
            if($update->save()){
                return json_encode([
                    'status' => 202,
                    'message' => 'Update',
                    'value' => [
                        'data' => $update->description,                        
                    ]
                ]);             
            }else {
                return json_encode([
                    'status' => 500,
                    'message' => json_encode($update->getErrors(),JSON_UNESCAPED_UNICODE),
                    'value' => [
                        'data' => $update->description
                    ]
                ]);
            }
        }
         
    }
    
  }

  public function actionDeleteReference(){
    $model = ItemCrossReference::findOne($_POST['key']);
     
    if($model->delete()){
        return json_encode([
            'status' => 200,
            'message' => 'Delete',
            'value' => [
                'data' => $_POST['key'],                 
            ]
        ]);
    }else {
        return json_encode([
            'status' => 500,
            'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
            'value' => [
                'data' => $_POST['key']
            ]
        ]);
    }
  }


  static function getMyitem($company)
    {


        if(ItemMystore::find()->where(['comp_id' => $company])->count() > 0 )
        {
            $model = ItemMystore::find()->where(['comp_id' => $company])->all();
            foreach ($model as $value) {
                $itemArr[]= $value->item;
            }

            return $itemArr;
        } else {
            return '0';
            //throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAutoMasterCode($code){
         
        /*
            MASTER CODE AUTO GENERATE
        |-------------------|--------------------|
        | 885-01-6-70-0001  |  Explanation       |
        |-------------------|--------------------|
        | 885               |  Country           |
        | xxx-01            |  Company           |
        | xxx-xx-6          |  Parent Group      |
        | xxx-xx-x-70       |  Child Group       |
        | XXX-xx-x-xx-0001  |  Runing            |
        |-------------------|--------------------|
        */
        $data = $_POST['data'];
        
        $models = Items::find()
        ->where('master_code LIKE :code', [':code' => $code.'%'])
        ->orderBy(['master_code' => SORT_DESC])
        ->one();

        $last = '1';
        if($models !== null){           
            $getCode    = explode('-',$models->master_code);
            $last       = (end($getCode) *1) +1;            
            $itemNewCode= $code; 
        }else{
            $itemNewCode= $code;
        }

        return json_encode([
            'status' => 200,
            'message' => 'done',
            'value' => [
                'code' => $itemNewCode,
                'last' => str_pad($last, 4, "0", STR_PAD_LEFT),
                'newcode' => (string)$itemNewCode.'-'.str_pad($last, 4, "0", STR_PAD_LEFT),
                'barcode' => Yii::$app->user->identity->profile->country.$data.str_pad($last, 4, "0", STR_PAD_LEFT)
            ]
        ]);
            
         
    }


    public function actionCreateItemOfMeasure($key){
        $model =  new \common\models\Itemunitofmeasure();
        $model->measure         = $_POST['measure'];
        $model->qty_per_unit    = $_POST['qty'];
        $model->item            = ($_POST['item']!=null)? $_POST['item'] : 1;
        $model->session_id      = $_POST['session'];
        $model->description     = 'Add New';
        $model->unit_base       = 0;
        $model->user_id         = Yii::$app->user->identity->getId();
        $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
        
        if($model->save()) {
            return json_encode([
                'id'        => $model->id,
                'measure'   => (string)$model->measure,
                'item'      => $model->item,
                'qty'       => $model->qty_per_unit * 1,
                'status'    => 200,
            ]);
        }else{
            return json_encode([
                'status' => 500,
                'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
            ]);
        }
    }
    
    public function actionItemOfMeasure($id)
    {
        $models = \common\models\Itemunitofmeasure::find()
                    ->where(['item' => $id])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->orderBy(['measure' => SORT_ASC])
                    ->all();
        //if($models->exists()){        
        $data = [];
        foreach ($models as $key => $model) {
            $data[] = [
                'id'        => $model->id,
                'measure'   => (string)$model->measure,
                'item'      => $model->item,
                'qty'       => $model->qty_per_unit * 1,
                'status'    => 200,
                'default'   => $model->unit_base
            ];
        }
        return json_encode([
            'list' => $data,
        ]);
        // }else{
        //     $models = \common\models\Itemunitofmeasure::find()
        //     ->where(['item' => 1])
        //     ->andWhere(['session_id' => Yii::$app->session->id])
        //     ->orderBy(['qty_per_unit' => SORT_ASC])
        //     ->all();
        //     $data = [];
        //     foreach ($models as $key => $model) {
        //         $data[] = [
        //             'id'        => $model->id,
        //             'measure'   => (string)$model->measure,
        //             'item'      => $model->item,
        //             'qty'       => $model->qty_per_unit * 1,
        //             'status'    => 200,
        //             'default'   => $model->unit_base
        //         ];
        //     }
        //     return json_encode([
        //         'list' => $data
        //     ]);
        // }
    }



    public function actionUpdateItemOfMeasure($id){
        $model =  \common\models\Itemunitofmeasure::findOne($id);
        if(isset($_POST['measure']))    $model->measure         = $_POST['measure'];
        if(isset($_POST['qty']))        $model->qty_per_unit    = $_POST['qty'];
        
        // หากมี Transection แล้ว ไม่ให้แก้หน่วย
        $wh = \common\models\WarehouseMoving::find()
        ->where(['item' => $model->item])
        ->andWhere(['unit_of_measure' => $model->id])
        ->andWhere(['qty_per_unit' => $model->qty_per_unit]);
        if($wh->count() <= 0){
            if($model->save()) {
                return json_encode([
                    'status' => 200,
                    'value' => [
                        'id'    => $model->id,
                        'qty'   => $model->qty_per_unit * 1,
                        'm'     => $model->measure
                    ]
                ]);
            }else{
                return json_encode([
                    'status' => 500,
                    'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                ]);
            }
        }else{
            return json_encode([
                'status' => 201,
                'message' => 'Not allowed to change',
                'value' => json_decode(self::actionItemOfMeasure($model->item))
            ]);
        }
    }

    public function actionDeleteItemOfMeasure($id){
        if($id!=''){
            $model =  \common\models\Itemunitofmeasure::findOne($id);
            // หากมี Transection แล้ว ไม่ให้ลบ
            $wh = \common\models\WarehouseMoving::find()
            ->where(['item' => $model->item])
            ->andWhere(['unit_of_measure' => $model->id])
            ->andWhere(['qty_per_unit' => $model->qty_per_unit]);
            if($wh->count() <= 0){
                // ถ้าเป็น Default ห้ามลบ
                if($model->unit_base==1){
                    return json_encode([
                        'status' => 500,
                        'message' => Yii::t('common','Not allowed to delete Default base unit of measure')
                    ]);
                }else{
                    if($model->delete()) {
                        return json_encode([
                            'status' => 200
                        ]);
                    }else{                 
                        return json_encode([
                            'status' => 500,
                            'message' => 'Error'
                        ]);
                    }
                }
            }else{
                return json_encode([
                    'status' => 201,
                    'message' => 'Not allowed to change',
                    'value' => json_decode(self::actionItemOfMeasure($model->item))
                ]);
            }
        }else{
            return json_encode([
                'status' => 404
            ]);
        }
        
    }

    public function actionSetDefaultOfMeasure($id){
        $model =  \common\models\Itemunitofmeasure::findOne($id);
        $model->unit_base = 1;
        if($model->save()){
            $company =  Yii::$app->session->get('Rules')['comp_id'];
            \common\models\Itemunitofmeasure::updateAll(['unit_base' => 0],"item = $model->item AND comp_id = $company AND id <> $id");
            self::updateDefaultMeasure($model->item);
            return json_encode([
                'status' => 200,
                'value' => json_decode(self::actionItemOfMeasure($model->item))
            ]);
        }
    }


    protected function updateDefaultMeasure($id){
        $model = \common\models\Itemunitofmeasure::find()
        ->where(['item' => $id])
        ->andWhere(['unit_base' => 1])
        ->one();
        if($model !== null){
            $item = Items::findOne($model->item);
            $myItem = ItemMystore::findOne(['item' => $item->id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            if($myItem != null){
                $myItem->unit_of_measure    = $model->measures->id;
                $myItem->qty_per_unit       = $model->qty_per_unit;
                $myItem->save();  
            }else{
                // Clone Item
                $Mystore                    = new ItemMystore();

                $Mystore->item              = $item->id;
                $Mystore->item_no           = $item->No;
                $Mystore->master_code       = $item->master_code;
                $Mystore->barcode           = $item->barcode;
                $Mystore->user_added        = Yii::$app->user->identity->id;
                $Mystore->comp_id           = Yii::$app->session->get('Rules')['comp_id'];
                $Mystore->name              = $item->description_th;
                $Mystore->name_en           = $item->Description;
                $Mystore->detail            = $item->detail;
                $Mystore->date_added        = date('Y-m-d H:i:s');
                $Mystore->unit_cost         = ($item->UnitCost)? $item->UnitCost : ($item->StandardCost)? $item->StandardCost : 0 ;
                $Mystore->sale_price        = ($item->CostGP)? $item->CostGP : 0;
                $Mystore->unit_of_measure   = $model->measures->id;  
                $Mystore->qty_per_unit      = $model->qty_per_unit;
                $Mystore->clone             = 1;
                $Mystore->size              = $item->size;

                $Mystore->save();
            }
            if($item->owner==true){
                $item->unit_of_measure      = $model->measures->id;
                $item->UnitOfMeasure        = $model->measures->UnitCode;
                $item->quantity_per_unit    = $model->qty_per_unit;
                $item->save();   
            } 
        } 
    }

    public function actionGetItemInInv(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $query          = \common\models\RcInvoiceLine::find()->where(['item' => $data->id])->andWhere(['customer_no_' => $data->cust])->all();
        $data           = [];
        foreach ($query as $key => $model) {
            $data[] = (Object)[
                'id'    => $model->orderNo ? $model->orderNo->id : '',
                'date'  => $model->orderNo ? ($model->orderNo->saleOrder ? $model->orderNo->saleOrder->order_date : '') : '',
                'row'   => $model->id,
                'no'    => $model->orderNo ? $model->orderNo->no_ : '',
                'soId'  => $model->orderNo ? ($model->orderNo->saleOrder ? $model->orderNo->saleOrder->id : '') : '',
                'so'    => $model->orderNo ? ($model->orderNo->saleOrder ? $model->orderNo->saleOrder->no : '') : '',
                'qty'   => $model->quantity * 1,
                'price' => $model->unit_price * 1
            ];
        }

        return json_encode($data);
    }

    public function actionGetItemInInvBySale(){
        $request_body   = file_get_contents('php://input');
        $body           = json_decode($request_body);
        $comp           = Yii::$app->session->get('Rules')['comp_id'];
        $keys           = 'GetItemInInvBySale&id:'.$body->id.'&sale:'.$body->sale;


        if(!Yii::$app->cache->get($keys)){
            $query = \common\models\RcInvoiceLine::find()
                            ->joinWith('rcInvoiceHeader') 
                            ->where(['rc_invoice_header.sale_id' => $body->sale])
                            ->andWhere(['rc_invoice_header.comp_id' => $comp])
                            ->andWhere(['rc_invoice_line.item' => $body->id])
                            ->all();
            $data           = [];
            foreach ($query as $key => $model) {
                $data[] = (Object)[
                    'id'    => $model->orderNo->id,
                    'date'  => date('Y-m-d', strtotime($model->orderNo->saleOrder ? $model->orderNo->saleOrder->order_date : $model->orderNo->posting_date)),
                    'row'   => $model->id,
                    'no'    => $model->orderNo->no_,
                    'soId'  => $model->orderNo->saleOrder ? $model->orderNo->saleOrder->id : '',
                    'so'    => $model->orderNo->saleOrder ? $model->orderNo->saleOrder->no : '',
                    'qty'   => $model->quantity * 1,
                    'price' => $model->unit_price * 1
                ];
            }

            Yii::$app->cache->set($keys, $data, 60);
        }

        return json_encode(Yii::$app->cache->get($keys));
    }



    public function actionSearchItemsJson(){

        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $words = explode(" ",$data->search);
        $limit  = isset($data->limit) ? $data->limit : null;

        $query = Items::find()
                ->joinWith('mystore')
                ->where(['or',
                    ['like','items.Description', $words],
                    ['like','items.description_th', $words],
                    ['like','items.master_code', $words],
                    ['like','items.barcode', $words],
                    ['like','items.name', $words],
                    ['like','items.alias', $words]
                ])
                ->andWhere(['item_mystore.comp_id'=> Yii::$app->session->get('Rules')['comp_id']])
                ->orderBy(['items.StandardCost' => SORT_DESC])
                ->limit($limit);

      $htmlPrint =[];

      if($query->count()>=1){

          foreach ($query->all() as $model) {

            $htmlPrint[]= [
                'id'        => $model->id,
                'img'       => $model->myItems->picture,
                'name'      => $model->description_th,
                'alias'     => $model->name != '' ? $model->name : ' ',
                'item'      => $model->master_code,
                'stock'     => $model->qtyAfter,
                'code'      => $model->master_code,
            ];

          }

      }

        return json_encode([
            'raws' => $htmlPrint
        ]);

    }


    public function actionRecalculate(){

        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;
        $message        = '';
        $raws           = [];
        $model          = Items::findOne($data->id);
        
        if($model!=null){
            $raws = (Object)[
                'id'        => $model->id,
                'force'     => $model->countStock->last_stock,
                'stock'     => $model->ProductionBom > 0 
                                ? ($model->myItems ? $model->myItems->last_possible * 1 : 0)
                                : ($model->myItems ? $model->myItems->last_stock * 1 : 0),
            ];
        }else{
            $status = 404;
            $message= Yii::t('common','Not found');
        }

        return json_encode([
            'status' => $status,
            'message' => $message,
            'raws' => $raws
        ]);
    }
}
