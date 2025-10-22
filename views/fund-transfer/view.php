<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\FundTransfer */

$this->title = 'Fund Transfer #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fund Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="fund-transfer-view">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Fund Transfer Details</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'from_customer_id',
                                'label' => 'From Customer',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<strong>' . Html::encode($model->fromCustomer->name) . '</strong><br>' .
                                           '<small class="text-muted">Username: ' . Html::encode($model->fromCustomer->user ? $model->fromCustomer->user->username : 'N/A') . '</small><br>' .
                                           '<small class="text-muted">Email: ' . Html::encode($model->fromCustomer->user ? $model->fromCustomer->user->email : 'N/A') . '</small>';
                                }
                            ],
                            [
                                'attribute' => 'to_customer_id',
                                'label' => 'To Customer',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<strong>' . Html::encode($model->toCustomer->name) . '</strong><br>' .
                                           '<small class="text-muted">Username: ' . Html::encode($model->toCustomer->user ? $model->toCustomer->user->username : 'N/A') . '</small><br>' .
                                           '<small class="text-muted">Email: ' . Html::encode($model->toCustomer->user ? $model->toCustomer->user->email : 'N/A') . '</small>';
                                }
                            ],
                            [
                                'attribute' => 'amount',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<strong class="text-success" style="font-size: 1.2em;">' . $model->getFormattedAmount() . '</strong>';
                                }
                            ],
                            [
                                'attribute' => 'transfer_date',
                                'value' => function ($model) {
                                    return $model->getFormattedTransferDate();
                                }
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $statusClass = '';
                                    switch ($model->status) {
                                        case \app\models\FundTransfer::STATUS_PENDING:
                                            $statusClass = 'badge-warning';
                                            break;
                                        case \app\models\FundTransfer::STATUS_APPROVED:
                                            $statusClass = 'badge-success';
                                            break;
                                        case \app\models\FundTransfer::STATUS_REJECTED:
                                            $statusClass = 'badge-danger';
                                            break;
                                    }
                                    return '<span class="badge ' . $statusClass . '">' . Html::encode($model->getStatusLabel()) . '</span>';
                                }
                            ],
                            [
                                'attribute' => 'transfer_type',
                                'value' => function ($model) {
                                    return $model->getTransferTypeLabel();
                                }
                            ],
                            [
                                'attribute' => 'comment',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return !empty($model->comment) ? Html::encode($model->comment) : '<span class="text-muted">No comment</span>';
                                }
                            ],
                            [
                                'attribute' => 'admin_comment',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return !empty($model->admin_comment) ? Html::encode($model->admin_comment) : '<span class="text-muted">No admin comment</span>';
                                }
                            ],
                            [
                                'attribute' => 'processed_by',
                                'label' => 'Processed By',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->processedBy) {
                                        return '<strong>' . Html::encode($model->processedBy->username) . '</strong><br>' .
                                               '<small class="text-muted">' . Html::encode($model->getFormattedProcessedDate()) . '</small>';
                                    }
                                    return '<span class="text-muted">Not Processed</span>';
                                }
                            ],
                            [
                                'attribute' => 'created_at',
                                'label' => 'Created At',
                                'value' => function ($model) {
                                    return date('M d, Y H:i:s', $model->created_at);
                                }
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <?php if ($model->isPending()): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Process Transfer</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <button type="button" class="btn btn-success btn-block mb-2" onclick="showApproveModal()">
                                <i class="feather icon-check"></i> Approve Transfer
                            </button>
                            
                            <button type="button" class="btn btn-danger btn-block" onclick="showRejectModal()">
                                <i class="feather icon-x"></i> Reject Transfer
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Transfer Status</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($model->isApproved()): ?>
                            <div class="alert alert-success">
                                <i class="feather icon-check-circle"></i>
                                <strong>Transfer Approved</strong><br>
                                <small>This transfer has been processed and funds have been moved.</small>
                            </div>
                        <?php elseif ($model->isRejected()): ?>
                            <div class="alert alert-danger">
                                <i class="feather icon-x-circle"></i>
                                <strong>Transfer Rejected</strong><br>
                                <small>This transfer has been rejected and no funds were moved.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical w-100" role="group">
                        <?= Html::a('<i class="feather icon-arrow-left"></i> Back to List', ['index'], [
                            'class' => 'btn btn-secondary'
                        ]) ?>
                        
                        <?= Html::a('<i class="feather icon-plus"></i> Create New Transfer', ['create'], [
                            'class' => 'btn btn-primary'
                        ]) ?>
                        
                        <?= Html::a('<i class="feather icon-download"></i> Export All', ['export'], [
                            'class' => 'btn btn-info'
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showApproveModal() {
    const modalId = 'approveModal_' + Date.now();
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="${modalId}Label">
                            <i class="feather icon-check-circle mr-2"></i>Approve Fund Transfer
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="feather icon-check-circle text-success" style="font-size: 3rem;"></i>
                            <p class="mt-3 mb-0">Are you sure you want to approve this fund transfer?</p>
                            <small class="text-muted">This will transfer $<?= number_format($model->amount, 2) ?> from <?= Html::encode($model->fromCustomer->name) ?> to <?= Html::encode($model->toCustomer->name) ?>.</small>
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
                            <i class="feather icon-check mr-1"></i>Approve Transfer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('[id^="approveModal_"]').remove();
    $('body').append(modalHtml);
    $('#' + modalId).modal('show');
    
    $('#' + modalId + '_confirm').on('click', function() {
        const comment = $('#' + modalId + '_comment').val();
        processTransfer('approve', comment);
        $('#' + modalId).modal('hide');
    });
    
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
                            <i class="feather icon-x-circle mr-2"></i>Reject Fund Transfer
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="feather icon-x-circle text-danger" style="font-size: 3rem;"></i>
                            <p class="mt-3 mb-0">Are you sure you want to reject this fund transfer?</p>
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
                            <i class="feather icon-x mr-1"></i>Reject Transfer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('[id^="rejectModal_"]').remove();
    $('body').append(modalHtml);
    $('#' + modalId).modal('show');
    
    $('#' + modalId + '_confirm').on('click', function() {
        const comment = $('#' + modalId + '_comment').val();
        if (!comment.trim()) {
            alert('Please provide a reason for rejection.');
            return;
        }
        processTransfer('reject', comment);
        $('#' + modalId).modal('hide');
    });
    
    $('#' + modalId).on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function processTransfer(action, comment) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= \yii\helpers\Url::to(['approve', 'id' => $model->id]) ?>';
    
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
