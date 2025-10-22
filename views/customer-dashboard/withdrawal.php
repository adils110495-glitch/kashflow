<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Ledger;

/* @var $this yii\web\View */
/* @var $customer app\models\Customer */
/* @var $currentBalance float */
/* @var $withdrawalHistory app\models\Ledger[] */

$this->title = 'Withdrawal';
$this->params['breadcrumbs'][] = $this->title;
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

                    <!-- Balance Cards -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-white">$<?= number_format($customer->getTotalIncome(), 2) ?></h4>
                                            <h6 class="text-white m-b-0">Total Income</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-arrow-up f-28"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-white">$<?= number_format($customer->getTotalWithdrawals(), 2) ?></h4>
                                            <h6 class="text-white m-b-0">Total Withdrawals</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-arrow-down f-28"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-white">$<?= number_format($currentBalance, 2) ?></h4>
                                            <h6 class="text-white m-b-0">Available Balance</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-wallet f-28"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Balance Breakdown -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Balance Breakdown</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="balance-breakdown">
                                                <div class="breakdown-item">
                                                    <span class="label">Total Income:</span>
                                                    <span class="value text-success">+$<?= number_format($customer->getTotalIncome(), 2) ?></span>
                                                </div>
                                                <div class="breakdown-item">
                                                    <span class="label">Total Withdrawals:</span>
                                                    <span class="value text-danger">-$<?= number_format($customer->getTotalWithdrawals(), 2) ?></span>
                                                </div>
                                                <hr>
                                                <div class="breakdown-item total">
                                                    <span class="label"><strong>Available Balance:</strong></span>
                                                    <span class="value text-primary"><strong>$<?= number_format($currentBalance, 2) ?></strong></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="balance-formula">
                                                <h6>Balance Calculation:</h6>
                                                <div class="formula">
                                                    <code>Available Balance = Total Income - Total Withdrawals</code>
                                                </div>
                                                <div class="formula-calculation">
                                                    <code>$<?= number_format($currentBalance, 2) ?> = $<?= number_format($customer->getTotalIncome(), 2) ?> - $<?= number_format($customer->getTotalWithdrawals(), 2) ?></code>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Withdrawal Form -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Request Withdrawal</h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($currentBalance > 0): ?>
                                        <?php $form = ActiveForm::begin([
                                            'options' => ['class' => 'needs-validation', 'novalidate' => true]
                                        ]); ?>
                                        
                                        <div class="form-group">
                                            <?= Html::label('Withdrawal Method', 'withdrawal_method', ['class' => 'form-label']) ?>
                                            <?= Html::dropDownList('withdrawal_method', 'UPI', [
                                                'UPI' => 'UPI',
                                                'Cash' => 'Cash',
                                                'Crypto' => 'Crypto'
                                            ], [
                                                'class' => 'form-control',
                                                'required' => true,
                                                'id' => 'withdrawal_method'
                                            ]) ?>
                                            <small class="form-text text-muted">
                                                Select your preferred withdrawal method.
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <?= Html::label('Withdrawal Amount', 'amount', ['class' => 'form-label']) ?>
                                            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">₹</span>
                </div>
                                                <?= Html::input('number', 'amount', '', [
                                                    'class' => 'form-control',
                                                    'step' => '0.01',
                                                    'min' => \app\models\Withdrawal::MIN_WITHDRAWAL_AMOUNT,
                                                    'max' => $currentBalance,
                                                    'required' => true,
                                                    'placeholder' => 'Enter withdrawal amount (minimum ₹' . \app\models\Withdrawal::MIN_WITHDRAWAL_AMOUNT . ')'
                                                ]) ?>
                                            </div>
                                            <small class="form-text text-muted">
                                                Minimum withdrawal: ₹<?= number_format(\app\models\Withdrawal::MIN_WITHDRAWAL_AMOUNT, 2) ?> | Maximum withdrawal: ₹<?= number_format($currentBalance, 2) ?>
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <?= Html::label('Account Details', 'account_details', ['class' => 'form-label']) ?>
                                            <?= Html::textarea('account_details', '', [
                                                'class' => 'form-control',
                                                'rows' => 4,
                                                'required' => true,
                                                'placeholder' => 'Enter your UPI ID (e.g., username@paytm, username@phonepe)',
                                                'id' => 'account_details'
                                            ]) ?>
                                            <small class="form-text text-muted" id="account_details_help">
                                                Please provide accurate account details for processing your withdrawal.
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-check">
                                                <?= Html::checkbox('terms_accepted', false, [
                                                    'class' => 'form-check-input',
                                                    'required' => true,
                                                    'id' => 'terms_accepted'
                                                ]) ?>
                                                <?= Html::label('I agree to the withdrawal terms and conditions', 'terms_accepted', ['class' => 'form-check-label']) ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <?= Html::submitButton('Submit Withdrawal Request', [
                                                'class' => 'btn btn-primary btn-lg',
                                                'data-confirm' => 'Are you sure you want to submit this withdrawal request?'
                                            ]) ?>
                                        </div>

                                        <?php ActiveForm::end(); ?>
                                        
                                        <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const withdrawalMethodSelect = document.getElementById('withdrawal_method');
                                            const accountDetailsTextarea = document.getElementById('account_details');
                                            const accountDetailsHelp = document.getElementById('account_details_help');
                                            
                                            const methodPlaceholders = {
                                                'UPI': 'Enter your UPI ID (e.g., username@paytm, username@phonepe)',
                                                'Cash': 'Enter your bank account details (Account number, IFSC code, Bank name)',
                                                'Crypto': 'Enter your crypto wallet address (Bitcoin, Ethereum, etc.)'
                                            };
                                            
                                            const methodHelpTexts = {
                                                'UPI': 'Please provide your UPI ID for instant transfer.',
                                                'Cash': 'Please provide accurate bank account details for wire transfer.',
                                                'Crypto': 'Please provide your crypto wallet address for digital currency transfer.'
                                            };
                                            
                                            function updatePlaceholder() {
                                                const selectedMethod = withdrawalMethodSelect.value;
                                                accountDetailsTextarea.placeholder = methodPlaceholders[selectedMethod];
                                                accountDetailsHelp.textContent = methodHelpTexts[selectedMethod];
                                            }
                                            
                                            withdrawalMethodSelect.addEventListener('change', updatePlaceholder);
                                            updatePlaceholder(); // Initialize on page load
                                        });
                                        </script>
                                    <?php else: ?>
                                        <div class="alert alert-warning text-center">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <h5>No Available Balance</h5>
                                            <p>You don't have sufficient balance to make a withdrawal.</p>
                                            <a href="<?= Url::to(['income']) ?>" class="btn btn-primary">
                                                <i class="feather icon-arrow-up"></i> Check Income
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Withdrawal Information -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Withdrawal Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="feather icon-clock"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Processing Time</span>
                                            <span class="info-box-number">1-3 Business Days</span>
                                        </div>
                                    </div>

                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="feather icon-shield"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Minimum Amount</span>
                                            <span class="info-box-number">₹<?= number_format(\app\models\Withdrawal::MIN_WITHDRAWAL_AMOUNT, 2) ?></span>
                                        </div>
                                    </div>

                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning"><i class="feather icon-dollar-sign"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Daily Limit</span>
                                            <span class="info-box-number">$1,000.00</span>
                                        </div>
                                    </div>

                                    <div class="alert alert-info">
                                        <h6><i class="feather icon-info"></i> Important Notes:</h6>
                                        <ul class="mb-0">
                                            <li>Withdrawals are processed during business hours</li>
                                            <li>Account verification may be required</li>
                                            <li>Processing fees may apply</li>
                                            <li>Contact support for urgent requests</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Withdrawal History -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Recent Withdrawal History</h5>
                                    <div class="card-header-right">
                                        <span class="badge badge-primary"><?= count($withdrawalHistory) ?> Records</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($withdrawalHistory)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <th>Method</th>
                                                        <th>Status</th>
                                                        <th>Action By</th>
                                                        <th>Action Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($withdrawalHistory as $withdrawal): ?>
                                                        <tr>
                                                            <td><?= Html::encode($withdrawal->getFormattedDate()) ?></td>
                <td>
                    <strong class="text-danger">-₹<?= number_format($withdrawal->amount, 2) ?></strong>
                </td>
                                                            <td>
                                                                <?= $withdrawal->getWithdrawalMethodLabel() ?>
                                                            </td>
                                                            <td>
                                                                <?= $withdrawal->getStatusLabel() ?>
                                                            </td>
                                                            <td>
                                                                <?= Html::encode($withdrawal->actionBy ? $withdrawal->actionBy->username : 'System') ?>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted"><?= Html::encode($withdrawal->getFormattedActionDateTime()) ?></small>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle"></i>
                                            No withdrawal history found.
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
.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}

