<?php

namespace backend\modules\common\models;

use Yii;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use backend\modules\common\models\ItemGroup;
use backend\modules\common\models\Customer;
use backend\modules\common\models\Destination;
use DateTime;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class Schedule extends \yii\db\ActiveRecord {

    public function init() {
        
    }

    public function import($inputFiles, $sheetNo) {
//        var_dump($inputFiles);die();
        try {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFiles);
//            var_dump($inputFileType);die();
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

            $objPHPExcel = $objReader->load($inputFiles);
        } catch (Exception $ex) {
            die('Error');
        }
        $sheet = $objPHPExcel->getSheetByName($sheetNo);
//        var_dump($sheet);
        $totalRow = $sheet->getHighestRow();
        $higestColum = $sheet->getHighestColumn();
        $itemGroupModel = new ItemGroup();
        $customerModel = new Customer();
        $itemGroupModel = new ItemGroup();
        for ($i = 2; $i <= $totalRow; ++$i) {
            $rowData = $sheet->rangeToArray('A' . $i . ':' . $higestColum . $totalRow, NULL, TRUE, FALSE);
            if ($rowData && $rowData[0] && $rowData[0][0]) {
                $ik = $rowData[0][0];
                $itemGroup = $rowData[0][1];
                $container = $rowData[0][2];
                $requestDateFrom = $rowData[0][3];
                $requestDateTo = $rowData[0][4];
                $customer = $rowData[0][5];
                $assemblyDateFrom = $rowData[0][6];
                $assemblyDateTo = $rowData[0][7];
                $warehousingDateFrom = $rowData[0][8];
                $warehousingDateTo = $rowData[0][9];
                $cutting = strtolower($rowData[0][10]);
                $line = $rowData[0][11];
                $factory = $rowData[0][12];
                $period = $rowData[0][13];

                $key = substr($ik, 0, 4) . '_' . $itemGroup;
                $scheduleID = $this->checkExit($key, 'ik_item', $period);
                $itemGroupId = $itemGroupModel->getItem($itemGroup);
                if(!$itemGroupId){
                    printf('ik --' . $ik);
                    die('no item ' . $itemGroup);
                }
                $customerId = $customerModel->getCustomer($customer);
                if(!$customerId){
                    printf('ik --' . $ik);
                    die('no customer ' . $customer);
                }
                $data = [
                    'ik_item' => $key,
                    'ik' => $ik,
                    'item_group_id' => $itemGroupId,
                    'number_container' => $container,
                    'customer_id' => $customerId,
                    'requested_date_from' => ($requestDateFrom) ? date('Y/m/d', PHPExcel_Shared_Date::ExcelToPHP($requestDateFrom)) : NULL,
                    'requested_date_to' => ($requestDateTo) ? date('Y/m/d', PHPExcel_Shared_Date::ExcelToPHP($requestDateTo)) : NULL,
                    'start_date' => ($assemblyDateFrom) ? date('Y/m/d', PHPExcel_Shared_Date::ExcelToPHP($assemblyDateFrom)) : NULL,
                    'end_date' => ($assemblyDateTo) ? date('Y/m/d', PHPExcel_Shared_Date::ExcelToPHP($assemblyDateTo)) : NULL,
                    'warehousing_date_from' => ($warehousingDateFrom) ? date('Y/m/d', PHPExcel_Shared_Date::ExcelToPHP($warehousingDateFrom)) : NULL,
                    'warehousing_date_to' => ($warehousingDateTo) ? date('Y/m/d', PHPExcel_Shared_Date::ExcelToPHP($warehousingDateTo)) : NULL,
                    'cutting' => $cutting,
                    'line' => $line,
                    'factory' => $factory,
                    'period' => $period,
                ];
                if (!$scheduleID) {
                    Yii::$app->db->createCommand()
                            ->insert('schedule', $data)
                            ->execute();
                } else {
                    Yii::$app->db->createCommand()
                            ->update('schedule', $data, 'id = ' . $scheduleID)
                            ->execute();
                }
            }
        }
    }
    public function checkExit($key, $columKey, $period) {
        $tableName = 'schedule';
        $sql = 'SELECT id, ' . $columKey
                . ' FROM ' . $tableName
                . ' WHERE ' . $columKey . ' = :key'
                . ' AND period = :period';
        $sqlCommand = Yii::$app->db->createCommand($sql);
        $sqlCommand->bindValue(':key', $key);
        $sqlCommand->bindValue(':period', $period);
        $sqlResult = $sqlCommand->queryAll();
//        var_dump($sqlResult);die();
        if ($sqlResult) {
            return $sqlResult[0]['id'];
        }
        return FALSE;
    }

}
