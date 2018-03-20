<?php

namespace app\modules\inventory_report\models;

use \PHPExcel_Chart_DataSeriesValues;
use \PHPExcel_Chart_DataSeries;
use \PHPExcel_Chart_PlotArea;
use \PHPExcel_Chart_Legend;
use \PHPExcel_Chart_Title;
use \PHPExcel_Chart;
use \PHPExcel_Style_Alignment;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class Common extends \yii\db\ActiveRecord
{
    /**Generate bar char
     * $option inclue title; yLabel
     */
    public function generateBarChar($objPHPExcel, $data, $sheet = 1, $option){
        $objPHPExcel->createSheet($sheet);
        
        $objPHPExcel->setActiveSheetIndex($sheet);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $worksheet = $option['worksheet'];
//        $worksheet = 'Chart';
        $objWorksheet->setTitle($worksheet); // Chart Fails !
        $objWorksheet->fromArray($data);
        $noComlum = count($data[0]);
        $noRow = count($data);

        $dataseriesLabelsBar = [];
        $dataSeriesValuesBar = [];
        
        $dataseriesLabelsLine = [];
        $dataSeriesValuesLine = [];
        
        $letterColumn = 'B';
        

        $theEndDataCol = chr(ord($letterColumn)+($noComlum - 3));

        for($i = 2; $i <= $noRow -2; $i++){
            $dataseriesLabelsBar[] = new PHPExcel_Chart_DataSeriesValues('String', $worksheet . '!$A$' . $i, null, 1);
            $dataSeriesValuesBar[] = new PHPExcel_Chart_DataSeriesValues('Number', $worksheet . '!$B$' . $i. ':$' . $theEndDataCol .'$' . $i, null, 3);
            
            
        }
        $xAxisTickValuesBar = array(
            new PHPExcel_Chart_DataSeriesValues('String', $worksheet . '!$B$1:$' . $theEndDataCol .'$1', null, 3),
        );
        $series = new PHPExcel_Chart_DataSeries(
                        PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                        PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                        range(0, count($dataSeriesValuesBar)-1),           // plotOrder
                        $dataseriesLabelsBar,                              // plotLabel
                        $xAxisTickValuesBar,                               // plotCategory
                        $dataSeriesValuesBar                               // plotValues
                    );
        //  Set additional dataseries parameters
        //   Make it a horizontal bar rather than a vertical column graph
        $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
        
        if($option['chart_total_line'] == 1){
            $dataseriesLabelsLine[] = new PHPExcel_Chart_DataSeriesValues('String', $worksheet . '!$A$' . ($noRow - 1) , null, 1);
            $dataSeriesValuesLine[] = new PHPExcel_Chart_DataSeriesValues('Number', $worksheet . '!$B$' . ($noRow - 1) . ':$' . $theEndDataCol .'$' . ($noRow - 1), null, 3);
            $xAxisTickValuesLine = array(
                new PHPExcel_Chart_DataSeriesValues('String', $worksheet . '!$B$1:$' . $theEndDataCol .'$1', null, 3),
            );

            $seriesLine = new PHPExcel_Chart_DataSeries(
                    PHPExcel_Chart_DataSeries::TYPE_LINECHART, // plotType
                    PHPExcel_Chart_DataSeries::GROUPING_STANDARD, // plotGrouping
                    range(0, count($dataSeriesValuesLine) - 1), // plotOrder
                    $dataseriesLabelsLine, // plotLabel
                    $xAxisTickValuesLine, // plotCategory
                    $dataSeriesValuesLine                           // plotValues
            );
            //  Set the series in the plot area
            $plotarea = new PHPExcel_Chart_PlotArea(null, array($series, $seriesLine));
        } else {
            $plotarea = new PHPExcel_Chart_PlotArea(null, array($series));
        }
        
        //  Set the chart legend
        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
        $title = new PHPExcel_Chart_Title($option['title']);
        $yAxisLabel = new PHPExcel_Chart_Title($option['yLabel']);
        //  Create the chart
        $chart = new PHPExcel_Chart(
                'chart1', // name
                $title, // title
                $legend, // legend
                $plotarea, // plotArea
                true, // plotVisibleOnly
                0, // displayBlanksAs
                null, // xAxisLabel
                $yAxisLabel     // yAxisLabel
        );
        //  Set the position where the chart should appear in the worksheet
        $chartPosiotnX1 = chr(ord($theEndDataCol)+2);
        $chartPosiotnY1 = 2;
        $chart->setTopLeftPosition($chartPosiotnX1 . $chartPosiotnY1);
        $chartPosiotnX2 = chr(ord($theEndDataCol)+15);
        $chartPosiotnY2 = 20;
        $chart->setBottomRightPosition($chartPosiotnX2 . $chartPosiotnY2);
        $objWorksheet->addChart($chart);
        
        
        //add container chart
        $dataseriesLabelsContainer[] = new PHPExcel_Chart_DataSeriesValues('String', $worksheet . '!$A$' . $noRow, null, 1);
        $dataSeriesValuesContainer[] = new PHPExcel_Chart_DataSeriesValues('Number', $worksheet . '!$B$' . $noRow . ':$' . $theEndDataCol . '$' . $noRow, null, 3);
        $xAxisTickValuesContainer = array(
            new PHPExcel_Chart_DataSeriesValues('String', $worksheet . '!$B$1:$' . $theEndDataCol . '$1', null, 3),
        );

        $seriesContainer = new PHPExcel_Chart_DataSeries(
                PHPExcel_Chart_DataSeries::TYPE_LINECHART, // plotType
                PHPExcel_Chart_DataSeries::GROUPING_STANDARD, // plotGrouping
                range(0, count($xAxisTickValuesContainer) - 1), // plotOrder
                $dataseriesLabelsContainer, // plotLabel
                $xAxisTickValuesContainer, // plotCategory
                $dataSeriesValuesContainer                           // plotValues
        );
        //  Set the series in the plot area
        $plotareaContainer = new PHPExcel_Chart_PlotArea(null, array($seriesContainer));
        //  Set the chart legend
        $legendContainer = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
        $titleContainer = new PHPExcel_Chart_Title('Container');
        $yAxisLabelContainer = new PHPExcel_Chart_Title('Number Container');
        //  Create the chart
        $chartContainer = new PHPExcel_Chart(
                'container', // name
                $titleContainer, // title
                $legendContainer, // legend
                $plotareaContainer, // plotArea
                true, // plotVisibleOnly
                0, // displayBlanksAs
                null, // xAxisLabel
                $yAxisLabelContainer     // yAxisLabel
        );
        //  Set the position where the chart should appear in the worksheet
        $chartPosiotnX1Container = chr(ord($theEndDataCol)+2);
        $chartPosiotnY1Container = 21;
        $chartContainer->setTopLeftPosition($chartPosiotnX1Container . $chartPosiotnY1Container);
        $chartPosiotnX2Container = chr(ord($theEndDataCol)+15);
        $chartPosiotnY2Container = 40;
        $chartContainer->setBottomRightPosition($chartPosiotnX2Container . $chartPosiotnY2Container);
        $objWorksheet->addChart($chartContainer);

        //auto size colum
        $letterColumn = 'A';
        for($i=0; $i<$noComlum; $i++){
            $objPHPExcel->getActiveSheet()->getColumnDimension($letterColumn)->setAutoSize(true);
            $letterColumn ++;
        }
        $this->formatHeader($objPHPExcel);
        
        return $objPHPExcel;
    }
    
    public function writeToSheet($objPHPExcel, $arrColumn, $arrData, $sheetName = 'Sheet1', $sheet = 0){
        if(empty($arrData)){
            return $objPHPExcel;
        }
        $objPHPExcel->createSheet($sheet);
        $objPHPExcel->setActiveSheetIndex($sheet);
        $lineNo = 1;
//        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
//        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->setTitle($sheetName);
        $letterHeaderColumn = 'A';
        foreach($arrColumn as $columnTech => $columnName){
            $objPHPExcel->getActiveSheet()->setCellValue($letterHeaderColumn . $lineNo, $columnName);
            $letterHeaderColumn ++;
        }
        $letterColumn = 'A';
        foreach ($arrData as $row) {  
            $lineNo++;
            $letterColumn = 'A';
            foreach($arrColumn as $columnTech => $columnName){

                $objPHPExcel->getActiveSheet()->setCellValue($letterColumn.$lineNo,$row[$columnTech]);
                $letterColumn++;
            }
        }
        $letterColumn = 'A';
        foreach ($arrColumn as $columnTech => $columnName) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($letterColumn)->setAutoSize(true);
            $letterColumn ++;
        }
        $this->formatHeader($objPHPExcel);
        return $objPHPExcel;
    }
    public function formatHeader($objPHPExcel){
        $ojbSheet = $objPHPExcel->getActiveSheet();
        $style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $ojbSheet->getStyle("A1:Z1")->applyFromArray($style);
        
        $ojbSheet->getStyle("A1:Z1")->getFont()->setBold(true);
        return $objPHPExcel;
    }
//    public function addTotalLine($arrData){
//        foreach($arrData as $row){
//            for($)
//        }
//    }
}
