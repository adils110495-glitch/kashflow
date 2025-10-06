<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Package;

/* @var $this yii\web\View */
/* @var $model app\models\CustomerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'first_name') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'last_name') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'phone') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'package_id')->dropDownList(
                ArrayHelper::map(Package::find()->all(), 'id', 'name'),
                ['prompt' => 'All Packages']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'city') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'state') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'country') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'status')->dropDownList([
                '' => 'All Status',
                1 => 'Active',
                0 => 'Inactive'
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>