<?php

namespace admin\controllers;

use Yii;
use common\models\SourceMessage;
use admin\models\MessageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\Message;
/**
 * LanguageController implements the CRUD actions for SourceMessage model.
 */
class LanguageController extends Controller
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
     * Lists all SourceMessage models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy(['message' => SORT_ASC]);
        $dataProvider->pagination->pageSize=100;

        if(Yii::$app->request->post()){
          // ถ้าไม่มี id ต้องสร้างใหม่
          $query = Message::find()
                  ->where(['id' => $_POST['id']])
                  ->andWhere(['language' => $_POST['language']])->one();
          if($query){

            $query->translation = trim($_POST['text']);
            if(!$query->save()){
              print_r($query->getErrors());
            }

          }else {

            $model = new Message();

            $model->id = $_POST['parent'];
            $model->language = trim($_POST['language']);
            $model->translation = trim($_POST['text']);
            if(!$model->save()){
              print_r($model->getErrors());
            }
          }
        }else {


          return $this->render('index', [
              'searchModel' => $searchModel,
              'dataProvider' => $dataProvider,
          ]);

        }
    }

    /**
     * Displays a single SourceMessage model.
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
     * Creates a new SourceMessage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SourceMessage();

        if ($model->load(Yii::$app->request->post())) {




            $model->save();

            foreach ($_POST['language'] as $key => $value) {

              $message = new Message();
              $message->id = $model->id;
              $message->language = $key;
              $message->translation = $value;
              $message->save(false);
            }
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SourceMessage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            foreach ($_POST['language'] as $key => $value) {
               
              $message = new Message();
              $message->id = $model->id;
              $message->language = trim($key);
              $message->translation = trim($value);
              $message->save(false);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SourceMessage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SourceMessage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SourceMessage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SourceMessage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionTranslate(){

      $text   = urldecode($_GET['text']);
      //$text   = str_replace("%20", " ", $_GET['text']);
      $source = trim($_GET['source']);

      $JSON   = [
                  'id'        => '',
                  'language'  => $source,
                  'text'      =>$text
                ];

      $query  = SourceMessage::find()
              ->where(['category' => $source])
              ->andWhere(['message' => $text])->one();
      if($query){

        $model = Message::find()
                ->where(['id' => $query->id])
                ->andWhere(['language' => substr(Yii::$app->language,0,2)])
                ->one();
        if($model){
          $JSON   = [
                      'id'        => $model->id,
                      'language'  => $model->language,
                      'text'      => (trim($model->translation))? trim($model->translation) : $text,
                    ];
        }

      }

      return json_encode($JSON,JSON_UNESCAPED_UNICODE);

    }
}
