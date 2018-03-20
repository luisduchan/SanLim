<?php

namespace backend\modules\po\controllers;

use yii\web\Controller;
use backend\modules\po\models\Po;
/**
 * Default controller for the `po` module
 */
class DefaultController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionDetail() {
        $header = [];
        $lines = [];
        $request = \Yii::$app->request;
        $poNO = (empty($request->get('pono')) ? NULL : $request->get('pono'));
        if ($poNO) {
            $poModule = new Po();
            list($header, $lines) = $poModule->getDetail($poNO);
//            var_dump($lines);die();
        }

        return $this->render('detail', ['header' => $header,
                    'lines' => $lines]);
    }
    public function actionQuerybyitem() {
        $header = [];
        $lines = [];
        $request = \Yii::$app->request;
        $poNO = (empty($request->get('pono')) ? NULL : $request->get('pono'));
        if ($poNO) {
            $poModule = new Po();
            list($header, $lines) = $poModule->getDetail($poNO);
//            var_dump($lines);die();
        }

        return $this->render('detail', ['header' => $header,
                    'lines' => $lines]);
    }

}
