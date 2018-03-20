<?php

namespace backend\components;

use yii\base\BootstrapInterface;

class Settings implements BootstrapInterface {

    public function bootstrap($app) {
        // Here you can refer to Application object through $app variable
        $app->params['current_schedule_group'] = 'APR-2018';
        $app->params['current_order_groups'] = [
            'MAY-2018' => '2018/05',
            'JUN-2018' => '2018/06',
            'JUL-2018' => '2018/07',
            'AUG-2018' => '2018/08',
        ];
        $app->params['customers_invidual_po'] = [
            'C24000' => 'YWT',
            'C60000' => 'ULTIMATE',
            'C23000' => 'FURN-TECH',
        ];
    }

}
