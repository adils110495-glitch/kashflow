<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $customer app\models\Customer */
/* @var $fundTransfer app\models\FundTransfer */

$this->title = 'Fund Transfer Approval';
$this->params['breadcrumbs'][] = ['label' => 'Fund Transfer', 'url' => ['fund-transfer']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="feather icon-check-circle mr-2"></i>
                    Fund Transfer Approval
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Transfer Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Transfer ID:</label>
                                            <p class="form-control-plaintext">#<?= $fundTransfer->id ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Amount:</label>
                                            <p class="form-control-plaintext text-success font-weight-bold">
                                                <?= $fundTransfer->getFormattedAmount() ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">From Customer:</label>
                                            <p class="form-control-plaintext">
                                                <strong><?= Html::encode($fundTransfer->fromCustomer->name) ?></strong><br>
                                                <small class="text-muted"><?= Html::encode($fundTransfer->fromCustomer->user ? $fundTransfer->fromCustomer->user->username : 'N/A') ?></small>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">To Customer:</label>
                                            <p class="form-control-plaintext">
                                                <strong><?= Html::encode($fundTransfer->toCustomer->name) ?></strong><br>
                                                <small class="text-muted"><?= Html::encode($fundTransfer->toCustomer->user ? $fundTransfer->toCustomer->user->username : 'N/A') ?></small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Transfer Date:</label>
                                            <p class="form-control-plaintext"><?= $fundTransfer->getFormattedTransferDate() ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Status:</label>
                                            <p class="form-control-plaintext">
                                                <span class="badge badge-warning"><?= $fundTransfer->getStatusLabel() ?></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if (!empty($fundTransfer->comment)): ?>
                                <div class="form-group">
                                    <label class="form-label">Transfer Comment:</label>
                                    <p class="form-control-plaintext"><?= Html::encode($fundTransfer->comment) ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($fundTransfer->admin_comment)): ?>
                                <div class="form-group">
                                    <label class="form-label">Admin Comment:</label>
                                    <p class="form-control-plaintext"><?= Html::encode($fundTransfer->admin_comment) ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Your Action</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="feather icon-info mr-2"></i>
                                    <strong>You have received a fund transfer request.</strong><br>
                                    Please review the details and approve or reject this transfer.
                                </div>
                                
                                <?php $form = ActiveForm::begin([
                                    'action' => ['fund-transfer-approval', 'id' => $fundTransfer->id],
                                    'method' => 'post',
                                ]); ?>
                                
                                <div class="form-group">
                                    <label class="form-label">Your Comment (Optional):</label>
                                    <?= $form->field($fundTransfer, 'admin_comment')->textarea([
                                        'rows' => 3,
                                        'class' => 'form-control',
                                        'placeholder' => 'Add your comment here...',
                                        'name' => 'receiver_comment'
                                    ])->label(false) ?>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-block mb-2" onclick="return confirm('Are you sure you want to approve this fund transfer?')">
                                        <i class="feather icon-check mr-2"></i>Approve Transfer
                                    </button>
                                    
                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to reject this fund transfer?')">
                                        <i class="feather icon-x mr-2"></i>Reject Transfer
                                    </button>
                                </div>
                                
                                <?php ActiveForm::end(); ?>
                                
                                <div class="mt-3">
                                    <?= Html::a('<i class="feather icon-arrow-left mr-2"></i>Back to Fund Transfer', ['fund-transfer'], [
                                        'class' => 'btn btn-secondary btn-block'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add confirmation dialogs
document.addEventListener('DOMContentLoaded', function() {
    const approveBtn = document.querySelector('button[value="approve"]');
    const rejectBtn = document.querySelector('button[value="reject"]');
    
    if (approveBtn) {
        approveBtn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to approve this fund transfer?\n\nThis will credit your account with <?= $fundTransfer->getFormattedAmount() ?>.')) {
                e.preventDefault();
            }
        });
    }
    
    if (rejectBtn) {
        rejectBtn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to reject this fund transfer?\n\nThis action cannot be undone.')) {
                e.preventDefault();
            }
        });
    }
});
</script>
