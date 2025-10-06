<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\Package $model
*/

$this->title = Yii::t('models', 'Package');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Package'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="giiant-crud package-update card">
    <div class="card-body">

    <?php echo $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
</div>
