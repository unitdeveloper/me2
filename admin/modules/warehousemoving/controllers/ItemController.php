<?php

namespace admin\modules\warehousemoving\controllers;

use admin\modules\warehousemoving\models\SearchItems;
use common\models\ItemMystore;
use common\models\Items;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ItemController extends \yii\web\Controller
{
    public $company;
    public function actionIndex()
    {
        $searchModel = new SearchItems();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->pagination->pageSize=20;
        $this->company = Yii::$app->session->get('Rules')['comp_id'];
        if ($this->company != 1) {
            $dataProvider->query->andWhere(['No' => $this->getMyitem($this->company)]);
        }
        $dataProvider->query->andWhere(['product_group' => 'RM']);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'Title' => 'RM'
        ]);
    }

    public function actionFinishGoods()
    {
        $searchModel = new SearchItems();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->pagination->pageSize=20;
        $this->company = Yii::$app->session->get('Rules')['comp_id'];
        if ($this->company != 1) {
            $dataProvider->query->andWhere(['No' => $this->getMyitem($this->company)]);
        }
        $dataProvider->query->andWhere(['product_group' => 'FG']);
        return $this->render('finished-goods', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'Title' => 'FG'
        ]);
    }

    public function actionCreate()
    {
        return $this->redirect(['/items/items/create']);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdate($id)
    {
        return $this->redirect(['/items/items/update',
            'id' => $id,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Items::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function getMyitem($company)
    {

        if (ItemMystore::find()->where(['comp_id' => $company])->count() > 0) {
            $model = ItemMystore::find()->where(['comp_id' => $company])->all();
            foreach ($model as $value) {
                $itemArr[] = $value->item_no;
            }
            return $itemArr;
        } else {
            return '0';
            //throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
