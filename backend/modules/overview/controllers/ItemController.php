<?php

namespace app\modules\overview\controllers;

use yii\web\Controller;
use backend\modules\overview\forms\PlanF;
use common\modules\sanlim\models\Date;
use backend\modules\overview\models\ItemOverview;
use backend\modules\overview\models\Overview;

/**
 * Default controller for the `overview` module
 */
class ItemController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $itemModel = new ItemOverview();
        $items = $itemModel->getList();
//        var_dump($items);die();
        return $this->render('index',['items'=>$items]);
    }

    public function actionPlanning() {
        $planForm = new PlanF();
        $requestParam = [];
        $showChart = FALSE;
        $result = [];
        $arrPeriod = [];
        $arrPastPeriod = [];
        $limitQuantity = 5;
        $outputData = [];
        
        if ($planForm->load(\Yii::$app->request->get()) && $planForm->validate()) {
            $requestParam['item_no'] = (empty($planForm->item_no) ? NULL : $planForm->item_no);
            $requestParam['date_from'] = (empty($planForm->date_from) ? NULL : $planForm->date_from);
            $requestParam['date_to'] = (empty($planForm->date_to) ? NULL : $planForm->date_to);
            $requestParam['date_from_outst'] = '2017-01-01'; //(empty($planForm->date_from_outst) ? NULL : $planForm->date_from_outst);
            $requestParam['date_to_outst'] = '2017-02-31'; //(empty($planForm->date_to_outst) ? NULL : $planForm->date_to_outst);
            $itemModelOverview = new ItemOverview();
            $verviewModel = new Overview();
            $resultTemp = $itemModelOverview->getPlanningItem($requestParam);
            $showChart = TRUE;

            $arrPeriod = Date::getPeriod($requestParam['date_from_outst'], $requestParam['date_to_outst']);
            $arrPastPeriod = Date::getPeriod($requestParam['date_from'], $requestParam['date_to']);
            for ($i = 0; $i < count($resultTemp); $i++) {
                $isValidate = FALSE;
                if ($resultTemp[$i]['output'] > $limitQuantity || $resultTemp[$i]['stock'] > $limitQuantity) {
                    $isValidate = TRUE;
                }
                if (!$isValidate) {
                    foreach ($arrPeriod as $monthYear => $period) {
                        if ($resultTemp[$i][$monthYear] > $limitQuantity){
                            $isValidate = TRUE;
                        }
                    }
                }
                if ($isValidate){
                    $result[] = $resultTemp[$i];
                }
            }
            
            $outputData = $verviewModel->getMaterialOutput($requestParam);
//            var_dump($outputData);die();
        }
        return $this->render('planning', [
                    'planForm' => $planForm,
                    'showChart' => $showChart,
                    'data' => $result,
                    'month_year_outst' => array_keys($arrPeriod),
                    'arrPastPeriod' => array_keys($arrPastPeriod),
                    'outputData' => $outputData
        ]);
    }

}
