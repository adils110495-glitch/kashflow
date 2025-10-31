<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\RewardPlan[] $models
*/

$this->title = 'Manage Reward Plans';
$this->params['breadcrumbs'][] = ['label' => 'Reward Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud reward-plan-manage card">
    <div class="card-header">
        <h4 class="card-title">
            <i class="feather icon-settings"></i> <?= 'Reward Plans' ?>
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
            <strong>Manage All Reward Plans:</strong> You can add, edit, or remove multiple reward plans at once using this form.
        </div>

        <?= $this->render('_form', [
            'models' => $models,
        ]) ?>
    </div>
</div>


