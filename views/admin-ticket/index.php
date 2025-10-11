<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\models\Ticket;

$this->title = 'Admin - Support Tickets';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-ticket-index">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <i class="feather icon-ticket me-2"></i>
                                Support Tickets Management
                            </h5>
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
                        <div class="col-md-12">
                            <form method="get" class="d-flex gap-2 flex-wrap">
                                <select name="status" class="form-select" style="width: auto;">
                                    <option value="">All Statuses</option>
                                    <option value="<?= Ticket::STATUS_OPEN ?>" <?= $statusFilter == Ticket::STATUS_OPEN ? 'selected' : '' ?>>Open</option>
                                    <option value="<?= Ticket::STATUS_IN_PROGRESS ?>" <?= $statusFilter == Ticket::STATUS_IN_PROGRESS ? 'selected' : '' ?>>In Progress</option>
                                    <option value="<?= Ticket::STATUS_RESOLVED ?>" <?= $statusFilter == Ticket::STATUS_RESOLVED ? 'selected' : '' ?>>Resolved</option>
                                    <option value="<?= Ticket::STATUS_CLOSED ?>" <?= $statusFilter == Ticket::STATUS_CLOSED ? 'selected' : '' ?>>Closed</option>
                                </select>
                                <select name="priority" class="form-select" style="width: auto;">
                                    <option value="">All Priorities</option>
                                    <option value="<?= Ticket::PRIORITY_LOW ?>" <?= $priorityFilter === Ticket::PRIORITY_LOW ? 'selected' : '' ?>>Low</option>
                                    <option value="<?= Ticket::PRIORITY_MEDIUM ?>" <?= $priorityFilter === Ticket::PRIORITY_MEDIUM ? 'selected' : '' ?>>Medium</option>
                                    <option value="<?= Ticket::PRIORITY_HIGH ?>" <?= $priorityFilter === Ticket::PRIORITY_HIGH ? 'selected' : '' ?>>High</option>
                                    <option value="<?= Ticket::PRIORITY_URGENT ?>" <?= $priorityFilter === Ticket::PRIORITY_URGENT ? 'selected' : '' ?>>Urgent</option>
                                </select>
                                <input type="text" name="customer" class="form-control" placeholder="Customer Username" value="<?= Html::encode($customerFilter) ?>" style="width: 200px;">
                                <button type="submit" class="btn btn-outline-primary">Filter</button>
                                <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">Clear</a>
                            </form>
                        </div>
                    </div>

                    <!-- Tickets Table -->
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'label' => 'Ticket ID',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<strong>#' . $model->id . '</strong>';
                                },
                            ],
                            [
                                'attribute' => 'subject',
                                'label' => 'Subject',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<div class="d-flex flex-column">
                                                <span class="fw-bold">' . Html::encode($model->subject) . '</span>
                                                <small class="text-muted">' . Html::encode(substr($model->description, 0, 100)) . '...</small>
                                            </div>';
                                },
                            ],
                            [
                                'attribute' => 'customer_id',
                                'label' => 'Customer',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<div class="d-flex flex-column">
                                                <span class="fw-bold">' . Html::encode($model->customer->name) . '</span>
                                                <small class="text-muted">@' . Html::encode($model->customer->user->username) . '</small>
                                            </div>';
                                },
                            ],
                            [
                                'attribute' => 'status',
                                'label' => 'Status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->getStatusLabel();
                                },
                            ],
                            [
                                'attribute' => 'priority',
                                'label' => 'Priority',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->getPriorityLabel();
                                },
                            ],
                            [
                                'attribute' => 'created_at',
                                'label' => 'Created',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<div class="d-flex flex-column">
                                                <span>' . date('M d, Y', strtotime($model->created_at)) . '</span>
                                                <small class="text-muted">' . date('h:i A', strtotime($model->created_at)) . '</small>
                                            </div>';
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Actions',
                                'template' => '{view} {update-status}',
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        return Html::a('<i class="feather icon-eye"></i>', ['view', 'id' => $model->id], [
                                            'class' => 'btn btn-sm btn-outline-primary',
                                            'title' => 'View Ticket'
                                        ]);
                                    },
                                    'update-status' => function ($url, $model, $key) {
                                        $statuses = [
                                            Ticket::STATUS_OPEN => 'Open',
                                            Ticket::STATUS_IN_PROGRESS => 'In Progress',
                                            Ticket::STATUS_RESOLVED => 'Resolved',
                                            Ticket::STATUS_CLOSED => 'Closed'
                                        ];
                                        
                                        $dropdown = '<div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Status
                                            </button>
                                            <ul class="dropdown-menu">';
                                        
                                        foreach ($statuses as $value => $label) {
                                            $active = $model->status == $value ? 'active' : '';
                                            $dropdown .= '<li><a class="dropdown-item ' . $active . '" href="#" onclick="updateStatus(' . $model->id . ', ' . $value . ')">' . $label . '</a></li>';
                                        }
                                        
                                        $dropdown .= '</ul></div>';
                                        return $dropdown;
                                    },
                                ],
                            ],
                        ],
                    ]) ?>
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

.dropdown-menu {
    min-width: 120px;
}
</style>

<script>
function updateStatus(ticketId, status) {
    if (confirm('Are you sure you want to update the ticket status?')) {
        fetch('<?= Url::to(['update-status']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
            },
            body: 'ticket_id=' + ticketId + '&status=' + status
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Ticket status updated successfully.');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status.');
        });
    }
}
</script>
