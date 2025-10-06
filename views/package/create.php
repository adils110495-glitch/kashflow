<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\Package $model
*/

$this->title = Yii::t('models', 'Package');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Packages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud package-create card">
    <div class="card-body">
    <?= $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
</div>
