<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/**
* @var yii\web\View $this
* @var app\models\RewardPlan $model
*/

$this->title = 'Create Reward Plan';
$this->params['breadcrumbs'][] = ['label' => 'Reward Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reward-plan-create card">
    <div class="card-header">
        <h4 class="card-title">
            <i class="feather icon-plus"></i> <?= Html::encode($this->title) ?>
        </h4>
    </div>
    <div class="card-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'business_amount')->textInput(['type' => 'number', 'step' => '0.01', 'min' => 0]) ?>
        <?= $form->field($model, 'reward')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'reward_amount')->textInput(['type' => 'number', 'step' => '0.01', 'min' => 0]) ?>
        <?= $form->field($model, 'status')->dropDownList(\app\models\RewardPlan::getStatusOptions(), ['prompt' => 'Select Status']) ?>

        <div class="form-group">
            <?= Html::submitButton('<i class="feather icon-save"></i> Save', ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="feather icon-x"></i> Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>


