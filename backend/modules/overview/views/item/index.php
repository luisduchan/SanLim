<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\data\SqlDataProvider;

//$gridViewDataProvider = new \yii\data\ArrayDataProvider([
//    'allModels' => $items,
//    'pagination' => ['pageSize' => 20]
//        ]);
$count = Yii::$app->dbMS->createCommand('
    SELECT COUNT(*) FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item]')->queryScalar();
$dataProvider = new SqlDataProvider([
    'db' => Yii::$app->dbMS,
    'sql' => 'SELECT * FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item]',
    'totalCount' => $count,
    'pagination' => [
        'pageSize' => 20,
    ],
]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'showFooter' => TRUE,
    'tableOptions' => ['class' => 'table table-hover'],
    'columns' =>
        [
            [
                'label' => 'Item No',
                'format' => 'raw',
                // here comes the problem - instead of parent_region I need to have parent
                'value' => function ($data) {
                     return Html::a($data['No_'], 'admin/region/view?id=' . 1);
                 },
            ],
            ['attribute' => 'Description'],
            ['attribute' => 'Base Unit of Measure'],
            
//            ['attribute' => 'stock',
//            'format' => 'decimal',
//            'footer' => Po::pageTotal($gridViewDataProvider, 'stock')],
//            ['attribute' => 'output',
//            'format' => 'decimal',
//            'footer' => Po::pageTotal($gridViewDataProvider, 'output')],
    ]
]);
?>
