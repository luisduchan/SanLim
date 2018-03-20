<?php

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;
use yii\web\JsExpression;
use backend\modules\common\models\GridTool;
use kartik\export\ExportMenu;
use yii\data\Sort;
use miloschuman\highcharts\Highcharts;
use yii\jui\DatePicker;

$this->title = 'Item Order';
?>


<?php
if ($mainData) {
    $series = [];
    foreach ($mainData as $row) {
        $data = [];
        foreach ($months as $month) {
            $data[] = $row[$month];
        }
        $series[] = [
            'type' => 'line',
            'name' => $row['group_by'],
            'data' => $data,
        ];
    }
    ?>
    <div class="chart">
        <?php
        echo Highcharts::widget([
            'options' => [
                'chart' => [
                    'height' => 600,
                ],
                'title' => ['text' => 'Order Status'],
                'xAxis' => [
                    'categories' => $months
                ],
                'yAxis' => [
                    'title' => ['text' => 'Container']
                ],
                'series' => $series,
                'plotOptions' => [
                    'line' => ['dataLabels' => ['enabled' => TRUE]]
                ],
//                        'colors' => ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9',
//   '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1','#00FF00','#008000','#00FFFF','#0000FF','#000080','#FF00FF'],
            ]
        ]);
        ?>

    </div>
    <?php
    $sortColumn = [];
    foreach ($months as $month) {
        $sortColumn [] = $month;
    }
    $sortColumn[] = 'total';
    $sort = new Sort([
        'attributes' => $sortColumn,
    ]);
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'sort' => $sort,
        'pagination' => ['pageSize' => 100]
    ]);

    $columns = [
        [
            'attribute' => 'group_by',
            'label' => 'Item No',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a($data['group_by'], Url::to(['/item/default/detail',
                                    'itemno' => $data['group_by']
                ]));
            }
        ],
    ];
    foreach ($months as $month) {
        $columns[] = ['attribute' => $month,
            'footer' => GridTool::pageTotal($gridViewDataProvider, $month)];
    }
    $columns[] = ['attribute' => 'total',
        'footer' => GridTool::pageTotal($gridViewDataProvider, 'total')];



    $gridColumns = array_column($columns, 'attribute');
    echo ExportMenu::widget([
        'dataProvider' => $gridViewDataProvider,
        'columns' => $gridColumns,
        'filename' => 'Schedule Report'
    ]);
//    $gridViewDataProvider->sort = [
//        'attributes' => [
//            array_column($columns, 'attribute')
//        ],
//    ];
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
}
?>


<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Query PO By Item Form</h3>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(['id' => 'po_query_by_item', 'method' => 'get', 'action' => Url::to(['index']),]) ?>
        <div class="row">
            <div class="col-md-5">
                <?php
                $url = 'index.php?r=item/ajax/getitemnoselect2';
                echo $form->field($queryByItemForm, 'item_nos')->widget(Select2::className(), [

                    'options' => [
                        'placeholder' => 'Select Item No',
                        'multiple' => true
                    ],
                    'pluginOptions' => [
                        'tags' => true,
                        'tokenSeparators' => [',', ' '],
                        'ajax' => [
                            'url' => $url,
                            'dataType' => 'json',
                            'delay' => 250,
                            'data' => new JsExpression('function(params) { return {item_no:params.term}; }'),

                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(item) { return (item.text); }'),
                        'templateSelection' => new JsExpression('function (item) { return (item.id); }'),
                    ],
                ])->label('Item No');
                ?>
                <?=
                $form->field($queryByItemForm, 'item_no'
                )->textInput([
                    'type' => 'text'
                ])->label('Match Item No')
                ?>
                <?php
                $url = 'index.php?r=item/ajax/getitemnoselect2';
                echo $form->field($queryByItemForm, 'customers')->widget(Select2::className(), [
                    'data' => $customerList,
                    'options' => [
                        'placeholder' => 'Select Customer',
                        'multiple' => true
                    ],
                    'pluginOptions' => [
                        'tags' => true,
                        'tokenSeparators' => [',', ' '],
                        'maximumInputLength' => 10,
                    ],
                ])->label('Customer');
                ?>
                <?= $form->field($queryByItemForm, 'group_by_item_group')->checkbox(); ?>
                <?= $form->field($queryByItemForm, 'unit_quantity')->checkbox(['label' => 'Unit by Quantity']); ?>
            </div>
            <div class="col-md-5 col-md-offset-1 ">
                <?=
                $form->field($queryByItemForm, 'description'
                )->textInput([
                    'type' => 'text'
                ])->label('Description')
                ?>
                <?=
                        $form->field($queryByItemForm, 'date_from')
                        ->widget(DatePicker::classname(), [
                            'dateFormat' => 'yyyy-MM-dd',
                            'options' => [
                                'class' => 'form-control'
                            ]
                        ])->label('Date From *')
                ?>
                <?=
                        $form->field($queryByItemForm, 'date_to')
                        ->widget(DatePicker::classname(), [
                            'dateFormat' => 'yyyy-MM-dd',
                            'options' => [
                                'class' => 'form-control'
                            ]
                        ])->label('Date To *')
                ?>
                <?= $form->field($queryByItemForm, 'date_type')->dropDownList($dateTypeList) ?>
            </div>

        </div>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
        </div>
        <?php ActiveForm::end() ?>
        <p>Note: * is required field</p>
    </div>
</div>
