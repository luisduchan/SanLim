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
class CrmController extends Controller {

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
}
