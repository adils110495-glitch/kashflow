<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="app">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> - KashFlow Customer Portal</title>
    <?php $this->head() ?>
    
    <!-- Customer-specific styles -->
    <style>
        .customer-layout {
            background: #f8f9fa;
        }
        .customer-content {
            min-height: calc(100vh - 60px);
        }
        .pcoded-main-container {
            margin-left: 270px;
            transition: margin-left 0.3s ease;
        }
        @media (max-width: 991px) {
            .pcoded-main-container {
                margin-left: 0;
            }
        }
        .customer-badge {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }
    </style>
</head>
<body class="customer-layout">
<?php $this->beginBody() ?>

<!-- Customer Topbar -->
<?= $this->render('customer-topbar') ?>

<!-- Customer Sidebar -->
<?= $this->render('customer-sidebar') ?>

<!-- Main content container -->
<div class="pcoded-main-container customer-content">
    <div class="pcoded-content">
        <!-- Header -->
        <?php //= $this->render('header') ?>
        
        <!-- Page content -->
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <!-- Breadcrumbs (if needed) -->
                    <?php if (isset($this->params['breadcrumbs']) && !empty($this->params['breadcrumbs'])): ?>
                    <div class="page-header">
                        <div class="page-block">
                            <div class="row align-items-center">
                                <div class="col-md-12">
                                    <div class="page-header-title">
                                        <h5 class="m-b-10"><?= Html::encode($this->title) ?></h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="<?= Url::to(['/customer-dashboard/index']) ?>">
                                                <i class="feather icon-home"></i>
                                            </a>
                                        </li>
                                        <?php foreach ($this->params['breadcrumbs'] as $breadcrumb): ?>
                                            <?php if (is_string($breadcrumb)): ?>
                                                <li class="breadcrumb-item active"><?= Html::encode($breadcrumb) ?></li>
                                            <?php else: ?>
                                                <li class="breadcrumb-item">
                                                    <?= Html::a(Html::encode($breadcrumb['label']), $breadcrumb['url']) ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Flash messages -->
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="feather icon-check-circle"></i>
                            <?= Yii::$app->session->getFlash('success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="feather icon-alert-circle"></i>
                            <?= Yii::$app->session->getFlash('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (Yii::$app->session->hasFlash('warning')): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="feather icon-alert-triangle"></i>
                            <?= Yii::$app->session->getFlash('warning') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (Yii::$app->session->hasFlash('info')): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="feather icon-info"></i>
                            <?= Yii::$app->session->getFlash('info') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Main content -->
                    <div class="page-body">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>