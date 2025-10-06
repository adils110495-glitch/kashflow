<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/**
* @var yii\web\View $this
* @var app\models\LevelPlan $model
*/

$this->title = 'Level Plan: ' . $model->level;
$this->params['breadcrumbs'][] = ['label' => 'Level Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Level ' . $model->level, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'View';
?>
<div class="giiant-crud level-plan-view">

    <h1>
        <?= Html::encode('Level ' . $model->level) ?>
        <small><?= 'Level Plan Details' ?></small>
    </h1>

    <div class="clearfix crud-navigation">

        <!-- menu buttons -->
        <div class='pull-left'>
            <?= Html::a(
                '<i class="feather icon-edit"></i> ' . 'Edit Level Plan',
                ['update', 'id' => $model->id],
                ['class' => 'btn btn-info']
            ) ?>
            
            <?= Html::a(
                '<i class="feather icon-copy"></i> ' . 'Copy Level Plan',
                ['create', 'id' => $model->id, 'LevelPlan'=> $model->attributes],
                ['class' => 'btn btn-success']
            ) ?>
                        
            <?= Html::a(
                '<i class="feather icon-plus"></i> ' . 'New Level Plan',
                ['create'],
                ['class' => 'btn btn-success']
            ) ?>
        </div>

        <div class="pull-right">
            <?= Html::a('<i class="feather icon-list"></i> ' . 'Full list', ['index'], ['class'=>'btn btn-default']) ?>
        </div>

    </div>

    <hr/>

    <?php $this->beginBlock('app\\models\\LevelPlan'); ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'level',
                'format' => 'text',
                'label' => 'Level Number',
            ],
            [
                'attribute' => 'rate',
                'format' => 'raw',
                'label' => 'Commission Rate',
                'value' => $model->getFormattedRate(),
            ],
            [
                'attribute' => 'no_of_directs',
                'format' => 'text',
                'label' => 'Required Direct Referrals',
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'label' => 'Status',
                'value' => function($model) {
                    $class = $model->status == 1 ? 'label-success' : 'label-danger';
                    return '<span class="label ' . $class . '">' . $model->getStatusLabel() . '</span>';
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'label' => 'Created At',
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'label' => 'Updated At',
            ],
        ],
    ]); ?>

    <hr/>

    <?= Html::a('<i class="feather icon-trash-2"></i> ' . 'Delete Level Plan', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete Level ' . $model->level . '?',
            'method' => 'post',
        ],
    ]) ?>
    <?php $this->endBlock(); ?>

    <?= $this->blocks['app\\models\\LevelPlan']; ?>

</div>