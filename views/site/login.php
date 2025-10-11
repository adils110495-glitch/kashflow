<?php
/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Sign in';
?>

<!-- Modern Sign-in Page (Yii2) -->
<div class="modern-signin-container">
    <div class="signin-wrapper">
        <!-- Left Section - Sign-in Form -->
        <div class="signin-form-section">
            <div class="signin-form-container">
                <!-- Logo and Header -->
                <div class="signin-header">
                    <div class="logo-container">
                        <div class="logo-icon">
                            <img src="<?= Yii::getAlias('@web') ?>/images/logo.png" alt="Logo" class="logo-image">
                        </div>
                        <h1 class="brand-name">KashFlow</h1>
                    </div>
                    <p class="signin-subtitle">Welcome back! Please sign in to your account.</p>
                </div>

                <!-- Sign-in Form -->
                <div class="signin-form">
                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'options' => ['class' => 'modern-form'],
                    ]); ?>

                    <!-- Username / Email Field -->
                    <div class="form-field">
                        <label class="field-label">Email</label>
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.5 6.25L10 10.625L17.5 6.25M2.5 6.25L10 2.5L17.5 6.25M2.5 6.25V15C2.5 15.3315 2.6317 15.6495 2.86612 15.8839C3.10054 16.1183 3.41848 16.25 3.75 16.25H16.25C16.5815 16.25 16.8995 16.1183 17.1339 15.8839C17.3683 15.6495 17.5 15.3315 17.5 15V6.25" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <?= $form->field($model, 'username', [
                                'template' => "{input}\n{error}",
                                'inputOptions' => [
                                    'class' => 'modern-input',
                                    'placeholder' => 'Enter your email',
                                    'autofocus' => true,
                                ],
                            ])->label(false) ?>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-field">
                        <label class="field-label">Password</label>
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 8.75H15.625C16.1223 8.75 16.5992 8.94754 16.9508 9.29918C17.3025 9.65081 17.5 10.1277 17.5 10.625V16.875C17.5 17.3723 17.3025 17.8492 16.9508 18.2008C16.5992 18.5525 16.1223 18.75 15.625 18.75H4.375C3.87772 18.75 3.40081 18.5525 3.04917 18.2008C2.69754 17.8492 2.5 17.3723 2.5 16.875V10.625C2.5 10.1277 2.69754 9.65081 3.04917 9.29918C3.40081 8.94754 3.87772 8.75 4.375 8.75H5M15 8.75V6.25C15 4.17893 13.3211 2.5 11.25 2.5H8.75C6.67893 2.5 5 4.17893 5 6.25V8.75M15 8.75H5" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <?= $form->field($model, 'password', [
                                'template' => "{input}\n{error}",
                                'inputOptions' => [
                                    'class' => 'modern-input',
                                    'placeholder' => 'Enter your password',
                                ],
                            ])->passwordInput()->label(false) ?>
                            <div class="password-toggle" onclick="(function(){var el=document.querySelector('#loginform-password'); if(!el) return; el.type= el.type==='password' ? 'text':'password';})()">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 3.75C3.75 3.75 1.25 10 1.25 10C1.25 10 3.75 16.25 10 16.25C16.25 16.25 18.75 10 18.75 10C18.75 10 16.25 3.75 10 3.75Z" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="10" cy="10" r="2.5" stroke="#6b7280" stroke-width="1.5"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="remember-me">
                        <label class="checkbox-container">
                            <?= $form->field($model, 'rememberMe')->checkbox(['label' => false, 'class' => 'checkbox-input'])->label(false) ?>
                            <span class="checkmark"></span>
                            <span class="checkbox-label">Remember me</span>
                        </label>
                        <span class="remember-text">Save my login details for next time.</span>
                    </div>

                    <!-- Sign In Button -->
                    <?= Html::submitButton('Sign In', ['class' => 'signin-button']) ?>
                    <?php ActiveForm::end(); ?>

                    <!-- Separator -->
                    <div class="separator">
                        <div class="separator-line"></div>
                        <span class="separator-text">or</span>
                        <div class="separator-line"></div>
                    </div>

                    <!-- Social Sign-in Buttons (static placeholders) -->
                    <div class="social-buttons">
                        <button type="button" class="social-button google-button" onclick="alert('Social authentication not configured.')">Sign in with Google</button>
                        <button type="button" class="social-button facebook-button" onclick="alert('Social authentication not configured.')">Sign in with Facebook</button>
                        <button type="button" class="social-button apple-button" onclick="alert('Social authentication not configured.')">Sign in with Apple</button>
                    </div>

                    <!-- Sign Up Link -->
                    <div class="signup-link">
                        <span class="signup-text">Don't have an account?</span>
                        <?= Html::a('Sign up', ['/user/registration/register'], ['class' => 'signup-button']) ?>
                    </div>

                    <!-- Copyright -->
                    <div class="copyright">
                        <span>Copyright 2023 KashFlow Corporation</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Section - Marketing Content -->
        <div class="marketing-section">
            <div class="marketing-content">
                <h2 class="marketing-headline">
                    Securely Upload And Store<br>
                    Your Important Documents<br>
                    With <span class="highlight">KashFlow</span>!
                </h2>

                <div class="marketing-illustration">
                    <div class="cloud-character">
                        <div class="cloud-body"></div>
                        <div class="cloud-eye"></div>
                        <div class="cloud-mouth"></div>
                        <div class="cloud-arms">
                            <div class="arm-left"></div>
                            <div class="arm-right"></div>
                        </div>
                        <div class="cloud-legs">
                            <div class="leg-left"></div>
                            <div class="leg-right"></div>
                        </div>
                        <div class="laptop"></div>
                    </div>

                    <div class="decorative-elements">
                        <div class="dashed-box"></div>
                        <div class="wavy-lines">
                            <div class="wave wave-1"></div>
                            <div class="wave wave-2"></div>
                            <div class="wave wave-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
