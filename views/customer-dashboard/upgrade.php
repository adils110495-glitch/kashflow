<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $customer app\models\Customer */
/* @var $availablePackages app\models\Package[] */
/* @var $canUpgrade bool */

$this->title = 'Upgrade Package';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Get customer's currency information
$customerCurrency = $customer->getCurrencyForDisplay();
?>

<div class="upgrade-package">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-arrow-up"></i> <?= Html::encode($this->title) ?>
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Current Package Info -->
                    <div class="current-package-info mb-4">
                        <h5><i class="fas fa-box"></i> Current Package</h5>
                        <?php if ($customer->currentPackage): ?>
                            <div class="alert alert-info">
                                <strong><?= Html::encode($customer->currentPackage->name) ?></strong> - 
                                <?= $customer->formatCurrencyAmount($customer->convertFromInr($customer->currentPackage->amount)) ?> 
                                (Fee: <?= $customer->formatCurrencyAmount($customer->convertFromInr($customer->currentPackage->fee)) ?>)
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                No package assigned
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!$canUpgrade): ?>
                        <!-- Cannot Upgrade Message -->
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Upgrade Not Available</h5>
                            <p>You are not eligible for package upgrade. This could be because:</p>
                            <ul>
                                <li>You already have a paid package</li>
                                <li>You have previously upgraded from free to paid package</li>
                                <li>No package is currently assigned to your account</li>
                            </ul>
                            <p><strong>Note:</strong> Customers can only upgrade once from free to paid package.</p>
                        </div>
                        
                        <div class="mt-3">
                            <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Dashboard', ['index'], ['class' => 'btn btn-primary']) ?>
                        </div>
                    <?php else: ?>
                        <!-- Available Packages -->
                        <div class="available-packages">
                            <h5><i class="fas fa-star"></i> Available Upgrade Plans</h5>
                            <p class="text-muted mb-4">Choose a package to upgrade to. You can only upgrade once from free to paid package.</p>
                            
                            <?php if (empty($availablePackages)): ?>
                                <div class="alert alert-info">
                                    No upgrade packages are currently available.
                                </div>
                            <?php else: ?>
                                <!-- Upgrade packages display -->
                                
                                <div class="row">
                                    <?php foreach ($availablePackages as $package): ?>
                                        <div class="col-md-6 col-lg-12 mb-4">
                                            <div class="card package-card h-100">
                                                <div class="card-header text-center bg-<?= $package->status == 2 ? 'warning' : 'primary' ?>">
                                                    <h4 class="text-white mb-0">
                                                        <?= Html::encode($package->name) ?>
                                                        <?php if ($package->status == 2): ?>
                                                            <span class="badge badge-light ml-2">Premium</span>
                                                        <?php endif; ?>
                                                    </h4>
                                                </div>
                                                <div class="card-body text-center">
                                                    <div class="package-price mb-3">
                                                        <h2 class="text-success mb-0"><?= $customer->formatCurrencyAmount($customer->convertFromInr($package->amount)) ?></h2>
                                                        <small class="text-muted">Package Amount</small>
                                                    </div>
                                                    
                                                    <div class="package-fee mb-3">
                                                        <h4 class="text-info mb-0"><?= $customer->formatCurrencyAmount($customer->convertFromInr($package->fee)) ?></h4>
                                                        <small class="text-muted">Processing Fee</small>
                                                    </div>
                                                    
                                                    <div class="package-features mb-3">
                                                        <ul class="list-unstyled">
                                                            <li><i class="fas fa-check text-success"></i> ROI Generation</li>
                                                            <li><i class="fas fa-check text-success"></i> Level Income</li>
                                                            <li><i class="fas fa-check text-success"></i> Team Building</li>
                                                            <?php if ($package->status == 2): ?>
                                                                <li><i class="fas fa-star text-warning"></i> Premium Benefits</li>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-center">
                                                    <?= Html::button('<i class="fas fa-arrow-up"></i> Upgrade to ' . Html::encode($package->name), [
                                                        'class' => 'btn btn-success btn-block upgrade-btn',
                                                        'value' => $package->id,
                                                        'data-package-name' => $package->name,
                                                        'data-package-price' => $customer->formatCurrencyAmount($customer->convertFromInr($package->amount))
                                                    ]) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- End upgrade packages display -->

                                <?= $this->render('_upgrade_modal') ?>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Important Notes -->
                        <div class="upgrade-notes mt-4">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Important Notes:</h6>
                                <ul class="mb-0">
                                    <li>You can only upgrade <strong>once</strong> from free to paid package</li>
                                    <li>After upgrading, you cannot downgrade back to free package</li>
                                    <li>Package upgrade requires payment processing</li>
                                    <li>Your new package will be activated after successful payment</li>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.package-card {
    transition: transform 0.2s;
    border: 2px solid #e9ecef;
}

.package-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #007bff;
}

.package-card.selected {
    border-color: #28a745 !important;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3) !important;
    transform: translateY(-3px);
}

.package-card.selected .card-header {
    background-color: #28a745;
    color: white;
}

.package-price h2 {
    font-size: 2.5rem;
    font-weight: bold;
}

.package-fee h4 {
    font-size: 1.5rem;
}

.upgrade-btn {
    font-weight: bold;
    padding: 10px;
}

.upgrade-btn:hover {
    transform: scale(1.02);
}

.current-package-info .alert {
    border-left: 4px solid #17a2b8;
}

.package-features li {
    padding: 2px 0;
}
</style>