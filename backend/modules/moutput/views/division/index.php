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
use backend\modules\moutput\assets\MouputAsset;
MouputAsset::register($this);

$this->title = 'Material Output (' . $itemNo. ') by Division' ;

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

            'name' => $row['description'],
            'data' => $data,
//            'description' => $row['description'],
        ];
    }
    ?>
    <div class="chart">
        <div id="chart_moutput_division">

        </div>
        <?php
        echo Highcharts::widget([
            'scripts' => [
                'modules/exporting',
            ],
            'options' => [
                'chart' => [
                    'type' => 'line',
                    'height' => 600,
                    'renderTo' => 'chart_moutput_division'
                ],
                'title' => ['text' => 'Material Output (' . $itemNo .') by Division'],
                'xAxis' => [
                    'categories' => $monthData
                ],
                'yAxis' => [
                    'title' => ['text' => 'Quantity']
                ],
                'series' => $series,
                'plotOptions' => [
                    'line' => [
                        'dataLabels' => ['enabled' => TRUE],
                        'allowPointSelect' => true,
                        ]
                ],
                'exporting' => [
                    'enabled' => true,
                    'filename' => 'Material Output '
                ],
//                'tooltip' => [
//                    'pointFormat' => '{point.description} afsasf'
//                ],
                        'colors' => ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9',
   '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1','#00FF00','#008000','#00FFFF','#0000FF','#000080','#FF00FF'],
            ]
        ]);
        ?>
        <button id="chart_type_line" type="button" class="btn btn-primary btn-md">Line</button>
        <button id="chart_type_column" type="button" class="btn btn-primary btn-md">Column</button>
    </div>

    <?php
    $sortColumn = [];
    foreach ($monthData as $month) {
        $sortColumn [] = $month;
    }
    $sortColumn[] = 'division_code';
    $sortColumn[] = 'total';
//    $sortColumn[] = 'location';
    $sortColumn[] = 'description';
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
            'attribute' => 'division_code',
//            'format' => 'raw',
//            'value' => function ($data) {
//                return Html::a($data['item_no'], Url::to(['/overview/schedule/itemdetail',
//                                    'itemno' => $data['item_no']
//                ]));
//            }
        ],
        [
            'attribute' => 'description',
        ],
//        [
//            'attribute' => 'location',
//        ],
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
                $form->field($divisionF, 'item_no')->widget(Select2::className(), [
                    'options' => [
                        'placeholder' => 'Select Item No',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'tags' => false,
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
                        $form->field($divisionF, 'date_from')
                        ->widget(DatePicker::classname(), [
                            'dateFormat' => 'yyyy-MM-dd',
                            'options' => [
                                'class' => 'form-control'
                            ]
                        ])->label('Date From *')
                ?>
                <?=
                        $form->field($divisionF, 'date_to')
                        ->widget(DatePicker::classname(), [
                            'dateFormat' => 'yyyy-MM-dd',
                            'options' => [
                                'class' => 'form-control'
                            ]
                        ])->label('Date To *')
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
