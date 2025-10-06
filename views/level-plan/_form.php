<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Tabs;
use yii\helpers\StringHelper;
use wbraganca\dynamicform\DynamicFormWidget;
use app\models\LevelPlan;

/**
* @var yii\web\View $this
* @var app\models\LevelPlan[] $models
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="level-plan-form">

    <?php $form = ActiveForm::begin([
        'id' => 'dynamic-form',
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
                'widgetContainer' => 'dynamicform_wrapper',
                'widgetBody' => '.container-items',
                'widgetItem' => '.item',
                'limit' => 20,
                'min' => 1,
                'insertButton' => '.add-item',
                'deleteButton' => '.remove-item',
                'model' => $models[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'level',
                    'rate',
                    'no_of_directs',
                    'status',
                ],
            ]); ?>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="container-items">
                        <?php foreach ($models as $i => $model): ?>
                            <div class="item panel panel-default">
                                <div class="panel-body">
                                    <?php
                                    // necessary for update action.
                                    if (!$model->isNewRecord) {
                                        echo Html::activeHiddenInput($model, "[{$i}]id");
                                    }
                                    ?>
                                    
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <?= $form->field($model, "[{$i}]level")->textInput([
                                                'type' => 'number',
                                                'min' => 1,
                                                'max' => 999,
                                                'placeholder' => 'Enter level number (e.g., 1, 2, 3...)'
                                            ])->hint('Level number must be unique') ?>
                                        </div>
                                        <div class="col-sm-5">
                                            <?= $form->field($model, "[{$i}]rate")->textInput([
                                                'type' => 'number',
                                                'step' => '0.01',
                                                'min' => 0,
                                                'max' => 100,
                                                'placeholder' => 'Enter commission rate (0-100)'
                                            ])->hint('Commission rate as percentage') ?>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <?= $form->field($model, "[{$i}]no_of_directs")->textInput([
                                                'type' => 'number',
                                                'min' => 0,
                                                'max' => 9999,
                                                'placeholder' => 'Enter required number of direct referrals'
                                            ])->hint('Number of direct referrals required') ?>
                                        </div>
                                        <div class="col-sm-5">
                                            <?= $form->field($model, "[{$i}]status")->dropDownList(
                                                LevelPlan::getStatusOptions(),
                                                [
                                                    'prompt' => 'Select Status',
                                                    'class' => 'form-control'
                                                ]
                                            )->hint('Set to Active to enable this level plan') ?>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="remove-item btn btn-danger btn-xs">
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
                    '<i class="feather icon-save"></i> Save Level Plans',
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