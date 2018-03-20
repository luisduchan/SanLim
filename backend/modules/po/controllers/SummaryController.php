<?php

namespace backend\modules\po\controllers;

use yii\web\Controller;
use backend\modules\po\models\Po;
use backend\modules\po\forms\PoSummaryForm;
use common\modules\sanlim\models\Date;

/**
 * Default controller for the `po` module
 */
class SummaryController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $poModel = new Po();
        $requestParam['item_no'] = 'WOODRBB%';
        $requestParam['purchaser'] = NULL;
        $requestParam['vendor'] = NULL;
        $requestParam['date_from'] = '2016-12-01';
        $requestParam['date_to'] = NULL;
        $requestParam['pcs_metric'] = True;
        $data = $poModel->getPOSummary($requestParam);
        $arrayPo = array_values(array_column($data, 'po_no'));
        return $this->render('index', ['arrayPo' => $arrayPo, 'chartData' => $data]);
    }

    public function actionQuery() {
        $poModel = new Po();
        $poSummaryForm = new PoSummaryForm();
        $showChart = FALSE;
        $arrayPo = [];
        $chartData = [];
        if ($poSummaryForm->load(\Yii::$app->request->get()) && $poSummaryForm->validate()) {
            $requestParam['item_no'] = (empty($poSummaryForm->item_no) ? NULL : $poSummaryForm->item_no);
            $requestParam['purchaser'] = (empty($poSummaryForm->purchaser) ? NULL : $poSummaryForm->purchaser);
            $requestParam['vendor'] = (empty($poSummaryForm->vendor) ? NULL : $poSummaryForm->vendor);
            $requestParam['date_from'] = (empty($poSummaryForm->date_from) ? NULL : $poSummaryForm->date_from);
            $requestParam['date_to'] = (empty($poSummaryForm->date_to) ? NULL : $poSummaryForm->date_to);
            $requestParam['pcs_metric'] = $poSummaryForm->pcs_metric;
            $requestParam['date_type'] = $poSummaryForm->date_type;
            $requestParam['po_status'] = (empty($poSummaryForm->po_status) ? NULL : $poSummaryForm->po_status);
            $showChart = TRUE;
            $chartData = $poModel->getPOSummary($requestParam);
            $arrayPo = array_values(array_column($chartData, 'po_no'));
        } else {
            
        }
        $dateModel = new Date();
        return $this->render('query', [
            'poSummaryForm' => $poSummaryForm, 
            'showChart' => $showChart, 
            'arrayPo' => $arrayPo, 
            'chartData' => $chartData,
            'dateType' => $dateModel->getDateToShow()]);
    }

}
