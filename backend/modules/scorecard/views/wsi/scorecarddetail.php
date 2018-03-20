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

$this->title = 'WSI Score Card Detail - ' . $type_name . ' POs';
?>
<?php

$sort = new Sort([
    'attributes' => [
        'po_no',
        'order_date',
        'max_delay_day_display',
        'ignore_display',
        'confirm_etd' => [
            'asc' => ['confirm_etd_from' => SORT_ASC, 'confirm_etd_to' => SORT_ASC],
            'desc' => ['confirm_etd_from' => SORT_DESC, 'confirm_etd_from' => SORT_DESC],
            'default' => SORT_ASC,
        ],
        'current_etd_end',
        'noted'
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
    ['attribute' => 'confirm_etd',
        'label' => 'Confirm ETD'],
    ['attribute' => 'current_etd_end',
        'label' => 'Current ETD End'],
//    ['attribute' => 'confirm_etd_from',
//        'label' => 'Confirm ETD Start'],
//    ['attribute' => 'confirm_etd_to',
//        'label' => 'Confirm ETD End'],
    ['attribute' => 'max_delay_day_display',
        'label' => 'Delay Days',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data['max_delay_day_display'], Url::to(['/scorecard/wsi/shippingdetail',
                                'pono' => $data['po_no'],
            ]));
        },],
    ['attribute' => 'ignore_display',
        'label' => 'Exception'],
    ['attribute' => 'noted',
        'label' => 'Note',
        'contentOptions' => ['style' => 'max-width: 250px;overflow: auto; word-wrap: break-word;']],
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