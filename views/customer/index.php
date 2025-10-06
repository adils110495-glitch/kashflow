<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use app\models\Customer;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customers';
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
<div class="giiant-crud customer-index card">

    <?php \yii\widgets\Pjax::begin(['id'=>'pjax-main', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-main ul.pagination a, th a']) ?>
    <?php $create_button = Html::a('<i class="feather icon-plus"></i> ' . 'New Customer', ['create'], ['class' => 'btn btn-success']); ?>
    <?= $this->render('../card/header-button.php', ['create_button' => $create_button])?>
    </div>
    
    <!-- Filter Form -->
    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'options' => ['class' => 'form-inline mb-3'],
        ]); ?>
        
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <?= Html::label('Username', 'customersearch-username', ['class' => 'form-label']) ?>
                    <?= Html::textInput('CustomerSearch[username]', $searchModel->username, [
                        'placeholder' => 'Search by username...',
                        'class' => 'form-control',
                        'id' => 'customersearch-username'
                    ]) ?>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group">
                    <?= Html::label('Package', 'customersearch-package_id', ['class' => 'form-label']) ?>
                    <?= Html::dropDownList('CustomerSearch[package_id]', $searchModel->package_id,
                        \yii\helpers\ArrayHelper::map(\app\models\Package::find()->all(), 'id', 'name'),
                        ['prompt' => 'All Packages', 'class' => 'form-control', 'id' => 'customersearch-package_id']
                    ) ?>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group">
                    <?= Html::label('From Date', 'customersearch-from_date', ['class' => 'form-label']) ?>
                    <?= Html::textInput('CustomerSearch[from_date]', $searchModel->from_date, [
                        'placeholder' => 'From Date',
                        'class' => 'form-control',
                        'id' => 'customersearch-from_date',
                        'type' => 'date'
                    ]) ?>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group">
                    <?= Html::label('To Date', 'customersearch-to_date', ['class' => 'form-label']) ?>
                    <?= Html::textInput('CustomerSearch[to_date]', $searchModel->to_date, [
                        'placeholder' => 'To Date',
                        'class' => 'form-control',
                        'id' => 'customersearch-to_date',
                        'type' => 'date'
                    ]) ?>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group">
                    <label>&nbsp;</label><br>
                    <?= Html::submitButton('Filter', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Reset', ['index'], ['class' => 'btn btn-secondary ml-2']) ?>
                </div>
            </div>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>
    <div class="card-body">
    <div class="table-responsive">

        <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
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
			[
				'attribute' => 'status',
				'format' => 'raw',
				'value' => function($model) {
					$class = $model->status == 1 ? 'badge-success' : 'badge-danger';
                    $text = $model->status == 1 ? 'Active' : 'Inactive';
                    return "<span class='badge {$class}'>{$text}</span>";
				}
			],
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
			'name',
			'mobile_no',
            [
                'attribute' => 'referral_code',
                'label' => 'Referral Code',
                'value' => function($model) {
                    return $model->referral_code ?: 'N/A';
                }
            ],
            [
                'attribute' => 'currentPackage.name',
                'label' => 'Package',
                'value' => function($model) {
                    return $model->currentPackage ? $model->currentPackage->name : 'N/A';
                }
            ],
            [
                'attribute' => 'currentPackage.amount',
                'label' => 'Package Amount',
                'value' => function($model) {
                    return $model->currentPackage ? '$' . number_format($model->currentPackage->amount, 2) : 'N/A';
                }
            ],
            [
                'attribute' => 'buy_date',
                'label' => 'Buy Date',
                'value' => function($model) {
                    if ($model->currentPackage) {
                        $customerPackage = \app\models\CustomerPackage::find()
                            ->where(['customer_id' => $model->id])
                            ->andWhere(['package_id' => $model->currentPackage->id])
                            ->orderBy(['date' => SORT_DESC])
                            ->one();
                        return $customerPackage ? $customerPackage->date : 'N/A';
                    }
                    return 'N/A';
                }
            ],
			'created_at:datetime'
                ]
        ]); ?>
    </div>
    </div>

</div>


<?php \yii\widgets\Pjax::end() ?>