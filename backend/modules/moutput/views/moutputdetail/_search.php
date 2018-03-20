<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\moutput\models\MoutputdetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="material-out-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'division_code') ?>

    <?= $form->field($model, 'workcenter_code') ?>

    <?= $form->field($model, 'machine_code') ?>

    <?= $form->field($model, 'item_code') ?>

    <?php // echo $form->field($model, 'document_no') ?>

    <?php // echo $form->field($model, 'used_mass') ?>

    <?php // echo $form->field($model, 'uom') ?>

    <?php // echo $form->field($model, 'create_date') ?>

    <?php // echo $form->field($model, 'last_update') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
