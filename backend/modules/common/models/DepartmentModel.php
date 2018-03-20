<?php

namespace backend\modules\common\models;

use yii\helpers\Url;
use Yii;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class DepartmentModel extends \yii\db\ActiveRecord {

    public function init() {

    }

    public function getDepartment() {
//        $sql = "EXEC sys.sp_setapprole @rolename = 'app_datareader', @password = 'SL@app2017'";
//        @mssql_query($sql);
        $sql = 'SELECT
                        wc.[Work Center Group Code] location,
                        dt.Code division_code,
                        dt.Description division_name,
                        wc.No_ work_center,
                        wc.Name work_center_name,
                        mc.No_ machine_code,
                        mc.Name machine_name
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Division_Transaction] dt
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Work Center] wc ON wc.Division = dt.Code
                LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Machine Center] mc ON mc.[Work Center No_] = wc.No_
                WHERE
                        wc.[Work Center Group Code] <> \'\'
                --AND mc.InActive = 0
                AND dt.Type = 0
                ORDER BY
                        wc.[Work Center Group Code],
                        dt.Description,
                        work_center_name,
                        mc.Name;';

        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
//        $sqlCommand->bindValue(':item_no', $itemNo . '%');
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            return $sqlResult;
        }
        return [];
    }

    public function getDepartmentTreeView() {
        $data = $this->getDepartment();
        $templResult = [];
        $result = [];
        for ($i = 0; $i < count($data); $i++) {
            $row = $data[$i];
            if (!isset($templResult[$row['location']])) {
                $templResult[$row['location']] = [
                    'text' => $row['location'],
                    'href' => Url::to(['', 'page' => $row['location']]),
                    'nodes' => [],
                ];
            }

            if (!isset($templResult[$row['location']]['nodes'][$row['division_name']])) {
                $templResult[$row['location']]['nodes'][$row['division_name']] = [
                    'text' => '[' . $row['division_code'] . '] '. $row['division_name'] ,
                    'href' => Url::to(['', 'page' => $row['division_name']]),
                    'nodes' => [],
                ];
            }
            if (!isset($templResult[$row['location']]['nodes'][$row['division_name']]['nodes'][$row['work_center_name']])) {
                if ($row['work_center_name']) {
                    $templResult[$row['location']]['nodes'][$row['division_name']]['nodes'][$row['work_center_name']] = [
                        'text' => '[' . $row['work_center'] . '] ' . $row['work_center_name'],
                        'href' => Url::to(['', 'page' => $row['work_center_name']]),
                        'nodes' => [],
                    ];
                }
            }
            if (!isset($templResult[$row['location']]['nodes'][$row['division_name']]['nodes'][$row['work_center_name']]['nodes'][$row['machine_code']])) {
                if ($row['machine_code']) {
                    $templResult[$row['location']]['nodes'][$row['division_name']]['nodes'][$row['work_center_name']]['nodes'][$row['machine_code']] = [
                        'text' =>  '[' . $row['machine_code'] . '] ' . $row['machine_name'],
                        'href' => Url::to(['', 'page' => $row['machine_code']]),
                        'nodes' => [],
                    ];
                }
            }
        }
//        var_dump($templResult);die();
        $result = array_values($templResult);
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]['nodes'] = array_values($result[$i]['nodes']);
//            var_dump($result);die();
            $j = 0;
            foreach ($result[$i]['nodes'] as $workcenterName => $workCenter) {
                $result[$i]['nodes'][$j]['nodes'] = array_values($result[$i]['nodes'][$j]['nodes']);

                for ($k = 0; $k < count($result[$i]['nodes'][$j]['nodes']); $k++) {
                    $result[$i]['nodes'][$j]['nodes'][$k]['nodes'] = array_values($result[$i]['nodes'][$j]['nodes'][$k]['nodes']);
                }
                $j++;
            }
        }
//        var_dump($result);
//        die();
        return $result;
    }

}
