<?php

//use yii\grid\GridView;
use yii\helpers\Html;
use yii\data\SqlDataProvider;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use backend\modules\po\models\Po;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\bootstrap\Carousel;

$this->title = 'Item ' . $itemNO;
$this->registerCss(".carousel-inner > .item > img { margin: 0 auto 0;}"
        . ".carousel-control.left,.carousel-control.right{background-image:-webkit-linear-gradient(left, rgba(0, 0, 0, .5) 0%, rgba(0, 0, 0, .0001) 100%)}");
?>
<div class="box-body">
    <div class="col-md-12">
        
    </div>
    <div class="row">
        <form class="form-horizontal">
            <div class="col-md-5 col-md-offset-0">
                <div class="form-group">
                    <!-- blank line -->
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Item No:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $itemData['item_no']?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Abbreviation:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $itemData['abbreviation']?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Description:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $itemData['description']?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Category Code:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $itemData['category_code']?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Specification:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $itemData['specification'] ?>">

                </div>
                <div class="form-group">
                    <label class="col-sm-5">Product Group Code:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $itemData['product_group_code'] ?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Unit of Measure:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $itemData['uom'] ?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">CUFT:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $itemData['cuft'] ?>">
                </div>
                
                
            </div>
            <div class="col-md-5 col-md-offset-0 ">
                <?php
                    if($imageBLODs){
                        $content = [];
                        foreach ($imageBLODs as $imageBLOD) {
                            $content[] = ['url' => 'data:image/jpg;base64,' . base64_encode($imageBLOD['nxpimg05']),
                                'src' => 'data:image/jpg;base64,' . base64_encode($imageBLOD['nxpimg05'])];
                        }
                    }else{
                        $content[] = ['url' => '/uploads/images/no_image_available.jpg',
                                'src' => '/uploads/images/no_image_available.jpg'];
                    }
                    echo dosamigos\gallery\Carousel::widget(['items' => $content], ['options' => ['fullScreen' => True]]);
                ?>
                <div class="form-group">
                    <!-- blank line -->
                </div>
                <div class="form-group">
                    <!-- blank line -->
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Last Date Modified:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $itemData['last_date_modified'] ?>">
                </div>
                <div class="form-group">
                    <label class="col-sm-5">Last User Modified:</label>
                    <input class="col-sm-7" readonly="true" value="<?= $itemData['last_user_modified'] ?>">
                </div>
            </div>
            <div class="col-md-1 col-md-offset-0 ">
                <?=
                Html::a('BOM', Url::to(['/item/bom/detail',
                            'bomno' => $itemData['item_no']
                        ]), ['class' => ['btn', 'btn-app'], 'title' => 'PO detail (Item)'])
                ?>
                <?=
                Html::a('Image', Url::to(['/overview/schedule/itemdetail',
                            'itemno' => $itemData['item_no']
                        ]), ['class' => ['btn', 'btn-app'], 'title' => 'PO detail (Item)'])
                ?>

            </div>
        </form>
    </div>
</div>