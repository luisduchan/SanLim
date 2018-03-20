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

//use kartik\
//$this->title = 'Schedule' . ($group ? ' ' . $group: '');
?>
<?php
if ($mainData) {
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'pagination' => ['pageSize' => 1000]
    ]);
    $i = 0;
    $columns = [
            ['attribute' => 'production_line', 'footer' => 'Total'],
            ['attribute' => 'blanket'],
            ['attribute' => 'ik'],
            ['attribute' => 'report_group'],
            ['attribute' => 'assembly_date'],
            ['attribute' => 'total_container',
                'footer' => Po::pageTotal($gridViewDataProvider, 'total_container'),],
        ];
    echo ExportMenu::widget([
        'dataProvider' => $gridViewDataProvider,
        'columns' => $columns,
        'enableFormatter' => TRUE,
        'target' => ExportMenu::TARGET_BLANK,
        'filename' => 'Schedule Report_' . date('m_d_Y'),
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_PDF => false,
            ExportMenu::FORMAT_HTML => false
        ]
    ]);
    echo GridView::widget([
        'dataProvider' => $gridViewDataProvider,
        'showFooter' => TRUE,
        'hover' => TRUE,
        'responsiveWrap' => TRUE,
        'columns' => $columns,
        'floatHeader' => TRUE,
        'floatHeaderOptions' => [
            'position' => 'absolute'],
//        'pjax' => true,
    ]);
}
?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Schedule Form</h3>

    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(['id' => 'customerpo_schedule', 'method' => 'get', 'action' => Url::to(['index']),]) ?>
        <div class="row">
            <div class="col-md-5">
                <?php
                echo $form->field($scheduleF, 'reportGroup')->widget(Select2::className(), [
                    'name' => 'reportGroup',
                    'data' => $reportGroups,
                    'options' => ['multiple' => FALSE, 'value' => $mainData ? $group : strtoupper(date('M-Y')),],
//                    'pluginOptions' => [
//                        'tags' => true,
//                        'tokenSeparators' => [',', ' '],
//                        'maximumInputLength' => 10
//                    ],
                ]);
                ?>
                <?= $form->field($scheduleF, 'downLoad')->radioList(array('2003'=>'Excel 2003','2007'=>'Excel 2007')); ?>

                <div class="form-group">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
                </div>
                <?php ActiveForm::end() ?>
                <p>Note: * is required field</p>
            </div>
        </div>
    </div>
</div>

