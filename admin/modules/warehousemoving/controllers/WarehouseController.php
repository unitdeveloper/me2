<?php

namespace admin\modules\warehousemoving\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\WarehouseMoving;
use admin\modules\warehousemoving\models\WarehouseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\SqlDataProvider;

/**
 * WarehouseController implements the CRUD actions for WarehouseMoving model.
 */
class WarehouseController extends Controller
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
     * Lists all WarehouseMoving models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WarehouseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=150;

         
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionIndexFilter() // ยกเลิกใช้งาน
    {
        
        $Filter = (isset($_GET['Filter']))? $_GET['Filter'] : '' ;

         //------Search Date------
         $formdate   = date('Y-m-d');
         $todate     = date('Y-m-d');
         
         if(isset($_GET['fil_from_date']))    $formdate   = date('Y-m-d',strtotime($_GET['fil_from_date']));
         if(isset($_GET['fil_to_date']))      $todate     = date('Y-m-d',strtotime($_GET['fil_to_date']));         

        
           
         //------/. Search Date ------
        switch ($Filter) {
            case 'SV':
                # Sale Vat
                $sql = "SELECT 
                            w.id,
                            w.TypeOfDocument,
                            w.PostingDate,
                            w.SourceDocNo,
                            w.DocumentNo,
                            w.Quantity,
                            s.unit_price,
                            i.master_code,
                            i.description_th,
                            sh.vat_type
                            FROM (((warehouse_moving as w  INNER JOIN warehouse_header as h  ON w.source_id = h.id) 
                        INNER JOIN items as i ON w.item = i.id)     
                        INNER JOIN sale_line as s  ON w.SourceDoc = s.id)   
                        INNER JOIN sale_header as sh  ON sh.id = s.sourcedoc  
                        WHERE sh.vat_type = 1 
                        AND (w.PostingDate BETWEEN  '$formdate  00:00:0000' AND '$todate 23:59:9999')
                        AND w.Quantity < 0 
                        ORDER BY  w.PostingDate DESC
                        ";
                 
                //$query = \Yii::$app->db->createCommand($sql)->queryAll();
                $dataProvider = new SqlDataProvider(['sql' => $sql]);
                $dataProvider->pagination->pageSize=50;
                
                
                break;
            case 'SN':
                # Sale No Vat
                $sql = "SELECT 
                            w.id,
                            w.TypeOfDocument,
                            w.PostingDate,
                            w.SourceDocNo,
                            w.DocumentNo,
                            w.Quantity,
                            s.unit_price,
                            i.master_code,
                            i.description_th,
                            sh.vat_type
                        FROM (((warehouse_moving as w  INNER JOIN warehouse_header as h  ON w.source_id = h.id) 
                        INNER JOIN items as i ON w.item = i.id)     
                        INNER JOIN sale_line as s  ON w.SourceDoc = s.id)   
                        INNER JOIN sale_header as sh  ON sh.id = s.sourcedoc  
                        WHERE sh.vat_type = 2
                        AND  (w.PostingDate BETWEEN  '$formdate  00:00:0000' AND '$todate 23:59:9999')
                        AND w.Quantity < 0 
                        ORDER BY w.PostingDate DESC
                        
                        ";
                        //echo $sql;
                $dataProvider = new SqlDataProvider(['sql' => $sql]);
                $dataProvider->pagination->pageSize=50;
                break;
            case 'OP':
                $sql = "SELECT 
                            w.id,
                            w.TypeOfDocument,
                            w.PostingDate,
                            w.SourceDocNo,
                            w.DocumentNo,
                            w.Quantity,
                            s.unit_price,
                            i.master_code,
                            i.description_th,
                            sh.vat_type
                        FROM (((warehouse_moving as w  INNER JOIN warehouse_header as h  ON w.source_id = h.id) 
                        INNER JOIN items as i ON w.item = i.id)     
                        INNER JOIN sale_line as s  ON w.SourceDoc = s.id)   
                        INNER JOIN sale_header as sh  ON sh.id = s.sourcedoc    
                        WHERE  w.TypeOfDocument = 'Output'
                        ORDER BY  w.PostingDate DESC             
                                    
                        ";
                $dataProvider = new SqlDataProvider(['sql' => $sql]);
                $dataProvider->pagination->pageSize=50;
                break;
            case 'CO':
                $sql = "SELECT 
                            w.id,
                            w.TypeOfDocument,
                            w.PostingDate,
                            w.SourceDocNo,
                            w.DocumentNo,
                            w.Quantity,
                            s.unit_price,
                            i.master_code,
                            i.description_th,
                            sh.vat_type
                        FROM (((warehouse_moving as w  INNER JOIN warehouse_header as h  ON w.source_id = h.id) 
                        INNER JOIN items as i ON w.item = i.id)     
                        INNER JOIN sale_line as s  ON w.SourceDoc = s.id)   
                        INNER JOIN sale_header as sh  ON sh.id = s.sourcedoc   
                        WHERE  w.TypeOfDocument = 'Consumption'  
                        ORDER BY  w.PostingDate DESC             
                                    
                        ";
                $dataProvider = new SqlDataProvider(['sql' => $sql]);
                $dataProvider->pagination->pageSize=50;
                break;
            default:
                $sql = "SELECT 
                            w.id,
                            w.TypeOfDocument,
                            w.PostingDate,
                            w.SourceDocNo,
                            w.DocumentNo,
                            w.Quantity,
                            s.unit_price,
                            i.master_code,
                            i.description_th,
                            sh.vat_type
                        FROM (((warehouse_moving as w  INNER JOIN warehouse_header as h  ON w.source_id = h.id) 
                        INNER JOIN items as i ON w.item = i.id)     
                        INNER JOIN sale_line as s  ON w.SourceDoc = s.id)   
                        INNER JOIN sale_header as sh  ON sh.id = s.sourcedoc     
                        ORDER BY  w.PostingDate DESC             
                                     
                        ";
                $dataProvider = new SqlDataProvider(['sql' => $sql]);
                $dataProvider->pagination->pageSize=50;
                break;
        }

       
        
        return $this->render('index_filter', [
            //'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single WarehouseMoving model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new WarehouseMoving model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WarehouseMoving();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WarehouseMoving model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing WarehouseMoving model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(Yii::$app->user->identity->id !==1){
            Yii::$app->session->setFlash('error', Yii::t('common','Not Allow'));             
        }else{
            $this->findModel($id)->delete();
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the WarehouseMoving model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WarehouseMoving the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WarehouseMoving::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
