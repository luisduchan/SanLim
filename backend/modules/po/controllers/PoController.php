<?php

namespace backend\modules\po\controllers;

use yii\web\Controller;
use backend\modules\po\models\Po;
use backend\modules\po\forms\PoSummaryForm;
use common\modules\sanlim\models\Date;

/**
 * Default controller for the `po` module
 */
class PoController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {

        return $this->render('index');
    }

    //list item and po for blanket order
    public function actionPodetailblanket() {
        $request = \Yii::$app->request;
        $poController = new Po();
        $mainData = [];
        $blanketOrder = (empty($request->get('blanket')) ? False : $request->get('blanket'));
        if ($blanketOrder) {
            $mainData = $poController->getPODetailBlanket($blanketOrder);
        }
        return $this->render('podetailblanket', [
            'mainData' => $mainData,
            'blanketOrder' => $blanketOrder,
        ]);
    }

}
