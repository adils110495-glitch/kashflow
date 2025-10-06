<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\LevelPlan[] $models
*/

$this->title = 'Manage Level Plans';
$this->params['breadcrumbs'][] = ['label' => 'Level Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud level-plan-manage card">
    <div class="card-header">
        <h4 class="card-title">
            <i class="feather icon-settings"></i> <?= 'Level Plans' ?>
            <small><?= 'Manage All' ?></small>
        </h4>
        <div class="card-header-right">
            <?= Html::a('<i class="feather icon-arrow-left"></i> Back to Index', ['index'], ['class' => 'btn btn-secondary btn-sm']) ?>
            <?= Html::a('<i class="feather icon-plus"></i> Create New', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="feather icon-info"></i>
            <strong>Manage All Level Plans:</strong> You can add, edit, or remove multiple level plans at once using this form.
            Use the "Add Level" button to add new levels and the remove button to delete existing ones.
        </div>

        <?= $this->render('_form', [
            'models' => $models,
        ]) ?>
    </div>
</div>