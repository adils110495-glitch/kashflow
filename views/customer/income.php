<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $incomeData array */
/* @var $customers app\models\Customer[] */
/* @var $selectedCustomer string */

$this->title = 'Customer Income';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="income-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Select Customer</h5>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'options' => ['class' => 'row g-3']
            ]); ?>
            
            <div class="col-md-6">
                <?= Html::label('Select Customer', 'customer', ['class' => 'form-label']) ?>
                <?= Html::dropDownList('customer', $selectedCustomer, 
                    ArrayHelper::map($customers, 'id', function($model) {
                        return $model->user ? $model->user->username : 'N/A';
                    }), 
                    ['class' => 'form-select', 'prompt' => 'Select Customer']
                ) ?>
            </div>
            
            <div class="col-md-2">
                <?= Html::label('&nbsp;', '', ['class' => 'form-label d-block']) ?>
                <?= Html::submitButton('Show Income', ['class' => 'btn btn-primary']) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php if ($selectedCustomer && !empty($incomeData)): ?>
        <?php 
        $selectedCustomerModel = \app\models\Customer::findOne($selectedCustomer);
        $customerName = $selectedCustomerModel && $selectedCustomerModel->user ? $selectedCustomerModel->user->username : 'Unknown';
        ?>
        <div class="alert alert-info">
            <strong>Income Report for:</strong> <?= Html::encode($customerName) ?>
        </div>

        <!-- Income Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">$<?= number_format($incomeData['total_income'], 2) ?></h4>
                                <p class="mb-0">Total Income</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">$<?= number_format($incomeData['referral_income'], 2) ?></h4>
                                <p class="mb-0">Referral Income</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">$<?= number_format($incomeData['level_income'], 2) ?></h4>
                                <p class="mb-0">Level Income</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-layer-group fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= date('M Y') ?></h4>
                                <p class="mb-0">Current Month</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income Breakdown -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Income Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td><strong>Direct Referral Income</strong></td>
                                        <td class="text-end">$<?= number_format($incomeData['referral_income'], 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Level 2 Income</strong></td>
                                        <td class="text-end">$<?= number_format(($incomeData['level_income'] * 0.67), 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Level 3 Income</strong></td>
                                        <td class="text-end">$<?= number_format(($incomeData['level_income'] * 0.33), 2) ?></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td><strong>Total Income</strong></td>
                                        <td class="text-end"><strong>$<?= number_format($incomeData['total_income'], 2) ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Monthly Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($incomeData['monthly_breakdown'])): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th class="text-end">Income</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($incomeData['monthly_breakdown'] as $month => $income): ?>
                                            <tr>
                                                <td><?= Html::encode($month) ?></td>
                                                <td class="text-end">$<?= number_format($income, 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No income data available for monthly breakdown.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income Calculation Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Income Calculation Details</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light">
                    <h6>Income Structure:</h6>
                    <ul class="mb-0">
                        <li><strong>Direct Referrals:</strong> $100 per referral</li>
                        <li><strong>Level 2 Team:</strong> $50 per member</li>
                        <li><strong>Level 3 Team:</strong> $25 per member</li>
                    </ul>
                </div>
            </div>
        </div>

    <?php elseif ($selectedCustomer): ?>
        <div class="alert alert-warning">
            <strong>No income data found</strong> for the selected customer.
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <strong>Please select a customer</strong> to view their income report.
        </div>
    <?php endif; ?>

</div>