<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="customer-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'user.username',
                'label' => 'Username',
                'value' => function($model) {
                    return $model->user ? $model->user->username : 'N/A';
                }
            ],
            [
                'attribute' => 'user.email',
                'label' => 'Email',
                'value' => function($model) {
                    return $model->user ? $model->user->email : 'N/A';
                }
            ],
            'name',
            'mobile_no',
            [
                'attribute' => 'country.name',
                'label' => 'Country',
                'value' => function($model) {
                    return $model->country ? $model->country->name : 'N/A';
                }
            ],
            'referral_code',
            [
                'attribute' => 'currentPackage.name',
                'label' => 'Package',
                'value' => function($model) {
                    return $model->currentPackage ? $model->currentPackage->name : 'N/A';
                }
            ],
            [
                'attribute' => 'currentPackage.amount',
                'label' => 'Package Amount',
                'value' => function($model) {
                    return $model->currentPackage ? '$' . number_format($model->currentPackage->amount, 2) : 'N/A';
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model) {
                    $class = $model->status == 1 ? 'badge-success' : 'badge-danger';
                    $text = $model->status == 1 ? 'Active' : 'Inactive';
                    return "<span class='badge {$class}'>{$text}</span>";
                }
            ],
        ],
    ]) ?>

</div>