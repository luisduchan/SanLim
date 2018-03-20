
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\widgets\Breadcrumbs;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use miloschuman\highcharts\Highcharts;
use yii\grid\GridView;
use brussens\bootstrap\select\Widget as Select;
use yii\helpers\Url;
use backend\modules\po\models\Po;

$this->title = 'PO SUMMARY REPORT';
echo Breadcrumbs::widget([
    'itemTemplate' => "<li><i>{link}</i></li>\n", // template for all links
    'links' => [
//            [
//            'label' => 'Inventory Report',
//            'url' => Yii::$app->getHomeUrl() . '?r=inventory_report',
//            'template' => "<li><b>{link}</b></li>\n", // template for this link only
//        ],
            ['label' => $this->title],
    ],
]);

//
?>
<h1>PO SUMMARY REPORT</h1>
<?php
if ($showChart) {
    $series = [];
    $arOutStQty = array_values(array_column($chartData, 'total_outst_qty'));
    $series[] = [
        'type' => 'column',
        'name' => 'Out Standing',
        'data' => array_map(
                function($value) {
                    return (float) $value;
                }, $arOutStQty),
    ];

    $arWaitingQty = array_values(array_column($chartData, 'total_waiting_qty'));
    $series[] = [
        'type' => 'column',
        'name' => 'Waiting',
        'data' => array_map(
                function($value) {
                    return (float) $value;
                }, $arWaitingQty),
    ];

    $arReceiptQty = array_values(array_column($chartData, 'total_receipt_qty'));
    $series[] = [
        'type' => 'column',
        'name' => 'Received',
        'data' => array_map(
                function($value) {
                    return (float) $value;
                }, $arReceiptQty),
    ];
    ?>
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Chart</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="chart">
                <?php
                echo Highcharts::widget([
                    'scripts' => [
                        'highcharts-3d',
                    ],
                    'options' => [
                        'credits' => ['enabled' => FALSE],
                        'title' => ['text' => 'PO Status'],
                        'xAxis' => [
                            'categories' => $arrayPo
                        ],
                        'yAxis' => [
                            'title' => ['text' => 'Quantity']
                        ],
                        'series' => $series,
                        'plotOptions' => [
                            'series' => [
                                'stacking' => 'normal'
                            ]
                        ],
                    ]
                ]);
                ?>

            </div>
        </div>

        <!-- /.box-body -->
    </div>






    <?php
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $chartData,

        'pagination' => ['pageSize' => 1000]
    ]);
    ?>
    <!--begin detail-->
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Detail</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body table-responsive no-padding">
            <?=
//            $po = new Po();
            GridView::widget([
                'dataProvider' => $gridViewDataProvider,
                'showFooter'=>TRUE,
                'tableOptions'=>['class'=>'table table-hover'],
                'columns' => [
                    'vendor_name',
                    ['attribute' => 'po_no', 'footer'=> 'Total'],
                    ['attribute' => 'total_qty',
                        'format' => 'decimal',
                        'footer'=> Po::pageTotal($gridViewDataProvider, 'total_qty')],
                    ['attribute' => 'total_receipt_qty',
                        'format' => 'decimal',
                        'footer'=> Po::pageTotal($gridViewDataProvider, 'total_receipt_qty')],
                    ['attribute' => 'total_waiting_qty',
                        'format' => 'decimal',
                        'footer'=> Po::pageTotal($gridViewDataProvider, 'total_waiting_qty')],
                    ['attribute' => 'total_outst_qty',
                        'format' => 'decimal',
                        'footer'=> Po::pageTotal($gridViewDataProvider, 'total_outst_qty')],
//                    'total_receipt_qty:decimal',
//                    'total_waiting_qty:decimal',
//                    'total_outst_qty:decimal',
                    'date',
                    'request_receipt_date_to',
                ]
            ])
            ?>
        </div>
    </div>
    <!--end detail-->
<?php } //end if show chart ?>
<!--begin form-->
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Form</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(['id' => 'po_summary_query', 'method' => 'get','action' => Url::to(['query']),]) ?>
        <div class="row">
            <div class="col-md-5">
                <?= $form->field($poSummaryForm, 'vendor')->dropDownList(['100854' => 'CTY TNHH  SOKLUCO', '100912' => 'CTY TNHH MTV TRÍ THỊNH', '101070' => 'CTY TNHH HƯNG NHƠN'], ['prompt' => 'Select Vendor']) ?>
                <?php
                $url = '/' . Yii::$app->urlManager->createUrl('./../frontend') . 'index.php?r=inventory_report/moutput/getitemno';
                echo $form->field($poSummaryForm, 'item_no')
                        ->widget(AutoComplete::classname(), ['clientOptions' => [
                                'source' => new JsExpression("function(request, response) {
                            $.getJSON('" . $url . "', {
                                item_no: request.term
                            }, response);
                        }"),
                            ], 'options' => ['class' => 'form-control']])->label('Item No');
                ?>

                <?= $form->field($poSummaryForm, 'purchaser')->widget(Select::className(), [
                            'options' => ['data-live-search' => 'true', 'promt' => 'aaa'],
                            'items' => ['0' => 'Select',
                                        'LINHDHT' => 'Dau Ha Tung Linh', 
                                        'ANHTMT' => 'Ho Thi Mong Thien An', 
                                        'TRANGHT' => 'Huynh Thi Trang']]) ?>
                

                <?= $form->field($poSummaryForm, 'po_status')->dropDownList(['2' => 'Done', '1' => 'Open'], ['prompt' => 'Select Status']) ?>


            </div>
            <div class="col-md-5 col-md-offset-1 ">
                <div class="well well-sm">
                    <?= $form->field($poSummaryForm, 'date_type')->dropDownList($dateType) ?>
                    <?=
                            $form->field($poSummaryForm, 'date_from')
                            ->widget(DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd', 'options' => ['class' => 'form-control']])->label('Date From *')
                    ?>
                    <?=
                            $form->field($poSummaryForm, 'date_to')
                            ->widget(DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd', 'options' => ['class' => 'form-control']])->label('Date To *')
                    ?>
                </div>


            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
        </div>
        <?php ActiveForm::end() ?>
        <p>Note: * is required field</p>
    </div>
</div>
<!--end form-->
