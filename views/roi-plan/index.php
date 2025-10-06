<?php

use app\models\RoiPlan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Roi Plans';
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
<div class="giiant-crud roi-plan-index card">

    
    
    <?php \yii\widgets\Pjax::begin(['id'=>'pjax-main', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-main ul.pagination a, th a']) ?>
    <?php $create_button = Html::a('<i class="feather icon-plus"></i> ' . 'Create Roi Plan', ['create'], ['class' => 'btn btn-success']); ?>
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
            'lastPageLabel' => 'Last',
        ],
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $actionColumnTemplateString,
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="feather icon-eye"></i>', $url, [
                            'title' => 'View',
                            'class' => 'btn btn-info btn-xs'
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<i class="feather icon-edit"></i>', $url, [
                            'title' => 'Update',
                            'class' => 'btn btn-primary btn-xs'
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="feather icon-trash-2"></i>', $url, [
                            'title' => 'Delete',
                            'class' => 'btn btn-danger btn-xs',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this ROI Plan?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
                'urlCreator' => function ($action, RoiPlan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'contentOptions' => ['nowrap'=>'nowrap']
            ],
            ['class' => 'yii\grid\SerialColumn'],
            'rate',
            [
                'attribute' => 'frequency',
                'value' => function($model) {
                    return $model->getFrequencyLabel();
                }
            ],
            [
                'attribute' => 'tenure',
                'value' => function($model) {
                    return $model->getTenureLabel();
                }
            ],
            'release_date',
            //'status',
            //'created_at',
            //'updated_at',
            
        ],
    ]); ?>
    </div>
    </div>

</div>


<?php \yii\widgets\Pjax::end() ?>
