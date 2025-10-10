<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Withdrawal;

/* @var $this yii\web\View */
/* @var $withdrawals app\models\Withdrawal[] */
/* @var $stats array */
/* @var $statusFilter string */
/* @var $searchTerm string */

$this->title = 'Withdrawal Management';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-withdrawals">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header-title">
                <h4 class="page-title"><?= Html::encode($this->title) ?></h4>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="text-center">
                        <h4><?= $stats['total'] ?></h4>
                        <small>Total Requests</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="text-center">
                        <h4><?= $stats['pending'] ?></h4>
                        <small>Pending</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="text-center">
                        <h4><?= $stats['approved'] ?></h4>
                        <small>Approved</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="text-center">
                        <h4><?= $stats['processing'] ?></h4>
                        <small>Processing</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="text-center">
                        <h4><?= $stats['completed'] ?></h4>
                        <small>Completed</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="text-center">
                        <h4><?= $stats['rejected'] ?></h4>
                        <small>Rejected</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Amount Statistics -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Amount Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Amount</span>
                                    <span class="info-box-number">$<?= number_format($stats['total_amount'], 2) ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending Amount</span>
                                    <span class="info-box-number">$<?= number_format($stats['pending_amount'], 2) ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Approved Amount</span>
                                    <span class="info-box-number">$<?= number_format($stats['approved_amount'], 2) ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-check-double"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completed Amount</span>
                                    <span class="info-box-number">$<?= number_format($stats['completed_amount'], 2) ?></span>
                                </div>
                            </div>
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
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'method' => 'get',
                        'options' => ['class' => 'form-inline']
                    ]); ?>
                    
                    <div class="form-group mr-3">
                        <?= Html::label('Status:', 'status', ['class' => 'mr-2']) ?>
                        <?= Html::dropDownList('status', $statusFilter, [
                            '' => 'All Status',
                            Withdrawal::STATUS_PENDING => 'Pending',
                            Withdrawal::STATUS_APPROVED => 'Approved',
                            Withdrawal::STATUS_REJECTED => 'Rejected',
                            Withdrawal::STATUS_PROCESSING => 'Processing',
                            Withdrawal::STATUS_COMPLETED => 'Completed',
                        ], ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="form-group mr-3">
                        <?= Html::label('Search:', 'search', ['class' => 'mr-2']) ?>
                        <?= Html::textInput('search', $searchTerm, [
                            'class' => 'form-control',
                            'placeholder' => 'Customer name, email, or mobile'
                        ]) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= Html::submitButton('Filter', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Reset', ['withdrawals'], ['class' => 'btn btn-secondary ml-2']) ?>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawals Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Withdrawal Requests</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($withdrawals)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($withdrawals as $withdrawal): ?>
                                        <tr>
                                            <td><?= $withdrawal->id ?></td>
                                            <td>
                                                <strong><?= Html::encode($withdrawal->customer->name) ?></strong><br>
                                                <small class="text-muted"><?= Html::encode($withdrawal->customer->email) ?></small>
                                            </td>
                                            <td>
                                                <strong class="text-danger">$<?= number_format($withdrawal->amount, 2) ?></strong>
                                            </td>
                                            <td><?= Html::encode($withdrawal->getFormattedDate()) ?></td>
                                            <td><?= $withdrawal->getStatusLabel() ?></td>
                                            <td>
                                                <?= Html::encode($withdrawal->actionBy ? $withdrawal->actionBy->username : 'System') ?><br>
                                                <small class="text-muted"><?= Html::encode($withdrawal->getFormattedActionDateTime()) ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?= Html::a('View', ['view-withdrawal', 'id' => $withdrawal->id], [
                                                        'class' => 'btn btn-sm btn-info'
                                                    ]) ?>
                                                    
                                                    <?php if ($withdrawal->status == Withdrawal::STATUS_PENDING): ?>
                                                        <?= Html::a('Approve', ['approve-withdrawal', 'id' => $withdrawal->id], [
                                                            'class' => 'btn btn-sm btn-success',
                                                            'data-confirm' => 'Are you sure you want to approve this withdrawal?'
                                                        ]) ?>
                                                        
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                onclick="rejectWithdrawal(<?= $withdrawal->id ?>)">
                                                            Reject
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($withdrawal->status == Withdrawal::STATUS_APPROVED): ?>
                                                        <button type="button" class="btn btn-sm btn-info" 
                                                                onclick="processWithdrawal(<?= $withdrawal->id ?>)">
                                                            Process
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($withdrawal->status == Withdrawal::STATUS_PROCESSING): ?>
                                                        <button type="button" class="btn btn-sm btn-primary" 
                                                                onclick="completeWithdrawal(<?= $withdrawal->id ?>)">
                                                            Complete
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i>
                            No withdrawal requests found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Withdrawal</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'action' => ['reject-withdrawal'],
                'options' => ['id' => 'rejectForm']
            ]); ?>
            <div class="modal-body">
                <?= Html::hiddenInput('id', '', ['id' => 'rejectWithdrawalId']) ?>
                <div class="form-group">
                    <label for="rejectComment">Reason for rejection:</label>
                    <?= Html::textarea('comment', '', [
                        'id' => 'rejectComment',
                        'class' => 'form-control',
                        'rows' => 3,
                        'placeholder' => 'Enter reason for rejection...'
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <?= Html::submitButton('Reject Withdrawal', ['class' => 'btn btn-danger']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<!-- Process Modal -->
<div class="modal fade" id="processModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Processing</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'action' => ['process-withdrawal'],
                'options' => ['id' => 'processForm']
            ]); ?>
            <div class="modal-body">
                <?= Html::hiddenInput('id', '', ['id' => 'processWithdrawalId']) ?>
                <div class="form-group">
                    <label for="processComment">Processing notes:</label>
                    <?= Html::textarea('comment', '', [
                        'id' => 'processComment',
                        'class' => 'form-control',
                        'rows' => 3,
                        'placeholder' => 'Enter processing notes...'
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <?= Html::submitButton('Mark as Processing', ['class' => 'btn btn-info']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Completed</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'action' => ['complete-withdrawal'],
                'options' => ['id' => 'completeForm']
            ]); ?>
            <div class="modal-body">
                <?= Html::hiddenInput('id', '', ['id' => 'completeWithdrawalId']) ?>
                <div class="form-group">
                    <label for="completeComment">Completion notes:</label>
                    <?= Html::textarea('comment', '', [
                        'id' => 'completeComment',
                        'class' => 'form-control',
                        'rows' => 3,
                        'placeholder' => 'Enter completion notes...'
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <?= Html::submitButton('Mark as Completed', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
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

.bg-primary {
    background-color: #007bff !important;
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

.bg-info {
    background-color: #17a2b8 !important;
    color: #fff;
}

.bg-danger {
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
function rejectWithdrawal(id) {
    document.getElementById('rejectWithdrawalId').value = id;
    $('#rejectModal').modal('show');
}

function processWithdrawal(id) {
    document.getElementById('processWithdrawalId').value = id;
    $('#processModal').modal('show');
}

function completeWithdrawal(id) {
    document.getElementById('completeWithdrawalId').value = id;
    $('#completeModal').modal('show');
}
</script>
