<?php

//use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\common\models\GridTool;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->registerJs(
        "$('div[data-toggle=\"tooltip\"]').tooltip({
    placement: 'right',
    html: true
});", \yii\web\View::POS_READY, 'my-button-handler'
);
$this->registerCss(".span6{
    overflow:hidden;display:inline;
}
.span6 label, .span6 input {
display:inline-block;
}
.span6 input {
    width:70%;
    margin-left:3%;
}");
$this->title = 'PO Detail';
?>
<?php if ($header) { ?>
    <h3><?= $header['po_no'] ?></h3>
    <div class="box-body">
        <div class="row">
            <form class="form-horizontal">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email" class="col-sm-5">Customer Name:</label>
                        <input class="col-sm-7" readonly="true" value="<?= $header['cust_name'] . ' (' . $header['customer_no'] . ')' ?>">
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5">Order Date:</label>
                        <input class="col-sm-7" readonly="true" value="<?= $header['po_date'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5">Request Ship Date:</label>
                        <input class="col-sm-7" readonly="true" value="<?= $header['request_ship_date'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5">Confirm Ship Date:</label>
                        <input class="col-sm-7" readonly="true" value="<?= $header['confirm_ship_date'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5">Current Ship Date:</label>
                        <input class="col-sm-7" readonly="true" value="<?= $header['current_ship_date'] ?>">
                    </div>
                </div>
                <div class="col-md-4 col-md-offset-1 ">
                    <div class="form-group">
                        <label class="col-sm-5">Expect Assy Date:</label>
                        <input class="col-sm-7" readonly="true" value="<?= $header['expect_assembly_date'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5">Expect WH Date:</label>
                        <input class="col-sm-7" readonly="true" value="<?= $header['expect_wh_date'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5">Expect ETD:</label>
                        <input class="col-sm-7" readonly="true" value="<?= $header['expect_etd'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5">Destination:</label>
                        <input class="col-sm-7" readonly="true" value="<?= $header['des_city'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5">Note:</label>
                        <input class="col-sm-7" readonly="true" value="<?= $header['noted'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5">Create By:</label>
                        <input class="col-sm-7" readonly="true" value="<?= $header['created_user'] ?>">
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1 ">
                    <a class="btn btn-app" href="<?=
                    Url::to(['/scorecard/wsi/shippingdetail',
                        'pono' => $header['po_no'],
                    ])
                    ?>" title="Shipment Detail"><i class="fa fa-ship"></i></a>
                    <a class="btn btn-app" href="#" title="History (Under-construction)"><i class="fa fa-history"></i></a>
                </div>
            </form>
        </div>

        <?php
        $gridViewDataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $lines,
            'pagination' => ['pageSize' => 1000]
        ]);
        $columns = [
            [
                'attribute' => 'item_no',
                'label' => 'Item No',
                'format' => 'raw',
                'value' => function ($data) {
                    $image = $data['image'] ? True : False;
                    $imageIcon = $image ? '<span class="glyphicon glyphicon-picture"></span> ' : '<span class="glyphicon glyphicon-none"></span>';
                    return Html::tag('div', $imageIcon . Html::a($data['item_no'], Url::to(['/item/default/detail',
                                                'itemno' => $data['item_no']
                                    ])), ['data-toggle' => 'tooltip',
                                'data-placement' => 'auto',
                                'title' => $data['description'] . ($image ? '<br/>' . Html::img('data:image/jpg;base64,' . base64_encode($data['image']), ['style' => 'height:300px;max-width:800px;']) : ''),
                                'style' => 'cursor:default;'
                    ]);
                }
            ],
            ['attribute' => 'description'],
            ['attribute' => 'blanket_po',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data['blanket_po'], Url::to(['/overview/schedule/podetail',
                                        'pono' => $data['blanket_po'],
                    ]));
                },],
            ['attribute' => 'quantity',
                'footer' => GridTool::pageTotal($gridViewDataProvider, 'quantity')],
            ['attribute' => 'cuft'],
            ['attribute' => 'total_cuft',
                'footer' => GridTool::pageTotal($gridViewDataProvider, 'total_cuft')],
            ['attribute' => 'total_conatiner',
                'footer' => GridTool::pageTotal($gridViewDataProvider, 'total_conatiner')],
        ];
        $gridColumns = array_column($columns, 'attribute');
        echo ExportMenu::widget([
            'dataProvider' => $gridViewDataProvider,
            'columns' => $gridColumns,
            'filename' => 'Schedule Report'
        ]);
        echo GridView::widget([
            'dataProvider' => $gridViewDataProvider,
            'showFooter' => TRUE,
            'hover' => TRUE,
            'responsiveWrap' => FALSE,
            'columns' => $columns,
//            'floatHeader' => TRUE,
//            'floatHeaderOptions' => [
//                'position' => 'absolute'],
        ]);
        ?>

    </div>
<?php } else { ?>
    <h3>PO Not Found</h3>
    <?php
}?>