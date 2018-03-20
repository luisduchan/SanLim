<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\base\View;

$this->title = 'Sanlim Furniture App';

$this->registerJs("$(document).ready(function(){
    $('.nav-tabs a').click(function(){
        $(this).tab('show');
        $('#total_order_' + \$(this).attr('name')).highcharts().reflow();
        console.log('#total_order_' + \$(this).attr('name'));
    });
});", \yii\web\View::POS_READY
);
?>
<div class="site-index">

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><b>Total Order</b></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-4">
                        <?php
                        for ($i = 0; $i < count($dataTotalOrders); $i++) {
                            $dataTotalOrder = $dataTotalOrders[$i];
                            ?>
                            <div class="info-box <?= $dataTotalOrder['color']; ?>">
                                <span class="info-box-icon" style="background:none"><b><?= substr($dataTotalOrder['name'], 0, 3); ?></b></span>
                                <div class="info-box-content small-box">
                                    <h3><b><?= $dataTotalOrder['total']; ?></b></h3>
                                    <?php
                                    foreach ($dataTotalOrders[$i]['months'] as $month => $monthData) {
                                        if (array_sum($monthData) >= 0) {
                                            echo '<span class="info-box-text"><span class="glyphicon glyphicon-plus"></span>' . $month . ': ' . array_sum($monthData) . '</span>';
                                        } else {
                                            echo '<span class="info-box-text"><span class="glyphicon glyphicon-minus"></span>' . $month . ': ' . array_sum($monthData) . '</span>';
                                        }
                                    }
                                    ?>
                                    <?php
                                    if($i==0){
                                        echo Html::a("More info <i class=\"fa fa-arrow-circle-right\"></i>", Url::to(['/customerpo/default/custponeedplan',
                                                    'NeedPlanF[group]' => $dataTotalOrder['name'],
                                                    'NeedPlanF[date_type]' => 'expected_aseembling_date',
                                                ]), ['class' => 'small-box-footer']);
                                    }else{
                                        echo Html::a("More info <i class=\"fa fa-arrow-circle-right\"></i>", Url::to(['/customerpo/default/custponeedplan',
                                                    'NeedPlanF[group]' => $dataTotalOrder['name'],
                                                    'NeedPlanF[date_type]' => 'expected_aseembling_date',
                                                    'NeedPlanF[not_delay]' => 1,
                                                    'NeedPlanF[not_scheduled]' => 1,
                                                ]), ['class' => 'small-box-footer']);
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                    <section class="col-lg-8 connectedSortable">
                        <div class="nav-tabs-custom">
                            <!-- Tabs within a box -->
                            <ul class="nav nav-tabs pull-right">
                                <li class="pull-left header"><i class="fa fa-inbox"></i> Chart</li>
                                <?php
                                for ($i = 0; $i < count($dataTotalOrders); $i++) {
                                    $dataTotalOrder = $dataTotalOrders[count($groups) - 1 - $i];
                                    ?>
                                    <li class="<?= $i == (count($groups) - 1) ? "active" : "" ?>"><a onclick="$('#<?= 'total_order_' . $dataTotalOrder['name'] ?>').highcharts().reflow();" name="<?= $dataTotalOrder['name']; ?>" href="#<?= $dataTotalOrder['name']; ?>" data-toggle="tab"><?= substr($dataTotalOrder['name'], 0, 3); ?></a></li>
                                <?php } ?>

                            </ul>
                            <div class="tab-content no-padding">
                                <!-- Morris chart - Sales -->
                                <?php
                                for ($i = 0; $i < count($dataTotalOrders); $i++) {
                                    $dataTotalOrder = $dataTotalOrders[$i];
                                    if ($dataTotalOrder['total_order_detail']) {
                                        foreach ($dataTotalOrder['total_order_detail'] as $customer => $totalOrderContainer) {
                                            $charData[$i][] = ['name' => $customer, 'y' => round($totalOrderContainer, 2)];
                                        }
                                        $series = [
//                                            'type' => 'pie',
                                            'name' => 'Total',
                                            'data' => $charData[$i],
                                        ];
                                    } else {
                                        $series = [
//                                            'type' => 'pie',
                                            'name' => 'Total',
                                            'data' => [],
                                        ];
                                    }
                                    ?>
                                    <div class="chart tab-pane <?= $i == 0 ? "active" : "" ?>" id="<?= $dataTotalOrder['name']; ?>" style="position: relative; height: 100%;">
                                        <?=
                                        Highcharts::widget([
                                            'id' => 'total_order_' . $dataTotalOrder['name'],
//                                            'scripts' => [
//                                                'highcharts-3d',
//                                                'modules/exporting',
//                                                'modules/offline-exporting',
//                                            ],
                                            'options' => [
                                                'chart' => [
                                                    'type' => 'pie',
                                                    'options3d' => [
                                                        'enabled' => true,
                                                        'alpha' => 45,
                                                        'beta' => 0,
                                                    ],
                                                    'margin' => 0,
                                                ],
                                                'exporting' => [
                                                    'enabled' => true,
                                                    'filename' => 'Total Order ' . $dataTotalOrder['name']
                                                ],
                                                'title' => ['text' => 'Total Order ' . $dataTotalOrder['name']],
                                                'plotOptions' => [
                                                    'pie' => [
                                                        'allowPointSelect' => true,
                                                        'cursor' => 'pointer',
                                                        'depth' => 35,
                                                        'dataLabels' => [
                                                            'enabled' => true,
                                                            'format' => '<b>{point.name}</b>: {point.y:.2f} ({point.percentage:.1f}%)',
                                                        ],
//                                                'point' => [
//                                                    'events' => [
//                                                        'click' => new JsExpression('function() {
//                                                    window.open(this.options.ownURL)
//                                                  }'),
//                                                    ]
//                                                ],
//                                                'showInLegend' => true
                                                    ],
                                                ],
                                                'colors' => ['#7cb5ec',
                                                    '#434348',
                                                    '#90ed7d',
                                                    '#f7a35c',
                                                    '#8085e9',
                                                    '#f15c80',
                                                    '#e4d354',
                                                    '#2b908f',
                                                    '#f45b5b',
                                                    '#91e8e1',
                                                    '#0444BF',
                                                    '#F3D4A0',
                                                    '#00743F',
                                                    '#0294A5',
                                                ],
                                                'series' => [$series],
                                                'credits' => [
                                                    'enabled' => false
                                                ],
                                            ]
                                        ]);
                                        ?>

                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <!-- ./col -->
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><b>Schedule</b></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-3">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3><?= $dataTotalSchedule['name']; ?></h3>
                                    <h3><?= array_sum(array_values($dataTotalSchedule['total_schedule'])); ?></h3>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <?=
                                Html::a("More info <i class=\"fa fa-arrow-circle-right\"></i>", Url::to(['/customerpo/schedule/index',
                                            'ScheduleF[reportGroup]' => $dataTotalSchedule['name'],
                                        ]), ['class' => 'small-box-footer'])
                                ?>
                            </div>
                        </div>
                        <section class="col-lg-9">
                            <div class="chart">
                                <?php
                                $toalOrderFirstMonth = [];
                                foreach ($dataTotalOrders[0]['months'] as $month => $monthData) {
                                    $toalOrderFirstMonth[$month] = array_sum($monthData);
                                }
                                $seriesSchedule[] = [
                                    'name' => 'Order',
                                    'data' => array_column($scheduleChart, 'order'),
                                ];
                                $seriesSchedule[] = [
                                    'name' => 'Schedule',
                                    'data' => array_column($scheduleChart, 'schedule'),
                                ];

                                echo Highcharts::widget([
                                    'id' => 'chart_schedule',
                                    'scripts' => [
                                        'highcharts-3d',
                                        'modules/exporting',
//                                        'modules/offline-exporting',
                                    ],
                                    'options' => [
                                        'chart' => [
                                            'type' => 'column',
                                            'options3d' => [
                                                'enabled' => true,
                                                'alpha' => 5,
                                                'beta' => 10,
                                            ]
                                        ],
//                                        'tooltip' => [
//                                            'headerFormat' => '<span style="font-size:10px">{point.key}</span><table>',
//                                            'pointFormat' => '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' .
//                                            '<td style="padding:0"><b>{point.y:.2f} conts</b></td></tr>',
//                                            'footerFormat' => '</table>',
//                                            'shared' => true,
//                                            'useHTML' => true
//                                        ],
                                        'exporting' => [
                                            'enabled' => true,
                                            'filename' => 'Schedule ' . $dataTotalSchedule['name']
                                        ],
                                        'credits' => ['enabled' => FALSE],
                                        'title' => ['text' => 'Schedule ' . $dataTotalSchedule['name']],
                                        'xAxis' => [
                                            'categories' => array_column($scheduleChart, 'months')
                                        ],
                                        'yAxis' => [
                                            'title' => ['text' => 'Container']
                                        ],
                                        'series' => $seriesSchedule,
                                        'plotOptions' => [
                                            'column' => [
                                                'allowPointSelect' => TRUE,
                                                'states' => [
                                                    'select' => [
                                                        'color' => '#ff8566',
                                                    ],
                                                ],
                                                'cursor' => 'pointer',
                                                'dataLabels' => [
                                                    'enabled' => TRUE,
                                                    'format' => '<b>{point.y:.2f}</b>',
                                                ],
                                            ]
                                        ]
                                    ]
                                ]);
                                ?>
                                <!--                                <button id="plain">Plain</button>-->
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

