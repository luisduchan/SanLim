<?php

//use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\common\models\GridTool;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\data\Sort;

$this->title = 'Items Detail for Blanket' . $blanketOrder;
?>

<?php

$sort = new Sort([
    'attributes' => [
        'po_no',
        'item_no',
        'item_description',
        'quantity',
        'order_date',
        'total_container',
        'confirm_ship_date' => [
            'asc' => ['confirm_ship_date_from' => SORT_ASC, 'confirm_ship_date_to' => SORT_ASC],
            'desc' => ['confirm_ship_date_from' => SORT_DESC, 'confirm_ship_date_to' => SORT_DESC],
            'default' => SORT_ASC,
        ],
        'request_ship_date' => [
            'asc' => ['request_ship_date_from' => SORT_ASC, 'request_ship_date_to' => SORT_ASC],
            'desc' => ['request_ship_date_from' => SORT_DESC, 'request_ship_date_to' => SORT_DESC],
            'default' => SORT_ASC,
        ],
    // or any other attribute
    ],
        ]);
$gridViewDataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $mainData,
    'sort' => $sort,
    'pagination' => ['pageSize' => 1000]
        ]);

$columns = [
    ['attribute' => 'po_no',
        'label' => 'PO',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data['po_no'], Url::to(['/po/default/detail',
                                'pono' => $data['po_no'],
            ]));
        }
    ],
    ['attribute' => 'item_no',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data['item_no'], Url::to(['/overview/schedule/itemdetail',
                                'itemno' => $data['item_no']
            ]));
        }],
    ['attribute' => 'item_description',
        'label' => 'Description'],
    ['attribute' => 'quantity',
        'format' => ['integer'],
        'footer' => GridTool::pageTotal($gridViewDataProvider, 'quantity')],
    ['attribute' => 'total_container',
        'format' => ['decimal', 2],
        'footer' => GridTool::pageTotal($gridViewDataProvider, 'total_container')],
    ['attribute' => 'order_date'],
    ['attribute' => 'request_ship_date'],
    ['attribute' => 'confirm_ship_date'],
];
echo ExportMenu::widget([
    'dataProvider' => $gridViewDataProvider,
    'columns' => array_column($columns, 'attribute'),
    'filename' => 'Item Detail for Blanket ' . $blanketOrder
]);
echo GridView::widget([
    'dataProvider' => $gridViewDataProvider,
    'showFooter' => TRUE,
    'hover' => TRUE,
    'responsiveWrap' => FALSE,
    'columns' => $columns
]);
?>