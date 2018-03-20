<?php

namespace backend\modules\item\controllers;
use Yii;
use yii\web\Controller;
use backend\modules\item\forms\ItemQueryF;
use backend\modules\common\models\Customer;
use backend\modules\item\models\ItemModel;
use backend\modules\common\models\POCus;

/**
 * Default controller for the `item` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionQuery()
    {
        $customerModel = new Customer();
        $customerList = $customerModel->getListCusKeyVal();
        $itemQueryF = new ItemQueryF();
        $itemModel = new ItemModel();
        $mainData = [];
        $numberPerPage = 30;
        if ($itemQueryF->load(\Yii::$app->request->get()) && $itemQueryF->validate()) {
            $itemNo = (empty($itemQueryF->itemNo) ? NULL : $itemQueryF->itemNo);
            $customer = (empty($itemQueryF->customer) ? NULL : $itemQueryF->customer);
            $numberPerPage = (empty($itemQueryF->numberPerPage) ? 30 : $itemQueryF->numberPerPage);
            if($itemNo == '%' && !$customer){
                Yii::$app->session->setFlash('error','You select all items so please select a customer!');
            }else{
                $mainData = $itemModel->queryItem($itemNo,$customer);
            }

        }
        return $this->render('query', [
            'itemQueryF' => $itemQueryF,
            'customerList' => $customerList,
            'mainData' => $mainData,
            'numberPerPage' => $numberPerPage,
        ]);
    }
    public function actionDetail() {
        $poCusModel = new POCus();
        $request = \Yii::$app->request;
        $itemModel = new ItemModel();
        $itemNO = (empty($request->get('itemno')) ? NULL : $request->get('itemno'));
        $imageBLODs = '';

        $itemData = $itemModel->getItemDetail($itemNO);
        if ($itemNO) {
            $imageBLODs = $poCusModel->getImage($itemNO);
        }
        return $this->render('detail',[
            'imageBLODs' => $imageBLODs,
            'itemNO' => $itemNO,
            'itemData' => $itemData,
            ]);
    }
}
