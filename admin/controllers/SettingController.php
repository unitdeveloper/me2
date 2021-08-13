<?php

namespace admin\controllers;
use Yii;
use yii\data\ActiveDataProvider;
use common\models\PrintPage;
use common\models\PrintManual;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class SettingController extends \yii\web\Controller
{
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

    public function actionIndex()
    {
        $query = PrintPage::find()
        //->where(['module_group' => 'purchase'])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        if($query->count() <= 0){
            $query = PrintPage::find()
            ->where(['module_group' => 'purchase'])
            ->andWhere(['comp_id' => 1]);
        }
  
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['module'=>SORT_ASC]],
        ]);
  
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionPrinterEditable($page)
    {
        $model = PrintPage::findOne(['id' => PrintPage::findCustomPrint($page)]);

        if ($model->load(Yii::$app->request->post())) {

            // ถ้าปรับค่าให้สร้างใหม่เป็นของตัวเอง
            if($model->comp_id === (int)Yii::$app->session->get('Rules')['comp_id']){
                $model->water_mark_img    = $model->upload($model,'water_mark_img');
                $model->save();
            }else{
                $model = $model->clonePurchasePrint($model);                
            }
            
            return $this->render('printer-editable',[
                'model' => $model
            ]);
        }else{
            return $this->render('printer-editable',[
                'model' => $model
            ]);
        }
 
 
    }

    public function actionPrinterIndex()
    {
        $query = PrintPage::find()
        //->where(['module_group' => 'purchase'])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        if($query->count() <= 0){
            $query = PrintPage::find()
            ->where(['module_group' => 'purchase'])
            ->andWhere(['comp_id' => 1]);
        }
  
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['module'=>SORT_ASC]],
        ]);
  
        return $this->render('printer-index', [
            'dataProvider' => $dataProvider,
        ]);
 
    }

    public function actionCreate(){
        return $this->render('create');
    }

    public function actionCreateMeaning(){
        return $this->render('create-meaning');
    }
 
    public function actionPrintManual(){
        $models = PrintManual::find()->all();
        $data = [];
        $i = 0;
        foreach ($models as $key => $model) {
            $i++;
            $data[] = [
                'id' => $model->id,
                'variable' => $model->variable,
                'meaning' => $model->meaning
            ];
        }
        return json_encode([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function actionPrintManualAdd(){
        $model = new PrintManual();
        
        $model->variable = $_POST['variable'];
        $model->meaning     = $_POST['meaning'];
        if($model->save()){
            return json_encode([
                'status' => 200,
                'id' => $model->id,
                'variable' => $model->variable,
                'meaning'   => $model->meaning
            ]);
        }else{
            return json_encode([
                'status' => 500,
                'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
            ]);
        }

        // $models = PrintManual::find()->all();
        // $data = [];
        // $i = 0;
        // foreach ($models as $key => $model) {
        //     $i++;
        //     $data[] = [
        //         'i' => $i,
        //         'id' => $model->id,
        //         'variable' => $model->variable,
        //         'meaning' => $model->meaning
        //     ];
        // }
       
    }

    public function actionPrintManualRemove(){
 

        if(PrintManual::findOne($_POST['id'])->delete()){
            return json_encode([
                'status' => 200,
                'id' => $_POST['id']
            ]);
        }else{
            return json_encode([
                'status' => 500,
                'id' => $_POST['id']
            ]);
        }
        
    }


    public function actionPrintManualUpdate(){
 
        $model = PrintManual::findOne($_POST['id']);
        $field = (String)$_POST['field'];
        $model->$field = $_POST['data'];
      
        if($model->save()){
            return json_encode([
                'status' => 200,
                'id' => $model->id,
                'variable' => $field
            ]);
        }else{
            return json_encode([
                'status' => 404,
                'id' => $_POST['id'],
                'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
            ]);
        }
        
    }

    public function actionDelete($id){
        $model = PrintPage::findOne($id);
        if($model!==null){
            if($model->delete()){
                Yii::$app->getSession()->addFlash('success',Yii::t('common','Deleted')); 
                return $this->redirect(['index']);
            }else{
                Yii::$app->getSession()->addFlash('warning',Yii::t('common','Error Delete')); 
                return $this->redirect(['index']);
            }
            
        }else{
            Yii::$app->getSession()->addFlash('warning',Yii::t('common','No data Found')); 
            return $this->redirect(['index']);
        }
    }


}
