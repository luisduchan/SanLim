<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\moutput\models\MaterialOutDetail */

$this->title = 'Create Material Out Detail';
$this->params['breadcrumbs'][] = ['label' => 'Material Out Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-out-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
