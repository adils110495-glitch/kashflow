<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\AdminLoginForm $model */

$this->title = 'Admin Login';
?>

<div class="admin-login-form">
    <?php $form = ActiveForm::begin([
        'id' => 'admin-login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-12\">{input}</div>\n<div class=\"col-lg-12\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-12 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'username')->textInput([
        'autofocus' => true,
        'placeholder' => 'Enter your username',
        'class' => 'form-control'
    ]) ?>

    <?= $form->field($model, 'password')->passwordInput([
        'placeholder' => 'Enter your password',
        'class' => 'form-control'
    ]) ?>

    <?= $form->field($model, 'rememberMe')->checkbox([
        'template' => "<div class=\"col-lg-12\"><div class=\"form-check\">{input} {label}</div></div>\n<div class=\"col-lg-12\">{error}</div>",
    ]) ?>

    <div class="form-group">
        <div class="col-lg-12">
            <?= Html::submitButton('Login', [
                'class' => 'btn btn-primary btn-admin-login w-100',
                'name' => 'login-button'
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="text-center mt-3">
        <small class="text-muted">
            <i class="fas fa-lock"></i> Secure Admin Access
        </small>
    </div>
</div>

