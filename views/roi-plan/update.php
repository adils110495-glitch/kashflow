<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\RoiPlan $model */

$this->title = 'Update Roi Plan: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Roi Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="giiant-crud roi-plan-update card">
    <div class="card-body">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</div>
