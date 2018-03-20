<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\sanlim\models\Component */

$this->title = 'Update Component: ' . $model->item_no;
$this->params['breadcrumbs'][] = ['label' => 'Components', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->item_no, 'url' => ['view', 'id' => $model->item_no]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="component-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
