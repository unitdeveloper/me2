<?php

namespace admin\modules\items\controllers;

use Yii;
use common\models\Items;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use common\models\Property;


use yii\web\Response;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

use common\models\Unitofmeasure;
use common\models\Company;
use common\models\ItemMystore;

use yii\imagine\Image; 
use Imagine\Gd;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use WebPConvert\WebPConvert;


/**
 * ItemsController implements the CRUD actions for Items model.
 */
class MyStoreController extends Controller
{
     /**
     * @inheritdoc
     */
    public $company;

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
                            'delete'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
                ],
            ],
        ];
    }

    public function actionIndex(){

        $query   = ItemMystore::find()
                    ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->orderBy(['master_code' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $this->render('index', [            
            'dataProvider' => $dataProvider,
        ]);   


    }


    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = ItemMystore::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }

}