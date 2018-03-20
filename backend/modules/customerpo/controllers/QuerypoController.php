<?php

namespace backend\modules\customerpo\controllers;
use Yii;
use yii\web\Controller;
use backend\modules\overview\forms\CusPOReport;
use backend\modules\common\models\POCus;
use backend\modules\common\models\Customer;
use backend\modules\customerpo\forms\SummaryPOF;
use backend\modules\common\models\ReportGroup;
use backend\modules\common\models\POCusDetail;
use backend\modules\customerpo\forms\NeedPlanF;
use backend\modules\customerpo\models\CustomerPo;
use DateTime;

/**
 * Default controller for the `customerpo` module
 */
class QueryController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        return $this->render('index');
    }

}
