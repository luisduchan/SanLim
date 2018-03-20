<?php

namespace backend\modules\common\models;

use Yii;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use backend\modules\common\models\ItemGroup;
use backend\modules\common\models\Customer;
use backend\modules\common\models\Destination;
use backend\modules\common\models\DateTimeTool;
use DateTime;
use backend\modules\common\models\ReportGroup;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class CustomerPOCommon extends \yii\db\ActiveRecord {

    public function init() {

    }

    public static function getDb() {
        return \Yii::$app->dbMS;
    }

    public static function tableName() {
        return '[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader]';
    }

    public function getPODetail($poNO) {
        $querySql = 'SELECT
                            PONo po_no,
                            CustomerNo customer_no,
                            CustomerName customer_name,
                            FORMAT(PODate, \'MM/dd/yyyy\') po_date,
                            FORMAT(OriginalReqShipDateFrom, \'MM/dd/yyyy\') request_ship_date_from,
                            FORMAT(OriginalReqShipDateTo, \'MM/dd/yyyy\') request_ship_date_to,
                            FORMAT(CommitReqShipDateFrom, \'MM/dd/yyyy\') confirm_ship_date_from,
                            FORMAT(CommitReqShipDateTo, \'MM/dd/yyyy\') confirm_ship_date_to,
                            PPCDate expect_assembling_date,
                            ETD expect_etd,
                            DestinationPostCode destination,
                            Ignore ignore
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
                    WHERE
                            cph.PONo = :po_no;';
        $sqlCommand = Yii::$app->dbMS->createCommand($querySql);
        $sqlCommand->bindValue(':po_no', $poNO);
        $sqlResult = $sqlCommand->queryOne();
        if ($sqlResult) {
            $sqlResult['request_ship_date'] = DateTimeTool::getDateDiplay($sqlResult['request_ship_date_from'], $sqlResult['request_ship_date_to']);
            $sqlResult['confirm_ship_date'] = DateTimeTool::getDateDiplay($sqlResult['confirm_ship_date_from'], $sqlResult['confirm_ship_date_to']);
//            $sqlResult
            return $sqlResult;
        }
        return [];
    }

}
