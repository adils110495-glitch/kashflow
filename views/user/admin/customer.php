<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'All Customers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>

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
                'attribute' => 'referrer_id',
                'label' => 'Referrer',
                'value' => function($model) {
                    if ($model->referrer_id) {
                        $referrer = \app\models\Customer::findOne($model->referrer_id);
                        return $referrer && $referrer->user ? $referrer->user->username : 'N/A';
                    }
                    return 'None';
                }
            ],
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