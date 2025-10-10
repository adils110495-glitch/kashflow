<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Withdrawal;

/* @var $this yii\web\View */
/* @var $pendingWithdrawals app\models\Withdrawal[] */
/* @var $stats array */
/* @var $searchTerm string */

$this->title = 'Pending Withdrawal Requests';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-pending-withdrawals">
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

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Quick Actions</h6>
                            <div class="btn-group" role="group">
                                <?= Html::a('All Withdrawals', ['all-withdrawals'], [
                                    'class' => 'btn btn-primary'
                                ]) ?>
                                <?= Html::a('Withdrawal Management', ['withdrawals'], [
                                    'class' => 'btn btn-secondary'
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <h6>Pending Requests: <span class="badge badge-warning"><?= count($pendingWithdrawals) ?></span></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'method' => 'get',
                        'options' => ['class' => 'form-inline']
                    ]); ?>
                    
                    <div class="form-group mr-3">
                        <?= Html::label('Search:', 'search', ['class' => 'mr-2']) ?>
                        <?= Html::textInput('search', $searchTerm, [
                            'class' => 'form-control',
                            'placeholder' => 'Customer name, email, or mobile'
                        ]) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Reset', ['pending-withdrawals'], ['class' => 'btn btn-secondary ml-2']) ?>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Withdrawals Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Pending Withdrawal Requests</h5>
                    <div class="card-header-right">
                        <span class="badge badge-warning"><?= count($pendingWithdrawals) ?> Pending</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($pendingWithdrawals)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Request Date</th>
                                        <th>Status</th>
                                        <th>Action By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingWithdrawals as $withdrawal): ?>
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
                                                    
                                                    <?= Html::a('Approve', ['approve-withdrawal', 'id' => $withdrawal->id], [
                                                        'class' => 'btn btn-sm btn-success',
                                                        'data-confirm' => 'Are you sure you want to approve this withdrawal?'
                                                    ]) ?>
                                                    
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="rejectWithdrawal(<?= $withdrawal->id ?>)">
                                                        Reject
                                                    </button>
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
                            No pending withdrawal requests found.
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
                'action' => ['reject-withdrawal', 'id' => ''],
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

<style>
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

.badge {
    font-size: 0.75em;
}
</style>

<script>
function rejectWithdrawal(id) {
    document.getElementById('rejectWithdrawalId').value = id;
    // Update form action URL with the correct id
    $('#rejectForm').attr('action', '<?= Url::to(['reject-withdrawal']) ?>?id=' + id);
    $('#rejectModal').modal('show');
}
</script>
