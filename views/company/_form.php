<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\Company */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'type' => 'email']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'phone_no')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'logo')->widget(FileInput::classname(), [
                'options' => [
                    'accept' => 'image/*',
                    'multiple' => false
                ],
                'pluginOptions' => [
                    'allowedFileExtensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg'],
                    'showPreview' => true,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'browseClass' => 'btn btn-primary',
                    'browseIcon' => '<i class="feather icon-upload"></i> ',
                    'browseLabel' => 'Select Logo',
                    'removeLabel' => 'Remove',
                    'removeIcon' => '<i class="feather icon-trash"></i> ',
                    'previewFileType' => 'image',
                    'initialPreview' => $model->logo ? [Html::img(Yii::getAlias('@web/' . $model->logo), ['class' => 'file-preview-image', 'style' => 'width:auto;height:160px'])] : [],
                    'initialPreviewAsData' => true,
                    'initialCaption' => $model->logo ? basename($model->logo) : '',
                    'overwriteInitial' => true,
                    'maxFileSize' => 2048, // 2MB
                    'resizeImage' => true,
                    'resizePreference' => 'width',
                    'resizeQuality' => 0.9,
                    'resizeDefaultImageType' => 'image/jpeg'
                ]
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'website_link')->textInput(['maxlength' => true, 'type' => 'url']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>