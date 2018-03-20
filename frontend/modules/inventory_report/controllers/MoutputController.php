<?php

namespace app\modules\inventory_report\controllers;

use Yii;
use frontend\modules\inventory_report\models\Moutput;
use app\modules\inventory_report\forms\MaterialOutputForm;
use app\modules\inventory_report\models\Common;
use \PHPExcel_IOFactory;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use \yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class MoutputController extends Controller {
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'login', 'logout', 'signup'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'signup'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        if (Yii::$app->user->can('material_ouput_report')) {
            $materialOut = new MaterialOutputForm();
            if ($materialOut->load(\Yii::$app->request->post()) && $materialOut->validate()) {
                $requestParam = [];

                $requestParam['item_no'] = (empty($materialOut->item_no) ? NULL : $materialOut->item_no);
                $requestParam['item_cat'] = (empty($materialOut->item_cat) ? NULL : $materialOut->item_cat);
                $requestParam['location'] = (empty($materialOut->location) ? NULL : $materialOut->location);
                $requestParam['date_from'] = (empty($materialOut->date_from) ? NULL : $materialOut->date_from);
                $requestParam['date_to'] = (empty($materialOut->date_to) ? NULL : $materialOut->date_to);
                $requestParam['pcs_metric'] = $materialOut->pcs_metric;
                $requestParam['chart_total_line'] = $materialOut->chart_total_line;
                $requestParam['chart_gorup_by_location'] = $materialOut->chart_gorup_by_location;
                $requestParam['not_include_component'] = $materialOut->not_include_component;

                $moutputModel = new Moutput();
                list($arrColumSumm, $resultSummary) = $moutputModel->getSummary($requestParam);

                if (empty($resultSummary)) {
                    Yii::$app->session->setFlash('error', 'No data found');
                } else {
                    $commondModel = new Common();
                    $sheetNo = 0;
                    //Generate Excel Report
                    $objPHPExcel = new \PHPExcel();
                    $objPHPExcel->setActiveSheetIndex(0);
                    $commondModel->writeToSheet($objPHPExcel, $arrColumSumm, $resultSummary, 'Summary', $sheetNo);

                    if ($materialOut->detail_info != 0) {
                        list($arrColumDetail, $resultDetail) = $moutputModel->getDetail($requestParam);
                        $sheetNo ++;
                        $commondModel->writeToSheet($objPHPExcel, $arrColumDetail, $resultDetail, 'Detail', $sheetNo);
                    }
                    //Generate Chart
                    if ($materialOut->generate_chart == 1) {

                        if ($requestParam['chart_gorup_by_location'] == 1) {
                            $requestParam['location'] = 'SANLIM 1';
                            $chartData = $moutputModel->getSummaryData($requestParam);

                            $chartOption = ['title' => 'WOOD OUTPUT SL1',
                                'yLabel' => 'Quantity',
                                'chart_total_line' => $requestParam['chart_total_line'],
                                'worksheet' => 'ChartSL1'];
                            $sheetNo ++;
                            $objPHPExcel = $commondModel->generateBarChar($objPHPExcel, $chartData, $sheet = $sheetNo, $chartOption);


                            $requestParam['location'] = 'SANLIM 2';
                            $chartData = $moutputModel->getSummaryData($requestParam);
                            $chartOption = ['title' => 'WOOD OUTPUT SL2',
                                'yLabel' => 'Quantity',
                                'chart_total_line' => $requestParam['chart_total_line'],
                                'worksheet' => 'ChartSL2'];

                            $sheetNo ++;
                            $objPHPExcel = $commondModel->generateBarChar($objPHPExcel, $chartData, $sheet = $sheetNo, $chartOption);
                        } else {
                            $sheetNo ++;
                            $chartData = $moutputModel->getSummaryData($requestParam);
                            $chartOption = ['title' => 'WOOD OUTPUT',
                                'yLabel' => 'Quantity',
                                'chart_total_line' => $requestParam['chart_total_line'],
                                'worksheet' => 'ChartSL'];
                            $objPHPExcel = $commondModel->generateBarChar($objPHPExcel, $chartData, $sheet = $sheetNo, $chartOption);
                        }
                    }
                    ob_end_clean();
                    ob_start();

                    $filename = "MaterialOutputReport_" . date("d-m-Y-His") . ".xlsx";
                    header('Content-Type: application/openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename=' . $filename);
                    header('Cache-Control: max-age=0');

                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

                    $objWriter->setIncludeCharts(TRUE);
                    $objWriter->save('php://output');
                    exit();
                }
            }

            //New request from client
            //Get location data 
            $sqlQueryLocation = 'SELECT Code, Name FROM location';
            $sqlCommandLoaction = Yii::$app->db->createCommand($sqlQueryLocation);
            $location = ArrayHelper::map($sqlCommandLoaction->queryAll(), 'Code', 'Name');
            //Get Item Category
            $sqlItemCat = 'SELECT Code, Code FROM item_category';
            $cmdItemCat = Yii::$app->db->createCommand($sqlItemCat);
            $item_cat = ArrayHelper::map($cmdItemCat->queryAll(), 'Code', 'Code');
            //Assign data(location, item category) to view
            return $this->render('index', ['material_out' => $materialOut,
                        'location' => $location,
                        'item_cat' => $item_cat]);
        } else {
            throw new ForbiddenHttpException('You don\'t have permission on this page. Please contact Sanlim Administrator!');
        }
    }

    public function actionGetitemno() {
        if (isset($_REQUEST['item_no'])) {
            $itemNo = $_REQUEST['item_no'];
            $moutputModel = new Moutput();
            $result = $moutputModel->findItemNo($itemNo, 20);
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $result;
        }
    }

}
