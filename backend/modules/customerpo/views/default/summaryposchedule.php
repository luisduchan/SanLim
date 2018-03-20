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
?>
<?php
$columns[] = ['attribute' => 'customer', 'footer' => 'Total'];
$color = ['#B8F8CA', '#F8EBB8', '#B8F0F8', '#B8DBF8', '#FFA07A', '#00FF00', '#cce0ff'];
$gridViewDataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $mainData,
    'pagination' => ['pageSize' => 1000]
        ]);
$i = 0;
foreach ($groups as $group) {
    $columns[] = ['attribute' => 'total_order_' . $group,
        'format' => 'raw',
        'value' => function ($data) use ($group, $dateType) {
            return Html::a($data['total_order_' . $group], Url::to(['/customerpo/default/custpolistforsummary',
                                'group' => $data['report_group_' . $group],
                                'customer' => $data['customer'],
                                'date_type' => $dateType,
            ]));
        },
        'footer' => Po::pageTotal($gridViewDataProvider, 'total_order_' . $group),
        'options' => ['style' => 'background-color:' . $color[$i]]
    ];
//    $columns[] = ['attribute' => 'total_order_late_' . $group,
//        'format' => 'raw',
//        'label' => 'Late in ' . $group,
//        'value' => function ($data) use ($group, $dateType) {
//            return Html::a($data['total_order_late_' . $group], Url::to(['/customerpo/default/custpolistforsummary',
//                                'group' => $data['report_group_' . $group],
//                                'customer' => $data['customer'],
//                                'date_type' => $dateType,
//                                'po_type' => 'late'
//            ]));
//        },
//        'footer' => Po::pageTotal($gridViewDataProvider, 'total_order_late_' . $group),
//        'options' => ['style' => 'background-color:' . $color[$i]]
//    ];
//    $columns[] = ['attribute' => 'total_order_notplan_' . $group,
//        'format' => 'raw',
//        'label' => 'Not Plan in ' . $group,
//        'value' => function ($data) use ($group, $dateType) {
//            return Html::a($data['total_order_notplan_' . $group], Url::to(['/customerpo/default/custpolistforsummary',
//                                'group' => $data['report_group_' . $group],
//                                'customer' => $data['customer'],
//                                'date_type' => $dateType,
//                                'po_type' => 'notplan'
//            ]));
//        },
//        'footer' => Po::pageTotal($gridViewDataProvider, 'total_order_notplan_' . $group),
//        'options' => ['style' => 'background-color:' . $color[$i]]
//    ];
    $columns[] = [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign' => 'middle',
        'template' => '{order_status}',
        'buttons' => [
            'order_status' => function ($url, $model, $key) use ($group) {
                return Html::a('<span class="glyphicon glyphicon-signal"></span>', Url::to(['/overview/schedule/postatus',
                                    'CusPOReport[customer]' => $model['cust_no'],
                                    'CusPOReport[date_type]' => $model['date_type'],
                                    'CusPOReport[date_from]' => $model['date_from_' . $group],
                                    'CusPOReport[date_to]' => $model['date_to_' . $group],
                ]));
            },
        ],
        'options' => ['style' => 'background-color:' . $color[$i]]
    ];

    $i++;
    if($i >=6){
        $i = 0;
    }
}
echo GridView::widget([
    'dataProvider' => $gridViewDataProvider,
    'showFooter' => TRUE,
    'hover' => TRUE,
    'responsiveWrap' => FALSE,
    'columns' => $columns,
    'floatHeader' => TRUE,
    'floatHeaderOptions' => [
        'position' => 'absolute'],
//    'pjax' => true,
//        'floatHeader' => true,
//        'floatOverflowContainer' => true,
//        'perfectScrollbar' => true,
//        'panel'=>[
//            'type'=>'primary',
//        ]
]);
?>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Form</h3>

    </div>
    <div class="box-body">
<?php $form = ActiveForm::begin(['id' => 'summaryposchedule', 'method' => 'get', 'action' => Url::to(['summaryposchedule']),]) ?>
        <div class="row">
            <div class="col-md-5">
<?php
echo $form->field($summaryPOF, 'reportGroup')->widget(Select2::className(), [
    'name' => 'reportGroup',
    'data' => $allGroups,
    'options' => ['placeholder' => 'Select Report Groups', 'multiple' => true],
    'pluginOptions' => [
        'tokenSeparators' => [',', ' '],
        'maximumInputLength' => 100
    ],
]);
?>
                <?= $form->field($summaryPOF, 'date_type')->dropDownList($dateTypeList) ?>
                <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
                </div>
                    <?php ActiveForm::end() ?>
                <p>Note: * is required field</p>
            </div>
        </div>
    </div>
</div>