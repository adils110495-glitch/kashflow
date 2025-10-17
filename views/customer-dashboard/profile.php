<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $customer app\models\Customer */

$this->title = 'Customer Profile';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="customer-profile">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <h1><?= Html::encode($this->title) ?></h1>
                <p class="lead">View and manage your profile information</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Full Name</strong></label>
                                <p class="form-control-static"><?= Html::encode($customer->name) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Email Address</strong></label>
                                <p class="form-control-static"><?= Html::encode($customer->email) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Mobile Number</strong></label>
                                <p class="form-control-static"><?= Html::encode($customer->mobile_no) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Country</strong></label>
                                <p class="form-control-static"><?= Html::encode($customer->country->name ?? 'Not specified') ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Referral Code</strong></label>
                                <p class="form-control-static">
                                    <?= $customer->referral_code ? Html::encode($customer->referral_code) : '<em class="text-muted">Not generated</em>' ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Account Status</strong></label>
                                <p class="form-control-static">
                                    <span class="badge <?= $customer->status == 1 ? 'badge-success' : 'badge-danger' ?>">
                                        <?= $customer->getStatusText() ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Member Since</strong></label>
                                <p class="form-control-static"><?= date('F j, Y', $customer->created_at) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Last Updated</strong></label>
                                <p class="form-control-static"><?= date('F j, Y g:i A', $customer->updated_at) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Current Package Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Current Package</h5>
                </div>
                <div class="card-body">
                    <?php if ($customer->currentPackage): ?>
                        <div class="package-info">
                            <h4 class="text-primary mb-3"><?= Html::encode($customer->currentPackage->name) ?></h4>
                            
                            <div class="package-details">
                                <div class="detail-item">
                                    <span class="label">Amount:</span>
                                    <span class="value">$<?= number_format($customer->currentPackage->amount, 2) ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="label">Fee:</span>
                                    <span class="value">$<?= number_format($customer->currentPackage->fee, 2) ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="label">Status:</span>
                                    <span class="value">
                                        <span class="badge <?= $customer->currentPackage->status == 1 ? 'badge-success' : 'badge-warning' ?>">
                                            <?= $customer->currentPackage->getStatusText() ?>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No package assigned</p>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <?php if (Customer::canCustomerUpgrade($customer->id, null)): ?>
                            <?= Html::button('<i class="fas fa-arrow-up"></i> Upgrade Package', ['class' => 'btn btn-success btn-sm btn-block upgrade-package-btn']) ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <small>You have already upgraded your package.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Dashboard', ['index'], ['class' => 'btn btn-outline-primary btn-sm mb-2']) ?>
                        <?php if (Customer::canCustomerUpgrade($customer->id, null)): ?>
                            <?= Html::button('<i class="fas fa-arrow-up"></i> Upgrade Package', ['class' => 'btn btn-success btn-sm mb-2 upgrade-package-btn']) ?>
                        <?php endif; ?>
                        <?= Html::a('<i class="fas fa-edit"></i> Update Profile', ['/user/settings/profile'], ['class' => 'btn btn-outline-info btn-sm mb-2']) ?>
                        <?= Html::a('<i class="fas fa-key"></i> Change Password', ['/user/settings/account'], ['class' => 'btn btn-outline-warning btn-sm mb-2']) ?>
                        <?= Html::a('<i class="fas fa-envelope"></i> Contact Support', ['/site/contact'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    </div>
                </div>
            </div>
            
            <!-- Account Security -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Account Security</h5>
                </div>
                <div class="card-body">
                    <div class="security-item">
                        <i class="fas fa-shield-alt text-success"></i>
                        <div class="security-details">
                            <strong>Account Status</strong>
                            <p class="<?= $customer->status ? 'text-success' : 'text-danger' ?>">
                                <?= $customer->status ? 'Active & Verified' : 'Inactive' ?>
                            </p>
                        </div>
                    </div>
                    <div class="security-item">
                        <i class="fas fa-clock text-info"></i>
                        <div class="security-details">
                            <strong>Last Updated</strong>
                            <p><?= date('M d, Y \a\t g:i A', $customer->updated_at) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    margin-bottom: 1.5rem;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    padding: 0.75rem 1.25rem;
}

.card-body {
    padding: 1.25rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
}

.form-control-static {
    padding: 0.375rem 0;
    margin-bottom: 0;
    border: none;
    background: none;
}

.package-details .detail-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f1f1;
}

.package-details .detail-item:last-child {
    border-bottom: none;
}

.package-details .label {
    font-weight: 600;
    color: #6c757d;
}

.package-details .value {
    font-weight: 500;
}

.badge {
    font-size: 0.75em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-block {
    width: 100%;
}

.security-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #e9ecef;
}

.security-item:last-child {
    border-bottom: none;
}

.security-item i {
    font-size: 1.5rem;
    width: 30px;
    text-align: center;
}

.security-details strong {
    display: block;
    margin-bottom: 5px;
}

.security-details p {
    margin: 0;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .d-grid {
        gap: 0.5rem !important;
    }
}
</style>

<?= $this->render('_upgrade_modal') ?>