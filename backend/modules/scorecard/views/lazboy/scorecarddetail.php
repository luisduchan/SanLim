<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use brussens\bootstrap\select\Widget as Select;
use yii\helpers\Url;
use backend\modules\po\models\Po;
use kartik\select2\Select2;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\data\Sort;
use backend\modules\common\models\GridTool;

$this->title = 'Lazboy Score Card Detail';
?>
<?php

$sort = new Sort([
    'attributes' => [
        'po_no',
        'po_type_display',
        'so',
        'item_no',
        'ordered_quantity',
        'real_calc_ship_date',
        'short_ship',
        'transfter_notice_date',
        'actual_day_delay',
        'exception_diplay',
        'remark',
    ],
        ]);
$gridViewDataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $mainData,
    'sort' => $sort,
    'pagination' => ['pageSize' => 1000]
        ]);
$columns = [
    ['attribute' => 'po_no',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data['po_no'], Url::to(['/po/default/detail',
                                'pono' => $data['po_no'],
            ]));
        }],
    ['attribute' => 'item_no'],
    ['attribute' => 'ordered_quantity',
        'footer' => GridTool::pageTotal($gridViewDataProvider, 'ordered_quantity'),],
    ['attribute' => 'transfter_notice_date'],
    ['attribute' => 'so',
        'label' => 'First SO'],
    ['attribute' => 'real_calc_ship_date',
        'label' => 'First ETD'],
    ['attribute' => 'total_shipped'],
    ['attribute' => 'short_ship'],
    ['attribute' => 'actual_day_delay'],
    ['attribute' => 'exception_diplay',
        'label' => 'Exception'],
    ['attribute' => 'remark',
        'contentOptions' => ['style' => 'max-width: 250px;overflow: auto; word-wrap: break-word;']],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign' => 'middle',
        'template' => '{shipment}',
        'buttons' => [
            'shipment' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-ship"></span>', Url::to(['/scorecard/wsi/shippingdetail',
                                    'pono' => $model['technical_po_no'],
                ]));
            },
        ]
    ],
];

echo GridView::widget([
    'dataProvider' => $gridViewDataProvider,
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