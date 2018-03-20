<?php

use yii\widgets\Pjax;
use yii\web\JsExpression;
use execut\widget\TreeView;
use yii\helpers\Url;

$this->title = 'Production Line';

$onSelect = new JsExpression(<<<JS
function (undefined, item) {
    if (item.href !== location.pathname) {
        $.pjax({
            container: '#pjax-container',
            url: item.href,
            timeout: null,
        }).done(function(a,b,c) { console.log(c) });;
    }


    var otherTreeWidgetEl = $('.treeview.small').not($(this)),
        otherTreeWidget = otherTreeWidgetEl.data('treeview'),
        selectedEl = otherTreeWidgetEl.find('.node-selected');
    if (selectedEl.length) {
        otherTreeWidget.unselectNode(Number(selectedEl.attr('data-nodeid')));
    }
}
JS
);

$items = [
    [
        'text' => 'Parent 1',
        'href' => Url::to(['', 'page' => 'parent1']),
        'nodes' => [
            [
                'text' => 'Child 1',
                'href' => Url::to(['', 'page' => 'child1']),
                'nodes' => [
                    [
                        'text' => 'Grandchild 1',
                        'href' => Url::to(['', 'page' => 'grandchild1']),
                        'nodes' => []
                    ],
                    [
                        'text' => 'Grandchild 2',
                        'href' => Url::to(['', 'page' => 'grandchild2'])
                    ]
                ]
            ],
        ],
    ],
];
?>
<div class="box-body">
    <div class="col-md-6">
        <?=
        TreeView::widget([
            'data' => $departments,
            'size' => TreeView::SIZE_NORMAL,
            'clientOptions' => [
                'onNodeSelected' => $onSelect,
            ],
            'header' => 'Deparment',
            'searchOptions' => [
                'inputOptions' => [
                    'placeholder' => 'Search...'
                ],
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6">
        <?php
        Pjax::begin([
            'id' => 'pjax-container',
        ]);

        echo \yii::$app->request->get('page');

        Pjax::end();
        ?>
    </div>
</div>