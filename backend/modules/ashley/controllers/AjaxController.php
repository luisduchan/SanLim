<?php

namespace backend\modules\ashley\controllers;

use yii\web\Controller;
use backend\modules\ashley\models\Ashley;

/**
 * Default controller for the `item` module
 */
class AjaxController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionGetblanketno() {
        if (isset($_REQUEST['blanket_no'])) {
            $blanketNo = $_REQUEST['blanket_no'];
            $ashleyModel = new Ashley();
            $result = $ashleyModel->findBlanket($blanketNo);
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $result;
        }
    }
    
}
