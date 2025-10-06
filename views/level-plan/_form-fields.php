<?php

use yii\helpers\Html;
use app\models\LevelPlan;

/**
 * @var yii\web\View $this
 * @var app\models\LevelPlan $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<!-- attribute level -->
<?= $form->field($model, 'level')->textInput([
    'type' => 'number',
    'min' => 1,
    'max' => 999,
    'placeholder' => 'Enter level number (e.g., 1, 2, 3...)'
])->hint('Level number must be unique') ?>

<!-- attribute rate -->
<?= $form->field($model, 'rate')->textInput([
    'type' => 'number',
    'step' => '0.01',
    'min' => 0,
    'max' => 100,
    'placeholder' => 'Enter commission rate (0-100)'
])->hint('Commission rate as percentage (e.g., 15.50 for 15.50%)') ?>

<!-- attribute no_of_directs -->
<?= $form->field($model, 'no_of_directs')->textInput([
    'type' => 'number',
    'min' => 0,
    'max' => 9999,
    'placeholder' => 'Enter required number of direct referrals'
])->hint('Number of direct referrals required for this level (0 if no requirement)') ?>

<!-- attribute status -->
<?= $form->field($model, 'status')->dropDownList(
    LevelPlan::getStatusOptions(),
    [
        'prompt' => 'Select Status',
        'class' => 'form-control'
    ]
)->hint('Set to Active to enable this level plan') ?>