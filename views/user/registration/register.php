<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
/** @var yii\web\View $this */
/** @var app\models\RegistrationForm $model */
/** @var dektrium\user\Module $module */

$this->title = Yii::t('user', 'Sign up');
$this->registerMetaTag(['name' => 'csrf-token', 'content' => Yii::$app->request->csrfToken]);
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="auth-wrapper">
    <div class="auth-content text-center container">
		<div class="card borderless">
			<div class="row align-items-stretch">
				<div class="col-md-6">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class=""><?= Html::encode($this->title) ?></h4>
                        <img src="<?= Yii::getAlias('@web') ?>/images/logo.png" alt="auth" class="img-fluid">
                    </div>
                    <div class="card-body">
					
                        <?php $form = ActiveForm::begin([
                            'id' => 'registration-form',
                            'enableAjaxValidation' => true,
                            'enableClientValidation' => false,
                            'validateOnBlur' => false,
                            'validateOnType' => false,
                            'validateOnChange' => false,
                        ]) ?>

                        <div class="form-group mb-3">
                            <?= $form->field($model, 'name', [
                                'template' => "{label}\n{input}\n{error}",
                                'labelOptions' => ['class' => 'form-label'],
                                'inputOptions' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'Full Name',
                                ],
                            ])->label(false) ?>
                        </div>

                        <div class="form-group mb-3">
                            <?= $form->field($model, 'email', [
                                'template' => "{label}\n{input}\n{error}",
                                'labelOptions' => ['class' => 'form-label'],
                                'inputOptions' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'Email Address',
                                ],
                            ])->label(false) ?>
                        </div>

                        <div class="form-group mb-3">
                            <?= $form->field($model, 'country_id', [
                                'template' => "{label}\n{input}\n{error}",
                                'labelOptions' => ['class' => 'form-label'],
                            ])->widget(Select2::class, [
                                'data' => ArrayHelper::map($model->getCountriesForSelect2(), 'id', 'text'),
                                'options' => [
                                    'placeholder' => 'Select Country',
                                    'id' => 'country-select'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'templateResult' => new \yii\web\JsExpression('function(country) {
                                        if (!country.id) return country.text;
                                        var countriesData = ' . json_encode($model->getCountriesForSelect2()) . ';
                                        var countryData = countriesData.find(function(c) { return c.id == country.id; });
                                        if (countryData) {
                                            return $("<span><span class=\"country-flag\">" + countryData.flag + "</span>" + country.text + "</span>");
                                        }
                                        return country.text;
                                    }'),
                                    'templateSelection' => new \yii\web\JsExpression('function(country) {
                                        if (!country.id) return country.text;
                                        var countriesData = ' . json_encode($model->getCountriesForSelect2()) . ';
                                        var countryData = countriesData.find(function(c) { return c.id == country.id; });
                                        if (countryData) {
                                            return $("<span><span class=\"country-flag\">" + countryData.flag + "</span>" + country.text + "</span>");
                                        }
                                        return country.text;
                                    }')
                                ],
                                'pluginEvents' => [
                                    'select2:open' => new \yii\web\JsExpression('function() {
                                        // Ensure Select2 is available before using it
                                        if (typeof $.fn.select2 === "undefined") {
                                            console.warn("Select2 not available");
                                            return false;
                                        }
                                    }')
                                ]
                            ])->label(false) ?>
                        </div>

                        <div class="form-group mb-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="mobile-code-display">+1</span>
                                </div>
                                <?= $form->field($model, 'mobile_no', [
                                    'template' => "{input}\n{error}",
                                    'inputOptions' => [
                                        'class' => 'form-control',
                                        'placeholder' => 'Mobile Number',
                                        'style' => 'border-left: none;'
                                    ],
                                ])->label(false) ?>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <?= $form->field($model, 'referral_code', [
                                'template' => "{label}\n{input}\n{error}\n<div id='referral-validation-message' class='mt-2'></div>",
                                'labelOptions' => ['class' => 'form-label'],
                                'inputOptions' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'Referral Code (Optional)',
                                    'id' => 'referral-code-input',
                                ],
                            ])->label(false) ?>
                        </div>

                        <div class="form-group mb-3">
                            <?= $form->field($model, 'password', [
                                'template' => "{label}\n{input}\n{error}",
                                'labelOptions' => ['class' => 'form-label'],
                                'inputOptions' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'Password',
                                ],
                            ])->passwordInput()->label(false) ?>
                        </div>

                        <div class="form-group mb-4">
                            <?= $form->field($model, 'password_repeat', [
                                'template' => "{label}\n{input}\n{error}",
                                'labelOptions' => ['class' => 'form-label'],
                                'inputOptions' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'Confirm Password',
                                ],
                            ])->passwordInput()->label(false) ?>
                        </div>

                        <?= Html::submitButton(Yii::t('user', 'Sign up'), ['class' => 'btn btn-primary btn-block']) ?>

                        <?php ActiveForm::end(); ?>

                        <p class="mb-0 mt-2">
                            <?= Html::a(Yii::t('user', 'Already have an account? Signin'), ['/user/security/login']) ?>
                        </p>
                        </div>
				</div>
                <div class="col-md-6 d-flex align-items-stretch">
                    <div class="signup-image-container w-100 d-flex align-items-center justify-content-center">
                        <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="signup illustration" class="img-fluid rounded shadow-sm signup-image">
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>

<?php
// Pass countries data to JavaScript
$this->registerJs('window.countriesData = ' . json_encode($model->getCountriesForSelect2()) . ';', \yii\web\View::POS_HEAD);

// Ensure Select2 is initialized
$this->registerJs('
$(document).ready(function() {
    // Wait for Select2 to be available
    function initSelect2() {
        if (typeof $.fn.select2 !== "undefined") {
            $("#country-select").each(function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    try {
                        $(this).select2();
                    } catch (e) {
                        console.error("Select2 initialization error:", e);
                    }
                }
            });
        } else {
            // Retry after a short delay
            setTimeout(initSelect2, 100);
        }
    }
    
    // Initialize immediately and also after a delay
    initSelect2();
    setTimeout(initSelect2, 500);
});
', \yii\web\View::POS_READY);
?>
