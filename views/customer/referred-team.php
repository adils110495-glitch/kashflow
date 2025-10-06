<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $customers app\models\Customer[] */
/* @var $selectedCustomer string */
/* @var $dateFrom string */
/* @var $dateTo string */

$this->title = 'Referred Team';
$this->params['breadcrumbs'][] = $this->title;

if (isset($actionColumnTemplates)) {
    $actionColumnTemplate = implode(' ', $actionColumnTemplates);
    $actionColumnTemplateString = $actionColumnTemplate;
} else {
    $actionColumnTemplateString = "{view}";
}
$actionColumnTemplateString = '<div class="action-buttons">'.$actionColumnTemplateString.'</div>';
?>
<div class="giiant-crud referred-team-index card">

    <?php \yii\widgets\Pjax::begin(['id'=>'pjax-main', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-main ul.pagination a, th a']) ?>
    <?= $this->render('../card/header-button.php', ['create_button' => '']) ?>
    </div>
    
    <!-- Filter Form -->
    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'options' => ['class' => 'form-inline mb-3'],
        ]); ?>
        
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <?= Html::label('Select Customer', 'customer', ['class' => 'form-label']) ?>
                    <?= Html::dropDownList('customer', $selectedCustomer, 
                        ArrayHelper::map($customers, 'id', function($model) {
                            return $model->user ? $model->user->username : 'N/A';
                        }), 
                        ['class' => 'form-control', 'prompt' => 'Select Customer', 'id' => 'customer']
                    ) ?>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <?= Html::label('From Date', 'date_from', ['class' => 'form-label']) ?>
                    <?= Html::textInput('date_from', $dateFrom, [
                        'placeholder' => 'From Date',
                        'class' => 'form-control',
                        'id' => 'date_from',
                        'type' => 'date'
                    ]) ?>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <?= Html::label('To Date', 'date_to', ['class' => 'form-label']) ?>
                    <?= Html::textInput('date_to', $dateTo, [
                        'placeholder' => 'To Date',
                        'class' => 'form-control',
                        'id' => 'date_to',
                        'type' => 'date'
                    ]) ?>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <label>&nbsp;</label><br>
                    <?= Html::submitButton('Filter', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Reset', ['referred-team'], ['class' => 'btn btn-secondary ml-2']) ?>
                </div>
            </div>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>
    
    <div class="card-body">
    <div class="table-responsive">

        <?php echo GridView::widget([
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