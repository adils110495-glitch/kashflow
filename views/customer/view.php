<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = $model->first_name . ' ' . $model->last_name;
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
            'first_name',
            'last_name',
            'phone',
            'address:ntext',
            'city',
            'state',
            'country',
            'zip_code',
            [
                'attribute' => 'referrer.user.username',
                'label' => 'Referrer',
                'value' => function($model) {
                    return $model->referrer && $model->referrer->user ? $model->referrer->user->username : 'N/A';
                }
            ],
            [
                'attribute' => 'package.name',
                'label' => 'Package',
                'value' => function($model) {
                    return $model->package ? $model->package->name : 'N/A';
                }
            ],
            [
                'attribute' => 'package.price',
                'label' => 'Package Price',
                'value' => function($model) {
                    return $model->package ? '$' . number_format($model->package->price, 2) : 'N/A';
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