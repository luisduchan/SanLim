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
$this->title = 'Schedule' . ($group ? ' ' . $group: '');
?>
<?php
if ($mainData) {
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'pagination' => ['pageSize' => 1000]
    ]);
    $i = 0;
    $columns[] = ['attribute' => 'cus_name', 'footer' => 'Total'];
    foreach ($months as $month) {
        $columns[] = ['attribute' => $month,
            'label' => $month,
//            'label' => Html::a($month,'gogel.com'),
            'format' => 'raw',
            'value' => function ($data) use ($month, $group) {
                return Html::a($data[$month], Url::to(['/customerpo/schedule/polist',
                                    'group' => $group,
                                    'customer' => $data['cus_name'],
//                                    'date_type' => $dateType,
                                    'month' => $month,
                ]));
            },
            'footer' => Po::pageTotal($gridViewDataProvider, $month),
        ];
        $i++;
    }
    $columns[] = ['attribute' => 'total',
        'format' => 'raw',
        'footer' => Po::pageTotal($gridViewDataProvider, 'total'),
    ];
    echo ExportMenu::widget([
        'dataProvider' => $gridViewDataProvider,
        'columns' => $columns,
        'enableFormatter' => TRUE,
        'target' => ExportMenu::TARGET_BLANK,
        'filename' => 'Total Order Report_' . date('m_d_Y'),
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
        'responsiveWrap' => FALSE,
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

                <div class="form-group">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
                </div>
                <?php ActiveForm::end() ?>
                <p>Note: * is required field</p>
            </div>
        </div>
    </div>
</div>
