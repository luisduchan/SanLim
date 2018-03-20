<?php

namespace backend\modules\item\controllers;
use Yii;
use yii\web\Controller;
use backend\modules\item\forms\ItemQueryF;
use backend\modules\common\models\Customer;
use backend\modules\item\models\ItemModel;
use backend\modules\item\models\BomModel;
use backend\modules\common\models\POCus;

/**
 * Default controller for the `item` module
 */
class BomController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionDetail() {
        $bomModel = new BomModel();
        $request = \Yii::$app->request;
        $itemModel = new ItemModel();
        $bomNo = (empty($request->get('bomno')) ? NULL : $request->get('bomno'));
        $imageBLODs = '';
        $headerData = [];
        $linesData = [];
        $itemData = $itemModel->getItemDetail($bomNo);
        if ($bomNo) {
            list($headerData, $linesData) = $bomModel->getBom($bomNo);
            // var_dump($headerData);die();
        }
        return $this->render('detail',[
            'bomNo' => $bomNo,
            'headerData' => $headerData,
            'linesData' => $linesData,
            ]);
    }
}
