<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\moutput\models\MaterialOutDetail */

$this->title = 'Update Material Out Detail: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Material Out Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="material-out-detail-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
