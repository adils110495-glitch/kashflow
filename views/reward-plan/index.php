<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
*/

$this->title = 'Reward Plans';
$this->params['breadcrumbs'][] = $this->title;

if (isset($actionColumnTemplates)) {
    $actionColumnTemplate = implode(' ', $actionColumnTemplates);
    $actionColumnTemplateString = $actionColumnTemplate;
} else {
    Yii::$app->view->params['pageButtons'] = Html::a('<i class="feather icon-plus"></i> ' . 'New', ['create'], ['class' => 'btn btn-success']);
    $actionColumnTemplateString = "{view} {update} {delete}";
}
$actionColumnTemplateString = '<div class="action-buttons">'.$actionColumnTemplateString.'</div>';
?>
<div class="giiant-crud reward-plan-index card">

    <?php \yii\widgets\Pjax::begin(['id'=>'pjax-main', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-main ul.pagination a, th a']) ?>
    <?php $create_button = Html::a('<i class="feather icon-plus"></i> ' . 'New Reward Plan', ['create'], ['class' => 'btn btn-success']) . ' ' . Html::a('<i class="feather icon-settings"></i> ' . 'Manage All', ['manage'], ['class' => 'btn btn-primary']); ?>
    <?= $this->render('../card/header-button.php', ['create_button' => $create_button])?>
    </div>
    <div class="card-body">
    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-hover'],
            'pager' => [
                'class' => yii\widgets\LinkPager::class,
                'firstPageLabel' => 'First',
                'lastPageLabel' => 'Last'
            ],
            'layout' => '{summary}{pager}<br/>{items}{pager}',
            'columns' => [
                [
            'class' => 'yii\grid\ActionColumn',
            'template' => $actionColumnTemplateString,
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    $options = [
                        'title' => 'View',
                        'aria-label' => 'View',
                        'data-pjax' => '0',
                    ];
                    return Html::a('<i class="feather icon-eye"></i>', $url, $options);
                }
            ],
            'urlCreator' => function($action, $model, $key, $index) {
                $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string) $key];
                $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
                return Url::toRoute($params);
            },
            'contentOptions' => ['nowrap'=>'nowrap']
        ],
                [
                    'class' => 'yii\grid\SerialColumn',
                ],
                [
                    'attribute' => 'business_amount',
                    'format' => ['decimal', 2],
                    'label' => 'Business Amount',
                ],
                [
                    'attribute' => 'reward',
                    'format' => 'text',
                    'label' => 'Reward',
                ],
                [
                    'attribute' => 'reward_amount',
                    'format' => ['decimal', 2],
                    'label' => 'Reward Amount',
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'label' => 'Status',
                    'value' => function($model) {
                        $class = $model->status == 1 ? 'label-success' : 'label-danger';
                        return '<span class="label ' . $class . '">' . $model->getStatusLabel() . '</span>';
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'datetime',
                    'label' => 'Created',
                ],
            ],
        ]); ?>
    </div>

    <?php \yii\widgets\Pjax::end() ?>

    </div>
</div>


