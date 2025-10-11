<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\models\Ticket;

$this->title = 'Support Tickets';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="customer-dashboard-tickets">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <i class="feather icon-help-circle me-2"></i>
                                Support Tickets & Complaints
                            </h5>
                        </div>
                        <div class="col-auto">
                            <a href="<?= Url::to(['create-ticket']) ?>" class="btn btn-primary">
                                <i class="feather icon-plus me-1"></i>
                                Create New Ticket
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0"><?= $stats['total'] ?></h4>
                                            <span>Total Tickets</span>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="feather icon-file-text fa-2x"></i>
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
                                            <h4 class="mb-0"><?= $stats['open'] ?></h4>
                                            <span>Open</span>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="feather icon-clock fa-2x"></i>
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
                                            <h4 class="mb-0"><?= $stats['in_progress'] ?></h4>
                                            <span>In Progress</span>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="feather icon-loader fa-2x"></i>
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
                                            <h4 class="mb-0"><?= $stats['resolved'] + $stats['closed'] ?></h4>
                                            <span>Resolved</span>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="feather icon-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="get" class="d-flex gap-2">
                                <select name="status" class="form-select" style="width: auto;">
                                    <option value="">All Statuses</option>
                                    <option value="<?= Ticket::STATUS_OPEN ?>" <?= $statusFilter === Ticket::STATUS_OPEN ? 'selected' : '' ?>>Open</option>
                                    <option value="<?= Ticket::STATUS_IN_PROGRESS ?>" <?= $statusFilter === Ticket::STATUS_IN_PROGRESS ? 'selected' : '' ?>>In Progress</option>
                                    <option value="<?= Ticket::STATUS_RESOLVED ?>" <?= $statusFilter === Ticket::STATUS_RESOLVED ? 'selected' : '' ?>>Resolved</option>
                                    <option value="<?= Ticket::STATUS_CLOSED ?>" <?= $statusFilter === Ticket::STATUS_CLOSED ? 'selected' : '' ?>>Closed</option>
                                </select>
                                <select name="priority" class="form-select" style="width: auto;">
                                    <option value="">All Priorities</option>
                                    <option value="<?= Ticket::PRIORITY_LOW ?>" <?= $priorityFilter === Ticket::PRIORITY_LOW ? 'selected' : '' ?>>Low</option>
                                    <option value="<?= Ticket::PRIORITY_MEDIUM ?>" <?= $priorityFilter === Ticket::PRIORITY_MEDIUM ? 'selected' : '' ?>>Medium</option>
                                    <option value="<?= Ticket::PRIORITY_HIGH ?>" <?= $priorityFilter === Ticket::PRIORITY_HIGH ? 'selected' : '' ?>>High</option>
                                    <option value="<?= Ticket::PRIORITY_URGENT ?>" <?= $priorityFilter === Ticket::PRIORITY_URGENT ? 'selected' : '' ?>>Urgent</option>
                                </select>
                                <button type="submit" class="btn btn-outline-primary">Filter</button>
                                <a href="<?= Url::to(['tickets']) ?>" class="btn btn-outline-secondary">Clear</a>
                            </form>
                        </div>
                    </div>

                    <!-- Tickets Table -->
                    <?php if (empty($tickets)): ?>
                        <div class="text-center py-5">
                            <i class="feather icon-help-circle fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No tickets found</h5>
                            <p class="text-muted">You haven't created any support tickets yet.</p>
                            <a href="<?= Url::to(['create-ticket']) ?>" class="btn btn-primary">
                                <i class="feather icon-plus me-1"></i>
                                Create Your First Ticket
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $ticket): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?= $ticket->id ?></strong>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold"><?= Html::encode($ticket->subject) ?></span>
                                                    <small class="text-muted">
                                                        <?= Html::encode(substr($ticket->description, 0, 100)) ?>
                                                        <?= strlen($ticket->description) > 100 ? '...' : '' ?>
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <?= $ticket->getStatusLabel() ?>
                                            </td>
                                            <td>
                                                <?= $ticket->getPriorityLabel() ?>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span><?= date('M d, Y', strtotime($ticket->created_at)) ?></span>
                                                    <small class="text-muted"><?= date('h:i A', strtotime($ticket->created_at)) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="<?= Url::to(['view-ticket', 'id' => $ticket->id]) ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="feather icon-eye"></i>
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.badge {
    font-size: 0.75em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
