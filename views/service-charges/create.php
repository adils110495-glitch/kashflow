<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\ServiceCharges $model
*/

$this->title = Yii::t('models', 'Service Charges');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Service Charges'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud service-charges-create card">
    <div class="card-body">

    <?= $this->render('_form', [
    'model' => $model,
    ]); ?>
    </div>
</div>