.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 13px;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}

.bg-info {
    background-color: #17a2b8 !important;
    color: #fff;
}

.bg-success {
    background-color: #28a745 !important;
    color: #fff;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #212529;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.075);
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.needs-validation .form-control:invalid {
    border-color: #dc3545;
}

.needs-validation .form-control:valid {
    border-color: #28a745;
}

.balance-breakdown {
    padding: 15px;
}

.breakdown-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.breakdown-item.total {
    border-bottom: none;
    padding-top: 15px;
    font-size: 16px;
}

.breakdown-item .label {
    font-weight: 500;
}

.breakdown-item .value {
    font-weight: 600;
}

.balance-formula {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}

.balance-formula h6 {
    margin-bottom: 15px;
    color: #495057;
}

.formula, .formula-calculation {
    margin-bottom: 10px;
}

.formula code, .formula-calculation code {
    background: #e9ecef;
    padding: 8px 12px;
    border-radius: 3px;
    font-size: 14px;
    display: block;
}

.formula-calculation code {
    background: #d4edda;
    color: #155724;
}
</style>

<script>
// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Amount validation
document.querySelector('input[name="amount"]').addEventListener('input', function() {
    var amount = parseFloat(this.value);
    var maxAmount = <?= $currentBalance ?>;
    
    if (amount > maxAmount) {
        this.setCustomValidity('Amount cannot exceed available balance');
    } else if (amount < 10) {
        this.setCustomValidity('Minimum withdrawal amount is $10.00');
    } else {
        this.setCustomValidity('');
    }
});
</script>
