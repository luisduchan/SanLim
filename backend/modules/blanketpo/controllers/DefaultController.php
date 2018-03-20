<?php

namespace backend\modules\blanketpo\controllers;

use yii\web\Controller;

use backend\modules\blanketpo\forms\QueryForm;
use backend\modules\common\models\ArrayTool;
use backend\modules\common\models\Customer;
use backend\modules\blanketpo\models\BlanketPo;
use backend\modules\common\models\DateTimeTool;
use yii\filters\AccessControl;
/**
 * Default controller for the `blanketpo` module
 */
class DefaultController extends Controller
{
	 public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['view_blanket'],
                    ],
                ],
            ],
        ];
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
    	$queryFrom  = new QueryForm();
    	$customerModel = new Customer();
        $customerList = $customerModel->getListCusKeyVal();

        $blanketPoModel = new BlanketPo();
        $mainData = [];
        if ($queryFrom->load(\Yii::$app->request->get()) && $queryFrom->validate()) {
        	$blanketNo = (empty($queryFrom->blanket_name) ? NULL : $queryFrom->blanket_name);
        	$itemNos = (empty($queryFrom->item_nos) ? NULL : $queryFrom->item_nos);
        	$custNO = (empty($queryFrom->customers) ? NULL : $queryFrom->customers);
        	$mainData = $blanketPoModel->getBlanketPO($blanketNo, $itemNos, $custNO);
    		if($mainData){
    			for($i=0; $i<count($mainData); $i++){
    				$row = $mainData[$i];
    				$mainData[$i]['assembly_date'] =  DateTimeTool::getDateDiplay($row['shceduled_date_start'], $row['scheduled_date_end']);
    				$mainData[$i]['cofirmed_etd'] =  DateTimeTool::getDateDiplay($row['cofirmed_etd_start'], $row['cofirmed_etd_end']);
    				$mainData[$i]['cont_adjmt'] = ROUND($row['cont_adjmt'],2);
    				$mainData[$i]['order_date'] = DateTimeTool::getDateDiplay($row['order_date']);
    			}
    		}
        	
        }

        return $this->render('index',
        	[
        		'queryFrom' => $queryFrom,
        		'customerList' => $customerList,
        		'mainData' => $mainData,
        	]
    	);
    }
}
