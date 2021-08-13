<?php

namespace admin\modules\itemgroup\controllers;

use Yii;
use common\models\Itemgroup;
use admin\modules\itemgroup\models\SearchItemGroup;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use admin\modules\itemgroup\models\PropertySearch;
/**
 * ItemgroupController implements the CRUD actions for Itemgroup model.
 */
class ItemgroupController extends Controller
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
     * Lists all Itemgroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchItemGroup();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['<=','Child',0]);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Itemgroup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $searchModel = new PropertySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where(['itemgroup' => $model->GroupID]);
        $dataProvider->query->orderBy(['priority' => SORT_ASC]);

        if(Yii::$app->request->isAjax) {

          if (Yii::$app->request->post('ids')) {

            $array      = $_POST['ids'];
            $id         = [];
            $priority   = [];
            $status     = 0;
            $message    = '';
            $i          = 0;

            foreach ($array as $key => $value) {
                $i++;
                $prop = \common\models\PropertyHasGroup::findOne($value['id']);

                if($prop){
                    $prop->priority = $i;

                    if($prop->save()){
                        $status = 200;
                        $id[] = $prop->id;
                        $priority[] = $prop->priority;
                        
                    }else{
                        $status = 500;
                        $message = json_encode($prop->getErrors(),JSON_UNESCAPED_UNICODE);
                            
                    }                
                }

            }

            return json_encode([
                'status' => $status,
                'data' => [
                    'id' => $id,
                    'priority' => $priority
                ],
                'message' => $message
            ]);
            
          }


        }else {
          return $this->render('view', [
              'model' => $model,
              'searchModel' => $searchModel,
              'dataProvider' => $dataProvider,
          ]);
        }


    }

    /**
     * Creates a new Itemgroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Itemgroup();

        if ($model->load(Yii::$app->request->post())) {

            $model->photo       = $model->upload($model,'photo');
            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            $model->save(false);
            return $this->redirect(['index', 'id' => $model->GroupID]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Itemgroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->photo       = $model->upload($model,'photo');
           
            $model->save(false);
            return $this->redirect(['index', 'id' => $model->GroupID]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Itemgroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = Itemgroup::findOne(['GroupID' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        if($model !==null){
            // ตรวจสอบการใช้งาน
            $item = \common\models\Items::findOne(['ItemGroup' => $model->GroupID]);

            if($item !==null){
                Yii::$app->session->setFlash('warning', '<i class="far fa-frown"></i> มีการเรียกใช้งานในสินค้า "'.$item->Description.'" ต้องทำการเปลี่ยนกลุ่มในสินค้านั้น ก่อนทำการลบกลุ่ม');
            }else{
                $this->findModel($id)->delete(); 
            }
            
            
        }else{
            Yii::$app->session->setFlash('error', '<i class="far fa-frown"></i> Error');
        }
        return $this->redirect(['index']);

    }



    public function actionUpdatePriority(){

        if(Yii::$app->request->isAjax) {

            if(Yii::$app->request->post('ids')){

                $array = $_POST['ids'];
                $res = [];
                $i = 0;
                foreach ($array as  $value) {
                    $i++;
                    $group     = $this->findModel($value['id']);
                    if($group){ 
                        $group->Child       = $value['parent'];     
                        $group->sequent     = $i;
                        if($group->save()){
                            $res[] = (Object)[
                                'status' => 200,
                                'message' => 'Done',
                            ];
                            
                            
                        }else{
                            $res[]    = (Object)[
                                'status' => 500,
                                'message' => json_encode($group->getErrors(),JSON_UNESCAPED_UNICODE)
                            ];
                             
                        }
                    }
    

                }

                return json_encode([
                    'response' => $res,                     

                ]);
            }


        }

    }

        
    /**
     * Finds the Itemgroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Itemgroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Itemgroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
