<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\bootstrap5\Tabs;

/**
* @var yii\web\View $this
* @var app\models\Package $model
*/

$this->title = 'Package';
$this->params['breadcrumbs'][] = ['label' => 'Packages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'View';
?>
<div class="giiant-crud package-view">

    <h1>
        <?= Html::encode($model->name) ?>
        <small><?= 'Package' ?></small>
    </h1>

    <div class="clearfix crud-navigation">

        <!-- menu buttons -->
        <div class='pull-left'>

                        <?php 
 echo Html::a(
            '<i class="feather icon-edit"></i> ' . 'Edit Package',
            [ 'update', 'id' => $model->id],
            ['class' => 'btn btn-info'])
            ?>
            
                                    <?php 
 echo Html::a(
            '<i class="feather icon-copy"></i> ' . 'Copy Package',
            ['create', 'id' => $model->id, 'Package'=> $model->hasMethod('getCopyParams') ? $model->getCopyParams() : $model->attributes],
            ['class' => 'btn btn-success'])
            ?>
                        
                        <?php 
 echo Html::a(
            '<i class="feather icon-plus"></i> ' . 'New Package',
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

    <?php $this->beginBlock('app\models\Package'); ?>

    
    <?php 
 echo DetailView::widget([
    'model' => $model,
    'attributes' => [
            'status',
        'name',
        'amount',
        'fee',
    ],
    ]);
    ?>

    
    <hr/>

        <?php 
 echo Html::a('<i class="feather icon-trash-2"></i> '
    . 'Delete Package', ['delete', 'id' => $model->id],
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
    'label'   => '<b>#' . Html::encode($model->id) . '</b>',
    'content' => $this->blocks['app\models\Package'],
    'active'  => true,
],
 ]
                 ]
    );
    ?>
</div>
