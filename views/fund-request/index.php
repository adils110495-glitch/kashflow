<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $fundRequests app\models\FundRequest[] */
/* @var $stats array */
/* @var $customers app\models\Customer[] */
/* @var $statusFilter string */
/* @var $customerFilter string */

$this->title = 'Fund Requests Management';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="fund-request-index">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Fund Requests Management</h5>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-white"><?= $stats['total'] ?></h4>
                                            <h6 class="text-white m-b-0">Total Requests</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-file-text f-28"></i>
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
                                            <h4 class="text-white"><?= $stats['pending'] ?></h4>
                                            <h6 class="text-white m-b-0">Pending</h6>
                                            <small class="text-white">$<?= number_format($stats['pending_amount'], 2) ?></small>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-clock f-28"></i>
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
                                            <h4 class="text-white"><?= $stats['approved'] ?></h4>
                                            <h6 class="text-white m-b-0">Approved</h6>
                                            <small class="text-white">$<?= number_format($stats['total_amount'], 2) ?></small>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-check-circle f-28"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="text-white"><?= $stats['rejected'] ?></h4>
                                            <h6 class="text-white m-b-0">Rejected</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-x-circle f-28"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Filter Fund Requests</h5>
                                </div>
                                <div class="card-body filter-section">
                                    <?= Html::beginForm(['index'], 'get', ['class' => 'row']) ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?= Html::label('Status', 'status', ['class' => 'form-label']) ?>
                                                <?= Html::dropDownList('status', $statusFilter, 
                                                    ['' => 'All Status'] + \app\models\FundRequest::getStatusLabels(), 
                                                    ['class' => 'form-control', 'id' => 'status']) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?= Html::label('Customer', 'customer', ['class' => 'form-label']) ?>
                                                <?= Html::dropDownList('customer', $customerFilter, 
                                                    ['' => 'All Customers'] + \yii\helpers\ArrayHelper::map($customers, 'id', function($customer) {
                                                        return $customer->name . ' (' . ($customer->user ? $customer->user->username : 'N/A') . ')';
                                                    }), 
                                                    ['class' => 'form-control', 'id' => 'customer']) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <div>
                                                    <?= Html::submitButton('<i class="feather icon-search"></i> Filter', ['class' => 'btn btn-primary']) ?>
                                                    <?= Html::a('<i class="feather icon-refresh-cw"></i> Reset', ['index'], ['class' => 'btn btn-secondary ml-2']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?= Html::endForm() ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="form-label">Bulk Actions:</label>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-success btn-sm" onclick="bulkAction('approve')">
                                                        <i class="feather icon-check"></i> Approve Selected
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="bulkAction('reject')">
                                                        <i class="feather icon-x"></i> Reject Selected
                                                    </button>
                                                    <a href="<?= Url::to(['export']) ?>" class="btn btn-info btn-sm">
                                                        <i class="feather icon-download"></i> Export CSV
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="form-label">Selected: <span id="selected-count">0</span> requests</label>
                                                <small class="form-text text-muted">Select requests using checkboxes above</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fund Requests Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="select-all" onchange="toggleAllCheckboxes()">
                                    </th>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th>Attachment</th>
                                    <th>Comment</th>
                                    <th>Processed By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fundRequests as $request): ?>
                                    <tr>
                                        <td>
                                            <?php if ($request->isPending()): ?>
                                                <input type="checkbox" class="request-checkbox" value="<?= $request->id ?>">
                                            <?php endif; ?>
                                        </td>
                                        <td>#<?= $request->id ?></td>
                                        <td>
                                            <strong><?= Html::encode($request->customer->name) ?></strong><br>
                                            <small class="text-muted"><?= Html::encode($request->customer->user ? $request->customer->user->username : 'N/A') ?></small>
                                        </td>
                                        <td>
                                            <strong class="text-success"><?= Html::encode($request->getFormattedAmount()) ?></strong>
                                        </td>
                                        <td><?= Html::encode($request->getFormattedRequestDate()) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            switch ($request->status) {
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
                                            ?>
                                            <span class="badge <?= $statusClass ?>">
                                                <?= Html::encode($request->getStatusLabel()) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($request->attachment_file)): ?>
                                                <?php 
                                                $fileInfo = pathinfo($request->attachment_file);
                                                $fileName = $fileInfo['basename'];
                                                $filePath = 'web/' . $request->attachment_file;
                                                $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                                                $fileSizeFormatted = $fileSize > 0 ? ' (' . round($fileSize / 1024, 2) . ' KB)' : '';
                                                ?>
                                                <?= Html::a('<i class="feather icon-download"></i> Download' . $fileSizeFormatted, ['/web/' . $request->attachment_file], [
                                                    'target' => '_blank',
                                                    'class' => 'btn btn-sm btn-outline-primary',
                                                    'title' => 'Download: ' . $fileName
                                                ]) ?>
                                            <?php else: ?>
                                                <span class="text-muted">No Attachment</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($request->comment)): ?>
                                                <small class="text-muted"><?= Html::encode(substr($request->comment, 0, 50)) ?><?= strlen($request->comment) > 50 ? '...' : '' ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($request->processedBy): ?>
                                                <small class="text-info"><?= Html::encode($request->processedBy->username) ?></small><br>
                                                <small class="text-muted"><?= Html::encode($request->getFormattedProcessedDate()) ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">Not Processed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= Html::a('<i class="feather icon-eye"></i> View', ['view', 'id' => $request->id], [
                                                'class' => 'btn btn-sm btn-info',
                                                'title' => 'View Details'
                                            ]) ?>
                                            
                                            <?php if ($request->isPending()): ?>
                                                <button type="button" class="btn btn-sm btn-success" onclick="showIndividualApproveModal(<?= $request->id ?>, <?= $request->amount ?>)">
                                                    <i class="feather icon-check"></i> Approve
                                                </button>
                                                
                                                <button type="button" class="btn btn-sm btn-danger" onclick="showIndividualRejectModal(<?= $request->id ?>)">
                                                    <i class="feather icon-x"></i> Reject
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (empty($fundRequests)): ?>
                        <div class="text-center py-4">
                            <i class="feather icon-inbox" style="font-size: 48px; color: #ccc;"></i>
                            <p class="text-muted mt-2">No fund requests found.</p>
                        </div>
                    <?php endif; ?>
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

