<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\States $model
*/

$this->title = Yii::t('models', 'States');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'States'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud states-create card">
    <div class="card-body">
    <?= $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
</div>
