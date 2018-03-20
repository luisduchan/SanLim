<?php

//use yii\grid\GridView;
use yii\helpers\Html;
use yii\data\SqlDataProvider;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use backend\modules\po\models\Po;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = 'Total Order Report in ' . $group . ' => List POs for ' . $customer . ' in ' . $month;
?>

<?php

$gridViewDataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $arrPO,
    'pagination' => ['pageSize' => 1000]
        ]);
?>
<?php

$gridColumns = ['po_no', 'blanket_po_no', 'total_cont'];
echo ExportMenu::widget([
    'dataProvider' => $gridViewDataProvider,
    'columns' => $gridColumns,
    'filename' => 'Schedule Report'
]);
echo GridView::widget([
    'dataProvider' => $gridViewDataProvider,
    'rowOptions' => function($data) use ($dateLimit) {
        if ($data['confirm_date_end'] > $dateLimit && $data['confirm_date_end'] < strtotime($data['schedule_etd_date'])) {
            return ['class' => 'danger'];
        } else if ($data['confirm_date_end'] < $dateLimit && $data['confirm_date_start'] < strtotime($data['schedule_etd_date'])) {
            return ['class' => 'danger'];
        }
        if ($data['schedule_etd_date'] == '') {
            return ['class' => 'warning'];
        }
    },
    'showFooter' => TRUE,
    'hover' => TRUE,
    'responsiveWrap' => FALSE,
    'columns' => [
        ['attribute' => 'po_no',
            'format' => 'raw',
            'value' => function ($data) {
            return Html::a($data['po_no'], Url::to(['/po/default/detail',
                                'pono' => $data['po_no'],
            ]));
        }],
        ['attribute' => 'blanket_po_no',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a($data['blanket_po_no'], Url::to(['/overview/schedule/podetail',
                                    'pono' => $data['blanket_po_no'],
                ]));
            },
        ],
        ['attribute' => 'report_group'],
        ['attribute' => 'total_cont',
            'format' => ['decimal', 2],
            'footer' => Po::pageTotal($gridViewDataProvider, 'total_cont')],
        ['attribute' => 'total_cont_on_po',
            'format' => ['decimal', 2],
            'footer' => Po::pageTotal($gridViewDataProvider, 'total_cont_on_po')],
        ['attribute' => 'po_date',
            'label' => 'Order Date',
            'format' => ['date', 'php:m/d/Y']],
        ['attribute' => 'cus_request_date'],
        ['attribute' => 'confirm_etd'],
        ['attribute' => 'schedule_etd_date',],
        ['attribute' => 'diff_days',],
        ['attribute' => 'expect_assembly_date',
            'format' => ['date', 'php:m/d/Y']],
        ['attribute' => 'schedule_assembly'],
    ],
//    'pjax' => true,
    'floatHeader' => TRUE,
    'floatHeaderOptions' => [
        'position' => 'absolute'],
]);

?>