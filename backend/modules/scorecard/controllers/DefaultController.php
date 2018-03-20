<?php

namespace backend\modules\scorecard\controllers;

use yii\web\Controller;
use backend\modules\scorecard\forms\GenralScoreCardForm;
use backend\modules\common\models\ReportGroup;
use backend\modules\common\models\Customer;
/**
 * Default controller for the `scorecard` module
 */
class DefaultController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $reportGroupModel = new ReportGroup();
        $genralScoreCardForm = new GenralScoreCardForm();
        $allGroups = $reportGroupModel->getValueKey();
        $customerModel = new Customer();
        $customerList = $customerModel->getListCusKeyVal();
        $mainData = [];
        $group = FALSE;
        if ($genralScoreCardForm->load(\Yii::$app->request->get()) && $genralScoreCardForm->validate()) {
            $group = (empty($genralScoreCardForm->reportGroup) ? NULL : $genralScoreCardForm->reportGroup);
            $cusNo = (empty($genralScoreCardForm->customer) ? NULL : $genralScoreCardForm->customer);
        }

        return $this->render('index', [
                    'allGroups' => $allGroups,
                    'mainData' => $mainData,
                    'genralScoreCardForm' => $genralScoreCardForm,
                    'group' => $group,
                    'customerList' => $customerList,
        ]);
    }

}
