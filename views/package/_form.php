<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var app\models\Package $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="package-form">

    <?php $form = ActiveForm::begin([
        'id' => 'Package',
        'layout' => 'horizontal',
        'enableClientValidation' => true,
        'errorSummaryCssClass' => 'error-summary alert alert-danger',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-2',
                'wrapper' => 'col-sm-8',
                'error' => '',
                'hint' => '',
            ],
        ],
    ]);
    ?>

    <?= $this->render('_form-fields', ['form' => $form, 'model' => $model]) ?>
    <hr/>

    <?php echo $form->errorSummary($model); ?>

    <?= Html::submitButton(
        '<i class="feather icon-check"></i> ' .
        ($model->isNewRecord ? 'Create' : 'Save'),
        [
            'id' => 'save-' . $model->formName(),
            'class' => 'btn btn-success'
        ]
    );
    ?>
    <?= Html::a(
        '<i class="feather icon-x"></i> ' .
        'Cancel',
        'index',
        [
            'id' => 'cancel-' . $model->formName(),
            'class' => 'btn btn-danger'
        ]
    );
    ?>

    <?php ActiveForm::end(); ?>

</div>

