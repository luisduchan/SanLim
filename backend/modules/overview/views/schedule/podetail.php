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
$this->title = 'Blanket PO';
?>
<?php if($header){?>
<h3><?=$header['po_no']?></h3>
<div class="box-body">
    <div class="row">
        <form class="form-horizontal">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="col-sm-5">Customer Name:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['cust_name'] . ' (' . $header['cust_no'] . ')' ?>">
                </div>

                <div class="form-group">
                    <label class="col-sm-5">IK:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['ik'] ?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Report Group:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['report_group'] ?>"/>
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Related Order:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['related_order'] ?>"/>
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Container Adj:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['cont_adjmt'] ?>"/>
                </div>


            </div>
            <div class="col-md-4 col-md-offset-1 ">
                <div class="form-group">
                    <label class="col-sm-5">Order Date:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['order_date'] ?>">

                </div>

                <div class="form-group">
                    <label class="col-sm-5">Confirm Ship Date:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['request_ship_date'] ?>"/>
                </div>

                <div class="form-group">
                    <label class="col-sm-5">Assembling Date:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['ass_date'] ?>"/>
                </div>

                <div class="form-group">
                    <label class="col-sm-5">Location:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $header['location_code'] ?>"/>
                </div>
            </div>
            <div class="col-md-2 col-md-offset-1 ">
                <?=
                Html::a('<i class="glyphicon glyphicon-list-alt"></i>', Url::to(['/po/po/podetailblanket',
                            'blanket' => $header['po_no']
                        ]), ['class' => ['btn', 'btn-app'], 'title' => 'PO detail (Item)'])
                ?>

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
        ['attribute' => 'quantity',
            'footer' => GridTool::pageTotal($gridViewDataProvider, 'quantity')],
        ['attribute' => 'cuft'],
        ['attribute' => 'total_cuft',
            'footer' => GridTool::pageTotal($gridViewDataProvider, 'total_cuft')],
        ['attribute' => 'total_conatiner',
            'footer' => GridTool::pageTotal($gridViewDataProvider, 'total_conatiner')],
        ['attribute' => 'qty_shipped',
            'footer' => GridTool::pageTotal($gridViewDataProvider, 'qty_shipped')]
    ];
    echo ExportMenu::widget([
        'dataProvider' => $gridViewDataProvider,
        'columns' => array_column($columns, 'attribute'),
        'filename' => 'Blanket PO Line _ '. $header['po_no'],
    ]);
    echo GridView::widget([
        'dataProvider' => $gridViewDataProvider,
        'showFooter' => TRUE,
        'hover' => TRUE,
        'responsiveWrap' => FALSE,
        'columns' => $columns,
    ]);
    ?>

</div>
<?php }else{ ?>
<h3>No Blanket Found</h3>
<?php } ?>