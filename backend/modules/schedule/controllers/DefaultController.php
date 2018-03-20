<?php

namespace backend\modules\schedule\controllers;

use yii\web\Controller;
use backend\modules\schedule\forms\ScheduleF;
use backend\modules\common\models\ReportGroup;
use backend\modules\schedule\models\Schedule;
use DateTime;
use backend\modules\common\models\ArrayTool;
use backend\modules\common\models\DateTimeTool;
use \PHPExcel_IOFactory;
use \PHPExcel_Settings;
use \PHPExcel_Chart_DataSeriesValues;
use \PHPExcel_Chart_DataSeries;
use \PHPExcel_Chart_PlotArea;
use \PHPExcel_Chart_Legend;
use \PHPExcel_Chart_Title;
use \PHPExcel_Chart;
use \PHPExcel_Style_Alignment;
use \PHPExcel_Style_Border;

/**
 * Default controller for the `schedule` module
 */
class DefaultController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $scheduleF = new ScheduleF();
        $reportGroupModel = new ReportGroup();
        $allGroups = $reportGroupModel->getValueKey();
        $scheudleModel = new Schedule();
        $mainData = [];
        $months = [];
        $group = FALSE;
        if ($scheduleF->load(\Yii::$app->request->get()) && $scheduleF->validate()) {
            $group = (empty($scheduleF->reportGroup) ? NULL : $scheduleF->reportGroup);
            $downLoad = (empty($scheduleF->downLoad) ? NULL : $scheduleF->downLoad);
            if (!$downLoad) {
                $mainData = $scheudleModel->getSchedule($group);
            } else {
                $sheetNo = 0;
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                //Generate Excel Report
                $productionLines = ['a' => [
                        'text' => 'A LINE',
                        'total' => 0,
                        'customer' => []
                    ],
                    'b' => [
                        'text' => 'B LINE',
                        'total' => 0,
                        'customer' => []
                    ],
                    'ii' => [
                        'text' => 'SAN LIM II',
                        'total' => 0,
                        'customer' => []],
                ];
                $objPHPExcel = new \PHPExcel();
                PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
                $border_style = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => '766f6e'),)));
                $borderRight = array('borders' => array(
                        'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => '766f6e')),
                ));
                $borderLeft = array('borders' => array(
                        'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => '766f6e')),
                ));
                $borderOutline = array('borders' => array(
                        'outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => '766f6e')),
                ));
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );
                $sheet = 0;
                foreach ($productionLines as $key => $productionLine) {

                    $scheduleData = $scheudleModel->getScheduleWithDetail($group, $productionLine['text']);
                    for ($i = 0; $i < count($scheduleData); $i++) {
                        $productionLines[$key]['total'] += $scheduleData[$i]['total_container'];
                        if (isset($productionLines[$key]['customer'][$scheduleData[$i]['customer_name']])) {
                            if ($key == 'a' || $key == 'b') {
                                $productionLines['a']['customer'][$scheduleData[$i]['customer_name']] += $scheduleData[$i]['total_container'];
                                $productionLines['b']['customer'][$scheduleData[$i]['customer_name']] += $scheduleData[$i]['total_container'];
                            } else {
                                $productionLines[$key]['customer'][$scheduleData[$i]['customer_name']] += $scheduleData[$i]['total_container'];
                            }
                        } else {
                            if ($key == 'a' || $key == 'b') {
                                $productionLines['a']['customer'][$scheduleData[$i]['customer_name']] = $scheduleData[$i]['total_container'];
                                $productionLines['b']['customer'][$scheduleData[$i]['customer_name']] = $scheduleData[$i]['total_container'];
                            } else {
                                $productionLines[$key]['customer'][$scheduleData[$i]['customer_name']] = $scheduleData[$i]['total_container'];
                            }
                        }
                    }
                }
                foreach ($productionLines as $key => $productionLine) {
//            for ($sheet = 0; $sheet < count($producttionLine); $sheet++) {

                    $objPHPExcel->createSheet($sheet);
                    $objPHPExcel->setActiveSheetIndex($sheet);
                    $lineNo = 1;
                    $objPHPExcel->getActiveSheet()->setTitle($productionLine['text']);
                    $beginColumn = 'A';
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . $lineNo, 'SANLIM FURNITURE (VN) CO., LTD.');
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + 1), 'PRODUCTION ASSEMBLY SCHEDULE');
                    $objPHPExcel->getActiveSheet()
                            ->getStyle($beginColumn . $lineNo . ':' . $beginColumn . ($lineNo + 1))
                            ->applyFromArray([
                                'font' => [
                                    'size' => 24,
                                    'bold' => true,
                                ],
                    ]);

                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + 2), 'Report Date:' . date('m/d/Y h:i:s a', time()));
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + 4), $productionLine['text'] . ' ' . $group);
                    $fontHeaderStyle = [
                        'font' => [
                            'size' => 20,
                            'bold' => true,
                        ],
                    ];
                    $objPHPExcel->getActiveSheet()
                            ->getStyle($beginColumn . ($lineNo + 4))
                            ->applyFromArray($fontHeaderStyle);
                    //left title
                    $n = 5;
                    $objPHPExcel->getActiveSheet()->getRowDimension($beginColumn . ($lineNo + 4))->setRowHeight(30);
                    for ($i = $lineNo + $n; $i < $lineNo + $n + 9; $i++) {
                        $objPHPExcel->getActiveSheet()->mergeCells($beginColumn . $i . ":" . chr(ord($beginColumn) + 1) . $i);
                        $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n), 'IK(PO#)');
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 1), "Item Name \n Tên hàng");
                    $objPHPExcel->getActiveSheet()->getStyle($beginColumn . ($lineNo + $n + 1))->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 2), "Container\nSố cont");
                    $objPHPExcel->getActiveSheet()->getStyle($beginColumn . ($lineNo + $n + 2))->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 3), "Customer Requested Date\nKhách hàng yêu cầu");
                    $objPHPExcel->getActiveSheet()->getStyle($beginColumn . ($lineNo + $n + 3))->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 4), "Customer Name \n Tên khách hàng");
                    $objPHPExcel->getActiveSheet()->getStyle($beginColumn . ($lineNo + $n + 4))->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 5), "Assembly Schedule Date\nKế hoạch lắp ráp");
                    $objPHPExcel->getActiveSheet()->getStyle($beginColumn . ($lineNo + $n + 5))->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 6), "WH Plan Date\nDự kiến nhập kho");
                    $objPHPExcel->getActiveSheet()->getStyle($beginColumn . ($lineNo + $n + 6))->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 7), "Cutting No.\nĐợt cắt");
                    $objPHPExcel->getActiveSheet()->getStyle($beginColumn . ($lineNo + $n + 7))->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 8), "Remark\nGhi chú");
                    $objPHPExcel->getActiveSheet()->getStyle($beginColumn . ($lineNo + $n + 8))->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($beginColumn)->setWidth(15);


                    $scheduleData = $scheudleModel->getScheduleWithDetail($group, $productionLine['text']);
                    //main schedule
                    $mainScheduleLine = $n;
                    $column = chr(ord($beginColumn) + 1);


                    $maxScheduleLine = 0;
                    for ($i = 0; $i < count($scheduleData); $i++) {
                        if ($maxScheduleLine < count($scheduleData[$i]['lines'])) {
                            $maxScheduleLine = count($scheduleData[$i]['lines']);
                        }
                    }
