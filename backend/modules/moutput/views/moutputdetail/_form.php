<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $model backend\modules\moutput\models\MaterialOutDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="material-out-detail-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-5">

            <?= $form->field($model, 'division_code')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'workcenter_code')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'machine_code')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'item_code')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'document_no')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($model, 'used_mass')->textInput() ?>

            <?= $form->field($model, 'uom')->textInput(['maxlength' => true]) ?>

            
            <?=
                    $form->field($model, 'create_date')
                    ->widget(DatePicker::classname(), [
                        'dateFormat' => 'yyyy-MM-dd',
                        'options' => [
                            'class' => 'form-control'
                        ]
                    ])->label('Date To *')
            ?>

            <?= $form->field($model, 'last_update')->textInput() ?>
        </div>


    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
