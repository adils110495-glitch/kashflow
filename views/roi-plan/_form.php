<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/** @var app\models\RoiPlan $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="roi-plan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'rate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'frequency')->dropDownList(
        \app\models\RoiPlan::buildFrequency(),
        ['prompt' => 'Select Frequency']
    ) ?>

    <?= $form->field($model, 'tenure')->dropDownList(
        \app\models\RoiPlan::buildTenure(),
        ['prompt' => 'Select Tenure']
    ) ?>

    <?= $form->field($model, 'release_date')->widget(DatePicker::class, [
        'options' => [
            'placeholder' => 'Select release date...',
            'class' => 'form-control',
            'id' => 'roiplan-release_date',
        ],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'todayBtn' => true,
            'clearBtn' => true,
            'orientation' => 'bottom auto'
        ]
    ]) ?>

    <?= $form->field($model, 'status')->dropDownList(
        \app\models\RoiPlan::buildStatus(),
        ['prompt' => 'Select Status']
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('<i class="feather icon-save"></i> Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="feather icon-x"></i> Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
