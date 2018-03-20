<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use brussens\bootstrap\select\Widget as Select;
use yii\helpers\Url;
use backend\modules\po\models\Po;
//use kartik\select2\Select2;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\data\Sort;
use miloschuman\highcharts\Highcharts;

//use DateTime;

$this->title = "Total Order Report in " . $group . ' by '. $dateName;
?>
<a href="#demo" data-toggle="collapse">Description</a>

<div id="demo" class="collapse">
    <blockquote>
        <p>This report show the total quantity that need to be plan in a selected month.</p>

        <p>It will filter out all orders by criteria:</p>
        <p> - POs with Date Type &lt= the <mark>last</mark> date of selected month AND have <mark>NOT</mark> yet <mark>assembling date</mark></p>
        <p> - POs with Date Type &lt= the <mark>first</mark> date of selected month AND <mark>assembling date</mark> in the selected month</p>
        <p> - POs with Date Type is in the selected month.</p>
    </blockquote>
</div>
<br/>

<?php
if ($mainData) {

    $i = 0;
    $columns[] = ['attribute' => 'cus_name', 'label' => 'Customer', 'footer' => 'Total'];
    $groupNumber = DateTime::createFromFormat('M-Y', $group);
    $groupNumber = $groupNumber->format('Y/m');
    $sort = new Sort([
        'attributes' => [
            'total',
            'cus_name',
        ],
    ]);
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'sort' => $sort,
        'pagination' => ['pageSize' => 1000]
    ]);
    foreach ($months as $month) {
        if (substr($month, 0, 1) == 's') {
            $monthScheduled = DateTime::createFromFormat('Y/m', substr($month, -7));
            $monthScheduled = mb_strtoupper($monthScheduled->format('M-Y'));
            $columns[] = ['attribute' => $month,
                'label' => 'Schedule in ' . substr($month, 1, strlen($month) - 1),
                'format' => 'raw',
                'value' => function ($data) use ($month, $group, $dateType, $monthScheduled, $groupNumber) {
                    return Html::a($data[$month], Url::to(['/customerpo/schedule/polist',
                                        'group' => $monthScheduled,
                                        'customer' => $data['cus_name'],
                                        'date_type' => $dateType,
                                        'month' => $groupNumber,
                                    ]), ['class' => 'text-danger']);
                },
                'footer' => Po::pageTotal($gridViewDataProvider, $month),
            ];
        } else {
            $columns[] = ['attribute' => $month,
                'label' => 'Order in ' . $month,
                'format' => 'raw',
                'value' => function ($data) use ($month, $group, $dateType) {
                    return Html::a($data[$month], Url::to(['/customerpo/default/custpolistforneedplan',
                                        'group' => $group,
                                        'customer' => $data['cus_name'],
                                        'date_type' => $dateType,
                                        'month' => $month,
                    ]));
                },
                'footer' => Po::pageTotal($gridViewDataProvider, $month),
            ];
        }

        $i++;
    }
    if ($scheduledMonths) {
        $groupNumber = DateTime::createFromFormat('M-Y', $group);
        $groupNumber = $groupNumber->format('m/Y');
        foreach ($scheduledMonths as $scheduledMonth) {
            $monthScheduled = DateTime::createFromFormat('m/Y', substr($scheduledMonth, -7));
            $monthScheduled = mb_strtoupper($monthScheduled->format('M-Y'));
            $columns[] = ['attribute' => $scheduledMonth,
                'format' => 'raw',
                'value' => function ($data) use ($monthScheduled, $groupNumber, $dateType, $scheduledMonth) {
                    return Html::a($data[$scheduledMonth], Url::to(['/customerpo/schedule/polist',
                                        'group' => $monthScheduled,
                                        'customer' => $data['cus_name'],
                                        'date_type' => $dateType,
                                        'month' => $groupNumber,
                                    ]), ['class' => 'text-danger']);
                },
                'footer' => Po::pageTotal($gridViewDataProvider, $scheduledMonth),
//                'options' => ['style' => 'background-color:#ffe6e6']
            ];
        }
    }

    $columns[] = ['attribute' => 'total',
        'format' => 'raw',
        'footer' => Po::pageTotal($gridViewDataProvider, 'total'),
    ];

    foreach ($mainData as $customer => $orderData) {
        $charData[] = ['name' => $customer, 'y' => round($orderData['total'], 2)];
    }
    $series = [
        'name' => 'Total',
        'data' => $charData,
    ];
//    var_dump($series);die();
    echo Highcharts::widget([
        'id' => 'total_order',
        'scripts' => [
            'highcharts-3d',
            'modules/exporting',
        ],
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
                'filename' => 'Total Order'
            ],
            'credits' => ['enabled' => FALSE],
            'title' => ['text' => 'Total Order'],
            'plotOptions' => [
                'pie' => [
                    'allowPointSelect' => true,
                    'cursor' => 'pointer',
                    'depth' => 35,
                    'dataLabels' => [
                        'enabled' => true,
                        'format' => '<b>{point.name}</b>: {point.y:.2f} ({point.percentage:.1f}%)',
                    ],
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
        ]
    ]);

    echo ExportMenu::widget([
        'dataProvider' => $gridViewDataProvider,
        'columns' => $columns,
        'enableFormatter' => TRUE,
        'target' => ExportMenu::TARGET_BLANK,
        'filename' => 'Total Order Report_' . date('m_d_Y'),
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_PDF => false,
            ExportMenu::FORMAT_HTML => false
        ]
    ]);
    echo GridView::widget([
        'dataProvider' => $gridViewDataProvider,
        'showFooter' => TRUE,
        'hover' => TRUE,
        'responsiveWrap' => FALSE,
        'columns' => $columns,
        'floatHeader' => TRUE,
        'floatHeaderOptions' => [
            'position' => 'absolute'],
//        'pjax' => true,
    ]);
}
?>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Total Order Report Form</h3>
    </div>
    <div class="box-body">
<?php $form = ActiveForm::begin(['id' => 'cusponeedplan', 'method' => 'get', 'action' => Url::to(['custponeedplan']),]) ?>
        <div class="row">
            <div class="col-md-5">
<?=
        $form->field($needPlanF, 'group')
        ->widget(Select::className(), [
            'options' => ['data-live-search' => 'true',
                'value' => $mainData ? $group : strtoupper(date('M-Y')),
            ],
            'items' => $allGroups])->label('Month*')
?>
                <?=
                        $form->field($needPlanF, 'date_type')
                        ->dropDownList($dateTypeList, ['options' => [
                                $dateType => array('selected' => 'selected')
                            ]
                        ])->label('Date Type*')
                ?>

                <?=$form->field($needPlanF, 'not_delay')->checkbox();?>

                <div class="form-group">
<?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
                </div>
                    <?php ActiveForm::end() ?>
                <p>Note: * is required field</p>
            </div>
        </div>
    </div>
</div>