<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use brussens\bootstrap\select\Widget as Select;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\jui\DatePicker;
use yii\data\Sort;
use backend\modules\common\models\GridTool;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use miloschuman\highcharts\Highcharts;
use kartik\slider\Slider;

// Yii::$app->assetManager->bundles['kartik\base\WidgetAsset'] = [
// 'depends' => ['kartik\slider\SliderAsset']
// ];
// Yii::$app->assetManager->bundles['kartik\slider\SliderAsset'] = [
// 'depends' => ['yii\jui\JuiAsset']
// ];
// Yii::$app->assetManager->bundles['yii\jui\JuiAsset'] = [
// 'depends' => ['yii\bootstrap\BootstrapPluginAsset']
// ];

$this->title = 'Material Output';

$materialUrl = 'index.php?r=item/ajax/getmaterialitem';
?>

<?php
if ($mainData) {
    $series = [];
    foreach ($mainData as $row) {
        $data = [];
        foreach ($monthData as $month) {
            $data[] = $row[$month];
        }
        $series[] = [
            'type' => 'line',
            'name' => $row['item_no'],
            'data' => $data,
        ];
    }
?>
    <div class="chart">
        <?php
        echo Highcharts::widget([
            'options' => [
                'chart' => [
                    'height' => 600,
                ],
                'title' => ['text' => 'Material Output'],
                'xAxis' => [
                    'categories' => $monthData
                ],
                'yAxis' => [
                    'title' => ['text' => 'Quantity']
                ],
                'series' => $series,
                'plotOptions' => [
                    'line' => ['dataLabels' => ['enabled' => TRUE]]
                ],
//                        'colors' => ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9',
//   '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1','#00FF00','#008000','#00FFFF','#0000FF','#000080','#FF00FF'],
            ]
        ]);
        ?>

    </div>

<?php
    $sortColumn = [];
    foreach ($monthData as $month) {
        $sortColumn [] = $month;
    }
    $sortColumn[] = 'total';
    $sort = new Sort([
        'attributes' => $sortColumn,
    ]);
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'sort' => $sort,
        'pagination' => ['pageSize' => 100]
    ]);

    $columns = [
        [
            'attribute' => 'item_no',
            'label' => 'Item No',
            'format' => 'raw',
            'value' => function ($data) use ($dateTo, $dateFrom) {
                return Html::a($data['item_no'], Url::to(['/moutput/division',
                                    'DivisionF[item_no]' => $data['item_no'],
                                    'DivisionF[date_from]' => $dateFrom,
                                    'DivisionF[date_to]' => $dateTo
                ]));
            }
        ],
        [
            'attribute' => 'description',
        ],
    ];
    foreach ($monthData as $month) {
        $columns[] = ['attribute' => $month,
            'footer' => GridTool::pageTotal($gridViewDataProvider, $month)];
    }
    $columns[] = ['attribute' => 'total',
        'footer' => GridTool::pageTotal($gridViewDataProvider, 'total')];



    $gridColumns = array_column($columns, 'attribute');
    echo ExportMenu::widget([
        'dataProvider' => $gridViewDataProvider,
        'columns' => $gridColumns,
        'filename' => 'Schedule Report'
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
    ]);
}
?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Material Output</h3>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(['id' => 'wsi_score_card', 'method' => 'get', 'action' => Url::to(['index']),]) ?>
        <div class="row">
            <div class="col-md-5">
                <?php
//                echo $form->field($generalF, 'reportGroup')->widget(Select2::className(), [
//                    'name' => 'reportGroup',
//                    'data' => $allGroups,
//                    'options' => ['placeholder' => 'Select Report Groups', 'multiple' => true],
//                    'pluginOptions' => [
////                        'tags' => true,
//                        'tokenSeparators' => [',', ' '],
//                        'maximumInputLength' => 10
//                    ],
//                ])->label('Months*');
                ?>
                <?=
                    $form->field($generalF, 'item_no')->widget(Select2::className(), [
                        'options' => [
                            'placeholder' => 'Select Item No',
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'tags' => true,
                            'tokenSeparators' => [',', ' '],
                            'ajax' => [
                                'url' => $materialUrl,
                                'dataType' => 'json',
                                'delay' => 250,
                                'data' => new JsExpression('function(params) { return {item_no:params.term}; }'),
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(item) { return (item.text); }'),
                            'templateSelection' => new JsExpression('function (item) { return (item.id); }'),
                            'allowClear' => true
                        ],
                    ])->label('Item No');
                ?>
                <?=
                    $form->field($generalF, 'date_from')
                    ->widget(DatePicker::classname(), [
                        'dateFormat' => 'yyyy-MM-dd',
                        'options' => [
                            'class' => 'form-control'
                        ]
                    ])->label('Date From *')
                ?>
                <?=
                    $form->field($generalF, 'date_to')
                    ->widget(DatePicker::classname(), [
                        'dateFormat' => 'yyyy-MM-dd',
                        'options' => [
                            'class' => 'form-control'
                        ]
                    ])->label('Date To *')
                ?>
                <?=
                    $form->field($generalF, 'range')->widget(Slider::classname(), [
                        'pluginOptions'=>[
                            'min'=>0,
                            'max'=>1000000000,
                            'step'=>100000000,
                            'range'=>true
                        ],
                        'pluginConflict' => true
                    ]);
                ?>
                <div class="form-group">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
                </div>
                <?php ActiveForm::end() ?>
                <p>Note: * is required field</p>
            </div>
        </div>
    </div>
</div>
