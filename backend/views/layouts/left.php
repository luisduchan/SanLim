<?php
use mdm\admin\components\Helper;
use mdm\admin\components\MenuHelper;
use yii\bootstrap\Nav;

// var_dump(MenuHelper::getAssignedMenu(Yii::$app->user->id));die();

?>

<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <?php
        $callback = function($menu){
            return [
                'label' => $menu['name'],
                'url' => [$menu['route']],
                'active' => $menu['route'],
                'icon' => $menu['data'],
                'items' => $menu['children'],
            ];
        };
        $items = MenuHelper::getAssignedMenu(Yii::$app->user->id);
        echo dmstr\widgets\Menu::widget(
                [
                    'options' => ['class' => 'sidebar-menu'],
                    'items' => $items
                    // 'items' => MenuHelper::getAssignedMenu(Yii::$app->user->id),
                ]
            );


        // dmstr\widgets\Menu::widget(
        //         [
        //             'options' => ['class' => 'sidebar-menu'],
        //             'items' => [
        //                 ['label' => 'Purchase', 'icon' => 'fa fa-bars', 'options' => ['class' => 'header']],
        //                 ['label' => 'Overview', 'icon' => 'fa fa-area-chart', 'url' => ['/overview/default/planning']],
        //                 ['label' => 'Document', 'icon' => 'fa fa-area-chart', 'url' => ['/document']],
        //                 ['label' => 'Marketing', 'icon' => 'fa fa-area-chart', 'url' => ['#'],
        //                     'items' => [
        //                         ['label' => 'Items', 'url' => ['/item/default/query']],
        //                         ['label' => 'Item Order', 'url' => ['/po/itemorder']],
        //                         ['label' => 'Total Order Report', 'url' => ['/customerpo/default/custponeedplan']],
        //                         ['label' => 'Order Status', 'url' => ['/overview/schedule/postatus']],
        //                         ['label' => 'Summary Cust PO', 'url' => ['/customerpo/default/summaryposchedule']],
        //                         ['label' => 'Schedule Base On Report Group', 'url' => ['/overview/schedule']],
        //                         ['label' => 'Schedule', 'url' => ['/customerpo/schedule']],
        //                         ['label' => 'PO Logs', 'url' => ['/customerpo/log']],
        //                     ],
        //                 ],
        //                 ['label' => 'Performance', 'url' => ['#'],
        //                     'items' => [
        //                         ['label' => 'WSI Scorecard', 'url' => ['/scorecard/wsi']],
        //                         ['label' => 'Lazboy Scorecard', 'url' => ['/scorecard/lazboy']],
        //                         ['label' => 'General', 'url' => ['/scorecard/default']],
        //                     ]
        //                 ],
        //                 ['label' => 'PPC', 'icon' => 'fa fa-area-chart', 'url' => ['#'],
        //                     'items' => [
        //                         ['label' => 'Scheudle', 'url' => ['/schedule']],
        //                         ['label' => 'Line', 'url' => ['/document/default/test']],
        //                     ],
        //                 ],
        //                 ['label' => 'PP/PMC', 'icon' => 'fa fa-area-chart', 'url' => ['#'],
        //                     'items' => [
        //                         ['label' => 'Material Output', 'url' => ['/moutput']],
        //                     ],
        //                 ],
        //                 ['label' => 'POSummary', 'icon' => 'fa fa-area-chart', 'url' => ['/po/summary/query']],
        //                 ['label' => 'Test Bar', 'icon' => 'fa fa-area-chart', 'url' => ['/site/test']],
        //                 ['label' => 'System', 'options' => ['class' => 'header']],
        //                 ['label' => 'Auth', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii'],
        //                     // 'items' => [
        //                     //     ['label' => 'Item', 'url' => ['/auth/rbac']],
        //                     //     ['label' => 'Item Child', 'url' => ['/auth/itemchild']],
        //                     //     ['label' => 'Assignment', 'url' => ['/auth/assignment']],
        //                     //     ['label' => 'User', 'url' => ['/auth/user']],
        //                     // ],
        //                     'items' => [
        //                         ['label' => 'Admin', 'url' => ['/admin']],
        //                         ['label' => 'Route', 'url' => ['/admin/route']],
        //                         ['label' => 'Permission', 'url' => ['/admin/permission']],
        //                         ['label' => 'Menu', 'url' => ['/admin/menu']],
        //                         ['label' => 'Role', 'url' => ['/admin/role']],
        //                         ['label' => 'Assignment', 'url' => ['/admin/assignment']],
        //                         ['label' => 'User', 'url' => ['/auth/user']],
        //                     ],
        //                 ],
        //                 ['label' => 'Gii', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii']],
        //                 ['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug']],
        //                 ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
        //                 [
        //                     'label' => 'Same tools',
        //                     'icon' => 'fa fa-share',
        //                     'url' => '#',
        //                     'items' => [
        //                         ['label' => 'Gii', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii'],],
        //                         ['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug'],],
        //                         [
        //                             'label' => 'Level One',
        //                             'icon' => 'fa fa-circle-o',
        //                             'url' => '#',
        //                             'items' => [
        //                                 ['label' => 'Level Two', 'icon' => 'fa fa-circle-o', 'url' => '#',],
        //                                 [
        //                                     'label' => 'Level Two',
        //                                     'icon' => 'fa fa-circle-o',
        //                                     'url' => '#',
        //                                     'items' => [
        //                                         ['label' => 'Level Three', 'icon' => 'fa fa-circle-o', 'url' => '#',],
        //                                         ['label' => 'Level Three', 'icon' => 'fa fa-circle-o', 'url' => '#',],
        //                                     ],
        //                                 ],
        //                             ],
        //                         ],
        //                     ],
        //                 ],
        //             ],
        //         ]
        // )
        ?>

       

    </section>

</aside>
