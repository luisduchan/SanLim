<?php

namespace backend\modules\blanketpo\controllers;

use yii\web\Controller;
use backend\modules\blanketpo\models\BlanketPo;
use backend\modules\common\models\DateTimeTool;

/**
 * Default controller for the `item` module
 */
class AjaxController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionGetblanketno() {
        if (isset($_REQUEST['blanketno'])) {
            $blanketNo = $_REQUEST['blanketno'];
            $blanketPoModel = new BlanketPo();
            $sqlResult = $blanketPoModel->findBlanket($blanketNo);
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $results = [];
            for($i=0;$i < count($sqlResult); $i++){
                $results[] = ['id' => $sqlResult[$i]['blanket_no'],'text' => $sqlResult[$i]['blanket_no'] . ' (' . DateTimeTool::getDateDiplay($sqlResult[$i]['confirm_date']) . ')'];
            }
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['results' => $results];
            return $result;
        }
    }
}
