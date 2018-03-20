<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\data\Sort;
use kartik\grid\GridView;
use backend\modules\common\models\GridTool;
use miloschuman\highcharts\Highcharts;

$this->title = 'Lazboy Scorecard';
?>
<?php
if ($mainData) {
    $i = 0;
    foreach ($mainData as $row) {
        if($row['total_quantity'] > 0){
        $series_data[] = ['name' => $row['type'], 'y' => $row['total_quantity']];
        }
        $i++;
    }
    $series = [
        'name' => 'Total',
        'data' => $series_data,
    ];
    ?>
    <div>
        <?=
        Highcharts::widget([
            'id' => 'wsi_score_card',
            'scripts' => [
                'highcharts-3d',
                'modules/exporting',
                'modules/offline-exporting',
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
                    'filename' => 'Lazboy Scorecard'
                ],
                'title' => ['text' => 'Lazboy Scorecard'],
                'plotOptions' => [
                    'pie' => [
                        'allowPointSelect' => true,
                        'cursor' => 'pointer',
                        'depth' => 35,
                        'dataLabels' => [
                            'enabled' => true,
                            'format' => '<b>{point.name}</b>: {point.y:.0f} ({point.percentage:.1f}%)',
                        ],
                    ],
                ],
                'series' => [$series],
                'credits' => [
                    'enabled' => false
                ],
            ]
        ]);
        ?>

    </div>
    <?php
    $sort = new Sort([
        'attributes' => [
            'type',
            'total_quantity',
        ],
    ]);
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'sort' => $sort,
        'pagination' => ['pageSize' => 200]
    ]);
    $columns = [['attribute' => 'type'],
        ['attribute' => 'total_quantity',
            'format' => 'raw',
            'value' => function ($data) use($dateFrom, $dateTo) {
                return Html::a($data['total_quantity'], Url::to(['/scorecard/lazboy/scorecarddetail',
                                    'dateFrom' => $dateFrom,
                                    'dateTo' => $dateTo,
                                    'type' => $data['type_code'],
                ]));
            },
            'footer' => GridTool::pageTotal($gridViewDataProvider, 'total_quantity'),],
    ];

    echo GridView::widget([
        'dataProvider' => $gridViewDataProvider,
//    'rowOptions' => function($data) {
//        if ($data['actual_day_delay'] < 0 || $data['actual_day_delay'] === '') {
//            return ['class' => 'info'];
//        } elseif ($data['exception'] == 1) {
//            return ['style' => 'background-color:#292b2c;color:#fff'];
//        } elseif ($data['actual_day_delay'] <= 13) {
//            return ['class' => 'success'];
//        } else {
//            return ['class' => 'danger'];
//        }
//    },
        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'showFooter' => TRUE,
        'hover' => TRUE,
        'responsiveWrap' => FALSE,
        'columns' => $columns,
        'floatHeader' => TRUE,
        'floatHeaderOptions' => [
            'position' => 'absolute'],
    ]);
    ?>
<?php } ?>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Form</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <?php
            $viewForm = ActiveForm::begin([
                        'id' => 'lazboy_scorecard_form',
                        'method' => 'get',
                        'action' => Url::to(['index'])])
            ?>
            <div class="col-md-5">
                <?=
                        $viewForm->field($lazboyScorecardFrom, 'dateFrom')
                        ->widget(DatePicker::classname(), [
                            'dateFormat' => 'yyyy-MM-dd',
                            'options' => [
                                'class' => 'form-control'
                            ]
                        ])->label('Date From *')
                ?>
                <?=
                        $viewForm->field($lazboyScorecardFrom, 'dateTo')
                        ->widget(DatePicker::classname(), [
                            'dateFormat' => 'yyyy-MM-dd',
                            'options' => ['class' => 'form-control',
                                'ignoreReadonly' => true,
                            ]
                        ])->label('Date To *')
                ?>

                <div class="form-group">
                    <?=
                    Html::submitButton('Submit', [
                        'class' => 'btn btn-primary',
                        'name' => 'submit-button'])
                    ?>
                </div>

                <p>Note: * is required field</p>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>