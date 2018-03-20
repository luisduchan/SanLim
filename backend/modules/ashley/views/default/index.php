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
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\jui\AutoComplete;


$this->title = 'Ashley';

$url = 'index.php?r=ashley/ajax/getblanketno';

?>

<?php
/*
$sort = new Sort([
    'attributes' => [
        'item_code',
        'description',
        'blanket_qty_total',
    ],
]);
$gridViewDataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $mainData,
    'sort' => $sort,
    'pagination' => ['pageSize' => 1000]
        ]);
$columns = [
   
    ['attribute' => 'item_code'],  
    ['attribute' => 'description'],
    ['attribute' => 'blanket_qty_total'],
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
*/
?>

<?php
if ($inventoryData) {

    $sort = new Sort([
    'attributes' => [ 
        'item_code',
        'description',
        'inventory_quantity',
        
    ],
    ]);

    $gridViewDataProvider = new \yii\data\ArrayDataProvider([

        'allModels' => $inventoryData,
        'sort' =>$sort,
        'pagination' => ['pageSize' => 200]
    ]);


    $columns = [
    ['attribute' => 'item_code'],  
    ['attribute' => 'description'],
    ['attribute' => 'inventory_quantity'],
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
}

    
?>

<?php
/* Highcharts::widget([
    'id' => 'wsi_score_card',
    'scripts' => [
        'highcharts-3d',
        'modules/exporting',
        'modules/offline-exporting',
    ],
    'options' => [
        'chart' => [
            'type' => 'pie',
            'options3d' => [
                'enabled' => true,
                'alpha' => 45,
                'beta' => 0,
            ],
            'margin' => 0,
        ],
        'exporting' => [
            'enabled' => true,
            'filename' => 'Lazboy Scorecard'
        ],
        'title' => ['text' => 'Lazboy Scorecard'],
        'plotOptions' => [
            'pie' => [
                'allowPointSelect' => true,
                'cursor' => 'pointer',
                'depth' => 35,
                'dataLabels' => [
                    'enabled' => true,
                    'format' => '<b>{point.name}</b>: {point.y:.0f} ({point.percentage:.1f}%)',
                ],
            ],
        ],
        'series' => [$series],
        'credits' => [
            'enabled' => false
        ],
    ]
]);
*/
?>




<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Inventory Quantity Total</h3>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(['id' => 'customer_performance', 'method' => 'get', 'action' => Url::to(['index']),]) ?>
        <div class="row">
            <div class="col-md-5">   
                <?=
                        $form->field($ashleyForm, 'blanketNo')
                        ->widget(AutoComplete::classname(), ['clientOptions' => [
                                'source' => new JsExpression("function(request, response) {
                        $.getJSON('" . $url . "', {
                            blanket_no: request.term
                        }, response);
                    }"),
                            ], 'options' => ['class' => 'form-control']])->label('Blanket No*');
                ?>

                <?=
                $form->field($ashleyForm, 'customer')->widget(Select::className(), [
                    'options' => ['data-live-search' => 'true'],
                    'items' => array('C01000' => 'ASHLEY', 'C47000' => 'WANVOG')]);
                ?>
                <div class="form-group">
                    <?=
                    Html::submitButton('Submit', [
                        'class' => 'btn btn-primary',
                        'name' => 'submit-button'])
                    ?>
                </div>
                <?php ActiveForm::end() ?>
                <p>Note: * is required field</p>
            </div>
        </div>
    </div>
</div>