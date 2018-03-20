
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\widgets\Breadcrumbs;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use nex\chosen\Chosen;

$this->title = 'WOOD Output Report';
echo Breadcrumbs::widget([
    'itemTemplate' => "<li><i>{link}</i></li>\n", // template for all links
    'links' => [
            [
            'label' => 'Inventory Report',
            'url' => Yii::$app->getHomeUrl() . '?r=inventory_report',
            'template' => "<li><b>{link}</b></li>\n", // template for this link only
        ],
            ['label' => $this->title],
    ],
]);

//
//$this->params['breadcrumbs'][] = ['label' => 'Inventory Report', 'url' => ['inventory_report']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<h1>WOOD Output Report</h1>

<?php $form = ActiveForm::begin(['id' => 'material_output',]) ?>
<div class="row">
    <div class="col-md-5">
        <?= $form->field($material_out, 'location')->dropDownList($location, ['prompt' => 'Select Loaction']) ?>
        <?= $form->field($material_out, 'item_cat')->dropDownList($item_cat, ['prompt' => 'Select Item Category'])->label('Item Category') ?>
        <?php
        $url = 'index.php?r=inventory_report/moutput/getitemno';
//        echo $form->field($material_out, 'item_no')
//                ->widget(
//    Chosen::className(), [
//        'items' => [1 => 'First item', 2 => 'Second item', 3 => 'Third item'],
//        'disableSearch' => 5, // Search input will be disabled while there are fewer than 5 items
//        'clientOptions' => [
//            'search_contains' => true,
//            'single_backstroke_delete' => false,
//        ]
//]);
        echo $form->field($material_out, 'item_no')
                ->widget(AutoComplete::classname(), ['clientOptions' => [
                        'source' => new JsExpression("function(request, response) {
                            $.getJSON('" . $url . "', {
                                item_no: request.term
                            }, response);
                        }"),
                    ], 'options' => ['class' => 'form-control']])->label('Item No');
        ?>
        <?= $form->field($material_out, "pcs_metric")->checkbox(['value' => 1, 'label' => 'Convert PCS to Metric (it use for PB, WDPL, MDF ...)']); ?>
        <?= $form->field($material_out, "not_include_component")->checkbox(['value' => 1, 'label' => 'Don`t Inclue Components']); ?>

    </div>
    <div class="col-md-5 col-md-offset-1 ">
        <?=
                $form->field($material_out, 'date_from')
                ->widget(DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd', 'options' => ['class' => 'form-control']])->label('Date From *')
        ?>
        <?=
                $form->field($material_out, 'date_to')
                ->widget(DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd', 'options' => ['class' => 'form-control']])->label('Date To *')
        ?>
        <?= $form->field($material_out, "detail_info")->checkbox(['value' => 1]); ?>
        <?= $form->field($material_out, "generate_chart")->checkbox(['value' => 1]); ?>
        <div class="row">
            <div class="col-md-12 col-md-offset-1 ">
                <?= $form->field($material_out, "chart_gorup_by_location")->checkbox(['value' => 1, 'label' => 'Group by Location']); ?>
                <?= $form->field($material_out, "chart_total_line")->checkbox(['value' => 1, 'label' => 'Total Line']); ?>
            </div>
        </div>

    </div>
</div>

<div class="form-group">
    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
</div>
<?php ActiveForm::end() ?>
<p>Note: * is required field</p>

