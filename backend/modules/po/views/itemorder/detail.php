<?php

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;
use yii\web\JsExpression;
use backend\modules\common\models\GridTool;
use kartik\export\ExportMenu;

$this->title = 'Item Order';
?>

<?php
if ($mainData) {
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'pagination' => ['pageSize' => 1000]
    ]);
    $columns = [
        ['attribute' => 'po_no',
            'label' => 'PO No',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a($data['po_no'], Url::to(['/po/default/detail', 'pono' => $data['po_no'],]));
            }],
        ['attribute' => 'confirm_ETD'],
        [
            'attribute' => 'item_no',
            'label' => 'Item No',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a($data['item_no'], Url::to(['/overview/schedule/itemdetail',
                                    'itemno' => $data['item_no']
                ]));
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
                /*
                  $url = 'index.php?r=item/ajax/getitemnoselect2';
                  echo $form->field($queryByItemForm, 'item_nos')->widget(Select2::className(), [
                  //'data' => ['a' => 'b', 'c' => 'd'],
                  'options' => [
                  'placeholder' => 'Select Report Groups',
                  'multiple' => true
                  ],
                  'pluginOptions' => [
                  'tags' => true,
                  'tokenSeparators' => [',', ' '],
                  'maximumInputLength' => 10,
                  'ajax' => [
                  'url' => $url,
                  'dataType' => 'json',
                  'data' => new JsExpression('function(params) { return {item_no:params.term}; }'),
                  'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                  'templateResult' => new JsExpression('function(city) { return city.text; }'),
                  'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                  ]
                  ],
                  ])->label('Item No'); */
                ?>
                <?=
                $form->field($queryByItemForm, 'item_no'
                )->textInput([
                    'type' => 'text'
                ])->label('Match Item No')
                ?>
            </div>
            <div class="col-md-5 col-md-offset-1 ">
                <?=
                $form->field($queryByItemForm, 'description'
                )->textInput([
                    'type' => 'text'
                ])->label('Description')
                ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
        </div>
        <?php ActiveForm::end() ?>
        <p>Note: * is required field</p>
    </div>
</div>
