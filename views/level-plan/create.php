<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\LevelPlan[] $models
*/

$this->title = 'Create Level Plans';
$this->params['breadcrumbs'][] = ['label' => 'Level Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud level-plan-create card">
    <div class="card-body">

    <?= $this->render('_form', [
        'models' => $models,
    ]) ?>
    </div>
</div>