<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\ServiceCharges $model
*/

$this->title = Yii::t('models', 'Service Charges');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Service Charges'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="giiant-crud service-charges-update card">
    <div class="card-body">

    <?php echo $this->render('_form', [
    'model' => $model,
    ]); ?>
    </div>
</div>
