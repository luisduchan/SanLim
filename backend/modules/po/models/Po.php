<?php

namespace backend\modules\po\models;

use Yii;
use DateTime;
use DateInterval;
use DatePeriod;
use yii\helpers\ArrayHelper;
use common\modules\sanlim\models\NumberContainer;
use common\modules\sanlim\models\Component;
use common\modules\sanlim\models\Date;
use backend\modules\common\models\DateTimeTool;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class Po extends \yii\db\ActiveRecord {

    public function init() {

    }

    public function getDetail($poNo) {
        $line = [];
        $header = [];
        $sqlHeader = 'SELECT
                            PONo po_no,
                            CustomerNo customer_no,
                            CustomerName cust_name,
                            PODate po_date,
                            OriginalReqShipDateFrom request_ship_date_from,
                            OriginalReqShipDateTo request_ship_date_to,
                            CommitReqShipDateFrom confirm_ship_date_from,
                            CommitReqShipDateTo confirm_ship_date_to,
                            CurrentShipDateFrom current_ship_date_to,
                            CurrentShipDateTo current_ship_date_to,
                            ReqWHDate expect_wh_date,
                            PPCDate expect_assembly_date,
                            ETD expect_etd,
                            UserCreated created_user,
                            CurrentShipDateFrom current_ship_date_from,
                            CurrentShipDateTo current_ship_date_to,
                            DestinationCity des_city,
                            Noted noted
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader]
                    WHERE PONo=:po_no ;';
        $sqlCommand = Yii::$app->dbMS->createCommand($sqlHeader);
        $sqlCommand->bindValue(':po_no', $poNo);
        $header = $sqlCommand->queryOne();
        if (!$header) {
            return [[], []];
        }

        $header['request_ship_date'] = DateTimeTool::getDateDiplay($header['request_ship_date_from'], $header['request_ship_date_to']);
        $header['confirm_ship_date'] = DateTimeTool::getDateDiplay($header['confirm_ship_date_from'], $header['confirm_ship_date_to']);
        $header['current_ship_date'] = DateTimeTool::getDateDiplay($header['current_ship_date_from'], $header['current_ship_date_to']);
        $header['po_date'] = DateTimeTool::getDateDiplay($header['po_date']);
        $header['expect_assembly_date'] = DateTimeTool::getDateDiplay($header['expect_assembly_date']);
        $header['expect_wh_date'] = DateTimeTool::getDateDiplay($header['expect_wh_date']);
        $header['expect_etd'] = DateTimeTool::getDateDiplay($header['expect_etd']);
        $sqlLineQuery = 'SELECT
                                cpl.PONo po_no,
                                cpl.ItemNo item_no,
                                cpl.Description description,
                                cpl.Quantity quantity,
                                cpl.UnitPrice unit_price,
                                cpl.[Blanket PO#] blanket_po,
                                cpl.Remark remark,
                                image.nxpimg05 image,
                                uom.CUFT cuft,
                                ROUND(
                                        CAST (
                                                cpl.Quantity * uom.CUFT AS DECIMAL (11, 2)
                                        ),
                                        2
                                ) total_cuft,
                                ROUND(
                                        CAST (
                                                cpl.Quantity * uom.CUFT /2350  AS DECIMAL (11, 2)
                                        ),
                                        2
                                ) total_conatiner
                        FROM
                                [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl OUTER APPLY (
                                            SELECT
                                                    TOP 1 *
                                            FROM
                                                    nxpimg
                                            WHERE
                                                    nxpimg01 = cpl.ItemNo
                                    ) image
                        LEFT OUTER JOIN dbo.[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] uom ON (
                            cpl.ItemNo = uom.[Item No_]
                            AND uom.Code = \'CTNS\'
                        )
                        WHERE
                                PONo =:po_no
                                AND cpl.Quantity > 0';
        $sqlCommand = Yii::$app->dbMS->createCommand($sqlLineQuery);
        $sqlCommand->bindValue(':po_no', $poNo);
        $line = $sqlCommand->queryAll();
        if ($line) {
            for ($i = 0; $i < count($line); $i++) {
                $row = $line[$i];
                $line[$i]['quantity'] = round($row['quantity']);
                $line[$i]['cuft'] = round($row['cuft'], 3);
                $line[$i]['total_cuft'] = round($row['total_cuft'], 3);
                $line[$i]['total_conatiner'] = round($row['total_conatiner'], 3);
            }
        }
        return [$header, $line];
    }

    public function getPOSummary($requestParam) {
        $itemNo = $requestParam['item_no'];
        $dateFrom = $requestParam['date_from'];
        $dateTo = $requestParam['date_to'];
        $purchaser = $requestParam['purchaser'];
        $vendor = $requestParam['vendor'];
        $dateType = $requestParam['date_type'];
        $poStatus = $requestParam['po_status'];
        $dateModel = new Date();
        $dateField = $dateModel->getDateTechField($dateType);
        $sqlQuery = 'SELECT'
                . '     ph.[Buy-from Vendor No_] AS vendor_no,'
                . '     ph.[Buy-from Vendor Name] AS vendor_name,'
                . '     ph.[No_] AS po_no,'
                . '     SUM(pl.Quantity) AS total_qty,'
                . '     SUM(pl.[Quantity Received]) AS total_receipt_qty,'
                . '     CASE WHEN SUM(delivered.qty) - SUM(pl.[Quantity Received]) > 0 THEN SUM(delivered.qty) - SUM(pl.[Quantity Received]) ELSE 0 END AS total_waiting_qty,'
                . '     SUM(pl.[Outstanding Quantity]) -CASE WHEN SUM(delivered.qty) - SUM(pl.[Quantity Received]) > 0 THEN SUM(delivered.qty) - SUM(pl.[Quantity Received]) ELSE 0 END  AS total_outst_qty,'
                . '     ' . $dateField . ' AS date,'
                . '     CASE WHEN ph.[Requested Receip To Date] = \'1753-01-01 00:00:00\' THEN NULL ELSE ph.[Requested Receip To Date] END AS request_receipt_date_to'
                . ' FROM'
                . '     [SAN LIM FURNITURE VIETNAM LTD$Purchase Line] AS pl WITH (NoLock)'
                . '     LEFT OUTER JOIN [SAN LIM FURNITURE VIETNAM LTD$Purchase Header] AS ph WITH (NoLock) ON pl.[Document No_] = ph.[No_]'
                . '     LEFT OUTER JOIN xloe as delivered WITH (NoLock) '
                . '         ON delivered.orderno = ph.[No_]'
                . '         AND delivered.orderline = pl.[Line No_] '
                . '         AND delivered.itemno = pl.[No_]'
                . ' WHERE'
                . '     pl.No_ LIKE COALESCE(:item_no, pl.No_)'
                . '     AND (ph.[Created By User ID] = COALESCE(:purchaser, ph.[Created By User ID])'
                . '         OR ph.[Last User Modified] = COALESCE(:purchaser, ph.[Last User Modified]))'
                . '     AND ph.[Buy-from Vendor No_] = COALESCE(:vendor, ph.[Buy-from Vendor No_])'
//                . '     AND ph.[Status] = COALESCE(:status, ph.[Status])'
                . '     AND ' . $dateField . ' >= COALESCE(:date_from,' . $dateField . ')'
                . '     AND ' . $dateField . ' <= COALESCE(:date_to,' . $dateField . ')'
                . ' GROUP BY'
                . '     ph.[Buy-from Vendor No_],'
                . '     ph.[Buy-from Vendor Name],'
                . '     ph.[No_],'
                . '     ' . $dateField . ','
                . '     ph.[Requested Receip To Date]';
        if (isset($poStatus)) {
            switch ($poStatus) {
                case 1:
                    $sqlQuery = $sqlQuery . ' HAVING SUM(pl.[Outstanding Quantity]) > 0';
                    break;
                case 2:
                    $sqlQuery = $sqlQuery . ' HAVING SUM(pl.[Outstanding Quantity]) <= 0';
            }
        }
        $sqlQuery = $sqlQuery . ' ORDER BY ph.[No_],' . $dateField;
//                if($dateField != 'Order Date'){
//                    $sqlQuery = $sqlQuery . ', pl.[Order Date] ASC';
//                }

        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);
        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlCommand->bindValue(':purchaser', $purchaser);
        $sqlCommand->bindValue(':vendor', $vendor);
        $sqlCommand->bindValue(':status', $poStatus);
        $sqlCommand->bindValue(':date_from', $dateFrom);
        $sqlCommand->bindValue(':date_to', $dateTo);
        $sqlResult = $sqlCommand->queryAll();

        $i = 0;
        foreach ($sqlResult as $row) {
            if ($row['total_outst_qty'] < 0) {
                $sqlResult[$i]['total_outst_qty'] = 0;
            }
            $i++;
        }
        return $sqlResult;
    }

    public static function pageTotal($provider, $colName) {
        $total = 0;
//        var_dump($provider);die();
        foreach ($provider->allModels as $key => $val) {
            $total += $val[$colName];
        }
        $total = round($total, 3);
//        var_dump($total);die();
        return $total;
    }

    public function getPODetailBlanket($blanketOrder) {
        $sqlQuery = 'SELECT
                            cpl.PONo po_no,
                            cpl.ItemNo item_no,
                            cpl.Description item_description,
                            cpl.Quantity quantity,
                            (cpl.Quantity * iuom.CUFT) / 2350 total_container,
                            cph.CustPODate order_date,
                            cph.OriginalReqShipDateFrom request_ship_date_from,
                            cph.OriginalReqShipDateTo request_ship_date_to,
                            cph.CommitReqShipDateFrom confirm_ship_date_from,
                            cph.CommitReqShipDateTo confirm_ship_date_to
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
                    JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cph.PONo = cpl.PONo)
                    LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                            cpl.ItemNo = iuom.[Item No_]
                            AND iuom.Code = \'CTNS\'
                    )
                    WHERE
                            cpl.[Blanket PO#] = :blanket_po
                    ORDER BY
                            cph.CommitReqShipDateFrom,
                            cpl.PONo
                            --cpl.ItemNo,
                            --cph.CommitReqShipDateTo;';
        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);
        $sqlCommand->bindValue(':blanket_po', $blanketOrder);
        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $row = $sqlResult[$i];
                $sqlResult[$i]['order_date'] = DateTimeTool::getDateDiplay($row['order_date']);
                $sqlResult[$i]['confirm_ship_date'] = DateTimeTool::getDateDiplay($row['confirm_ship_date_from'], $row['confirm_ship_date_to']);
                $sqlResult[$i]['request_ship_date'] = DateTimeTool::getDateDiplay($row['request_ship_date_from'], $row['request_ship_date_to']);
                $sqlResult[$i]['total_container'] = round($row['total_container'], 2);
            }
        }
        return $sqlResult;
    }

    public function getSummaryItemOrder($parameter) {
        $item_nos = $parameter['item_nos'];
        $item_no = $parameter['item_no'];
        $description = $parameter['description'];
        $group_by_item_group = $parameter['group_by_item_group'];
        $customers = $parameter['customers'];
        $unitQuantity = $parameter['unit_quantity'];
        $dateFrom = $parameter['date_from'];
        $dateTo = $parameter['date_to'];
        $dateType = $parameter['date_type'];

        $itemNoCondition = $item_no ? ' AND cpl.ItemNo LIKE :item_no' : '';
        $descriptionCondition = $description ? ' AND cpl.Description LIKE :description' : '';
        $group_by = $group_by_item_group ? 'item.[Product Group Code]' : 'item.No_';
        $customerCondition = '';
        $itemsCondition = '';

        $dateFields = [
            'expected_aseembling_date' => 'cph.PPCDate',
            'confirm_date' => 'cph.CommitReqShipDateFrom'];

        if ($customers) {
            $placeholders = '';
            for ($i = 0; $i < count($customers) - 1; $i++) {
                $placeholders .= ':' . $customers[$i] . ',';
            }
            $placeholders .= ':' . $customers[$i];

            $customerCondition = ' AND cph.CustomerNo IN (' . $placeholders . ')';
        }
        if ($item_nos) {
            $placeholders = '';
            for ($i = 0; $i < count($item_nos) - 1; $i++) {
                $placeholders .= ':item_no' . $i . ',';
            }
            $placeholders .= ':item_no' . $i;

            $itemsCondition = ' AND cpl.ItemNo IN (' . $placeholders . ')';
        }
        $unitField = 'SUM(cpl.Quantity * iuom.CUFT)/2350';
        if($unitQuantity){
            $unitField = 'SUM(cpl.Quantity)';
        }
        $sqlQuery = 'SELECT
                            ' . $group_by . ' group_by,
                            FORMAT(' . $dateFields[$dateType] . ', \'yyyy/MM\') month,
                            ' . $unitField . ' total
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
                            JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cph.PONo=cpl.PONo)
                            JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item] item ON item.No_=cpl.ItemNo
                            LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                                    cpl.ItemNo = iuom.[Item No_]
                                    AND iuom.Code = \'CTNS\'
                            )
                    WHERE
                            cpl.Quantity > 0
                            AND [Order Type] IN (0,1)
                            AND ' . $dateFields[$dateType] . ' BETWEEN :date_from AND :date_to'
                . $itemNoCondition
                . $descriptionCondition
                . $customerCondition
                . $itemsCondition
                . '
                    GROUP BY FORMAT(' . $dateFields[$dateType] . ', \'yyyy/MM\'),
                    ' . $group_by;

        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);
        $sqlCommand->bindValue(':date_from', $dateFrom);
        $sqlCommand->bindValue(':date_to', $dateTo);
        if ($item_no) {
            $sqlCommand->bindValue(':item_no', $item_no);
        }
        if ($description) {
            $sqlCommand->bindValue(':description', $description);
        }
        if ($customers) {
            foreach ($customers as $i => $customer) {
                $sqlCommand->bindValue(':' . $customer, $customer);
            }
        }
        if ($item_nos) {
            foreach ($item_nos as $i => $item_no) {
//                var_dump($item_no);die();
                $sqlCommand->bindValue(':item_no' . $i, $item_no);
            }
        }

        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $row = $sqlResult[$i];
                $sqlResult[$i]['total'] = round($row['total'], 2);
            }
        }
        return $sqlResult;
    }

    public function getItemOrder($item_nos, $item_no, $description) {
        $itemNoCondition = $item_no ? ' AND cpl.ItemNo LIKE :item_no' : '';
        $descriptionCondition = $description ? ' AND cpl.Description LIKE :description' : '';
        $sqlQuery = 'SELECT
                            cpl.PONo po_no,
                            cpl.ItemNo item_no,
                            cpl.Description description,
                            cpl.Quantity quantity,
                            cpl.[Blanket PO#] blanket_po,
                            iuom.CUFT cuft,
                            cph.CommitReqShipDateFrom confirm_date_from,
                            cph.CommitReqShipDateTo confirm_date_to
                    FROM
                            [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
                            JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cph.PONo=cpl.PONo)
                            LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                                    cpl.ItemNo = iuom.[Item No_]
                                    AND iuom.Code = \'CTNS\'
                            )
                    WHERE
                            cpl.Quantity > 0 ' . $itemNoCondition . $descriptionCondition;

        $sqlCommand = Yii::$app->dbMS->createCommand($sqlQuery);
        if ($item_no) {
            $sqlCommand->bindValue(':item_no', $item_no);
        }
        if ($description) {
            $sqlCommand->bindValue(':description', $description);
        }

        $sqlResult = $sqlCommand->queryAll();
        if ($sqlResult) {
            for ($i = 0; $i < count($sqlResult); $i++) {
                $row = $sqlResult[$i];
                $sqlResult[$i]['quantity'] = round($row['quantity'], 0);
                $sqlResult[$i]['cuft'] = round($row['cuft'], 2);
                $sqlResult[$i]['total_cuft'] = round($row['quantity'] * $row['cuft'], 2);
                $sqlResult[$i]['total_conatiner'] = round($row['quantity'] * $row['cuft'] / 2350, 2);
                $sqlResult[$i]['confirm_ETD'] = DateTimeTool::getDateDiplay($row['confirm_date_from'], $row['confirm_date_to']);
            }
        }
        return $sqlResult;
    }

}
