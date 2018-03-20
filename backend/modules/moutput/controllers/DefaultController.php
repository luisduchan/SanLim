<?php

namespace backend\modules\moutput\controllers;
use backend\modules\common\models\ReportGroup;
use backend\modules\moutput\forms\GeneralF;
use backend\modules\moutput\models\Moutput;
use backend\modules\common\models\ArrayTool;

use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * Default controller for the `moutput` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */

    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'login', 'logout', 'signup'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'signup'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index','logout'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $reportGroupModel = new ReportGroup();
        $allGroups = $reportGroupModel->getValueKey();
        $generalF = new GeneralF();
        $mainData = [];
        $monthData = [];
        $dateFrom = '';
        $dateTo = '';
        if ($generalF->load(\Yii::$app->request->get()) && $generalF->validate()) {
            $itemNo = (empty($generalF->item_no) ? NULL : $generalF->item_no);
            $dateFrom = (empty($generalF->date_from) ? NULL : $generalF->date_from);
            $dateTo = (empty($generalF->date_to) ? NULL : $generalF->date_to);
            $rangeString = (empty($generalF->range) ? NULL : $generalF->range);
            $range = explode(',', $rangeString);

            $moutputModel = new Moutput();
            $data = $moutputModel->getGeneralData($itemNo, $dateFrom, $dateTo);
            list($monthData, $data) = ArrayTool::convertColumsToHeaderCustom('item_no', 'month', 'total', $data, ['description']);
            // var_dump($data);die();
            $mainData = [];
            foreach($data as $key => $row){
                $add = false;
                foreach($monthData as $month){
                    if($row[$month] >= $range[0] && $row[$month] <= $range[1]){
                        $add = true;
                    }
                }
                if($add){
                    $mainData[$key] = $row;
                }
            }

        }
        return $this->render('index',[
            'allGroups' => $allGroups,
            'generalF' => $generalF,
            'mainData' => $mainData,
            'monthData' => $monthData,
            'dateTo' => $dateTo,
            'dateFrom' => $dateFrom,
        ]);
    }
}