//                    var_dump($maxScheduleLine);
                    if ($maxScheduleLine < count($productionLines[$key]['customer']) + 7) {
                        $maxScheduleLine = count($productionLines[$key]['customer']) + 7;
                    }
                    $lastLine = ($lineNo + $n + 8 + $maxScheduleLine);
                    for ($i = 0; $i < count($scheduleData); $i++) {
                        $column++;
//
                        $curentColumn = $column++;
                        $objPHPExcel->getActiveSheet()->mergeCells($curentColumn . ($mainScheduleLine + 1) . ":" . $column . ($mainScheduleLine + 1));
                        $objPHPExcel->getActiveSheet()->setCellValue($curentColumn . ($mainScheduleLine + 1), $scheduleData[$i]['ik']);
                        $objPHPExcel->getActiveSheet()->mergeCells($curentColumn . ($mainScheduleLine + 2) . ":" . $column . ($mainScheduleLine + 2));
                        $objPHPExcel->getActiveSheet()->setCellValue($curentColumn . ($mainScheduleLine + 2), $scheduleData[$i]['product_group']);
                        $objPHPExcel->getActiveSheet()->mergeCells($curentColumn . ($mainScheduleLine + 3) . ":" . $column . ($mainScheduleLine + 3));
                        $objPHPExcel->getActiveSheet()->setCellValue($curentColumn . ($mainScheduleLine + 3), $scheduleData[$i]['total_container']);

                        if ($scheduleData[$i]['total_container'] != $scheduleData[$i]['total']) {
                            $objPHPExcel->getActiveSheet()
                                    ->getComment($curentColumn . ($mainScheduleLine + 3))
                                    ->setAuthor('Mark Baker');
                            $objPHPExcel->getActiveSheet()
                                    ->getComment($curentColumn . ($mainScheduleLine + 3))
                                    ->getText()->createTextRun('Total:' . $scheduleData[$i]['total']);
                        }

                        $objPHPExcel->getActiveSheet()->mergeCells($curentColumn . ($mainScheduleLine + 4) . ":" . $column . ($mainScheduleLine + 4));
                        $objPHPExcel->getActiveSheet()->setCellValue($curentColumn . ($mainScheduleLine + 4), $scheduleData[$i]['customer_request_date']);
                        $objPHPExcel->getActiveSheet()->mergeCells($curentColumn . ($mainScheduleLine + 5) . ":" . $column . ($mainScheduleLine + 5));
                        $objPHPExcel->getActiveSheet()->setCellValue($curentColumn . ($mainScheduleLine + 5), $scheduleData[$i]['customer_name']);
                        $objPHPExcel->getActiveSheet()->mergeCells($curentColumn . ($mainScheduleLine + 6) . ":" . $column . ($mainScheduleLine + 6));
                        $objPHPExcel->getActiveSheet()->setCellValue($curentColumn . ($mainScheduleLine + 6), $scheduleData[$i]['assembly_date']);

                        $objPHPExcel->getActiveSheet()->mergeCells($curentColumn . ($mainScheduleLine + 7) . ":" . $column . ($mainScheduleLine + 7));
                        $objPHPExcel->getActiveSheet()->setCellValue($curentColumn . ($mainScheduleLine + 7), $scheduleData[$i]['wh_date']);
                        $objPHPExcel->getActiveSheet()->mergeCells($curentColumn . ($mainScheduleLine + 8) . ":" . $column . ($mainScheduleLine + 8));
                        $objPHPExcel->getActiveSheet()->setCellValue($curentColumn . ($mainScheduleLine + 8), $scheduleData[$i]['cutting_no']);
                        $objPHPExcel->getActiveSheet()->mergeCells($curentColumn . ($mainScheduleLine + 9) . ":" . $column . ($mainScheduleLine + 9));
                        $objPHPExcel->getActiveSheet()->setCellValue($curentColumn . ($mainScheduleLine + 9), $scheduleData[$i]['remark']);
                        $totalQuantity = 0;
                        for ($j = 0; $j < count($scheduleData[$i]['lines']); $j++) {
                            $line = $lineNo + $n + 9 + $j;
                            $totalQuantity += $scheduleData[$i]['lines'][$j]['quantity'];
                            $objPHPExcel->getActiveSheet()->setCellValue($curentColumn . $line, $scheduleData[$i]['lines'][$j]['abbrevaiation']);
                            $objPHPExcel->getActiveSheet()->setCellValue($column . $line, $scheduleData[$i]['lines'][$j]['quantity']);
                        }

                        $objPHPExcel->getActiveSheet()->getStyle($curentColumn . ($lineNo + $n + 9) . ":" . $column . $lastLine)->applyFromArray($borderOutline);
                        $objPHPExcel->getActiveSheet()->setCellValue($curentColumn . $lastLine, "Total");
                        $objPHPExcel->getActiveSheet()->setCellValue($column . $lastLine, $totalQuantity);
                        $objPHPExcel->getActiveSheet()->getStyle($column . ($lineNo + $n + 9) . ":" . $column . $lastLine)->applyFromArray($style);

                        $objPHPExcel->getActiveSheet()->getColumnDimension($curentColumn)->setWidth(10);
                        $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(5);
                    }


                    //left column format
                    $objPHPExcel->getActiveSheet()
                            ->getStyle($beginColumn . ($lineNo + $n) . ":" . chr(ord($beginColumn) + 1) . $lastLine)
                            ->applyFromArray($borderOutline);
                    $objPHPExcel->getActiveSheet()
                            ->getStyle($beginColumn . ($lineNo + $n) . ":" . chr(ord($beginColumn) + 1) . $lastLine)
                            ->getFont()->setBold(true);

                    $objPHPExcel->getActiveSheet()
                            ->getStyle($beginColumn . $lastLine . ":" . $column . $lastLine)
                            ->getFont()->setBold(true); 
                    //assebly date wrap text
                    $objPHPExcel->getActiveSheet()->getStyle($beginColumn .  ($mainScheduleLine + 1) . ':' . $column . ($mainScheduleLine + 9))->getAlignment()->setWrapText(true);


                    $objPHPExcel->getActiveSheet()->getStyle($beginColumn . ($mainScheduleLine + 1) . ":" . $column . ($lineNo + $n + 8))->applyFromArray($border_style);
                    $objPHPExcel->getActiveSheet()->getStyle($beginColumn . ($mainScheduleLine + 1) . ":" . $column . ($lineNo + $n + 8))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
