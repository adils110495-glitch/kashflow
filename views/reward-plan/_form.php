<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use app\models\RewardPlan;

/**
* @var yii\web\View $this
* @var app\models\RewardPlan[] $models
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="reward-plan-form">

    <?php $form = ActiveForm::begin([
        'id' => 'dynamic-form-reward',
        'layout' => 'horizontal',
        'enableClientValidation' => true,
        'errorSummaryCssClass' => 'error-summary alert alert-danger',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-4',
                'wrapper' => 'col-sm-6',
                'error' => '',
                'hint' => '',
            ],
        ],
    ]); ?>

    <div class="row">
        <div class="col-xs-12">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper_reward',
                'widgetBody' => '.container-items-reward',
                'widgetItem' => '.item-reward',
                'limit' => 50,
                'min' => 1,
                'insertButton' => '.add-item-reward',
                'deleteButton' => '.remove-item-reward',
                'model' => $models[0],
                'formId' => 'dynamic-form-reward',
                'formFields' => [
                    'business_amount',
                    'reward',
                    'reward_amount',
                    'status',
                ],
            ]); ?>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="container-items-reward">
                        <?php foreach ($models as $i => $model): ?>
                            <div class="item-reward panel panel-default">
                                <div class="panel-body">
                                    <?php if (!$model->isNewRecord) {
                                        echo Html::activeHiddenInput($model, "[{$i}]id");
                                    } ?>

                                    <div class="row">
                                        <div class="col-sm-5">
                                            <?= $form->field($model, "[{$i}]business_amount")->textInput([
                                                'type' => 'number',
                                                'step' => '0.01',
                                                'min' => 0,
                                                'placeholder' => 'Enter business amount'
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-5">
                                            <?= $form->field($model, "[{$i}]reward")->textInput([
                                                'maxlength' => true,
                                                'placeholder' => 'Enter reward name/description'
                                            ]) ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-5">
                                            <?= $form->field($model, "[{$i}]reward_amount")->textInput([
                                                'type' => 'number',
                                                'step' => '0.01',
                                                'min' => 0,
                                                'placeholder' => 'Enter reward amount'
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-5">
                                            <?= $form->field($model, "[{$i}]status")->dropDownList(
                                                RewardPlan::getStatusOptions(),
                                                [
                                                    'prompt' => 'Select Status',
                                                    'class' => 'form-control'
                                                ]
                                            ) ?>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="remove-item-reward btn btn-danger btn-xs">
                                                <i class="feather icon-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php DynamicFormWidget::end(); ?>
        </div>
    </div>

    <hr/>

    <div class="form-group">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <?= Html::submitButton(
                    '<i class="feather icon-save"></i> Save Reward Plans',
                    [
                        'class' => 'btn btn-success'
                    ]
                ); ?>
                <?= Html::a(
                    '<i class="feather icon-x"></i> Cancel',
                    ['index'],
                    [
                        'class' => 'btn btn-secondary'
                    ]
                ); ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>


