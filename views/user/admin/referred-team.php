<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $customers app\models\Customer[] */
/* @var $selectedCustomer string */
/* @var $dateFrom string */
/* @var $dateTo string */

$this->title = 'Referred Team';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="referred-team-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Filters</h5>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'options' => ['class' => 'row g-3']
            ]); ?>
            
            <div class="col-md-4">
                <?= Html::label('Select Customer', 'customer', ['class' => 'form-label']) ?>
                <?= Html::dropDownList('customer', $selectedCustomer, 
                    ArrayHelper::map($customers, 'id', function($model) {
                        return $model->user ? $model->user->username : 'N/A';
                    }), 
                    ['class' => 'form-select', 'prompt' => 'Select Customer']
                ) ?>
            </div>
            
            <div class="col-md-3">
                <?= Html::label('From Date', 'date_from', ['class' => 'form-label']) ?>
                <?= Html::input('date', 'date_from', $dateFrom, ['class' => 'form-control']) ?>
            </div>
            
            <div class="col-md-3">
                <?= Html::label('To Date', 'date_to', ['class' => 'form-label']) ?>
                <?= Html::input('date', 'date_to', $dateTo, ['class' => 'form-control']) ?>
            </div>
            
            <div class="col-md-2">
                <?= Html::label('&nbsp;', '', ['class' => 'form-label d-block']) ?>
                <?= Html::submitButton('Filter', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Clear', ['referred-team'], ['class' => 'btn btn-secondary ms-2']) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php if ($selectedCustomer): ?>
        <?php 
        $referrerCustomer = \app\models\Customer::findOne($selectedCustomer);
        $referrerName = $referrerCustomer && $referrerCustomer->user ? $referrerCustomer->user->username : 'Unknown';
        ?>
        <div class="alert alert-info">
            <strong>Showing referred team for:</strong> <?= Html::encode($referrerName) ?>
            <?php if ($dateFrom || $dateTo): ?>
                <br><strong>Date Range:</strong> 
                <?= $dateFrom ? Html::encode($dateFrom) : 'Start' ?> to <?= $dateTo ? Html::encode($dateTo) : 'End' ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\\grid\\SerialColumn'],

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
            [
                'attribute' => 'created_at',
                'label' => 'Joined Date',
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            [
                'attribute' => 'status',
                'label' => 'Status',
                'value' => function($model) {
                    return $model->status == 1 ? 'Active' : 'Inactive';
                },
                'contentOptions' => function($model) {
                    return ['class' => $model->status == 1 ? 'text-success' : 'text-danger'];
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>