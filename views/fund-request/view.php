<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $fundRequest app\models\FundRequest */

$this->title = 'Fund Request #' . $fundRequest->id;
$this->params['breadcrumbs'][] = ['label' => 'Fund Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="fund-request-view">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Fund Request Details</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $fundRequest,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'customer.name',
                                'label' => 'Customer Name',
                                'value' => function($model) {
                                    return $model->customer ? $model->customer->name : 'N/A';
                                }
                            ],
                            [
                                'attribute' => 'customer.user.username',
                                'label' => 'Username',
                                'value' => function($model) {
                                    return $model->customer && $model->customer->user ? $model->customer->user->username : 'N/A';
                                }
                            ],
                            [
                                'attribute' => 'amount',
                                'value' => function($model) {
                                    return $model->getFormattedAmount();
                                }
                            ],
                            [
                                'attribute' => 'request_date',
                                'value' => function($model) {
                                    return $model->getFormattedRequestDate();
                                }
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function($model) {
                                    $statusClass = '';
                                    switch ($model->status) {
                                        case \app\models\FundRequest::STATUS_PENDING:
                                            $statusClass = 'badge-warning';
                                            break;
                                        case \app\models\FundRequest::STATUS_APPROVED:
                                            $statusClass = 'badge-success';
                                            break;
                                        case \app\models\FundRequest::STATUS_REJECTED:
                                            $statusClass = 'badge-danger';
                                            break;
                                    }
                                    return '<span class="badge ' . $statusClass . '">' . Html::encode($model->getStatusLabel()) . '</span>';
                                }
                            ],
                            [
                                'attribute' => 'comment',
                                'format' => 'ntext',
                                'value' => function($model) {
                                    return $model->comment ?: 'No comment provided';
                                }
                            ],
                            [
                                'attribute' => 'attachment_file',
                                'format' => 'raw',
                                'value' => function($model) {
                                    if ($model->attachment_file) {
                                        $fileInfo = pathinfo($model->attachment_file);
                                        $fileName = $fileInfo['basename'];
                                        $fileSize = file_exists($model->attachment_file) ? filesize($model->attachment_file) : 0;
                                        $fileSizeFormatted = $fileSize > 0 ? ' (' . round($fileSize / 1024, 2) . ' KB)' : '';
                                        
                                        return Html::a('<i class="feather icon-download"></i> Download Attachment' . $fileSizeFormatted, 
                                            ['/web/' . $model->attachment_file], 
                                            ['class' => 'btn btn-sm btn-outline-primary', 'target' => '_blank']
                                        ) . '<br><small class="text-muted">' . Html::encode($fileName) . '</small>';
                                    }
                                    return '<span class="text-muted">No attachment</span>';
                                }
                            ],
                            [
                                'attribute' => 'admin_comment',
                                'format' => 'ntext',
                                'value' => function($model) {
                                    return $model->admin_comment ?: 'No admin comment';
                                }
                            ],
                            [
                                'attribute' => 'processedBy.username',
                                'label' => 'Processed By',
                                'value' => function($model) {
                                    return $model->processedBy ? $model->processedBy->username : 'Not Processed';
                                }
                            ],
                            [
                                'attribute' => 'processed_at',
                                'label' => 'Processed At',
                                'value' => function($model) {
                                    return $model->getFormattedProcessedDate();
                                }
                            ],
                            [
                                'attribute' => 'created_at',
                                'value' => function($model) {
                                    return date('M d, Y H:i', $model->created_at);
                                }
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <?php if ($fundRequest->isPending()): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Process Request</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <button type="button" class="btn btn-success btn-block mb-2" onclick="showApproveModal()">
                                <i class="feather icon-check"></i> Approve Request
                            </button>
                            
                            <button type="button" class="btn btn-danger btn-block" onclick="showRejectModal()">
                                <i class="feather icon-x"></i> Reject Request
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Request Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert <?= $fundRequest->isApproved() ? 'alert-success' : 'alert-danger' ?>">
                            <i class="feather <?= $fundRequest->isApproved() ? 'icon-check-circle' : 'icon-x-circle' ?>"></i>
                            <strong><?= Html::encode($fundRequest->getStatusLabel()) ?></strong>
                        </div>
                        
                        <?php if ($fundRequest->admin_comment): ?>
                            <h6>Admin Comment:</h6>
                            <p class="text-muted"><?= Html::encode($fundRequest->admin_comment) ?></p>
                        <?php endif; ?>
                        
                        <small class="text-muted">
                            Processed by: <?= Html::encode($fundRequest->processedBy ? $fundRequest->processedBy->username : 'N/A') ?><br>
                            Processed at: <?= Html::encode($fundRequest->getFormattedProcessedDate()) ?>
                        </small>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <?= Html::a('<i class="feather icon-arrow-left"></i> Back to List', ['index'], [
                        'class' => 'btn btn-outline-primary btn-block mb-2'
                    ]) ?>
                    
                    <?= Html::a('<i class="feather icon-user"></i> View Customer', ['/customer/view', 'id' => $fundRequest->customer_id], [
                        'class' => 'btn btn-outline-info btn-block',
                        'target' => '_blank'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.btn-block {
    width: 100%;
}
</style>

<script>
function showApproveModal() {
    const modalId = 'approveModal_' + Date.now();
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="${modalId}Label">
                            <i class="feather icon-check-circle mr-2"></i>Approve Fund Request
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="feather icon-check-circle text-success" style="font-size: 3rem;"></i>
                            <p class="mt-3 mb-0">Are you sure you want to approve this fund request?</p>
                            <small class="text-muted">This will add $<?= number_format($fundRequest->amount, 2) ?> to the customer's ledger.</small>
                        </div>
                        <div class="form-group">
                            <label for="${modalId}_comment" class="form-label">Admin Comment (Optional):</label>
                            <textarea id="${modalId}_comment" class="form-control" rows="3" placeholder="Add your comment here..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="${modalId}_cancel">
                            <i class="feather icon-x mr-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-success" id="${modalId}_confirm">
                            <i class="feather icon-check mr-1"></i>Approve Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modals if any
    $('[id^="approveModal_"]').remove();
    
    // Add modal to body
    $('body').append(modalHtml);
    
    // Show modal
    $('#' + modalId).modal('show');
    
    // Handle confirm button click
    $('#' + modalId + '_confirm').on('click', function() {
        const comment = $('#' + modalId + '_comment').val();
        processFundRequest('approve', comment);
        $('#' + modalId).modal('hide');
    });
    
    // Handle modal close events
    $('#' + modalId).on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function showRejectModal() {
    const modalId = 'rejectModal_' + Date.now();
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="${modalId}Label">
                            <i class="feather icon-x-circle mr-2"></i>Reject Fund Request
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="feather icon-x-circle text-danger" style="font-size: 3rem;"></i>
                            <p class="mt-3 mb-0">Are you sure you want to reject this fund request?</p>
                            <small class="text-muted">This action cannot be undone.</small>
                        </div>
                        <div class="form-group">
                            <label for="${modalId}_comment" class="form-label">Admin Comment (Required):</label>
                            <textarea id="${modalId}_comment" class="form-control" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="${modalId}_cancel">
                            <i class="feather icon-x mr-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-danger" id="${modalId}_confirm">
                            <i class="feather icon-x mr-1"></i>Reject Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modals if any
    $('[id^="rejectModal_"]').remove();
    
    // Add modal to body
    $('body').append(modalHtml);
    
    // Show modal
    $('#' + modalId).modal('show');
    
    // Handle confirm button click
    $('#' + modalId + '_confirm').on('click', function() {
        const comment = $('#' + modalId + '_comment').val();
        if (!comment.trim()) {
            showInfoModal('Validation Error', 'Please provide a reason for rejection.');
            return;
        }
        processFundRequest('reject', comment);
        $('#' + modalId).modal('hide');
    });
    
    // Handle modal close events
    $('#' + modalId).on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function processFundRequest(action, comment) {
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= \yii\helpers\Url::to(['approve', 'id' => $fundRequest->id]) ?>';
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;
    form.appendChild(actionInput);
    
    const commentInput = document.createElement('input');
    commentInput.type = 'hidden';
    commentInput.name = 'admin_comment';
    commentInput.value = comment;
    form.appendChild(commentInput);
    
    // Add CSRF token - Yii2 uses _csrf parameter
    const csrfToken = '<?= Yii::$app->request->csrfToken ?>';
    const csrfParam = '<?= Yii::$app->request->csrfParam ?>';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = csrfParam;
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    form.submit();
}
</script>