//                $objPHPExcel->getActiveSheet()->getStyle($column . ($mainScheduleLine + 1) . ":" . $column . $lastLine)->applyFromArray($border_style);

                    $objPHPExcel->getActiveSheet()->getStyle($beginColumn . ($lineNo + $n) . ":" . $column . ($lineNo + $n + 8))->applyFromArray($style);
                    $objPHPExcel->getActiveSheet()
                            ->getStyle($beginColumn . ($lineNo + $n) . ":" . $column . ($lineNo + $n))
                            ->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(85);
                    $sheet++;
                }
                $sheet = 0;
                foreach ($productionLines as $key => $productionLine) {
                    $objPHPExcel->setActiveSheetIndexByName($productionLine['text']);
                    $customerScheduleLine = $lineNo + $n + 15;
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . $customerScheduleLine, 'Customer');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($beginColumn) + 1) . $customerScheduleLine, 'Conts');
                    $customerScheduleLine++;
                    foreach ($productionLines[$key]['customer'] as $customerName => $customerTotal) {
                        $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . $customerScheduleLine, $customerName);
                        $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($beginColumn) + 1) . $customerScheduleLine, $customerTotal);
                        $customerScheduleLine++;
                    }

//                    $objPHPExcel->setActiveSheetIndex($sheet);

                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 9 ), 'Total');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($beginColumn) + 1) . ($lineNo + $n + 9 ), $productionLines['a']['total'] + $productionLines['b']['total'] + $productionLines['ii']['total']);
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 10), 'SAN LIM I');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($beginColumn) + 1) . ($lineNo + $n + 10), $productionLines['a']['total'] + $productionLines['b']['total']);
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 11), $productionLines['a']['text']);
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($beginColumn) + 1) . ($lineNo + $n + 11), $productionLines['a']['total']);
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 12), $productionLines['b']['text']);
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($beginColumn) + 1) . ($lineNo + $n + 12), $productionLines['b']['total']);
                    $objPHPExcel->getActiveSheet()->setCellValue($beginColumn . ($lineNo + $n + 13), 'SAN LIM II');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($beginColumn) + 1) . ($lineNo + $n + 13), $productionLines['ii']['total']);
                    $sheet++;
                }
                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);




//            $ojbSheet->getStyle("A1:Z1")->getFont()->setBold(true);

                ob_end_clean();
                ob_start();
                if ($downLoad == 2007) {
                    $filename = "Schedule_" . $group . ".xlsx";
                    header('Content-Type: application/openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename=' . $filename);
                    header('Cache-Control: max-age=0');

                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                } else {
                    $filename = "Schedule_" . $group . ".xls";
                    header('Content-Type: application/openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename=' . $filename);
                    header('Cache-Control: max-age=0');

                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                }

//            $objWriter->setIncludeCharts(TRUE);
                $objWriter->save('php://output');
                exit();
            }
        }
        return $this->render('index', [
                    'scheduleF' => $scheduleF,
                    'reportGroups' => $allGroups,
                    'months' => $months,
                    'mainData' => $mainData,
                    'group' => $group,
        ]);
    }

//    public function actionIndex()
//    {
//
//        return $this->render('index');
//    }
}
