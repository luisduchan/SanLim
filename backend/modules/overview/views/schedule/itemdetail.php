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
        <?php
        $content = [];
        foreach ($imageBLODs as $imageBLOD) {
//        $content[] = ['content' => '<img src="data:image/jpg;base64,' . base64_encode($imageBLOD['nxpimg05']) . '" class="embed-responsive" style="max-width:700px;max-height:400px"/>',
//            'options' => ['showIndicators' => TRUE]];
            $content[] = ['url' => 'data:image/jpg;base64,' . base64_encode($imageBLOD['nxpimg05']),
                'src' => 'data:image/jpg;base64,' . base64_encode($imageBLOD['nxpimg05'])];
        }
        echo dosamigos\gallery\Carousel::widget(['items' => $content], ['options' => ['fullScreen' => True]]);
//    echo Carousel::widget([
//        'items' => $content,
//        'options' => [
//            'style' => ['max-width'=>'700px','max-height' => '400px'],
//            'showIndicators' => TRUE
//        ],
//    ]);
        ?>
    </div>
</div>