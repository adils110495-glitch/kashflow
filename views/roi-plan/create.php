<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\RoiPlan $model */

$this->title = 'Create Roi Plan';
$this->params['breadcrumbs'][] = ['label' => 'Roi Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud roi-plan-create card">
    <div class="card-body">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</div>
