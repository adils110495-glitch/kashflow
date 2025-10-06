<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Customer;
use app\models\Package;
use app\models\Country;
use app\models\States;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'package_id')->dropDownList(
                ArrayHelper::map(Package::find()->all(), 'id', 'name'),
                ['prompt' => 'Select Package']
            ) ?>
        </div>
    </div>

    <?= $form->field($model, 'address')->textarea(['rows' => 3]) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'zip_code')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'country')->dropDownList(
                ArrayHelper::map(Country::find()->all(), 'name', 'name'),
                ['prompt' => 'Select Country', 'id' => 'country-dropdown']
            ) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'state')->dropDownList(
                ArrayHelper::map(States::find()->all(), 'name', 'name'),
                ['prompt' => 'Select State', 'id' => 'state-dropdown']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'referrer_id')->dropDownList(
                ArrayHelper::map(Customer::find()->all(), 'id', function($model) {
                    return $model->first_name . ' ' . $model->last_name . ' (' . ($model->user ? $model->user->username : 'N/A') . ')';
                }),
                ['prompt' => 'Select Referrer (Optional)']
            ) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'status')->dropDownList([
                1 => 'Active',
                0 => 'Inactive'
            ], ['prompt' => 'Select Status']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
    $('#country-dropdown').change(function() {
        var country = $(this).val();
        if (country) {
            $.ajax({
                url: '" . \yii\helpers\Url::to(['/states/get-by-country']) . "',
                type: 'GET',
                data: {country: country},
                success: function(data) {
                    $('#state-dropdown').html(data);
                }
            });
        } else {
            $('#state-dropdown').html('<option value=\"\">Select State</option>');
        }
    });
");
?>