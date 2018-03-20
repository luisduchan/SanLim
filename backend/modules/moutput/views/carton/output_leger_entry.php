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

$this->title = 'Material Output by Leger Entry';
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
            'attribute' => 'entry_no',
        ],
        ['attribute' => 'item_no',
            'label' => 'Material No',
            'format' => 'raw',
            'value' => function ($data){
                return Html::a($data['item_no'], Url::to(['/item/default/detail',
                                    'itemno' => $data['item_no'],
                ]));
            },],
        ['attribute' => 'description'],
        ['attribute' => 'ik'],
        ['attribute' => 'quantity'],
        ['attribute' => 'uom'],
        ['attribute' => 'cost_display',
            'footer' => GridTool::pageTotal($gridViewDataProvider, 'cost')],
        ['attribute' => 'posting_date'],
        // ['attribute' => 'last_invoice_date'],
        ['attribute' => 'location_code'],
        ['attribute' => 'ext_doc_no'],
        
        ['attribute' => 'item_category_code'],
        ['attribute' => 'posting_user'],
        
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