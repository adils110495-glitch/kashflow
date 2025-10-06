<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
*/

$this->title = Yii::t('models', 'Country');
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
<div class="giiant-crud country-index card">

    
    
    <?php \yii\widgets\Pjax::begin(['id'=>'pjax-main', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-main ul.pagination a, th a']) ?>
    <?php $create_button = Html::a('<i class="feather icon-plus"></i> ' . 'New Country', ['create'], ['class' => 'btn btn-success']); ?>
    <?= $this->render('../card/header-button.php', ['create_button' => $create_button])?>
    </div>
    <div class="card-body">
    <div class="table-responsive">
        <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
                // using the column name as key, not mapping to 'id' like the standard generator
                $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string) $key];
                $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
                return Url::toRoute($params);
            },
            'contentOptions' => ['nowrap'=>'nowrap']
        ],
			'status',
			'name',
			'language',
			'lang_code',
			'country_code',
			'mobile_code',
			'flag',
                ]
        ]); ?>
    </div>
    </div>

</div>


<?php \yii\widgets\Pjax::end() ?>


