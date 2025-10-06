<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var app\models\ServiceCharges $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="service-charges-form">

    <?php $form = ActiveForm::begin([
    'id' => 'ServiceCharges',
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
    ]
    );
    ?>

        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
                            'label'   => 'Service Charges',
                            'content' => $this->render('_form-fields', ['form' => $form, 'model' => $model]),
                            'active'  => true,
                        ]
                    ]
                 ]
    );
    ?>
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

        <?php ActiveForm::end(); ?>

</div>

