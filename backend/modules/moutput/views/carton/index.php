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


        $series[] = [
            'type' => 'column',
            'name' => 'Output',
            'data' => array_column($mainData, 'cost'),
            'dataLabels' => [
                'enabled' => true,
                // 'rotation' => '-60',
                'format' => '{point.y:,.1f}',
                // 'align' => 'right',
                // 'y' => 10
            ],
        ];
?>
    <div class="chart">
        <?php
        echo Highcharts::widget([
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
                    ],
                    'height' => 600,
                ],
                'title' => ['text' => 'Carton Output'],
                'xAxis' => [
                    'categories' => array_column($mainData, 'month'),
                    'labels'=> ['rotation' => '-45']
            
                ],
                'yAxis' => [
                    'title' => ['text' => 'Quantity']
                ],
                'series' => $series,
                'plotOptions' => [
                    'line' => ['dataLabels' => ['enabled' => TRUE]]
                ],
                'lang' => [
                    'thousandsSep' => ',']
//                        'colors' => ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9',
//   '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1','#00FF00','#008000','#00FFFF','#0000FF','#000080','#FF00FF'],
            ]
        ]);
        ?>

    </div>

<?php

    // $sortColumn = [];
    // foreach ($monthData as $month) {
    //     $sortColumn [] = $month;
    // }
    // $sortColumn[] = 'total';
    // $sort = new Sort([
    //     'attributes' => $sortColumn,
    // ]);
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        // 'sort' => $sort,
        'pagination' => ['pageSize' => 100]
    ]);

    $columns = [
        [
            'attribute' => 'month',
            'label' => 'Item No',
            'format' => 'raw',
            'value' => function ($data) use ($dateTo, $dateFrom) {
                return Html::a($data['month'], Url::to(['/moutput/division',
                                    'DivisionF[month]' => $data['month'],
                ]));
            }
        ],
        ['attribute' => 'quantity'],
        ['attribute' => 'cost_display',
            'format' => 'raw',
            'label' => 'Total Cost',
            'value' => function ($data) use ($dateTo, $dateFrom) {
                return Html::a($data['cost_display'], Url::to(['/moutput/carton/output-division',
                                    'month' => $data['month'],
                ]));
            },
            'footer' => GridTool::pageTotal($gridViewDataProvider, 'cost')],
    ];


    $gridColumns = array_column($columns, 'attribute');
    echo ExportMenu::widget([
        'dataProvider' => $gridViewDataProvider,
        'columns' => $gridColumns,
        'filename' => 'Carton Output'
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