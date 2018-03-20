<?php

namespace backend\modules\ashley\controllers;

use yii\web\Controller;
use backend\modules\common\models\ReportGroup;
use backend\modules\ashley\forms\AshleyForm;
use backend\modules\ashley\models\Ashley;
use backend\modules\common\models\CustomerPOCommon;
use backend\modules\common\models\Customer;
use backend\modules\common\models\ArrayTool;



class DefaultController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {

        $ashleyForm = new AshleyForm();
        $ashleyModel = new Ashley();

        $inventoryData = [];
        $blanketData = [];
        $serialData = [];
        $adjustData = [];
       

      

        if ($ashleyForm->load(\Yii::$app->request->get()) && $ashleyForm->validate()) {

            $customer = (empty($ashleyForm->customer) ? NULL : $ashleyForm->customer);
            $blanketNo = (empty($ashleyForm->blanketNo) ? NULL : $ashleyForm->blanketNo);
            $blanketData = $ashleyModel->getblanketQuantity($customer, $blanketNo); 
            $serialData = $ashleyModel->getAshleyQtySerial($customer, $blanketNo); 
            $adjustData = $ashleyModel->getAshleyQtyAjustment(); 
            

            $blanketData = ArrayTool::converKeyValueAdv($blanketData,'item_code', ['item_code', 'description', 'blanket_qty_total']);

            $serialData = ArrayTool::converKeyValueAdv($serialData,'item_code', ['item_code', 'serial_qty']);

           $adjustData = ArrayTool::converKeyValueAdv($adjustData,'item_code', ['item_code', 'description', 'adjust_qty']);


            /*foreach($adjustData as $item_code => $adjustRow){

                if(!isset($blanketData[$item_code])){
                    
                    $blanketData[$item_code]=['item_code'=>$item_code,'description' => $adjustRow['description'],'blanket_qty_total' => 0];
                }
            }

            $blanketData[] = ksort($blanketData);*/

            foreach($blanketData as $item_code => $blanketRow){
                
                $serialQty = isset($serialData[$item_code]) ? $serialData[$item_code]['serial_qty'] : 0;
                $adjustQty = isset($adjustData[$item_code]) ? $adjustData[$item_code]['adjust_qty'] : 0;

                $inventoryQty = $blanketRow['blanket_qty_total'] - $serialQty -  $adjustQty;

                $inventoryData[] = ['item_code' => $blanketRow['item_code'], 'description' => $blanketRow['description'],'inventory_quantity' => $inventoryQty];         
            }       
        }  


        return $this->render('index',[
             'ashleyForm' => $ashleyForm,
             'inventoryData'   => $inventoryData,
        ]);
    } 

}
