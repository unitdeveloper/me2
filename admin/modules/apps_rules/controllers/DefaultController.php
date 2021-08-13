<?php

namespace admin\modules\apps_rules\controllers;

use yii\web\Controller;

/**
 * Default controller for the `apps_rules` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
