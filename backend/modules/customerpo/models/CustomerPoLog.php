<?php

namespace backend\modules\customerpo\models;

use Yii;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use backend\modules\common\models\ItemGroup;
use backend\modules\common\models\Customer;
use backend\modules\common\models\Destination;
use DateTime;
use backend\modules\common\models\ReportGroup;
use backend\modules\common\models\POCus;
use yii\helpers\ArrayHelper;
use backend\modules\common\models\POCusDetail;
use backend\modules\common\models\ArrayTool;
use backend\modules\common\models\DateTimeTool;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class CustomerPoLog extends \yii\db\ActiveRecord {

    public function init() {

    }

    public static function getDb() {
        return \Yii::$app->dbMS;
    }

    public function getLog() {
        $querySql = 'SELECT
                            id,
                            purchase_order_no,
                            total_container,
                            (
                                    SELECT
                                            total_container
                                    FROM
                                            `purchase_order_log` pol1
                                    WHERE
                                            pol1.purchase_order_no = pol.purchase_order_no
                                    AND pol1.id < pol.id
                                    ORDER BY
                                            pol1.create_date DESC
                                    LIMIT 1
                            ) previous_total_container,
                            (
                                    SELECT
                                            total_container
                                    FROM
                                            `purchase_order_log` pol1
                                    WHERE
                                            pol1.purchase_order_no = pol.purchase_order_no
                                    AND pol1.id < pol.id
                                    ORDER BY
                                            pol1.create_date DESC
                                    LIMIT 2,
                                    1
                            ) previous_total_container1,
                            create_date,
                            nav_update_date,
                            customer_name,
                            confirm_date_from,
                            confirm_date_to
                    FROM
                            `purchase_order_log` pol
                    ORDER BY
                            nav_update_date DESC
                    LIMIT 1000;';
        $sqlCommand = Yii::$app->db->createCommand($querySql);


        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $row = $sqlResult[$i];
                if ($row['create_date']) {
                    $sqlResult[$i]['create_date'] = DateTimeTool::convertTimeZone($row['create_date']);
                }
                if ($row['nav_update_date']) {
                    $sqlResult[$i]['nav_update_date'] = DateTimeTool::convertTimeZone($row['nav_update_date']);
                }
                $sqlResult[$i]['previous_total_container'] = round($row['previous_total_container'], 2);
                $sqlResult[$i]['previous_total_container1'] = round($row['previous_total_container1'], 2);
                if ($row['confirm_date_to'] == '1753-01-01') {
                    $sqlResult[$i]['confirm_date_to'] = '';
                }
            }
            return $sqlResult;
        }
        return FALSE;
    }

}
