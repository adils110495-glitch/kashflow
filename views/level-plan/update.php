<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\LevelPlan[] $models
*/

$model = $models[0]; // For backward compatibility with breadcrumbs
$this->title = 'Update Level Plan: ' . $model->level;
$this->params['breadcrumbs'][] = ['label' => 'Level Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Level ' . $model->level, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="giiant-crud level-plan-update card">
    <div class="card-body">

    <?= $this->render('_form', [
        'models' => $models,
    ]) ?>
    </div>
</div>