<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use brussens\bootstrap\select\Widget as Select;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;
use backend\modules\po\models\Po;
use kartik\select2\Select2;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

$title = 'WSI Scorecard in ' . $group;
if($baseOnCofirmShipDate){
    $title = 'WSI Performance in ' . $group;
}
$this->title = $title;
?>
<?php
if ($mainData) {
    $i = 0;
    foreach ($mainData as $row) {
        if ($i != 0 && $row['total'] > 0) {
            $series_data[] = ['name' => $row['type'], 'y' => $row['total']];
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
                    'filename' => $title
                ],
                'title' => ['text' => $title],
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
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'pagination' => ['pageSize' => 1000]
    ]);

    $columns = [['attribute' => 'type'],
        ['attribute' => 'total',
            'label' => 'Total Number PO',
            'format' => 'raw',
            'value' => function ($data) use($group, $baseOnCofirmShipDate) {
                return Html::a($data['total'], Url::to(['/scorecard/wsi/scorecarddetail',
                                    'group' => $group,
                                    'type' => $data['type_code'],
                                    'baseOnCofirmShipDate' => $baseOnCofirmShipDate,
                ]));
            },
        ],
    ];
    echo GridView::widget([
        'dataProvider' => $gridViewDataProvider,
        'showFooter' => TRUE,
        'hover' => TRUE,
        'responsiveWrap' => FALSE,
        'columns' => $columns,
        'floatHeader' => TRUE,
        'floatHeaderOptions' => [
            'position' => 'absolute'],
    ]);
}
?>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">WSI Score Card</h3>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(['id' => 'wsi_score_card', 'method' => 'get', 'action' => Url::to(['index']),]) ?>
        <div class="row">
            <div class="col-md-5">
                <?=
                        $form->field($wsiScoreCardForm, 'reportGroup')
                        ->widget(Select::className(), [
                            'options' => ['data-live-search' => 'true',
                                'value' => $group ? $group : strtoupper(date('M-Y')),
                            ],
                            'items' => $allGroups])->label('Month*')
                ?>
                 <?=$form->field($wsiScoreCardForm, 'baseOnCofirmShipDate')->checkbox(['label' => 'Show Performace']);?>
                <div class="form-group">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
                </div>
                <?php ActiveForm::end() ?>
                <p>Note: * is required field</p>
            </div>
        </div>
    </div>
</div>
