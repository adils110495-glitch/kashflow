<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\bootstrap5\Tabs;

/**
* @var yii\web\View $this
* @var app\models\ServiceCharges $model
*/

$this->title = Yii::t('models', 'Service Charges');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models.plural', 'Service Charges'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'View';
?>
<div class="giiant-crud service-charges-view">

    <h1>
        <?= Html::encode($model->name) ?>
        <small><?= 'Service Charges' ?></small>
    </h1>

    <div class="clearfix crud-navigation">

        <!-- menu buttons -->
        <div class='pull-left'>

                        <?php 
 echo Html::a(
            '<i class="feather icon-edit"></i> ' . 'Edit Service Charges',
            [ 'update', 'id' => $model->id],
            ['class' => 'btn btn-info'])
            ?>
            
                                    <?php 
 echo Html::a(
            '<i class="feather icon-copy"></i> ' . 'Copy Service Charges',
            ['create', 'id' => $model->id, 'ServiceCharges'=> $model->hasMethod('getCopyParams') ? $model->getCopyParams() : $model->attributes],
            ['class' => 'btn btn-success'])
            ?>
                        
                        <?php 
 echo Html::a(
            '<i class="feather icon-plus"></i> ' . 'New Service Charges',
            ['create'],
            ['class' => 'btn btn-success'])
            ?>
                    </div>

        <div class="pull-right">
            <?= Html::a('<i class="feather icon-list"></i> '
            . 'Full list', ['index'], ['class'=>'btn btn-default']) ?>
        </div>

    </div>

    <hr/>

    <?php $this->beginBlock('app\models\ServiceCharges'); ?>

    
    <?php 
 echo DetailView::widget([
    'model' => $model,
    'attributes' => [
            'status',
        'name',
        'rate',
    ],
    ]);
    ?>

    
    <hr/>

        <?php 
 echo Html::a('<i class="feather icon-trash-2"></i> '
    . 'Delete Service Charges', ['delete', 'id' => $model->id],
    [
    'class' => 'btn btn-danger',
    'data-confirm' => '' . 'Are you sure to delete this item?' . '',
    'data-method' => 'post',
    ]);
    ?>
        <?php $this->endBlock(); ?>


    
    <?php 
        echo Tabs::widget(
                 [
                     'id' => 'relation-tabs',
                     'encodeLabels' => false,
                     'items' => [
 [
    'label'   => '<b>' . \Yii::t('cruds', '# {primaryKey}', ['primaryKey' => Html::encode($model->id)]) . '</b>',
    'content' => $this->blocks['app\models\ServiceCharges'],
    'active'  => true,
],
 ]
                 ]
    );
    ?>
</div>
