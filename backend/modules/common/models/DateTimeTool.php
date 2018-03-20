<?php

namespace backend\modules\common\models;

use Yii;
use DateTime;
use DateInterval;
use DatePeriod;
use DateTimeZone;

/**
 * This is the model class for table "number_container".
 *
 * @property integer $id
 * @property double $number_container
 * @property string $date
 * @property string $month_year
 * @property string $location_code
 */
class DateTimeTool extends \yii\db\ActiveRecord {

//    public static strToTime($strDate){
//        return date("m/d/Y", strtotime($sqlResult[$i]['selected_date']))
//    }
    public static function convertTimeZone($dateTime, $toTimeZone = False) {
        if (!$toTimeZone) {
            $toTimeZone = 'Asia/Ho_Chi_Minh';
        }
        $dateTime = new DateTime($dateTime, new DateTimeZone('UTC'));
        $dateTime->setTimezone(new DateTimeZone($toTimeZone));
        return $dateTime->format('Y-m-d H:i:s');
    }

    public static function getDateDiplay($dateStart, $dateEnd = False) {
        $result = '';
        $dateLimit = DateTime::createFromFormat('Y-m-d', '2009-02-15')->format('Y-m-d');
        if (gettype($dateStart) == 'string') {
            $dateStart = strtotime($dateStart);
        }
        if (gettype($dateEnd) == 'string') {
            $dateEnd = strtotime($dateEnd);
        }
        if (!$dateEnd) {
            $result = ($dateStart < $dateLimit) ? '' : date("m/d/Y", $dateStart);
        } elseif ($dateStart > $dateLimit) {
            if ($dateEnd > $dateLimit) {
                $result = date("m/d", $dateStart) . ' - ' . date("m/d/Y", $dateEnd);
            } else {
                $result = date("m/d/Y", $dateStart);
            }
        }
        return $result;
    }

    public static function getDiffDays($expectDateStart, $expectDateEnd, $realDate) {
        $dateLimit = DateTime::createFromFormat('Y-m-d', '2009-02-15')->format('Y-m-d');
        $result = '';
        if ($expectDateStart > $dateLimit && $realDate > $dateLimit) {
            if ($expectDateEnd > $dateLimit) {
                $result = ($realDate - $expectDateEnd) / (60 * 60 * 24);
            } else {
                $result = ($realDate - $expectDateStart) / (60 * 60 * 24);
            }
        }
        return $result;
    }

}
