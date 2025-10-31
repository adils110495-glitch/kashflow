<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var app\models\RewardPlan $model
*/

$this->title = 'Reward Plan #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Reward Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reward-plan-view card">
    <div class="card-header">
        <h4 class="card-title"><i class="feather icon-eye"></i> <?= Html::encode($this->title) ?></h4>
        <div class="card-header-right">
            <?= Html::a('<i class="feather icon-edit"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::a('<i class="feather icon-trash-2"></i> Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-sm',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <tr><th>ID</th><td><?= Html::encode($model->id) ?></td></tr>
            <tr><th>Business Amount</th><td><?= Yii::$app->formatter->asDecimal($model->business_amount, 2) ?></td></tr>
            <tr><th>Reward</th><td><?= Html::encode($model->reward) ?></td></tr>
            <tr><th>Reward Amount</th><td><?= Yii::$app->formatter->asDecimal($model->reward_amount, 2) ?></td></tr>
            <tr><th>Status</th><td><?= Html::encode($model->getStatusLabel()) ?></td></tr>
            <tr><th>Created At</th><td><?= Yii::$app->formatter->asDatetime($model->created_at) ?></td></tr>
            <tr><th>Updated At</th><td><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></td></tr>
        </table>
    </div>
</div>


