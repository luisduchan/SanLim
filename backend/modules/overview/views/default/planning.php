<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\widgets\Breadcrumbs;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use miloschuman\highcharts\Highcharts;
use yii\grid\GridView;
use brussens\bootstrap\select\Widget as Select;
use yii\helpers\Url;
use backend\modules\po\models\Po;
use backend\modules\overview\assets\OverviewAsset;
OverviewAsset::register($this);
$this->title = 'Overview';
echo Breadcrumbs::widget([
    'itemTemplate' => "<li><i>{link}</i></li>\n", // template for all links
    'links' => [
            ['label' => $this->title],
    ],
]);

//
?>

<?php
if ($showChart) {

    $series = [];
    $arOutput = array_values(array_column($data, 'output'));
    $series[] = [
        'type' => 'column',
        'name' => 'Used',
        'data' => array_map(
                function($value) {
                    return (float) $value;
                }, $arOutput),
        'color' => '#000000',
    ];
    $month_years = array_reverse($month_year_outst);
    foreach ($month_years as $month_year) {
        $arrayTemp = array_values(array_column($data, $month_year));
        $series[] = [
            'type' => 'column',
            'name' => 'Outstanding ' . $month_year,
            'data' => array_map(
                    function($value) {
                        return (float) $value;
                    }, $arrayTemp),
            'stack' => 'stock'
        ];
    }
    $arStock = array_values(array_column($data, 'stock'));
    $series[] = [
        'type' => 'column',
        'name' => 'Stock',
        'data' => array_map(
                function($value) {
                    return (float) $value;
                }, $arStock),
        'color' => '#0000ff',
        'stack' => 'stock'
    ];
    $arrayItem = array_values(array_column($data, 'item_no'));
    ?>
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Chart Comparing Used, Stock and Outstanding </h3>

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
                        'credits' => ['enabled' => FALSE],
                        'title' => ['text' => 'Comparing Used, Stock and Outstanding'],
                        'xAxis' => [
                            'categories' => $arrayItem
                        ],
                        'yAxis' => [
                            'title' => ['text' => 'Quantity']
                        ],
                        'series' => $series,
                        'plotOptions' => [
                            'series' => [
                                'stacking' => 'normal'
                            ],
                            'column' => ['dataLabels' => [
                                    'enabled' => true,
                                    'format' => '{point.y:,.0f}'
                                ]]
                        ],
                    ],
                ]);
                ?>
<button id="plain">Plain</button>
            </div>
        </div>

        <!-- /.box-body -->
    </div>

    <?php
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $data,
        'pagination' => ['pageSize' => 1000]
    ]);
    ?>
    <!--begin detail-->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title">Detail</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body table-responsive no-padding">
            <?php
//            $po = new Po();
            $total_output = Po::pageTotal($gridViewDataProvider, 'output');
            $columns = [
                    ['attribute' => 'item_no', 'header' => 'Item Code', 'footer' => 'Total'],
                    ['attribute' => 'output', 'format' => 'decimal', 'header' => 'Average <br> Material Output', 'footer' => $total_output],
                    ['attribute' => 'stock', 'format' => 'decimal', 'header' => 'Current Stock', 'footer' => Po::pageTotal($gridViewDataProvider, 'stock')]
            ];
            foreach ($month_year_outst as $month_year) {
                $columns[] = ['attribute' => $month_year,
                    'format' => 'decimal',
                    'header' => 'Out Standing <br> of ' . $month_year,
                    'footer' => Po::pageTotal($gridViewDataProvider, $month_year)];
            }
//    var_dump($columns);die();
            echo GridView::widget([
                'dataProvider' => $gridViewDataProvider,
                'showFooter' => TRUE,
                'tableOptions' => ['class' => 'table table-hover'],
                'columns' => $columns,
//        [
//                ['attribute' => 'item_no', 'footer' => 'Total'],
//                ['attribute' => 'stock',
//                'format' => 'decimal',
//                'footer' => Po::pageTotal($gridViewDataProvider, 'stock')],
//                ['attribute' => 'output',
//                'format' => 'decimal',
//                'footer' => Po::pageTotal($gridViewDataProvider, 'output')],
//        ]
            ])
            ?>
        </div>
    </div>
    <!--end detail-->
    <?php
    $seriesOutput = [];
    foreach($outputData as $outputRow){
        $row = array_values($outputRow);
        $seriesOutput[] = [
            'type' => 'line',
            'name' => $row[0],
            'data' => array_map(
                    function($value) {
                        return (float) $value;
                    }, array_slice($row, 1, count($row)-2)),
//            'stack' => 'stock'
        ];
    }
   
    ?>
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Chart Output</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="chart">
    <?php
    $arrayItemOutput = $arrPastPeriod;
