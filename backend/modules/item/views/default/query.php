<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use brussens\bootstrap\select\Widget as Select;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;
use backend\modules\po\models\Po;
use kartik\select2\Select2;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use nex\chosen\Chosen;
use yii\data\Sort;

$this->title = 'Query Items';

$url = 'index.php?r=item/ajax/getitemno';
ini_set('memory_limit', '512M');
?>

<?php
if ($mainData) {
    $sort = new Sort([
        'attributes' => [
            'item_no',
            'description',
            'image',
            'abbreviation',
            'cuft',
            'product_group_code',
        ],
    ]);
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'sort' => $sort,
        'pagination' => ['pageSize' => $numberPerPage]
    ]);
    $columns = [
        ['attribute' => 'image',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a(Html::img('data:image/jpg;base64,' . base64_encode($data['image']), ['style' => 'max-height:200px;max-width:200px;']), Url::to(['/item/default/detail',
                                    'itemno' => $data['item_no'],
                ]));
            }],
        ['attribute' => 'item_no',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a($data['item_no'], Url::to(['/item/default/detail',
                                    'itemno' => $data['item_no'],
                ]));
            },],
        ['attribute' => 'description'],
        ['attribute' => 'abbreviation'],
        ['attribute' => 'cuft'],
        ['attribute' => 'product_group_code'],
    ];
            $gridColumns = ['item_no', 'description','abbreviation','cuft','product_group_code'];
    echo ExportMenu::widget([
    'dataProvider' => $gridViewDataProvider,
    'columns' => $gridColumns,
    'filename' => 'Schedule Report'
    ]);
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

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Item Query</h3>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(['id' => 'item_query', 'method' => 'get', 'action' => Url::to(['query']),]) ?>
        <div class="row">
            <div class="col-md-5">
                <?=
                        $form->field($itemQueryF, 'itemNo')
                        ->widget(AutoComplete::classname(), ['clientOptions' => [
                                'source' => new JsExpression("function(request, response) {
                                    customer_no = $('#itemqueryf-customer').val();
                        $.getJSON('" . $url . "', {
                            item_no: request.term,customer_no: customer_no
                        }, response);
                    }"),
                            ], 'options' => ['class' => 'form-control']])->label('Item No');
                ?>
                <?=
                $form->field($itemQueryF, 'customer')->widget(Select::className(), [
                    'options' => ['data-live-search' => 'true', 'promt' => 'Select'],
                    'items' => array_merge([0 => 'Select Cusotmer'], $customerList)]);
                ?>
                <?=$form->field($itemQueryF, 'numberPerPage'
                                )->textInput([
                                 'type' => 'number'
                            ])->label('Number Item/Page')?>
                <div class="form-group">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
                </div>
                <?php ActiveForm::end() ?>
                <p>Note: * is required field</p>
            </div>
        </div>
    </div>
</div>
