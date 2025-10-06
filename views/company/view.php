<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Company */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="company-view">

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
                            'style' => 'max-width: 200px; max-height: 200px; object-fit: contain;'
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
                            'class' => 'btn btn-link'
                        ]);
                    }
                    return '';
                },
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>