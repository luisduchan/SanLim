<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\moutput\models\MaterialOutDetail */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Material Out Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-out-detail-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'division_code',
            'workcenter_code',
            'machine_code',
            'item_code',
            'document_no',
            'used_mass',
            'uom',
            'create_date',
            'last_update',
        ],
    ]) ?>

</div>
