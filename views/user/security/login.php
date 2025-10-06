<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dektrium\user\widgets\Connect;

/** @var yii\web\View $this */
/** @var dektrium\user\models\LoginForm $model */
/** @var dektrium\user\Module $module */

$this->title = Yii::t('user', 'Sign in');
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<!-- [ auth-signin ] start -->
<div class="auth-wrapper">
    <div class="auth-content text-center container">
		<div class="card borderless">
			<div class="row align-items-stretch">
				<div class="col-md-6 d-flex align-items-center justify-content-center">
                    <div class="w-100">
                        <div class="text-center mb-4">
                            <img src="<?= Yii::getAlias('@web') ?>/images/logo.png" alt="auth" class="img-fluid mb-3">
                            <h4 class=""><?= Html::encode($this->title) ?></h4>
                        </div>
                        <div class="card-body">
                        <?php $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'enableAjaxValidation' => true,
                            'enableClientValidation' => false,
                            'validateOnBlur' => false,
                            'validateOnType' => false,
                            'validateOnChange' => false,
                            'options' => ['class' => '']
                        ]) ?>

						<div class="form-group mb-3">
                            <?= $form->field($model, 'login', [
                                'template' => "{input}\n{error}",
                                'inputOptions' => [
                                    'autofocus' => true,
                                    'class' => 'form-control',
                                    'placeholder' => Yii::t('user', 'Email address'),
                                    'tabindex' => '1',
                                ],
                            ])->label(false) ?>
						</div>
						<div class="form-group mb-4">
                            <?= $form->field($model, 'password', [
                                'template' => "{input}\n{error}",
                                'inputOptions' => [
                                    'class' => 'form-control',
                                    'placeholder' => Yii::t('user', 'Password'),
                                    'tabindex' => '2',
                                ],
                            ])->passwordInput()->label(false) ?>
						</div>
						<div class="custom-control custom-checkbox text-left mb-4 mt-2">
                            <?= $form->field($model, 'rememberMe')->checkbox([
                                'tabindex' => '4',
                                'label' => Yii::t('user', 'Save credentials.'),
                                'class' => 'custom-control-input',
                                'labelOptions' => ['class' => 'custom-control-label']
                            ])->label(false) ?>
						</div>
						<?= Html::submitButton(Yii::t('user', 'Signin'), ['class' => 'btn btn-block btn-primary mb-4', 'tabindex' => '3']) ?>
                        <?php ActiveForm::end(); ?>
						
                        <?php if ($module->enablePasswordRecovery): ?>
                            <p class="mb-2 text-muted"><?= Html::a(Yii::t('user', 'Forgot password?'), ['/user/recovery/request'], ['tabindex' => '5', 'class' => 'f-w-400']) ?> <span class="f-w-400">Reset</span></p>
                        <?php endif; ?>
                        <?php if ($module->enableRegistration): ?>
                            <p class="mb-0 text-muted"><?= Html::a(Yii::t('user', 'Don\'t have an account?'), ['/user/registration/register'], ['class' => 'f-w-400']) ?> <span class="f-w-400">Signup</span></p>
                        <?php endif; ?>
                        </div>
                    </div>
				</div>
                <div class="col-md-6 d-flex align-items-stretch">
                    <div class="signup-image-container w-100 d-flex align-items-center justify-content-center">
                        <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="login illustration" class="img-fluid rounded shadow-sm signup-image">
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>
<!-- [ auth-signin ] end -->
