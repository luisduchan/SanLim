<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use \frontend\modules\inventory_report\models\Moutput;
use miloschuman\highcharts\SeriesDataHelper;
use miloschuman\highcharts\Highstock;

$this->title = 'Material output';
$requestParam['item_no'] = 'PB%';
$requestParam['item_cat'] = NULL;
$requestParam['location'] = NULL;
$requestParam['date_from'] = '2016-09-01';
$requestParam['date_to'] = '2016-12-31';
$requestParam['pcs_metric'] = TRUE;
$requestParam['chart_total_line'] = TRUE;
$requestParam['chart_gorup_by_location'] = FALSE;
$requestParam['not_include_component'] = TRUE;

$moutput = new Moutput();
$result = $moutput->getTotalContainer();
$series = [];
for ($i = 1; $i < count($result) - 2; $i++) {
    $series[] = [
        'type' => 'column',
        'name' => $result[$i][0],
        'data' => array_slice($result[$i], 1, count($result[$i]) - 2)
    ];
}


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
//                'scripts' => [
//                    'highcharts-3d',
//                ],
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
                    'title' => ['text' => 'WOOD Output'],
                    'xAxis' => [
                        'categories' => array_slice($result[0], 1, count($result[0]) - 2)
                    ],
                    'yAxis' => [
                        'title' => ['text' => 'Quantity']
                    ],
                    'series' => $series,
//                    'plotOptions' => [
//                        'series' => [
//                            'stacking' => 'normal'
//                        ]
//                    ],
                ]
            ]);
            ?>

        </div>
    </div>
    <?php 
    echo Highcharts::widget([
   'options' => [
      'title' => ['text' => 'Fruit Consumption'],
      'xAxis' => [
         'categories' => [['name'=> 's', 'categories' => ['Apples','Bananas']],['name' => 'aaa','categories'=> ['Oranges']]]
      ],
      'yAxis' => [
         'title' => ['text' => 'Fruit eaten']
      ],
      'series' => [
         ['name' => 'Jane', 'data' => [1, 0, 4]],
         ['name' => 'John', 'data' => [5, 7, 3]]
      ]
   ]
]);
    ?>
    <!-- /.box-body -->
</div>
