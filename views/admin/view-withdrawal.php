<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Withdrawal;

/* @var $this yii\web\View */
/* @var $withdrawal app\models\Withdrawal */

$this->title = 'Withdrawal Details #' . $withdrawal->id;
$this->params['breadcrumbs'][] = ['label' => 'Withdrawals', 'url' => ['withdrawals']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-view-withdrawal">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header-title">
                <h4 class="page-title"><?= Html::encode($this->title) ?></h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Withdrawal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Withdrawal ID:</strong></td>
                                    <td><?= $withdrawal->id ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Customer:</strong></td>
                                    <td>
                                        <?= Html::encode($withdrawal->customer->name) ?><br>
                                        <small class="text-muted"><?= Html::encode($withdrawal->customer->email) ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td>
                                        <span class="h4 text-danger">$<?= number_format($withdrawal->amount, 2) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Date:</strong></td>
                                    <td><?= Html::encode($withdrawal->getFormattedDate()) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><?= $withdrawal->getStatusLabel() ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Action By:</strong></td>
                                    <td>
                                        <?= Html::encode($withdrawal->actionBy ? $withdrawal->actionBy->username : 'System') ?><br>
                                        <small class="text-muted"><?= Html::encode($withdrawal->getFormattedActionDateTime()) ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td><?= Html::encode($withdrawal->getFormattedCreatedAt()) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td><?= Html::encode($withdrawal->getFormattedUpdatedAt()) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php if (!empty($withdrawal->comment)): ?>
                        <hr>
                        <h6>Comments:</h6>
                        <div class="alert alert-info">
                            <?= nl2br(Html::encode($withdrawal->comment)) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Actions</h5>
                </div>
                <div class="card-body">
                    <?php if ($withdrawal->status == Withdrawal::STATUS_PENDING): ?>
                        <div class="btn-group-vertical w-100" role="group">
                            <?= Html::a('Approve Withdrawal', ['approve-withdrawal', 'id' => $withdrawal->id], [
                                'class' => 'btn btn-success mb-2',
                                'data-confirm' => 'Are you sure you want to approve this withdrawal?'
                            ]) ?>
                            
                            <button type="button" class="btn btn-danger mb-2" 
                                    onclick="rejectWithdrawal(<?= $withdrawal->id ?>)">
                                Reject Withdrawal
                            </button>
                        </div>
                    <?php elseif ($withdrawal->status == Withdrawal::STATUS_APPROVED): ?>
                        <div class="btn-group-vertical w-100" role="group">
                            <button type="button" class="btn btn-info mb-2" 
                                    onclick="processWithdrawal(<?= $withdrawal->id ?>)">
                                Mark as Processing
                            </button>
                        </div>
                    <?php elseif ($withdrawal->status == Withdrawal::STATUS_PROCESSING): ?>
                        <div class="btn-group-vertical w-100" role="group">
                            <button type="button" class="btn btn-primary mb-2" 
                                    onclick="completeWithdrawal(<?= $withdrawal->id ?>)">
                                Mark as Completed
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div class="btn-group-vertical w-100" role="group">
                        <?= Html::a('Back to List', ['withdrawals'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Customer Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?= Html::encode($withdrawal->customer->name) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><?= Html::encode($withdrawal->customer->email) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mobile:</strong></td>
                            <td><?= Html::encode($withdrawal->customer->mobile_no) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Package:</strong></td>
                            <td>
                                <?= $withdrawal->customer->currentPackage ? 
                                    Html::encode($withdrawal->customer->currentPackage->name) : 
                                    'No Package' ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Balance:</strong></td>
                            <td>
                                <span class="text-success">$<?= number_format($withdrawal->customer->getLedgerBalance(), 2) ?></span>
                            </td>
                        </tr>
                    </table>
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
            <form action="<?= Url::to(['reject-withdrawal', 'id' => $withdrawal->id]) ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejectWithdrawalId">
                    <div class="form-group">
                        <label for="rejectComment">Reason for rejection:</label>
                        <textarea name="comment" id="rejectComment" class="form-control" rows="3" 
                                  placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Withdrawal</button>
                </div>
            </form>
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
            <form action="<?= Url::to(['process-withdrawal', 'id' => $withdrawal->id]) ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="processWithdrawalId">
                    <div class="form-group">
                        <label for="processComment">Processing notes:</label>
                        <textarea name="comment" id="processComment" class="form-control" rows="3" 
                                  placeholder="Enter processing notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Mark as Processing</button>
                </div>
            </form>
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
            <form action="<?= Url::to(['complete-withdrawal', 'id' => $withdrawal->id]) ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="completeWithdrawalId">
                    <div class="form-group">
                        <label for="completeComment">Completion notes:</label>
                        <textarea name="comment" id="completeComment" class="form-control" rows="3" 
                                  placeholder="Enter completion notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Mark as Completed</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
