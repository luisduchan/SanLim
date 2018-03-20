<?php

namespace app\modules\common\controllers;

use Yii;
use yii\web\Controller;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use backend\modules\common\models\ItemGroup;
use backend\modules\common\models\Customer;
use backend\modules\common\models\Destination;
use backend\modules\common\models\Schedule;
use DateTime;

/**
 * Default controller for the `common` module
 */
class DefaultController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionImport() {
        $inputFiles = 'import.xls';
        try {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFiles);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

            $objPHPExcel = $objReader->load($inputFiles);
        } catch (Exception $ex) {
            die('Error');
        }

        $sheet = $objPHPExcel->getSheet(0);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        for ($row = 2; $row <= $highestRow; ++$row) {

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            var_dump($rowData);
        }
        die('vvv');
        return $this->render('import');
    }

    public function actionIpcustomer() {
        $inputFiles = 'data/customer.xlsx';
        try {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFiles);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

            $objPHPExcel = $objReader->load($inputFiles);
        } catch (Exception $ex) {
            die('Error');
        }

        $sheet = $objPHPExcel->getSheet(0);
        $totalRow = $sheet->getHighestRow();
        $rowData = $sheet->rangeToArray('A1:A' . $totalRow, NULL, TRUE, FALSE);
        for ($i = 0; $i < count($rowData); $i++) {
            $customer = $rowData[$i][0];
            $customerID = $this->checkExit($customer, 'name', 'customer');
            if (!$customerID) {
                $sqlInser = 'INSERT INTO customer (name) VALUES (:customer)';
                $sqlCommand = Yii::$app->db->createCommand($sqlInser);
                $sqlCommand->bindValue(':customer', $customer);

                $sqlCommand->execute();
            } else {
                printf($customerID . '; ');
            }
        }
        die('Import Done!');
        return $this->render('import');
    }

    public function actionIpitem() {
        $inputFiles = 'data/item_group.xlsx';
//        var_dump(file_exists($inputFiles));die();
        try {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFiles);
//            var_dump($inputFileType);die();
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

            $objPHPExcel = $objReader->load($inputFiles);
        } catch (Exception $ex) {
            die('Error');
        }

        $sheet = $objPHPExcel->getSheet(0);
        $totalRow = $sheet->getHighestRow();
        $rowData = $sheet->rangeToArray('A1:A' . $totalRow, NULL, TRUE, FALSE);
        for ($i = 0; $i < count($rowData); $i++) {
            $itemGroup = $rowData[$i][0];
            $itemGroupID = $this->checkExit($itemGroup, 'name', 'item_groups');
            if (!$itemGroupID && $itemGroup) {
                $sqlInser = 'INSERT INTO item_groups (name) VALUES (:item)';
                $sqlCommand = Yii::$app->db->createCommand($sqlInser);
                $sqlCommand->bindValue(':item', $itemGroup);

                $sqlCommand->execute();
            } else {
                printf($itemGroupID . '; ');
            }
        }
