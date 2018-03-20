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

/**
* Bom Type:
* 1: item
* 2: production bom
*/

/**
* Bom Status:
* 0: new
* 1: verified
*/
class BomModel extends \yii\db\ActiveRecord {

    public $bomStatus = [
        '0' => 'New', 
        '1' => 'Verified', 
    ];

    public function init() {

    }

    
    public function getBom($bomNo){
        $sqlHeader = 'SELECT
                    bh.No_ bom_no,
                    bh.Description description,
                    bh.[Unit of Measure Code] uom,
                    bh.[Last User Modified] last_user_modified,
                    bh.[Last Date Modified] last_date_modified,
                    bh.Status status,
                    bh.Specification specification
                FROM
                    [dbo].[SAN LIM FURNITURE VIETNAM LTD$Production BOM Header] bh
                WHERE bh.No_ = :bom_no;';

        $sqlCommand = Yii::$app->dbMS->createCommand($sqlHeader);
        $sqlCommand->bindValue(':bom_no', $bomNo);
        $headerResult = $sqlCommand->queryOne();
        if ($headerResult) {
            $headerResult['status'] = $this->bomStatus[$headerResult['status']];
            $headerResult['last_date_modified'] = DateTimeTool::getDateDiplay($headerResult['last_date_modified']);
            $sqlLines = 'SELECT
                bl.No_ bom_no,
                bl.Description description,
                bl.[Unit of Measure Code] uomc,
                bl.Quantity quantity,
                bl.Variant variant,
                bl.Type type,
                bl.Specification spec,
                bl.[BL Description] bl_desc,
                bl.[Length (FS)] length_fs,
                bl.[Width (FS)] width_fs,
                bl.[Thick (FS)] thick_fs,
                bl.[Length (PS)] length_ps,
                bl.[Width (PS)] width_ps,
                bl.[Thick (PS)] thick_ps,
                bl.[OP Raito] op_raito,
                bl.[OP Scrap] op_scrap
            FROM
                [dbo].[SAN LIM FURNITURE VIETNAM LTD$Production BOM Line] bl
            WHERE
                [Production BOM No_] = :bom_no;';

            $sqlCommand = Yii::$app->dbMS->createCommand($sqlLines);
            $sqlCommand->bindValue(':bom_no', $bomNo);
            $linesResult = $sqlCommand->queryAll();
            if($linesResult){
                for($i = 0; $i < count($linesResult); $i++){
                    $row = $linesResult[$i];
                    $linesResult[$i]['quantity'] = round($row['quantity'], 7);
                    $linesResult[$i]['length_fs'] = round($row['length_fs'], 2);
                    $linesResult[$i]['width_fs'] = round($row['width_fs'], 2);
                    $linesResult[$i]['thick_fs'] = round($row['thick_fs'], 2);
                    $linesResult[$i]['length_ps'] = round($row['length_ps'], 2);
                    $linesResult[$i]['width_ps'] = round($row['width_ps'], 2);
                    $linesResult[$i]['thick_ps'] = round($row['thick_ps'], 2);
                    $linesResult[$i]['op_raito'] = round($row['op_raito'], 2);
                    $linesResult[$i]['op_scrap'] = round($row['op_scrap'], 2);
                }
            }

            return [$headerResult,$linesResult];
        }
        return [[],[]];
    }
}
