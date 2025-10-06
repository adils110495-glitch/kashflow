<?php
/**
 * @var yii\web\View $this
 * @var app\models\Country $model
 * @var yii\widgets\ActiveForm $form
 */
?>


<!-- attribute status -->
<?php echo $form->field($model, 'status')->textInput() ?>

<!-- attribute name -->
<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<!-- attribute language -->
<?php echo $form->field($model, 'language')->textInput(['maxlength' => true]) ?>

<!-- attribute lang_code -->
<?php echo $form->field($model, 'lang_code')->textInput(['maxlength' => true]) ?>

<!-- attribute country_code -->
<?php echo $form->field($model, 'country_code')->textInput(['maxlength' => true]) ?>

<!-- attribute mobile_code -->
<?php echo $form->field($model, 'mobile_code')->textInput(['maxlength' => true]) ?>

<!-- attribute flag -->
<?php echo $form->field($model, 'flag')->textInput(['maxlength' => true]) ?>