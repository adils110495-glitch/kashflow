<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Companies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Company', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'short_name',
            'email:email',
            'phone_no',
            [
                'attribute' => 'logo',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->logo) {
                        return Html::img(Yii::getAlias('@web/' . $model->logo), [
                            'style' => 'width: 50px; height: 50px; object-fit: cover;'
                        ]);
                    }
                    return 'No Logo';
                },
            ],
            [
                'attribute' => 'website_link',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->website_link) {
                        return Html::a($model->website_link, $model->website_link, [
                            'target' => '_blank',
                            'class' => 'btn btn-link btn-sm'
                        ]);
                    }
                    return '';
                },
            ],
            'created_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>