<?php

namespace backend\modules\item\controllers;

use yii\web\Controller;
use backend\modules\item\models\ItemModel;

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
    public function actionGetitemno() {
        if (isset($_REQUEST['item_no'])) {
            $itemNo = $_REQUEST['item_no'];
            $customerNo = isset($_REQUEST['customer_no']) ? $_REQUEST['customer_no'] : False;
            $itemModel = new ItemModel();
            $result = $itemModel->findItem($itemNo, $customerNo);
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $result;
        }
    }
    public function actionGetitemnoselect2() {
        if (isset($_REQUEST['item_no'])) {
            $itemNo = $_REQUEST['item_no'];
            $customerNo = isset($_REQUEST['customer_no']) ? $_REQUEST['customer_no'] : False;
            $itemModel = new ItemModel();
            $sqlResult = $itemModel->findItemWithDetail($itemNo, $customerNo);
            $results = [];
            for($i=0;$i < count($sqlResult); $i++){
                $results[] = ['id' => $sqlResult[$i]['item_no'],'text' => $sqlResult[$i]['item_no'] . ' ' . $sqlResult[$i]['description']];
            }
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['results' => $results];
        }
    }
    public function actionGetmaterialitem() {
        if (isset($_REQUEST['item_no'])) {
            $itemNo = $_REQUEST['item_no'];
            $itemModel = new ItemModel();
            $sqlResult = $itemModel->findMaterialItem($itemNo);
            $results = [];
            for($i=0;$i < count($sqlResult); $i++){
                $results[] = ['id' => $sqlResult[$i]['item_no'],'text' => $sqlResult[$i]['item_no'] . ' ' . $sqlResult[$i]['description']];
            }
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['results' => $results];
        }
    }
}
