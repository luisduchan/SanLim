<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use \frontend\modules\inventory_report\models\Moutput;
use miloschuman\highcharts\SeriesDataHelper;
use miloschuman\highcharts\Highstock;

$this->title = 'PO WOOD SUMMARY';
$series = [];
$arOutStQty = array_values(array_column($chartData, 'total_outst_qty'));
$series[] = [
    'type' => 'column',
    'name' => 'Out Standing',
    'data' => array_map(
            function($value) {
                return (float) $value;
            }, $arOutStQty),
];
            
$arWaitingQty = array_values(array_column($chartData, 'total_waiting_qty'));
$series[] = [
    'type' => 'column',
    'name' => 'Waiting',
    'data' => array_map(
            function($value) {
                return (float) $value;
            }, $arWaitingQty),
];
            
$arReceiptQty = array_values(array_column($chartData, 'total_receipt_qty'));
$series[] = [
    'type' => 'column',
    'name' => 'Received',
    'data' => array_map(
            function($value) {
                return (float) $value;
            }, $arReceiptQty),
];

//var_dump($series);die();
?>

<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Bar Chart</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="chart">
            <?php
            echo Highcharts::widget([
                'scripts' => [
                    'highcharts-3d',
                ],
                'options' => [
//                    'chart' => [
//                        'type' => 'column',
//                        'options3d' => [
//                            'enabled' => true,
//                            'alpha' => 15,
//                            'beta' => 15,
//                            'viewDistance' => 25,
//                            'depth' => 40
//                        ]
//                    ],
                    'credits' => ['enabled'=>FALSE],
      
                    'title' => ['text' => 'PO Status'],
                    'xAxis' => [
                        'categories' => $arrayPo
                    ],
//                    'tooltip' => [ 'pointFormat' => '{point.y:.2f}'],
                    'yAxis' => [
                        'title' => ['text' => 'Quantity']
                    ],
                    'series' => $series,
                    'plotOptions' => [
                        'series' => [
                            'stacking' => 'normal'
                        ]
                    ],
                ]
            ]);
            ?>

        </div>
    </div>
    
    <!-- /.box-body -->
</div>
