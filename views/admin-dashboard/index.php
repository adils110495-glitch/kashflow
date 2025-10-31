<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $stats */
/** @var array $recentWithdrawals */
/** @var array $recentCustomers */

$this->title = 'Admin Dashboard';
?>

<div class="admin-dashboard">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Admin Dashboard</h1>
                <div class="text-muted">
                    Welcome, <?= Html::encode(Yii::$app->user->identity->username) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= number_format($stats['total_customers']) ?></h4>
                            <p class="card-text">Total Customers</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= number_format($stats['active_customers']) ?></h4>
                            <p class="card-text">Active Customers</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= number_format($stats['pending_withdrawals']) ?></h4>
                            <p class="card-text">Pending Withdrawals</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">₹<?= number_format($stats['total_income'], 2) ?></h4>
                            <p class="card-text">Total Income</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-rupee-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <?= Html::a('<i class="fas fa-list"></i> Manage Withdrawals', ['/admin/withdrawals'], [
                                'class' => 'btn btn-outline-primary w-100'
                            ]) ?>
                        </div>
                        <div class="col-md-3 mb-2">
                            <?= Html::a('<i class="fas fa-users"></i> Manage Customers', ['/admin/customer'], [
                                'class' => 'btn btn-outline-success w-100'
                            ]) ?>
                        </div>
                        <div class="col-md-3 mb-2">
                            <?= Html::a('<i class="fas fa-comments"></i> Support Tickets', ['/admin-ticket/index'], [
                                'class' => 'btn btn-outline-info w-100'
                            ]) ?>
                        </div>
                        <div class="col-md-3 mb-2">
                            <?= Html::a('<i class="fas fa-sign-out-alt"></i> Logout', ['/admin-auth/logout'], [
                                'class' => 'btn btn-outline-danger w-100',
                                'data-method' => 'post'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Withdrawals</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recentWithdrawals)): ?>
                        <p class="text-muted">No recent withdrawals</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentWithdrawals as $withdrawal): ?>
                                        <tr>
                                            <td><?= Html::encode($withdrawal->customer->name ?? 'N/A') ?></td>
                                            <td>₹<?= number_format($withdrawal->amount, 2) ?></td>
                                            <td>
                                                <span class="badge badge-<?= $withdrawal->status == Withdrawal::STATUS_PENDING ? 'warning' : ($withdrawal->status == Withdrawal::STATUS_APPROVED ? 'success' : 'secondary') ?>">
                                                    <?= $withdrawal->getStatusText() ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', $withdrawal->created_at) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Customers</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recentCustomers)): ?>
                        <p class="text-muted">No recent customers</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentCustomers as $customer): ?>
                                        <tr>
                                            <td><?= Html::encode($customer->name) ?></td>
                                            <td><?= Html::encode($customer->email) ?></td>
                                            <td>
                                                <span class="badge badge-<?= $customer->status == Customer::STATUS_ACTIVE ? 'success' : 'secondary' ?>">
                                                    <?= $customer->getStatusText() ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', $customer->created_at) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
