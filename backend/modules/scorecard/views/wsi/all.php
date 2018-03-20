<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use brussens\bootstrap\select\Widget as Select;
use yii\helpers\Url;
use backend\modules\po\models\Po;
use kartik\select2\Select2;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

$this->title = 'All WSI POs in ' . $group
?>
<?php

$gridViewDataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $mainData,
    'pagination' => ['pageSize' => 1000]
        ]);
$columns = [['attribute' => 'po_no'],
    ['attribute' => 'order_date'],
    ['attribute' => 'confirm_etd_from'],
    ['attribute' => 'confirm_etd_to'],
    ['attribute' => 'max_delay_day_display',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data['max_delay_day_display'], Url::to(['/scorecard/wsi/detail',
                                'pono' => $data['po_no'],
            ]));
        },
    ],
    ['attribute' => 'ignore'],
    ['attribute' => 'noted'],
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
?>