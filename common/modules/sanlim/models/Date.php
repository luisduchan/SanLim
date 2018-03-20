<?php

namespace common\modules\sanlim\models;

use Yii;
use DateTime;
use DateInterval;
use DatePeriod;
/**
 * This is the model class for table "number_container".
 *
 * @property integer $id
 * @property double $number_container
 * @property string $date
 * @property string $month_year
 * @property string $location_code
 */
class Date extends \yii\db\ActiveRecord {

    var $dateShow = [
        'rrd' => 'Requested Receipt Date _ Line',
        'oder' => 'Order Date _ Header',
        'prd' => 'Promised Receipt Date _ Line',
        'eta' => 'ETA Date _ Line',
        'etd' => 'ETD Date _ Line',
        'pt' => 'Posting Date _ Header'
    ];
    var $dateTechField = [
        'rrd' => 'pl.[Requested Receipt Date]',
        'oder' => 'ph.[Order Date]',
        'prd' => 'pl.[Promised Receipt Date]',
        'eta' => 'pl.[DetailETA]',
        'etd' => 'pl.[DetailETD]',
        'pt' => 'ph.[Posting Date]'
    ];

    public function getDateToShow() {
        return $this->dateShow;
    }

    public function getDateTechField($key) {
        if(isset($this->dateTechField[$key])){
            return $this->dateTechField[$key];
        }
        return NULL;
    }
    /*
     * Get number of month  between 2 dates
     */
    public static function getMonthBetweentDates($dateFrom, $dateTo){
        $dateFrom = new DateTime($dateFrom);
        $dateTo = new DateTime($dateTo);
        return $dateFrom->diff($dateTo)->m + ($dateFrom->diff($dateTo)->y*12);
    }
    /*
     * Get months between 2 dates
     */
    public static function getPeriod($dateFrom, $dateTo){
        $result = [];
        $dateFrom = (new DateTime($dateFrom));
        $dateTo = (new DateTime($dateTo));
        
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($dateFrom, $interval, $dateTo);
        
        foreach ($period as $dt) {
            $key = $dt->format('n/Y');
            $first_date = $dt->format('Y-m-d');
            $end_date = date('Y-m-d', strtotime('+1 month', strtotime($first_date)));
            $end_date = date('Y-m-d', strtotime('-1 day', strtotime($end_date)));
            $result[$key] = [$first_date, $end_date];
        }
        return $result;
    }
}
