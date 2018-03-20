<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use brussens\bootstrap\select\Widget as Select;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\jui\DatePicker;
use yii\data\Sort;
use backend\modules\common\models\GridTool;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use miloschuman\highcharts\Highcharts;


$this->title = 'Blanket PO';

?>

<?php
if ($mainData) {

    $columns = [
        [
            'attribute' => 'blanket_no',
            'label' => 'Blanket',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a($data['blanket_no'], Url::to(['/overview/schedule/podetail',
                    'pono' => $data['blanket_no']
                ]));
            }
        ],
        // ['attribute' => 'related_order'],
        ['attribute' => 'cont_adjmt'],
        ['attribute' => 'ik'],
        ['attribute' => 'production_line'],
        ['attribute' => 'cutting_no'],
        ['attribute' => 'cust_name'],
        ['attribute' => 'cust_no'],
        ['attribute' => 'finished'],
        ['attribute' => 'assembly_date'],
        ['attribute' => 'cofirmed_etd'],
        ['attribute' => 'order_date'],
    ];
    $gridColumns = array_column($columns, 'attribute');
    
    $sort = new Sort([
        'attributes' => $gridColumns,
    ]);

    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'sort' => $sort,
        'pagination' => ['pageSize' => 100]
    ]);

    
    echo ExportMenu::widget([
        'dataProvider' => $gridViewDataProvider,
        'columns' => $gridColumns,
        'filename' => 'BlanketPo'
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
}
?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title"Blanket PO Form</h3>
        </div>
        <div class="box-body">
            <?php $form = ActiveForm::begin(['id' => 'blanket_po_query', 'method' => 'get', 'action' => Url::to(['index']),]) ?>
            <div class="row">
                <div class="col-md-5">
                    <?php
                    $url = 'index.php?r=blanketpo/ajax/getblanketno';
                    echo $form->field($queryFrom, 'blanket_name')->widget(Select2::className(), [

                        'options' => [
                            'placeholder' => 'Select Blanket',
                            'multiple' => false
                        ],
                        'pluginOptions' => [
                            'tags' => true,
                            'allowClear' => true,
                            'tokenSeparators' => [',', ' '],
                            'ajax' => [
                                'url' => $url,
                                'dataType' => 'json',
                                'delay' => 250,
                                'data' => new JsExpression('function(params) { return {blanketno:params.term}; }'),

                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(item) { return (item.text); }'),
                            'templateSelection' => new JsExpression('function (item) { return (item.id); }'),
                        ],
                    ])->label('Blanket');
                    ?>
                    <?php
                    $url = 'index.php?r=item/ajax/getitemnoselect2';
                    echo $form->field($queryFrom, 'item_nos')->widget(Select2::className(), [

                        'options' => [
                            'placeholder' => 'Select Item No',
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'tags' => false,
                            'allowClear' => true,
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

                    <?php
                    $url = 'index.php?r=item/ajax/getitemnoselect2';
                    echo $form->field($queryFrom, 'customers')->widget(Select2::className(), [
                        'data' => $customerList,
                        'options' => [
                            'placeholder' => 'Select Customer',
                            'multiple' => false
                        ],
                        'pluginOptions' => [
                            'tags' => true,
                            'allowClear' => true,
                            'tokenSeparators' => [',', ' '],
                            'maximumInputLength' => 10,
                        ],
                    ])->label('Customer');
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

