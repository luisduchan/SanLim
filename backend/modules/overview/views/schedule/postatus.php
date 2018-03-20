<?php

//use yii\grid\GridView;
use yii\helpers\Html;
use yii\data\SqlDataProvider;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
//use kartik\widgets\Select2;
use kartik\select2\Select2;
use kartik\grid\GridView;
use backend\modules\po\models\Po;
use kartik\export\ExportMenu;
use miloschuman\highcharts\Highcharts;
use brussens\bootstrap\select\Widget as Select;
use yii\jui\DatePicker;

//use kartik\
?>

<!--begin detail-->
<?php if(!empty($poStatusData)){?>
<?php
$series = [];
foreach($poStatusData as $row){
    $series[] = [
        'type' => 'column',
        'name' => $row['product_group'],
        'data' => array_values(array_slice($row, 1,count($row)-1)),
    ];
}
//var_dump($series);die();
$gridViewDataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $poStatusData,
    'pagination' => ['pageSize' => 1000]
        ]);
?>
<div class="chart">
                <?php
                echo Highcharts::widget([
//                    'scripts' => [
//                        'highcharts-3d',
//                    ],
                    
                    'options' => [
//                        'credits' => ['enabled' => FALSE],
                        'title' => ['text' => 'Order Status'],
                        'xAxis' => [
                            'categories' => $header
                        ],
                        'yAxis' => [
                            'title' => ['text' => 'Container']
                        ],
                        'series' => $series,
//                        'colors' => ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', 
//   '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1','#00FF00','#008000','#00FFFF','#0000FF','#000080','#FF00FF'],
                    ]
                ]);
                ?>

            </div>
<div class="box-body table-responsive no-padding">
    <?php
    $columns[] = ['attribute' => 'product_group',
                        'footer'=> 'Total'];
    for ($i = 0; $i < count($header); $i++) {
        $columns[] = ['attribute' => $header[$i],
                        'format' => 'decimal',
                        'footer'=> Po::pageTotal($gridViewDataProvider, $header[$i])];
    };
    ?>
    <?=
    GridView::widget([
        'dataProvider' => $gridViewDataProvider,
        'showFooter' => TRUE,
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => $columns,
        'floatHeader' => TRUE,
        'floatHeaderOptions' => [
            'position' => 'absolute'
        ],
        'resizableColumns' => FALSE,
        'resizableColumnsOptions' => ['resizeFromBody' => true],
        'persistResize' => FALSE,
    ])
    ?>
</div>

<!--end detail-->
<?php } ?> <!--end if-->
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Form</h3>

    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(['id' => 'overview_schedule', 'method' => 'get', 'action' => Url::to(['postatus'])]) ?>
        <div class="row">
            <div class="col-md-5">
                <?=
                $form->field($cusPOReportF, 'customer')->widget(Select::className(), [
                    'options' => ['data-live-search' => 'true', 'promt' => 'Select'],
                    'items' => array_merge([0 => 'Select Cusotmer'], $customerList)])
                ?>
                <?= $form->field($cusPOReportF, 'date_type')->dropDownList($dateTypeList) ?>
                <?=
                        $form->field($cusPOReportF, 'date_from')
                        ->widget(DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd', 'options' => ['class' => 'form-control']])->label('Date From *')
                ?>
                <?=
                        $form->field($cusPOReportF, 'date_to')
                        ->widget(DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd', 'options' => ['class' => 'form-control', 'ignoreReadonly'=>true,]])->label('Date To *')
                ?>
                
                <div class="form-group">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
                </div>
                <?php ActiveForm::end() ?>
                <p>Note: * is required field</p>
            </div>
        </div>
    </div>
</div>