.bg-danger {
    background-color: #dc3545 !important;
    color: #fff;
}

.bg-primary {
    background-color: #007bff !important;
    color: #fff;
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

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.075);
}
</style>

<script>
function toggleAllCheckboxes() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.request-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function bulkAction(action) {
    const checkboxes = document.querySelectorAll('.request-checkbox:checked');
    
    if (checkboxes.length === 0) {
        showInfoModal('Selection Required', 'Please select at least one fund request.');
        return;
    }
    
    const selectedIds = Array.from(checkboxes).map(cb => cb.value);
    showBulkConfirmModal(action, selectedIds);
}

function showBulkConfirmModal(action, selectedIds) {
    const modalId = 'bulkModal_' + Date.now();
    const isApprove = action === 'approve';
    const headerClass = isApprove ? 'bg-success' : 'bg-danger';
    const iconClass = isApprove ? 'icon-check-circle' : 'icon-x-circle';
    const iconColor = isApprove ? 'text-success' : 'text-danger';
    const buttonClass = isApprove ? 'btn-success' : 'btn-danger';
    const actionText = isApprove ? 'Approve' : 'Reject';
    
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header ${headerClass} text-white">
                        <h5 class="modal-title" id="${modalId}Label">
                            <i class="feather ${iconClass} mr-2"></i>${actionText} Fund Requests
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="feather ${iconClass} ${iconColor}" style="font-size: 3rem;"></i>
                            <p class="mt-3 mb-0">Are you sure you want to ${actionText.toLowerCase()} ${selectedIds.length} fund request(s)?</p>
                            ${isApprove ? '<small class="text-muted">This will add the amounts to the customers\' ledgers.</small>' : '<small class="text-muted">This action cannot be undone.</small>'}
                        </div>
                        <div class="form-group">
                            <label for="${modalId}_comment" class="form-label">Admin Comment ${isApprove ? '(Optional)' : '(Required)'}:</label>
                            <textarea id="${modalId}_comment" class="form-control" rows="3" placeholder="Add your comment here..." ${!isApprove ? 'required' : ''}></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="${modalId}_cancel">
                            <i class="feather icon-x mr-1"></i>Cancel
                        </button>
                        <button type="button" class="btn ${buttonClass}" id="${modalId}_confirm">
                            <i class="feather ${iconClass} mr-1"></i>${actionText} Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modals if any
    $('[id^="bulkModal_"]').remove();
    
    // Add modal to body
    $('body').append(modalHtml);
    
    // Show modal
    $('#' + modalId).modal('show');
    
    // Handle confirm button click
    $('#' + modalId + '_confirm').on('click', function() {
        const comment = $('#' + modalId + '_comment').val();
        if (!isApprove && !comment.trim()) {
            showInfoModal('Validation Error', 'Please provide a reason for rejection.');
            return;
        }
        processBulkAction(action, selectedIds, comment);
        $('#' + modalId).modal('hide');
    });
    
    // Handle modal close events
    $('#' + modalId).on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function processBulkAction(action, selectedIds, comment) {
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= Url::to(['bulk-action']) ?>';
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'bulk_action';
    actionInput.value = action;
    form.appendChild(actionInput);
    
    const commentInput = document.createElement('input');
    commentInput.type = 'hidden';
    commentInput.name = 'admin_comment';
    commentInput.value = comment;
    form.appendChild(commentInput);
    
    selectedIds.forEach(id => {
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'selected_ids[]';
        idInput.value = id;
        form.appendChild(idInput);
    });
    
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

function showIndividualApproveModal(requestId, amount) {
    const modalId = 'individualApproveModal_' + Date.now();
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="${modalId}Label">
                            <i class="feather icon-check-circle mr-2"></i>Approve Fund Request #${requestId}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="feather icon-check-circle text-success" style="font-size: 3rem;"></i>
                            <p class="mt-3 mb-0">Are you sure you want to approve this fund request?</p>
                            <small class="text-muted">This will add $${parseFloat(amount).toFixed(2)} to the customer's ledger.</small>
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
    $('[id^="individualApproveModal_"]').remove();
    
    // Add modal to body
    $('body').append(modalHtml);
    
    // Show modal
    $('#' + modalId).modal('show');
    
    // Handle confirm button click
    $('#' + modalId + '_confirm').on('click', function() {
        const comment = $('#' + modalId + '_comment').val();
        processIndividualFundRequest(requestId, 'approve', comment);
        $('#' + modalId).modal('hide');
    });
    
    // Handle modal close events
    $('#' + modalId).on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function showIndividualRejectModal(requestId) {
    const modalId = 'individualRejectModal_' + Date.now();
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="${modalId}Label">
                            <i class="feather icon-x-circle mr-2"></i>Reject Fund Request #${requestId}
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
    $('[id^="individualRejectModal_"]').remove();
    
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
        processIndividualFundRequest(requestId, 'reject', comment);
        $('#' + modalId).modal('hide');
    });
    
    // Handle modal close events
    $('#' + modalId).on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function processIndividualFundRequest(requestId, action, comment) {
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= Url::to(['approve']) ?>/' + requestId;
    
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

// Update selected count when checkboxes change
$(document).ready(function() {
    function updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.request-checkbox:checked');
        document.getElementById('selected-count').textContent = checkedBoxes.length;
    }
    
    // Update count when individual checkboxes change
    $(document).on('change', '.request-checkbox', updateSelectedCount);
    
    // Update count when select all changes
    $(document).on('change', '#select-all', function() {
        updateSelectedCount();
    });
    
    // Initial count update
    updateSelectedCount();
});
</script>