<?php

namespace admin\modules\warehousemoving\controllers;

use Yii;
use common\models\ViewInventory;
use admin\modules\warehousemoving\models\InventorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Items;
use common\models\WarehouseMoving;
/**
 * InventoryController implements the CRUD actions for ViewInventory model.
 */
class InventoryController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ViewInventory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InventorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=50;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ViewInventory model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ViewInventory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
     
    /**
     * Finds the ViewInventory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ViewInventory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ViewInventory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionInvenByLocation($id){        

        // มีคลังอะไรบ้าง
        $template   = '<div class="row">';
        $inven      = 0;
        $ledgers = WarehouseMoving::find()
        ->select('location')
        ->where(['item' => $id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->groupBy('location')->all();
        foreach ($ledgers as $key => $ledger) {
            $template.= '
                        <div class="col-sm-3">               
                            <div class="panel panel-default">                                    
                                <div class="panel-heading"><span class="glyphicon glyphicon-camera"></span> '.$ledger->locations->name.'</div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-12 text-left">'.$ledger->locations->code.'</div>
                                        <div class="col-xs-12 text-right">
                                        <h1><a href="index.php?WarehouseSearch[ItemId]='.base64_encode($id).'&r=warehousemoving%2Fwarehouse&WarehouseSearch[location]='.$ledger->location.'" target="_blank">
                                        '.number_format(self::getInventory($id,$ledger->location)).'</a>
                                        </h1></div>
                                    </div>
                                </div>                           
                            </div>
                        </div>                         
                        ';
            $inven+= self::getInventory($id,$ledger->location);
        }
        
        $template.= '</div>';
        return json_encode([
            'status'    => 200,
            'inven'     => $inven,
            'html'      => $template
        ]);
    }

    static function getInventory($id,$location){
        //var_dump($id);
        //$model = Items::findOne(['id' => $id]);
        $ledger = WarehouseMoving::find()
        ->select('sum(Quantity * qty_per_unit) as Quantity')
        ->where(['item' => $id])
        ->andWhere(['location' => $location])
        ->groupBy('location')
        ->one();
        if($ledger){
            return $ledger->Quantity;
        }else{
            return 0;
        }
        
    }
}
