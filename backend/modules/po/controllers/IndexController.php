<?php

namespace backend\modules\po\controllers;

use yii\web\Controller;
use backend\modules\po\models\Po;

/**
 * Default controller for the `po` module
 */
class SummaryController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $poModel = new Po();
        $data = $poModel->getPOSummary();
        $arrayPo = array_values(array_column($data,'po_no'));
        $arrayPo = array_values(array_column($data,'po_no'));
//        var_dump($data);die();
        return $this->render('index', [ 'arrayPo' => $arrayPo,'chartData'=>$data]);
    }
    public function actionDetail()
    {
        $poModel = new Po();
        $data = $poModel->getPOSummary();
        $arrayPo = array_values(array_column($data,'po_no'));
        $arrayPo = array_values(array_column($data,'po_no'));
//        var_dump($data);die();
        return $this->render('index', [ 'arrayPo' => $arrayPo,'chartData'=>$data]);
    }
}
