<?php

namespace backend\modules\schedule\models;

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
use \backend\modules\common\models\DateTimeTool;

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

    public static function getDb() {
        return \Yii::$app->dbMS;
    }

    public static function tableName() {
        return '[dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header]';
    }

    public function getSchedule($groupCode) {

        $sql = 'SELECT
                        POD production_line,
                        No_ blanket,
                        IK ik,
                        [Scheduled Ass_ Date Start] ass_date_start,
                        [Scheduled Ass_ Date End] ass_date_end,
                        [Your Reference] report_group,
                        CASE
                WHEN [Conts Adjmt] > 0 THEN
                        [Conts Adjmt]
                ELSE
                        (
                                SELECT
                                        SUM (sl.Quantity * iuom.CUFT) / 2350
                                FROM
                                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl
                                LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                                        sl.No_ = iuom.[Item No_]
                                        AND iuom.Code = \'CTNS\'
                                )
                                WHERE
                                        sl.[Document No_] = sh.No_
                                AND sh.[Document Type] = 4
                        ) + sh.[Conts Adjmt]
                END total_container
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
                WHERE
                    sh.[Document Type]=4
                    AND [Scheduled Ass_ Date Start] BETWEEN :date_from1
                    AND :date_to1
                    AND [Scheduled Ass_ Date End] BETWEEN :date_from2
                    AND :date_to2
                ORDER BY
                        [Scheduled Ass_ Date Start],
                        [Cargo Ready Date],
                        [CD Date]';

        $reportGroupModel = new ReportGroup();
        $period = $reportGroupModel->getPeriod($groupCode);

        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlCommand->bindValue(':date_from1', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_from2', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_to1', date('Y-m-d', strtotime($period[1])));
        $sqlCommand->bindValue(':date_to2', date('Y-m-d', strtotime($period[1])));
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $row = $sqlResult[$i];
                $sqlResult[$i]['assembly_date'] = DateTimeTool::getDateDiplay($row['ass_date_start'], $row['ass_date_end']);
                $sqlResult[$i]['total_container'] = round($row['total_container'], 2);
            }
//            $ratio = [];
//            $blanketAndMonth = [];
//            for ($i = 0; $i < count($sqlResult); $i++) {
//                if (!in_array($sqlResult[$i]['blanket_no'] . $sqlResult[$i]['months'], $blanketAndMonth)) {
//                    $blanketAndMonth[] = $sqlResult[$i]['blanket_no'] . $sqlResult[$i]['months'];
//                    if (!isset($ratioArr[$sqlResult[$i]['blanket_no']]) || !isset($ratioArr[$sqlResult[$i]['blanket_no']][$groupCode])) {
//                        $ratioArr[$sqlResult[$i]['blanket_no']][$groupCode] = $blanketPOModel->getRatioBlanketPo($sqlResult[$i]['blanket_no'], $groupCode);
//                    }
//                    $ratio = $ratioArr[$sqlResult[$i]['blanket_no']][$groupCode];
//
//                    $row = $sqlResult[$i];
//                    $sqlResult[$i]['scheduled_cont'] = $sqlResult[$i]['total_cont'] * $ratio;
//                } else {
//                    $sqlResult[$i]['scheduled_cont'] = 0;
//                }
//            }
//            var_dump($blanketAndMonth);
            return $sqlResult;
        }
        return [];
    }

    public function getScheduleWithDetail($groupCode, $productionLine = False) {

        $sql = 'SELECT
                        POD production_line,
                        No_ blanket,
                        IK ik,
                        [Customer PO 2] product_group,
                        [Sell-to Customer Name 2] customer_name,
                        [Requested Delivery Date] customer_request_date,
                        [Scheduled Ass_ Date Start] ass_date_start,
                        [Scheduled Ass_ Date End] ass_date_end,
                        [Cargo Ready Date] wh_date,
                        [Cutting No_] cutting_no,
                        Remark remark,
                        [Your Reference] report_group,
                        CASE
                WHEN [Conts Adjmt] > 0 THEN
                        [Conts Adjmt]
                ELSE
                        (
                                SELECT
                                        SUM (sl.Quantity * iuom.CUFT) / 2350
                                FROM
                                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl
                                LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                                        sl.No_ = iuom.[Item No_]
                                        AND iuom.Code = \'CTNS\'
                                )
                                WHERE
                                        sl.[Document No_] = sh.No_
                                AND sh.[Document Type] = 4
                        ) + sh.[Conts Adjmt]
                END total_container,
                (
                    SELECT
                            SUM (sl.Quantity * iuom.CUFT) / 2350
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                            sl.No_ = iuom.[Item No_]
                            AND iuom.Code = \'CTNS\'
                    )
                    WHERE
                            sl.[Document No_] = sh.No_ OR sl.[Document No_]=sh.[Related Order#]
                    AND sh.[Document Type] = 4
                ) total
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh
                WHERE
                    sh.[Document Type]=4
                    AND [Scheduled Ass_ Date Start] BETWEEN :date_from1
                    AND :date_to1
                    AND [Scheduled Ass_ Date End] BETWEEN :date_from2
                    AND :date_to2
                    AND POD = :production_line
                ORDER BY
                        [Scheduled Ass_ Date Start],
                        [Cargo Ready Date],
                        [CD Date]';

        $reportGroupModel = new ReportGroup();
        $period = $reportGroupModel->getPeriod($groupCode);

        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlCommand->bindValue(':date_from1', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_from2', date('Y-m-d', strtotime($period[0])));
        $sqlCommand->bindValue(':date_to1', date('Y-m-d', strtotime($period[1])));
        $sqlCommand->bindValue(':date_to2', date('Y-m-d', strtotime($period[1])));
        $sqlCommand->bindValue(':production_line', $productionLine);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $row = $sqlResult[$i];
                $sqlResult[$i]['assembly_date'] = DateTimeTool::getDateDiplay($row['ass_date_start'], $row['ass_date_end']);
                $sqlResult[$i]['wh_date'] = DateTimeTool::getDateDiplay($row['wh_date']);
                $sqlResult[$i]['customer_request_date'] = DateTimeTool::getDateDiplay($row['customer_request_date']);
                $sqlResult[$i]['total_container'] = round($row['total_container'], 2);
                $sqlResult[$i]['total'] = round($row['total'], 2);
                $sql = 'SELECT
                        item.Abbreviation abbrevaiation,
                        sl.Quantity quantity
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl
                JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item] item ON item.No_ = sl.No_
                JOIN (
                        SELECT
                                No_,
                                [Related Order#]
                        FROM
                                [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header]
                        WHERE
                                No_ = :blanket_no
                        AND [Document Type] = 4
                ) sh ON sh.No_ = sl.[Document No_]
                OR (
                        sh.[Related Order#] != \'\'
                        AND sh.[Related Order#] = sl.[Document No_]
                )
                WHERE sl.Quantity > 0;';
                $sqlCommand = Yii::$app->dbMS->createCommand($sql);
                $sqlCommand->bindValue(':blanket_no', $row['blanket']);
                $lineAqlResult = $sqlCommand->queryAll();
                $sqlResult[$i]['lines'] = $lineAqlResult;
            }

            return $sqlResult;
        }
        return [];
    }

}
