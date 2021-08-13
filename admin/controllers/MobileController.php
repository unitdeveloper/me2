<?php
namespace admin\controllers;

use Yii;
use common\models\Items;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


use yii\widgets\ActiveForm;

use common\models\Unitofmeasure;
use common\models\Company;
use common\models\ItemMystore;


/**
 * ItemsController implements the CRUD actions for Items model.
 */
class MobileController extends Controller
{
    /**
     * @inheritdoc
     */

    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }


    public function actionIndex(){
        return $this->renderpartial('index');
    }
     

    public function actionView($id){
        $model = Items::findOne($id);
        return $this->renderpartial('view',[
            'model' => $model
        ]);
    }
}
