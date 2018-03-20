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

$this->title = 'Scheduel Base On Report Group';
?>
<?php if ($mainData) { ?>

    <?php
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'pagination' => ['pageSize' => 1000]
    ]);
    ?>
    <?php
    $series = [];
    $gridColumns[] = 'customer';


    $columns[] = ['attribute' => 'customer', 'footer' => 'Total'];
    $color = ['#B8F8CA', '#F8EBB8', '#B8F0F8', '#B8DBF8'];
    $i = 0;
    foreach ($groups as $group) {
        $arScheduled = array_values(array_column($mainData, 'total_sheduled_' . $group));
        $series[] = [
            'type' => 'column',
            'name' => 'Total Sheduled ' . $group,
            'data' => array_map(
                    function($value) {
                        return (float) $value;
                    }, $arScheduled),
            'color' => $color[$i],
        ];
        $arOrdered = array_values(array_column($mainData, 'total_ordered_' . $group));
        $series[] = [
            'type' => 'column',
            'name' => 'Total Ordered ' . $group,
            'data' => array_map(
                    function($value) {
                        return (float) $value;
                    }, $arOrdered),
//            'color' => $color[$i],
        ];
        $gridColumns[] = 'total_ordered_' . $group;
        $gridColumns[] = ['attribute' => 'total_sheduled_' . $group, 'options' => ['style' => 'background-color:blue']];

        $report_group = 'report_group_' . $group;
        $columns[] = ['attribute' => 'total_ordered_' . $group,
            'format' => 'raw',
//                'value' => function ($data) use ($group) {
//                    return Html::a($data['total_ordered_' . $group], Url::to(['/overview/schedule/polist',
//                                        'group' => $data['report_group_' . $group],
//                                        'customer' => $data['customer'],
//                    ]));
//                },
            'footer' => Po::pageTotal($gridViewDataProvider, 'total_ordered_' . $group),
            'options' => ['style' => 'background-color:' . $color[$i]]
        ];
        $columns[] = ['attribute' => 'total_sheduled_' . $group,
            'format' => 'raw',
            'value' => function ($data) use ($group) {
                return Html::a($data['total_sheduled_' . $group], Url::to(['/overview/schedule/polist',
                                    'group' => $data['report_group_' . $group],
                                    'customer' => $data['customer'],
                ]));
            },
            'footer' => Po::pageTotal($gridViewDataProvider, 'total_sheduled_' . $group),
            'options' => ['style' => 'background-color:' . $color[$i]]];
        
        $i++;
    }
    ?>
    <!--begin detail-->
    <!--    <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Detail</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>-->
    <div class="box-body table-responsive no-padding">
        <?php
        echo ExportMenu::widget([
            'dataProvider' => $gridViewDataProvider,
//        'exportConfig' => [
//            ExportMenu::FORMAT_EXCEL_X => false
//        ],
            'columns' => $gridColumns,
            'enableFormatter' => TRUE,
            'target' => ExportMenu::TARGET_BLANK,
            'filename' => 'Schedule Report'
        ]);
        echo GridView::widget([
            'dataProvider' => $gridViewDataProvider,
            'autoXlFormat' => false,
            'export' => [
                'fontAwesome' => true,
                'showConfirmAlert' => True,
                'target' => GridView::TARGET_BLANK,
                'enableFormatter' => True,
                'filename' => 'Schedule Report'
            ],
            'showFooter' => TRUE,
            'hover' => TRUE,
            'responsiveWrap' => FALSE,
            'columns' => $columns,
            'floatHeader' => TRUE,
            'floatHeaderOptions' => [
                'position' => 'absolute'],
//            'pjax' => true,
//        'floatHeader' => true,
//        'floatOverflowContainer' => true,
//        'perfectScrollbar' => true,
//        'panel'=>[
//            'type'=>'primary',
//        ]
        ]);
        $arrayCus = array_values(array_column($mainData, 'customer'));
        ?>
    </div>
    <!--</div>-->
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Chart Comparing Used, Stock and Outstanding </h3>

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
                        'title' => ['text' => 'Planning Chart'],
                        'xAxis' => [
                            'categories' => $arrayCus
                        ],
                        'yAxis' => [
                            'title' => ['text' => 'Container']
                        ],
                        'series' => $series,
                        'plotOptions' => [
//                            'series' => [
//                                'stacking' => 'normal'
//                            ],
                            'column' => ['dataLabels' => [
                                    'enabled' => true,
                                    'format' => '{point.y:,.0f}'
                                ]]
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>

        <!-- /.box-body -->
    </div>
<?php }; ?>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Form</h3>

    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(['id' => 'overview_schedule', 'method' => 'get', 'action' => Url::to(['index']),]) ?>
        <div class="row">
            <div class="col-md-5">
                <?php
                echo $form->field($scheduleF, 'reportGroup')->widget(Select2::className(), [
                    'name' => 'reportGroup',
                    'data' => $reportGroups,
                    'options' => ['placeholder' => 'Select Report Groups', 'multiple' => true],
                    'pluginOptions' => [
//                        'tags' => true,
                        'tokenSeparators' => [',', ' '],
                        'maximumInputLength' => 10
                    ],
                ]);
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