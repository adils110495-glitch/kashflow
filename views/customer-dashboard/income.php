<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Income;

/* @var $this yii\web\View */
/* @var $customer app\models\Customer */
/* @var $incomes app\models\Income[] */
/* @var $stats array */
/* @var $typeFilter string */
/* @var $fromDate string */
/* @var $toDate string */
/* @var $statusFilter string */

$this->title = 'My Earnings';
$this->params['breadcrumbs'][] = $this->title;

// Get customer's currency information
$customerCurrency = $customer->getCurrencyForDisplay();
?>

<div class="pcoded-content">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <div class="page-body">
                    <!-- Page Header -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h4 class="page-title"><?= Html::encode($this->title) ?></h4>
                            </div>
                        </div>
                    </div>

                    <!-- Income Statistics -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-white"><?= $customer->formatCurrencyAmount($customer->convertFromInr($stats['total_income'])) ?></h4>
                                            <h6 class="text-white m-b-0">Total Earnings</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-dollar-sign f-28"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-white"><?= $customer->formatCurrencyAmount($customer->convertFromInr($stats['roi_income'])) ?></h4>
                                            <h6 class="text-white m-b-0">Regular Earnings</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-trending-up f-28"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-white"><?= $customer->formatCurrencyAmount($customer->convertFromInr($stats['level_income'])) ?></h4>
                                            <h6 class="text-white m-b-0">Network Earnings</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-layers f-28"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-white"><?= $customer->formatCurrencyAmount($customer->convertFromInr($stats['referral_income'])) ?></h4>
                                            <h6 class="text-white m-b-0">Referral Bonus</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-users f-28"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-white"><?= $customer->formatCurrencyAmount($customer->convertFromInr($stats['monthly_income'])) ?></h4>
                                            <h6 class="text-white m-b-0">This Month</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-calendar f-28"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Filter Earnings Records</h5>
                                </div>
                                <div class="card-body filter-section">
                                    <?= Html::beginForm(['income'], 'get', ['class' => 'row']) ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <?= Html::label('Type', 'type', ['class' => 'form-label']) ?>
                                                <?= Html::dropDownList('type', $typeFilter, 
                                                    ['' => 'All Types'] + Income::getTypeLabels(), 
                                                    ['class' => 'form-control', 'id' => 'type']) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <?= Html::label('From Date', 'from_date', ['class' => 'form-label']) ?>
                                                <?= Html::input('date', 'from_date', $fromDate, ['class' => 'form-control']) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <?= Html::label('To Date', 'to_date', ['class' => 'form-label']) ?>
                                                <?= Html::input('date', 'to_date', $toDate, ['class' => 'form-control']) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <div>
                                                    <?= Html::submitButton('<i class="feather icon-search"></i> Filter', ['class' => 'btn btn-primary']) ?>
                                                    <?= Html::a('<i class="feather icon-refresh-cw"></i> Reset', ['income'], ['class' => 'btn btn-secondary ml-2']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?= Html::endForm() ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Income Records -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Earning History</h5>
                                    <div class="card-header-right">
                                        <span class="badge badge-primary"><?= count($incomes) ?> Records</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($incomes)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Level</th>
                                                        <th>Amount</th>
                                                        <th>Details</th>
                                                        <th>Created</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($incomes as $income): ?>
                                                        <tr>
                                                            <td><?= Html::encode(date('M d, Y', strtotime($income->date))) ?></td>
                                                            <td>
                                                                <?php if ($income->isROI()): ?>
                                                                    <span class="badge badge-success">ROI Earnings</span>
                                                                <?php elseif ($income->isLevelIncome()): ?>
                                                                    <span class="badge badge-info">Network Earnings</span>
                                                                <?php elseif ($income->isReferralIncome()): ?>
                                                                    <span class="badge badge-warning">Referral Bonus</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary"><?= Html::encode(Income::getTypeLabels()[$income->type] ?? 'Unknown') ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($income->isLevelIncome() && $income->level > 0): ?>
                                                                    <span class="badge badge-outline-primary">Level <?= $income->level ?></span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <strong class="text-success"><?= $customer->formatCurrencyAmount($customer->convertFromInr($income->amount)) ?></strong>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($income->meta)): ?>
                                                                    <small class="text-muted"><?= Html::encode($income->meta) ?></small>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted"><?= Html::encode(date('M d, Y H:i', strtotime($income->created_at))) ?></small>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle"></i>
                                            No income records found.
                                            <?php if ($typeFilter || $statusFilter || $fromDate || $toDate): ?>
                                                Try adjusting your filters.
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.filter-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #212529;
}

.bg-success {
    background-color: #28a745 !important;
    color: #fff;
}

.bg-info {
    background-color: #17a2b8 !important;
    color: #fff;
}

.bg-secondary {
    background-color: #6c757d !important;
    color: #fff;
}

.badge-outline-primary {
    color: #007bff;
    border: 1px solid #007bff;
    background: transparent;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.075);
}
</style>