<?php

namespace backend\modules\po\controllers;

use yii\web\Controller;
use backend\modules\po\models\Po;
use backend\modules\po\forms\QueryByItemForm;
use backend\modules\common\models\ArrayTool;
use backend\modules\common\models\Customer;
/**
 * Default controller for the `po` module
 */
class ItemorderController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $header = [];
        $lines = [];
        $request = \Yii::$app->request;
        $poModel = new Po();
        $queryByItemForm = new QueryByItemForm();
        $mainData = [];
        $months = [];

        $customerModel = new Customer();
        $customerList = $customerModel->getListCusKeyVal();
        $dateTypeList = ['expected_aseembling_date' => 'Expected Assembling Date',
            'confirm_date' => 'Confirm Ship Date'
        ];

        if ($queryByItemForm->load(\Yii::$app->request->get()) && $queryByItemForm->validate()) {
            $item_nos = (empty($queryByItemForm->item_nos) ? NULL : $queryByItemForm->item_nos);
            $item_no = (empty($queryByItemForm->item_no) ? NULL : $queryByItemForm->item_no);
            $description = (empty($queryByItemForm->description) ? NULL : $queryByItemForm->description);
            $customers = (empty($queryByItemForm->customers) ? NULL : $queryByItemForm->customers);
            $group_by_item_group = (empty($queryByItemForm->group_by_item_group) ? NULL : $queryByItemForm->group_by_item_group);
            $unitQuantity = (empty($queryByItemForm->unit_quantity) ? NULL : $queryByItemForm->unit_quantity);
            $dateFrom = (empty($queryByItemForm->date_from) ? NULL : $queryByItemForm->date_from);
            $dateTo = (empty($queryByItemForm->date_to) ? NULL : $queryByItemForm->date_to);
            $dateType = (empty($queryByItemForm->date_type) ? NULL : $queryByItemForm->date_type);

            $parameter['item_nos'] = $item_nos;
            $parameter['item_no'] = $item_no;
            $parameter['description'] = $description;
            $parameter['customers'] = $customers;
            $parameter['group_by_item_group'] = $group_by_item_group;
            $parameter['unit_quantity'] = $unitQuantity;
            $parameter['date_from'] = $dateFrom;
            $parameter['date_to'] = $dateTo;
            $parameter['date_type'] = $dateType;


            $mainData = $poModel->getSummaryItemOrder($parameter);
            list($group_by, $months, $mainData) = ArrayTool::convertColumsToHeader('group_by', 'month', 'total', $mainData);

//            var_dump($mainData);die();
        }
        return $this->render('index', [
                    'queryByItemForm' => $queryByItemForm,
                    'mainData' => $mainData,
                    'months' => $months,
                    'customerList' => $customerList,
                    'dateTypeList' => $dateTypeList,
        ]);
    }

}
