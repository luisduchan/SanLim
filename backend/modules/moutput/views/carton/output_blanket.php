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

$this->title = 'Material Output by Blanket';
?>

<?php
if ($mainData) {
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        // 'sort' => $sort,
        'pagination' => ['pageSize' => 100]
    ]);

    $columns = [
        [
            'attribute' => 'blanket',
            'label' => 'Blanket',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a($data['blanket'], Url::to(['/overview/schedule/podetail',
                                    'pono' => $data['blanket'],
                ]));
            }
        ],
        ['attribute' => 'ik'],
        ['attribute' => 'quantity',
            'footer' => GridTool::pageTotal($gridViewDataProvider, 'quantity')],
        ['attribute' => 'cost',
            'label' => 'Total Cost',
            'format' => 'raw',
            'value' => function ($data) use ($monthYear, $divisionCode, $locationCode) {
                return Html::a($data['cost'], Url::to(['/moutput/carton/output-leger-entry',
                                    'month' => $monthYear,
                                    'division' => $divisionCode,
                                    'location' => $locationCode,
                                    'ik' => $data['ik'],
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