<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $customer app\models\Customer */

$this->title = 'Customer Dashboard';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="customer-dashboard-index">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <h1>Welcome back, <?= Html::encode($customer->name) ?>!</h1>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Activity or Statistics -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Account Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h3 class="text-primary"><?= Html::encode($customer->currentPackage->name ?? 'Free') ?></h3>
                                <p class="text-muted">Current Plan</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h3 class="text-success"><?= $customer->status == 1 ? 'Active' : 'Inactive' ?></h3>
                                <p class="text-muted">Account Status</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h3 class="text-info"><?= date('M Y', $customer->created_at) ?></h3>
                                <p class="text-muted">Member Since</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h3 class="text-warning"><?= Html::encode($customer->country->name ?? 'N/A') ?></h3>
                                <p class="text-muted">Location</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Customer Info Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Your Profile</h5>
                </div>
                <div class="card-body">
                    <p><strong>Username:</strong> <?= Html::encode($customer->user->username) ?></p>
                    <p><strong>Name:</strong> <?= Html::encode($customer->name) ?></p>
                    <p><strong>Email:</strong> <?= Html::encode($customer->email) ?></p>
                    <p><strong>Mobile:</strong> <?= Html::encode($customer->mobile_no) ?></p>
                    <p><strong>Country:</strong> <?= Html::encode($customer->country->name ?? 'N/A') ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge <?= $customer->status == 1 ? 'badge-success' : 'badge-danger' ?>">
                            <?= $customer->getStatusText() ?>
                        </span>
                    </p>
                    <div class="mt-3">
                        <?= Html::a('Edit Profile', ['profile'], ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Package Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Current Package</h5>
                </div>
                <div class="card-body">
                    <?php if ($customer->currentPackage): ?>
                        <h4 class="text-primary"><?= Html::encode($customer->currentPackage->name) ?></h4>
                        <p><strong>Amount:</strong> $<?= number_format($customer->currentPackage->amount, 2) ?></p>
                        <p><strong>Fee:</strong> $<?= number_format($customer->currentPackage->fee, 2) ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge <?= $customer->currentPackage->status == 1 ? 'badge-success' : 'badge-warning' ?>">
                                <?= $customer->currentPackage->getStatusText() ?>
                            </span>
                        </p>
                    <?php else: ?>
                        <p class="text-muted">No package assigned</p>
                    <?php endif; ?>
                    <div class="mt-3">
                        <?= Html::button('<i class="fas fa-arrow-up"></i> Upgrade Package', ['class' => 'btn btn-success btn-sm upgrade-package-btn']) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?= Html::a('<i class="fas fa-user"></i> View Profile', ['profile'], ['class' => 'btn btn-outline-primary btn-sm mb-2']) ?>
                        <?= Html::a('<i class="fas fa-box"></i> Browse Packages', ['/package/index'], ['class' => 'btn btn-outline-success btn-sm mb-2']) ?>
                        <?= Html::a('<i class="fas fa-chart-line"></i> ROI Plans', ['/roi-plan/index'], ['class' => 'btn btn-outline-info btn-sm mb-2']) ?>
                        <?= Html::a('<i class="fas fa-sign-out-alt"></i> Logout', ['/site/logout'], ['class' => 'btn btn-outline-secondary btn-sm', 'data-method' => 'post', 'data-confirm' => 'Are you sure you want to logout?']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        
        <!-- Recent Activity Section -->
        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        <div class="activity-item">
                            <i class="fas fa-user-plus text-success"></i>
                            <span>Account created successfully</span>
                            <small class="text-muted"><?= date('M d, Y', $customer->created_at) ?></small>
                        </div>
                        <div class="activity-item">
                            <i class="fas fa-box text-info"></i>
                            <span>Subscribed to <?= $customer->currentPackage ? Html::encode($customer->currentPackage->name) : 'Free' ?> package</span>
                            <small class="text-muted"><?= date('M d, Y', $customer->updated_at) ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

</style>

<?= $this->render('_upgrade_modal') ?>