<?php

//use yii\grid\GridView;
use yii\helpers\Html;
use yii\data\SqlDataProvider;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use backend\modules\po\models\Po;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
?>

<?php
$gridViewDataProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $arrPO,
    'pagination' => ['pageSize' => 1000]
        ]);
?>
<?php
$gridColumns = ['No_', 'order_date', 'total_cuft'];
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
    'columns' => [
                
                ['attribute' => 'No_',
                    'label' => 'PO',
                'format' => 'raw',
                    'value' => function ($data) {
                         return Html::a($data['No_'], Url::to(['/overview/schedule/podetail' ,
                             'pono' => $data['No_']
                             
                             ]));
                     }],
                ['attribute' => 'cus_no'],
                ['attribute' => 'order_date',
                 'format' => ['date', 'php:m/d/Y']],
                ['attribute' => 'created_date',
                 'format' => ['date', 'php:m/d/Y']],
                ['attribute' => 'modified_date',
                 'format' => ['date', 'php:m/d/Y']],
                ['attribute' => 'ass_date_start',
                 'format' => ['date', 'php:m/d/Y']],
                ['attribute' => 'ass_date_end',
                 'format' => ['date', 'php:m/d/Y']],
                ['attribute' => 'total_scheduled',
                    'format'=>['decimal',2],
                    'footer' => Po::pageTotal($gridViewDataProvider, 'total_scheduled')],
                ['attribute' => 'total_on_po',
                    'format'=>['decimal',2],
                    'footer' => Po::pageTotal($gridViewDataProvider, 'total_on_po')],
                
//        ]
]
]);
//echo GridView::widget([
//    'dataProvider' => $gridViewDataProvider,
//    'showFooter' => TRUE,
//    'tableOptions' => ['class' => 'table table-hover'],
//    'columns' => 
//        [
//                
//                ['attribute' => 'No_',
//                    'label' => 'PO',
//                'format' => 'raw',
//                    'value' => function ($data) {
//                         return Html::a($data['No_'], Url::to(['/overview/polist', 
//                             
//                             ]));
//                     }],
//                ['attribute' => 'cus_no'],
//                ['attribute' => 'order_date',
//                 'format' => ['date', 'php:m/d/Y']],
//                ['attribute' => 'total_cuft',
//                    'format'=>['decimal',2],
//                    'footer' => Po::pageTotal($gridViewDataProvider, 'total_cuft')],
//                ['attribute' => 'total_scheduled',
//                    'format'=>['decimal',2],
//                    'footer' => Po::pageTotal($gridViewDataProvider, 'total_scheduled')],
////        ]
//]]);
?>