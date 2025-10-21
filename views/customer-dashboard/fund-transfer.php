<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $customer app\models\Customer */
/* @var $fundTransfer app\models\FundTransfer */
/* @var $outgoingTransfers app\models\FundTransfer[] */
/* @var $incomingTransfers app\models\FundTransfer[] */
/* @var $customers app\models\Customer[] */

$this->title = 'Fund Transfer';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="fund-transfer">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Transfer Funds to Another Customer</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="feather icon-info"></i>
                        <strong>Current Balance:</strong> 
                        <span class="text-success font-weight-bold">$<?= number_format($customer->getLedgerBalance(), 2) ?></span>
                    </div>

                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($fundTransfer, 'to_customer_id')->dropDownList(
                                \yii\helpers\ArrayHelper::map($customers, 'id', function($customer) {
                                    return $customer->name . ' (' . ($customer->user ? $customer->user->username : 'N/A') . ')';
                                }),
                                ['prompt' => 'Select Customer to Transfer To', 'class' => 'form-control']
                            )->label('Transfer To <span class="text-danger">*</span>') ?>
                        </div>
                        
                        <div class="col-md-6">
                            <?= $form->field($fundTransfer, 'amount')->textInput([
                                'type' => 'number',
                                'step' => '0.01',
                                'min' => '0.01',
                                'max' => $customer->getLedgerBalance(),
                                'class' => 'form-control'
                            ])->label('Amount <span class="text-danger">*</span>') ?>
                        </div>
                    </div>

                    <?= $form->field($fundTransfer, 'comment')->textarea([
                        'rows' => 3,
                        'placeholder' => 'Enter reason for transfer...',
                        'class' => 'form-control'
                    ])->label('Transfer Reason') ?>

                    <div class="form-group">
                        <?= Html::submitButton('<i class="feather icon-send"></i> Submit Transfer Request', [
                            'class' => 'btn btn-primary btn-lg'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <div class="alert alert-warning mt-3">
                        <i class="feather icon-alert-triangle"></i>
                        <strong>Note:</strong> Transfer requests require admin approval before funds are moved.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Transfer Guidelines</h5>
                </div>
                <div class="card-body">
                    <h6>Transfer Process:</h6>
                    <ol class="small">
                        <li>Submit transfer request</li>
                        <li>Admin reviews and approves</li>
                        <li>Funds are moved between accounts</li>
                        <li>Both parties receive notifications</li>
                    </ol>
                    
                    <h6>Requirements:</h6>
                    <ul class="small">
                        <li>Sufficient balance in your account</li>
                        <li>Valid recipient customer</li>
                        <li>Amount must be greater than $0.01</li>
                        <li>Cannot transfer to yourself</li>
                    </ul>
                    
                    <h6>Processing Time:</h6>
                    <p class="small text-muted">Transfer requests are typically processed within 24 hours during business days.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Receiver Approval -->
    <?php if (!empty($pendingReceiverApprovalTransfers)): ?>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title text-white">
                        <i class="feather icon-clock mr-2"></i>
                        Transfers Pending Your Approval
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="feather icon-info mr-2"></i>
                        You have <strong><?= count($pendingReceiverApprovalTransfers) ?></strong> transfer(s) waiting for your approval.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>From Customer</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Comment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingReceiverApprovalTransfers as $transfer): ?>
                                    <tr>
                                        <td>
                                            <strong><?= Html::encode($transfer->fromCustomer->name) ?></strong><br>
                                            <small class="text-muted"><?= Html::encode($transfer->fromCustomer->user ? $transfer->fromCustomer->user->username : 'N/A') ?></small>
                                        </td>
                                        <td>
                                            <strong class="text-success"><?= Html::encode($transfer->getFormattedAmount()) ?></strong>
                                        </td>
                                        <td><?= Html::encode($transfer->getFormattedTransferDate()) ?></td>
                                        <td>
                                            <?php if (!empty($transfer->comment)): ?>
                                                <small class="text-muted"><?= Html::encode(substr($transfer->comment, 0, 50)) ?><?= strlen($transfer->comment) > 50 ? '...' : '' ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= Html::a('<i class="feather icon-check"></i> Approve', ['fund-transfer-approval', 'id' => $transfer->id], [
                                                'class' => 'btn btn-success btn-sm',
                                                'title' => 'Approve Transfer'
                                            ]) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Transfer History -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Outgoing Transfers</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($outgoingTransfers)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>To Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($outgoingTransfers as $transfer): ?>
                                        <tr>
                                            <td>
                                                <strong><?= Html::encode($transfer->toCustomer->name) ?></strong><br>
                                                <small class="text-muted"><?= Html::encode($transfer->toCustomer->user ? $transfer->toCustomer->user->username : 'N/A') ?></small>
                                            </td>
                                            <td>
                                                <strong class="text-success"><?= Html::encode($transfer->getFormattedAmount()) ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                switch ($transfer->status) {
                                                    case \app\models\FundTransfer::STATUS_PENDING:
                                                        $statusClass = 'badge-warning';
                                                        break;
                                                    case \app\models\FundTransfer::STATUS_APPROVED:
                                                        $statusClass = 'badge-success';
                                                        break;
                                                    case \app\models\FundTransfer::STATUS_REJECTED:
                                                        $statusClass = 'badge-danger';
                                                        break;
                                                    case \app\models\FundTransfer::STATUS_PENDING_RECEIVER_APPROVAL:
                                                        $statusClass = 'badge-info';
                                                        break;
                                                    case \app\models\FundTransfer::STATUS_RECEIVER_APPROVED:
                                                        $statusClass = 'badge-success';
                                                        break;
                                                    case \app\models\FundTransfer::STATUS_RECEIVER_REJECTED:
                                                        $statusClass = 'badge-danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?>">
                                                    <?= Html::encode($transfer->getStatusLabel()) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= Html::encode($transfer->getFormattedTransferDate()) ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="feather icon-send" style="font-size: 2rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">No outgoing transfers</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Incoming Transfers</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($incomingTransfers)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>From Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($incomingTransfers as $transfer): ?>
                                        <tr>
                                            <td>
                                                <strong><?= Html::encode($transfer->fromCustomer->name) ?></strong><br>
                                                <small class="text-muted"><?= Html::encode($transfer->fromCustomer->user ? $transfer->fromCustomer->user->username : 'N/A') ?></small>
                                            </td>
                                            <td>
                                                <strong class="text-success"><?= Html::encode($transfer->getFormattedAmount()) ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                switch ($transfer->status) {
                                                    case \app\models\FundTransfer::STATUS_PENDING:
                                                        $statusClass = 'badge-warning';
                                                        break;
                                                    case \app\models\FundTransfer::STATUS_APPROVED:
                                                        $statusClass = 'badge-success';
                                                        break;
                                                    case \app\models\FundTransfer::STATUS_REJECTED:
                                                        $statusClass = 'badge-danger';
                                                        break;
                                                    case \app\models\FundTransfer::STATUS_PENDING_RECEIVER_APPROVAL:
                                                        $statusClass = 'badge-info';
                                                        break;
                                                    case \app\models\FundTransfer::STATUS_RECEIVER_APPROVED:
                                                        $statusClass = 'badge-success';
                                                        break;
                                                    case \app\models\FundTransfer::STATUS_RECEIVER_REJECTED:
                                                        $statusClass = 'badge-danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?>">
                                                    <?= Html::encode($transfer->getStatusLabel()) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= Html::encode($transfer->getFormattedTransferDate()) ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="feather icon-download" style="font-size: 2rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">No incoming transfers</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Update max amount based on current balance
    const currentBalance = <?= $customer->getLedgerBalance() ?>;
    $('#fundtransfer-amount').attr('max', currentBalance);
    
    // Validate amount
    $('#fundtransfer-amount').on('input', function() {
        const amount = parseFloat($(this).val());
        const maxAmount = parseFloat($(this).attr('max'));
        
        if (amount > maxAmount) {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
            $(this).after('<div class="invalid-feedback">Amount cannot exceed your current balance of $' + maxAmount.toFixed(2) + '</div>');
        } else if (amount <= 0) {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
            $(this).after('<div class="invalid-feedback">Amount must be greater than $0.00</div>');
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    
    // Prevent selecting same customer
    $('#fundtransfer-to_customer_id').on('change', function() {
        const selectedCustomer = $(this).val();
        if (selectedCustomer) {
            // You could add additional validation here if needed
        }
    });
});
</script>

<style>
.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.alert-warning {
    background-color: #fff3cd;
    border-color: #ffeaa7;
    color: #856404;
}

.badge-warning {
    background-color: #ffc107 !important;
    color: #212529;
}

.badge-success {
    background-color: #28a745 !important;
    color: #fff;
}

.badge-danger {
    background-color: #dc3545 !important;
    color: #fff;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

.table-sm th,
.table-sm td {
    padding: 0.5rem;
}
</style>
