<?php
/**
 * @var yii\web\View $this
 * @var app\models\Package $model
 * @var yii\widgets\ActiveForm $form
 */
?>


<!-- attribute status -->
<?php echo $form->field($model, 'status')->dropDownList(
    \app\models\Package::buildStatus(),
    ['prompt' => 'Select Status']
) ?>

<!-- attribute name -->
<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<!-- attribute amount -->
<?php echo $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

<!-- attribute fee -->
<?php echo $form->field($model, 'fee')->textInput(['maxlength' => true]) ?>