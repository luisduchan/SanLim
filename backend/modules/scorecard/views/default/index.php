<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use brussens\bootstrap\select\Widget as Select;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;
use backend\modules\po\models\Po;
use kartik\select2\Select2;
use kartik\grid\GridView;
use kartik\export\ExportMenu;


$this->title = 'Customer Performance';
?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Customer Performance</h3>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(['id' => 'customer_performance', 'method' => 'get', 'action' => Url::to(['index']),]) ?>
        <div class="row">
            <div class="col-md-5">
                <?=
                        $form->field($genralScoreCardForm, 'reportGroup')
                        ->widget(Select::className(), [
                            'options' => ['data-live-search' => 'true',
                                'value' => $group ? $group : strtoupper(date('M-Y')),
                            ],
                            'items' => $allGroups])->label('Month*')
                ?>
                <?=
                $form->field($genralScoreCardForm, 'customer')->widget(Select::className(), [
                    'options' => ['data-live-search' => 'true'],
                    'items' => $customerList]);
                ?>
                <div class="form-group">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
                </div>
                <?php ActiveForm::end() ?>
                <p>Note: * is required field</p>
            </div>
        </div>
    </div>
</div>