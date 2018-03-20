<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use brussens\bootstrap\select\Widget as Select;
use yii\helpers\Url;
use backend\modules\po\models\Po;
use kartik\select2\Select2;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use backend\modules\common\models\GridTool;

$this->title = 'Shipping Detail';
$this->registerJs(
        "$('div[data-toggle=\"tooltip\"]').tooltip({
    placement: 'right',
    html: true
});", \yii\web\View::POS_READY, 'my-button-handler'
);
?>
<?php if($header){ ?>
<div class="box-body">
    <div class="row">
        <form class="form-horizontal">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="col-sm-5">PO:</label>
                    <a href="<?= Url::to(['/po/default/detail', 'pono' => $poNo,]) ?>">
                        <input class="col-sm-7" value="<?=$poNo?>" style="cursor: pointer" readonly="true">
                    </a>
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Customer Name:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['customer_name'] . ' (' . $header['customer_no'] . ')' ?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Order Date:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['po_date'] ?>">

                </div>
            </div>
            <div class="col-md-4 col-md-offset-1 ">
                <div class="form-group">
                    <label class="col-sm-5">Request Ship Date:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['request_ship_date'] ?>"/>
                </div>


                <div class="form-group">
                    <label class="col-sm-5">Confirm Ship Date:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['confirm_ship_date'] ?>"/>
                </div>
            </div>
        </form>
    </div>
    <?php
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'pagination' => ['pageSize' => 1000]
    ]);
    $columns = [
        ['attribute' => 'item_number',
            'format' => 'raw',
            'value' => function($data) {
                return Html::a($data['item_number'], Url::to(['/overview/schedule/itemdetail',
                                    'itemno' => $data['item_number']
                ]));
            }
//            'value' => function($data) {
//                if (isset($data['image'])) {
//                $image = $data['image'] ? True : False;
//                $imageIcon = $image ? '<span class="glyphicon glyphicon-picture"></span>' : '<span class="glyphicon glyphicon-none"></span>';
//                return Html::tag('div', $imageIcon . Html::a($data['item_number'], Url::to(['/overview/schedule/itemdetail',
//                                            'itemno' => $data['item_number']
//                                ])), ['data-toggle' => 'tooltip',
//                            'data-placement' => 'auto',
//                            'title' => $data['description'] . ($image ? '<br/>' . Html::img('data:image/jpg;base64,' . base64_encode($data['image']), ['style' => 'height:300px;max-width:800px;']) : ''),
//                            'style' => 'cursor:default;'
//                ]);
////                } else {
////                    return $data['item_number'];
////                }
//            },
        ],
//        ['attribute' => 'description'],
        ['attribute' => 'order_quantity'],
        ['attribute' => 'shipped_quantity',
            'footer' => GridTool::pageTotal($gridViewDataProvider, 'shipped_quantity')],
//        ['attribute' => 'order_date'],
//        ['attribute' => 'cofirm_etd_from'],
//        ['attribute' => 'confirm_etd_to'],
        ['attribute' => 'actual_etd',
            'label' => 'Actual ETD / Cargo Date',],
        ['attribute' => 'so_number'],
        ['attribute' => 'sik'],
        ['attribute' => 'invoice_number'],
        ['attribute' => 'transport_method',
            'label' => 'Type'],
        ['attribute' => 'completely_shipped_display', 'label' => 'Shipped'],
        ['attribute' => 'ignore_display',
            'label' => 'Exception'],
        ['attribute' => 'delay'],
        ['attribute' => 'remark'],
    ];
    echo ExportMenu::widget([
        'dataProvider' => $gridViewDataProvider,
        'columns' => $columns,
//        'filename' => 'Schedule Report'
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
    ?>
</div>
<?php }else{?>
<h4>PO Not Found</h4>
<?php } ?>
