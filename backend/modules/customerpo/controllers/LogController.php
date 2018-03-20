<?php

namespace backend\modules\customerpo\controllers;

use yii\web\Controller;
use backend\modules\customerpo\models\CustomerPoLog;
use backend\modules\common\models\POCusDetail;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Default controller for the `customerpo` module
 */
class LogController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        POCusDetail::updatePOContainer();
        $customerPoLog = new CustomerPoLog();
        $mainData = $customerPoLog->getLog();
//        var_dump($mainData);
//        die();
        $columns = [
            ['attribute' => 'purchase_order_no',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data['purchase_order_no'], Url::to(['/po/default/detail',
                                        'pono' => $data['purchase_order_no'],
                    ]));
                }],
            ['attribute' => 'customer_name'],
            ['attribute' => 'total_container'],
            ['attribute' => 'previous_total_container'],
            ['attribute' => 'previous_total_container1'],
            ['attribute' => 'nav_update_date'],
            ['attribute' => 'confirm_date_from'],
            ['attribute' => 'confirm_date_to'],
        ];
        $gridViewDataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $mainData,
            'pagination' => ['pageSize' => 200]
        ]);
        return $this->render('index.tpl', [
                    'mainData' => $mainData,
                    'columns' => $columns,
                    'gridViewDataProvider' => $gridViewDataProvider,
        ]);
    }

}
