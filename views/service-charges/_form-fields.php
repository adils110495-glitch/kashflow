<?php
/**
 * @var yii\web\View $this
 * @var app\models\ServiceCharges $model
 * @var yii\widgets\ActiveForm $form
 */
?>


<!-- attribute status -->
<?php echo $form->field($model, 'status')->textInput() ?>

<!-- attribute name -->
<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<!-- attribute rate -->
<?php echo $form->field($model, 'rate')->textInput(['maxlength' => true]) ?>