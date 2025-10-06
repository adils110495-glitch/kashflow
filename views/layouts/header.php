<?php
use yii\helpers\Html;
use yii\helpers\Url;

// Get current controller and action
$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;
$controllerName = ucfirst($controller);

// Define page titles and breadcrumbs based on controller/action
$pageConfig = [
    'site' => [
        'index' => ['title' => 'Dashboard Analytics', 'breadcrumbs' => [['label' => 'Dashboard Analytics', 'url' => null]]],
    ],
    'package' => [
        'index' => ['title' => 'Package Management', 'breadcrumbs' => [['label' => 'Packages', 'url' => null]]],
        'create' => ['title' => 'Create Package', 'breadcrumbs' => [['label' => 'Packages', 'url' => ['package/index']], ['label' => 'Create', 'url' => null]]],
        'update' => ['title' => 'Update Package', 'breadcrumbs' => [['label' => 'Packages', 'url' => ['package/index']], ['label' => 'Update', 'url' => null]]],
        'view' => ['title' => 'View Package', 'breadcrumbs' => [['label' => 'Packages', 'url' => ['package/index']], ['label' => 'View', 'url' => null]]],
    ],
    'country' => [
        'index' => ['title' => 'Country Management', 'breadcrumbs' => [['label' => 'Countries', 'url' => null]]],
        'create' => ['title' => 'Create Country', 'breadcrumbs' => [['label' => 'Countries', 'url' => ['country/index']], ['label' => 'Create', 'url' => null]]],
        'update' => ['title' => 'Update Country', 'breadcrumbs' => [['label' => 'Countries', 'url' => ['country/index']], ['label' => 'Update', 'url' => null]]],
        'view' => ['title' => 'View Country', 'breadcrumbs' => [['label' => 'Countries', 'url' => ['country/index']], ['label' => 'View', 'url' => null]]],
    ],
    'states' => [
        'index' => ['title' => 'States Management', 'breadcrumbs' => [['label' => 'States', 'url' => null]]],
        'create' => ['title' => 'Create State', 'breadcrumbs' => [['label' => 'States', 'url' => ['states/index']], ['label' => 'Create', 'url' => null]]],
        'update' => ['title' => 'Update State', 'breadcrumbs' => [['label' => 'States', 'url' => ['states/index']], ['label' => 'Update', 'url' => null]]],
        'view' => ['title' => 'View State', 'breadcrumbs' => [['label' => 'States', 'url' => ['states/index']], ['label' => 'View', 'url' => null]]],
    ],
    'level-plan' => [
        'index' => ['title' => 'Level Plan Management', 'breadcrumbs' => [['label' => 'Level Plans', 'url' => null]]],
        'create' => ['title' => 'Create Level Plan', 'breadcrumbs' => [['label' => 'Level Plans', 'url' => ['level-plan/index']], ['label' => 'Create', 'url' => null]]],
        'update' => ['title' => 'Update Level Plan', 'breadcrumbs' => [['label' => 'Level Plans', 'url' => ['level-plan/index']], ['label' => 'Update', 'url' => null]]],
        'view' => ['title' => 'View Level Plan', 'breadcrumbs' => [['label' => 'Level Plans', 'url' => ['level-plan/index']], ['label' => 'View', 'url' => null]]],
        'manage' => ['title' => 'Manage Level Plans', 'breadcrumbs' => [['label' => 'Level Plans', 'url' => ['level-plan/index']], ['label' => 'Manage', 'url' => null]]],
    ],
    'roi-plan' => [
        'index' => ['title' => 'ROI Plan Management', 'breadcrumbs' => [['label' => 'ROI Plans', 'url' => null]]],
        'create' => ['title' => 'Create ROI Plan', 'breadcrumbs' => [['label' => 'ROI Plans', 'url' => ['roi-plan/index']], ['label' => 'Create', 'url' => null]]],
        'update' => ['title' => 'Update ROI Plan', 'breadcrumbs' => [['label' => 'ROI Plans', 'url' => ['roi-plan/index']], ['label' => 'Update', 'url' => null]]],
        'view' => ['title' => 'View ROI Plan', 'breadcrumbs' => [['label' => 'ROI Plans', 'url' => ['roi-plan/index']], ['label' => 'View', 'url' => null]]],
    ],
    'service-charges' => [
        'index' => ['title' => 'Service Charges Management', 'breadcrumbs' => [['label' => 'Service Charges', 'url' => null]]],
        'create' => ['title' => 'Create Service Charge', 'breadcrumbs' => [['label' => 'Service Charges', 'url' => ['service-charges/index']], ['label' => 'Create', 'url' => null]]],
        'update' => ['title' => 'Update Service Charge', 'breadcrumbs' => [['label' => 'Service Charges', 'url' => ['service-charges/index']], ['label' => 'Update', 'url' => null]]],
        'view' => ['title' => 'View Service Charge', 'breadcrumbs' => [['label' => 'Service Charges', 'url' => ['service-charges/index']], ['label' => 'View', 'url' => null]]],
    ],
    'user' => [
        'index' => ['title' => 'User Management', 'breadcrumbs' => [['label' => 'Users', 'url' => null]]],
        'profile' => ['title' => 'User Profile', 'breadcrumbs' => [['label' => 'Profile', 'url' => null]]],
    ],
];

// Get current page configuration
$currentConfig = $pageConfig[$controller][$action] ?? [
    'title' => ucfirst($controller) . ' ' . ucfirst($action),
    'breadcrumbs' => [['label' => ucfirst($controller), 'url' => null]]
];

$pageTitle = $currentConfig['title'];
$breadcrumbs = $currentConfig['breadcrumbs'];
?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10"><?= Html::encode($pageTitle) ?></h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= Url::to(['/site/index']) ?>"><i class="feather icon-home"></i></a>
                    </li>
                    <?php foreach ($breadcrumbs as $breadcrumb): ?>
                        <li class="breadcrumb-item">
                            <?php if ($breadcrumb['url']): ?>
                                <a href="<?= Url::to($breadcrumb['url']) ?>"><?= Html::encode($breadcrumb['label']) ?></a>
                            <?php else: ?>
                                <?= Html::encode($breadcrumb['label']) ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>