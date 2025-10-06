<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\States $model
*/

$this->title = Yii::t('models', 'States');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'States'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="giiant-crud states-update card">
    <div class="card-body">

    <?php echo $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
</div>
