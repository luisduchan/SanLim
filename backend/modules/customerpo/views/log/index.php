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
$this->title = 'Logs';


if ($mainData) {
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $mainData,
        'pagination' => ['pageSize' => 200]
    ]);
    $i = 0;
    $columns = [
        ['attribute' => 'purchase_order_no'],
        ['attribute' => 'customer_name'],
        ['attribute' => 'total_container'],
        ['attribute' => 'previous_total_container'],
        ['attribute' => 'previous_total_container1'],
        ['attribute' => 'nav_update_date'],
        ['attribute' => 'confirm_date_from'],
        ['attribute' => 'confirm_date_to'],
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