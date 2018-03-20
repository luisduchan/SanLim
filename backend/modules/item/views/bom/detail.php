<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use brussens\bootstrap\select\Widget as Select;
use yii\helpers\Url;
use backend\modules\po\models\Po;
use kartik\select2\Select2;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use backend\modules\common\models\GridTool;

$this->title = 'Bom Detail';
?>
<?php if($headerData){ ?>
<div class="box-body">
    <div class="row">
        <form class="form-horizontal">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="col-sm-5">Bom Item:</label>
                    <a href="<?= Url::to(['/item/bom/detail', 'bomno' => $headerData['bom_no']]) ?>">
                        <input class="col-sm-7" value="<?=$headerData['bom_no']?>" style="cursor: pointer" readonly="true">
                    </a>
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Description:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $headerData['description']?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Unit of Measure:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $headerData['uom']?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Specification:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $headerData['specification']?>">
                </div>
                
            </div>
            <div class="col-md-4 col-md-offset-1 ">
                <div class="form-group">
                    <label class="col-sm-5">Last User Modified:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $headerData['last_user_modified']?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Last Date Modified:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $headerData['last_date_modified']?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Status:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $headerData['status']?>">
                </div>
            </div>
        </form>
    </div>
    <?php
    $gridViewDataProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $linesData,
        'pagination' => ['pageSize' => 1000]
    ]);

    $columns = [
        ['attribute' => 'bom_no',
            'format' => 'raw',
            'value' => function($data) {
                $itemUrl = '#';
                if($data['type'] == 2){
                    $itemUrl =  Html::a($data['bom_no'], Url::to(['/item/bom/detail',
                                    'bomno' => $data['bom_no']
                    ]));
                }elseif($data['type'] == 1){
                    $itemUrl =  Html::a($data['bom_no'], Url::to(['/item/default/detail',
                                    'itemno' => $data['bom_no']
                    ]));
                }
                return $itemUrl;
            }
        ],


        ['attribute' => 'description'],
        ['attribute' => 'uomc',
            'label' => 'Unit of Measure'],

        ['attribute' => 'quantity'],
        ['attribute' => 'variant'],
        ['attribute' => 'spec',
            'label' => 'Specification'],
        ['attribute' => 'bl_desc',
            'label' => 'WG Description'],
        ['attribute' => 'op_raito'],
        ['attribute' => 'op_scrap'],
        ['attribute' => 'length_fs'],
        ['attribute' => 'width_fs'],
        ['attribute' => 'thick_fs'],
        ['attribute' => 'length_ps'],
        ['attribute' => 'width_ps'],
        ['attribute' => 'thick_ps'],

    ];
    echo ExportMenu::widget([
        'dataProvider' => $gridViewDataProvider,
        'columns' => $columns,

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
    ]);
    ?>
</div>
<?php }else{?>
<h4>Bom Not Found</h4>
<?php } ?>
