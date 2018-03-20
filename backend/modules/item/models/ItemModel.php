<?php

namespace backend\modules\item\models;

use Yii;
use backend\modules\common\models\DateTimeTool;

/**
 * This is the model class for table "branches".
 *
 * @property integer $id
 * @property string $branch_name
 * @property string $branch_address
 */
class ItemModel extends \yii\db\ActiveRecord {

    public function init() {

    }

    public function findItem($itemNo, $customerNo = False) {
        $sqlCustomer = ' JOIN (SELECT
                                DISTINCT ItemNo
                        FROM
                                [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
                        JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cpl.PONo=cph.PONo AND cph.[Order Type] IN (0,1))
                        WHERE cpl.ItemNo <> \'\' AND cph.CustomerNo=:customer_no) cus_order ON (cus_order.ItemNo = item.No_)';
        $sql = 'SELECT
                        TOP 20 No_ item_no
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item] item';
        if($customerNo){
            $sql .= $sqlCustomer;
        }
        $sql .= ' WHERE
                        No_ LIKE :item_no AND item.[Item Category Code] = \'FG\';';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlCommand->bindValue(':item_no', $itemNo . '%');
        if($customerNo){
            $sqlCommand->bindValue(':customer_no', $customerNo);
        }
        $sqlResult = $sqlCommand->queryAll();
//        var_dump($sqlResult);die();
        if ($sqlResult) {
            return array_column($sqlResult, 'item_no');
        }
        return [];
    }
    public function findItemWithDetail($itemNo, $customerNo = False) {
        $sqlCustomer = ' JOIN (SELECT
                                DISTINCT ItemNo
                        FROM
                                [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
                        JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cpl.PONo=cph.PONo AND cph.[Order Type] IN (0,1))
                        WHERE cpl.ItemNo <> \'\' AND cph.CustomerNo=:customer_no) cus_order ON (cus_order.ItemNo = item.No_)';
        $sql = 'SELECT
                        TOP 20 item.No_ item_no, item.Description description
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item] item';
        if($customerNo){
            $sql .= $sqlCustomer;
        }
        $sql .= ' WHERE
                        No_ LIKE :item_no AND item.[Item Category Code] = \'FG\';';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlCommand->bindValue(':item_no', $itemNo . '%');
        if($customerNo){
            $sqlCommand->bindValue(':customer_no', $customerNo);
        }
        $sqlResult = $sqlCommand->queryAll();
//        var_dump($sqlResult);die();
        if ($sqlResult) {
            return $sqlResult;
        }
        return [];
    }
    public function queryItem($itemNo, $customerNo = False) {
        $sqlCustomer = ' JOIN (SELECT
                                DISTINCT ItemNo
                        FROM
                                [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
                        JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cpl.PONo=cph.PONo AND cph.[Order Type] IN (0,1))
                        WHERE cpl.ItemNo <> \'\' AND cph.CustomerNo=:customer_no) cus_order ON (cus_order.ItemNo = item.No_)';
        $sql = 'SELECT
                        No_ item_no,
                        Description description,
                        nxpimg05 image,
                        item.Abbreviation abbreviation,
                        CAST(iuom.CUFT AS DECIMAL(16,2)) cuft,
                        item.[Product Group Code] product_group_code

                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item] item
                        OUTER APPLY (
                                            SELECT
                                                    TOP 1 *
                                            FROM
                                                    nxpimg
                                            WHERE
                                                    nxpimg01 = item.No_
                                    ) image
                        LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                            item.No_ = iuom.[Item No_]
                            AND iuom.Code = \'CTNS\'
                        )';
        if($customerNo){
            $sql .= $sqlCustomer;
        }
        $sql .= ' WHERE
                        No_ LIKE :item_no AND item.[Item Category Code] = \'FG\';';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlCommand->bindValue(':item_no', $itemNo);
        if($customerNo){
            $sqlCommand->bindValue(':customer_no', $customerNo);
        }
        $sqlResult = $sqlCommand->queryAll();
//        var_dump($sqlResult);die();
        if ($sqlResult) {
            return $sqlResult;
        }
        return [];
    }

    public function findMaterialItem($itemNo) {
        $sqlCustomer = ' JOIN (SELECT
                                DISTINCT ItemNo
                        FROM
                                [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
                        JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cpl.PONo=cph.PONo AND cph.[Order Type] IN (0,1))
                        WHERE cpl.ItemNo <> \'\' AND cph.CustomerNo=:customer_no) cus_order ON (cus_order.ItemNo = item.No_)';
        $sql = 'SELECT
                        TOP 20 item.No_ item_no, item.Description description
                FROM
                        [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item] item';
        $sql .= ' WHERE
                        No_ LIKE :item_no AND item.[Item Category Code] NOT IN (\'FG\',\'FG-REPLACE\');';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlCommand->bindValue(':item_no', $itemNo . '%');

        $sqlResult = $sqlCommand->queryAll();
//        var_dump($sqlResult);die();
        if ($sqlResult) {
            return $sqlResult;
        }
        return [];
    }
    public function getItemDetail($itemNo){
        $sql = 'SELECT
                    item.No_ item_no,
                    item.Description description,
                    item.[Item Category Code] category_code,
                    item.Specification specification,
                    item.[Product Group Code] product_group_code,
                    item.[Production BOM No_] bom_no,
                    item.[Base Unit of Measure] uom,
                    item.Abbreviation abbreviation,
                    ISNULL(iuom.CUFT,0) cuft,
                    item.[Last Date Modified] last_date_modified,
                    item.[Last User Modified] last_user_modified
                FROM
                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item] item
                LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
                            item.No_ = iuom.[Item No_]
                            AND iuom.Code = \'CTNS\'
                    )
                WHERE item.No_= :item_no;';
        $sqlCommand = Yii::$app->dbMS->createCommand($sql);
        $sqlCommand->bindValue(':item_no', $itemNo);
        $sqlResult = $sqlCommand->queryOne();
        if ($sqlResult) {
            $sqlResult['cuft'] = round($sqlResult['cuft'],3);
            $sqlResult['last_date_modified'] = DateTimeTool::getDateDiplay($sqlResult['last_date_modified']);
            return $sqlResult;
        }
        return [];
    }
}