//    var_dump($arrPastPeriod);
//    var_dump($arrayItemOutput);die();
    echo Highcharts::widget([
        'scripts' => [
            'highcharts-3d',
        ],
        'options' => [
            'credits' => ['enabled' => FALSE],
            'title' => ['text' => 'Material Output'],
            'xAxis' => [
                'categories' => $arrayItemOutput
            ],
            'yAxis' => [
                'title' => ['text' => 'Quantity']
            ],
            'series' => $seriesOutput,
            'plotOptions' => [
//                'series' => [
//                    'stacking' => 'normal'
//                ],
                'line' => ['dataLabels' => [
                        'enabled' => true,
                        'format' => '{point.y:,.0f}'
                    ]]
            ],
        ],
    ]);
    ?>

            </div>
        </div>

        <!-- /.box-body -->
    </div>

    <?php
    $gridViewDataProviderOutput = new \yii\data\ArrayDataProvider([
        'allModels' => $outputData,
        'pagination' => ['pageSize' => 1000]
    ]);
    ?>
    <!--begin detail-->
    <div class="box box-success collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title">Detail</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body table-responsive no-padding">
    <?php
//            $po = new Po();
    $total_output = Po::pageTotal($gridViewDataProvider, 'output');
    $columns = [
            ['attribute' => 'item_no', 'header' => 'Item Code', 'footer' => 'Total'],
    ];
    foreach ($arrPastPeriod as $month_year) {
        $columns[] = ['attribute' => $month_year,
            'format' => 'decimal',
            'header' => 'Output <br> of ' . $month_year,
            'footer' => Po::pageTotal($gridViewDataProviderOutput, $month_year)];
    }
//    var_dump($columns);die();
    echo GridView::widget([
        'dataProvider' => $gridViewDataProviderOutput,
        'showFooter' => TRUE,
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => $columns,
//        [
//                ['attribute' => 'item_no', 'footer' => 'Total'],
//                ['attribute' => 'stock',
//                'format' => 'decimal',
//                'footer' => Po::pageTotal($gridViewDataProvider, 'stock')],
//                ['attribute' => 'output',
//                'format' => 'decimal',
//                'footer' => Po::pageTotal($gridViewDataProvider, 'output')],
//        ]
    ])
    ?>
        </div>
    </div>
    <!--end detail-->


<?php } ?>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Form</h3>

    </div>
    <div class="box-body">
<?php $form = ActiveForm::begin(['id' => 'overview_planning', 'method' => 'get', 'action' => Url::to(['planning']),]) ?>
        <div class="row">
            <div class="col-md-5">
        <?php
        $url = '/' . Yii::$app->urlManager->createUrl('./../frontend') . 'index.php?r=inventory_report/moutput/getitemno';
        echo $form->field($planForm, 'item_no')
                ->widget(AutoComplete::classname(), ['clientOptions' => [
                        'source' => new JsExpression("function(request, response) {
                            $.getJSON('" . $url . "', {
                                item_no: request.term
                            }, response);
                        }"),
                    ], 'options' => ['class' => 'form-control']])->label('Item No');
        ?>

            </div>
            <div class="col-md-5 col-md-offset-1 ">
                <div class="well well-sm">
<?=
        $form->field($planForm, 'date_from')
        ->widget(DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd', 'options' => ['class' => 'form-control']])->label('Date From *')
?>
                    <?=
                            $form->field($planForm, 'date_to')
                            ->widget(DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd', 'options' => ['class' => 'form-control']])->label('Date To *')
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
<?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
        </div>
            <?php ActiveForm::end() ?>
        <p>Note: * is required field</p>
    </div>
</div>