//        var_dump($rowData);   
        die('Import Done!');
        return $this->render('import');
    }

    public function actionIpdestination() {
        $inputFiles = 'data/destination.xlsx';
//        var_dump(file_exists($inputFiles));die();
        try {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFiles);
//            var_dump($inputFileType);die();
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

            $objPHPExcel = $objReader->load($inputFiles);
        } catch (Exception $ex) {
            die('Error');
        }

        $sheet = $objPHPExcel->getSheet(0);
        $totalRow = $sheet->getHighestRow();
        $rowData = $sheet->rangeToArray('A1:A' . $totalRow, NULL, TRUE, FALSE);
        for ($i = 0; $i < count($rowData); $i++) {
            $destination = $rowData[$i][0];
            $destinationID = $this->checkExit($destination, 'name', 'destination');
            if (!$destinationID && $destination) {
                $sqlInser = 'INSERT INTO destination (name) VALUES (:name)';
                $sqlCommand = Yii::$app->db->createCommand($sqlInser);
                $sqlCommand->bindValue(':name', $destination);

                $sqlCommand->execute();
            } else {
                printf($destinationID . '; ');
            }
        }
        die('Import Done!');
        return $this->render('import');
    }

    public function actionIpschedule() {
        $inputFiles = [
            'data/schedule/2017.xlsx',
        ];
        $sheets = [
            '1702',
            '1703',
            '1704',
            '1705'
        ];
        $scheduleModel = new Schedule();
        foreach ($sheets as $sheetNo) {
            var_dump($sheetNo);
            $scheduleModel->import($inputFiles[0], $sheetNo);
        }
//        foreach($inputFiles as $inputFile){
//            $scheduleModel->import($inputFile);
//        }
        die('Import Done!');
        return $this->render('import');
    }

    public function actionIppo() {
        $inputFiles354 = [
            'data/po/354/1701.xlsx',
            'data/po/354/1702.xlsx',
            'data/po/354/1703.xlsx',
            'data/po/354/1704.xlsx',
            'data/po/354/1705.xlsx',
        ];
        foreach ($inputFiles354 as $inputFiles) {
            $this->importPO($inputFiles, 354, 33);
        }

        $inputFiles405 = [
            'data/po/405/1702.xlsx',
            'data/po/405/1703.xlsx',
            'data/po/405/1704.xlsx',
            'data/po/405/1705.xlsx',
        ];
        foreach ($inputFiles405 as $inputFiles) {
            $this->importPO($inputFiles, 405, 42);
        }

        $inputFiles554 = [
            'data/po/554/1701.xlsx',
            'data/po/554/1702.xlsx',
            'data/po/554/1703.xlsx',
            'data/po/554/1704.xlsx',
            'data/po/554/1705.xlsx',
        ];
        foreach ($inputFiles554 as $inputFiles) {
            $this->importPO($inputFiles, 554, 37);
        }
        $inputFiles400 = [
            'data/po/dei/400/1703.xlsx',
//            'data/po/554/1703.xlsx',
//            'data/po/554/1704.xlsx',
//            'data/po/554/1705.xlsx',
        ];
        foreach ($inputFiles400 as $inputFiles) {
            $this->importPO($inputFiles, 400, 26);
        }
        die('aaa');
    }

    public function importPO($filePath, $itemGroup, $lineSummary) {
        try {
            $inputFileType = \PHPExcel_IOFactory::identify($filePath);
//            var_dump($inputFileType);die();
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

            $objPHPExcel = $objReader->load($filePath);
        } catch (Exception $ex) {
            die('Error');
        }
        $lineSummary = $lineSummary - 1;
        $sheet = $objPHPExcel->getSheet(0);
        $totalRow = $sheet->getHighestRow();
        $higestColum = $sheet->getHighestColumn();
        $itemGroupModel = new ItemGroup();
        $customerModel = new Customer();
        $destinationModel = new Destination();
        $colIndexFrom = \PHPExcel_Cell::columnIndexFromString('A');
        $colIndexTo = \PHPExcel_Cell::columnIndexFromString($higestColum);
        var_dump($higestColum);
        for ($column = $colIndexFrom - 1; $column < $colIndexTo; $column++) {
            $columnLetter = \PHPExcel_Cell::stringFromColumnIndex($column);
            var_dump($columnLetter);
            $colData = $sheet->rangeToArray($columnLetter . 1 . ':' . $columnLetter . $totalRow, NULL, TRUE, FALSE);
            $itemGroupId = $itemGroupModel->getItem($itemGroup);
            $customerId = $customerModel->getCustomer($colData[4][0]);
            if (!$customerId) {
                var_dump($filePath);
                var_dump('po' . $colData[3][0]);
                var_dump('not customer ' . $colData[4][0]);
                die();
            }
            $destinationId = $destinationModel->getDestination($colData[$lineSummary + 3][0]);
            if (!$destinationId) {
                var_dump($filePath);
                var_dump('po' . $colData[3][0]);
                var_dump('not destination ' . $colData[$lineSummary + 3][0]);
                die();
            }

            $data = [
                'status' => ($colData[0][0] && strtolower(trim($colData[0][0])) == 'cancel') ? 2 : 1,
                'po_name' => $colData[3][0],
                'customer_id' => $customerId,
                'recieved_date' => ($colData[5][0]) ? date('Y/m/d', PHPExcel_Shared_Date::ExcelToPHP($colData[5][0])) : NULL,
                'required_ship_date' => ($colData[6][0]) ? date('Y/m/d', PHPExcel_Shared_Date::ExcelToPHP($colData[6][0])) : NULL,
                'factory_confirm_date' => ($colData[7][0]) ? date('Y/m/d', PHPExcel_Shared_Date::ExcelToPHP($colData[7][0])) : NULL,
                'total_qty' => $colData[$lineSummary][0],
                'container_size' => $colData[$lineSummary + 1][0],
                'total_cuft' => $colData[$lineSummary + 2][0],
                'desination_id' => $destinationId,
                'sik' => $colData[$lineSummary + 4][0],
                'item_group_id' => $itemGroupId,
                'ik_item' => substr($colData[$lineSummary + 4][0], 0, 4) . '_' . $itemGroup,
            ];
            $poID = $this->checkExit($colData[3][0], 'po_name', 'purchase_order');
            if (!$poID) {
                Yii::$app->db->createCommand()
                        ->insert('purchase_order', $data)
                        ->execute();
            } else {
                Yii::$app->db->createCommand()
                        ->update('purchase_order', $data, 'id = ' . $poID)
                        ->execute();
            }
//            die();
        }
    }

    public function checkExit($key, $columKey, $tableName) {
        $sql = 'SELECT id, ' . $columKey
                . ' FROM ' . $tableName
                . ' WHERE ' . $columKey . ' = :key';
        $sqlCommand = Yii::$app->db->createCommand($sql);
        $sqlCommand->bindValue(':key', $key);
        $sqlResult = $sqlCommand->queryAll();
//        var_dump($sqlResult);die();
        if ($sqlResult) {
            return $sqlResult[0]['id'];
        }
        return FALSE;
    }

}
