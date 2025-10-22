<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $stats array */
/* @var $customers app\models\Customer[] */
/* @var $statusFilter string */
/* @var $customerFilter string */

$this->title = 'Fund Transfers Management';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="fund-transfer-index">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Fund Transfers Management</h5>
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
                                            <h6 class="text-white m-b-0">Total Transfers</h6>
                                        </div>
                                        <div class="col-4 text-right">
                                            <i class="feather icon-repeat f-28"></i>
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
                                    <h5>Filter Fund Transfers</h5>
                                </div>
                                <div class="card-body filter-section">
                                    <?= Html::beginForm(['index'], 'get', ['class' => 'row']) ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?= Html::label('Status', 'status', ['class' => 'form-label']) ?>
                                                <?= Html::dropDownList('status', $statusFilter, 
                                                    ['' => 'All Status'] + \app\models\FundTransfer::getStatusLabels(), 
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

                    <!-- Actions -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="form-label">Actions:</label>
                                                <div class="btn-group" role="group">
                                                    <?= Html::a('<i class="feather icon-plus"></i> Create Transfer', ['create'], [
                                                        'class' => 'btn btn-primary btn-sm'
                                                    ]) ?>
                                                    <a href="<?= Url::to(['export']) ?>" class="btn btn-info btn-sm">
                                                        <i class="feather icon-download"></i> Export CSV
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fund Transfers Table -->
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => ['class' => 'table table-striped table-hover'],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            
                            [
                                'attribute' => 'id',
                                'label' => 'ID',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '#' . $model->id;
                                }
                            ],
                            
                            [
                                'attribute' => 'from_customer_id',
                                'label' => 'From Customer',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<strong>' . Html::encode($model->fromCustomer->name) . '</strong><br>' .
                                           '<small class="text-muted">' . Html::encode($model->fromCustomer->user ? $model->fromCustomer->user->username : 'N/A') . '</small>';
                                }
                            ],
                            
                            [
                                'attribute' => 'to_customer_id',
                                'label' => 'To Customer',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<strong>' . Html::encode($model->toCustomer->name) . '</strong><br>' .
                                           '<small class="text-muted">' . Html::encode($model->toCustomer->user ? $model->toCustomer->user->username : 'N/A') . '</small>';
                                }
                            ],
                            
                            [
                                'attribute' => 'amount',
                                'label' => 'Amount',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<strong class="text-success">' . $model->getFormattedAmount() . '</strong>';
                                }
                            ],
                            
                            [
                                'attribute' => 'transfer_date',
                                'label' => 'Transfer Date',
                                'value' => function ($model) {
                                    return $model->getFormattedTransferDate();
                                }
                            ],
                            
                            [
                                'attribute' => 'status',
                                'label' => 'Status',
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
                                'label' => 'Type',
                                'value' => function ($model) {
                                    return $model->getTransferTypeLabel();
                                }
                            ],
                            
                            [
                                'attribute' => 'comment',
                                'label' => 'Comment',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if (!empty($model->comment)) {
                                        return '<small class="text-muted">' . Html::encode(substr($model->comment, 0, 50)) . (strlen($model->comment) > 50 ? '...' : '') . '</small>';
                                    }
                                    return '<span class="text-muted">-</span>';
                                }
                            ],
                            
                            [
                                'attribute' => 'processed_by',
                                'label' => 'Processed By',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->processedBy) {
                                        return '<small class="text-info">' . Html::encode($model->processedBy->username) . '</small><br>' .
                                               '<small class="text-muted">' . Html::encode($model->getFormattedProcessedDate()) . '</small>';
                                    }
                                    return '<span class="text-muted">Not Processed</span>';
                                }
                            ],
                            
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Actions',
                                'template' => '{view} {approve} {reject}',
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        return Html::a('<i class="feather icon-eye"></i>', ['view', 'id' => $model->id], [
                                            'class' => 'btn btn-sm btn-info',
                                            'title' => 'View Details'
                                        ]);
                                    },
                                    'approve' => function ($url, $model, $key) {
                                        if ($model->isPending()) {
                                            return Html::a('<i class="feather icon-check"></i>', ['approve', 'id' => $model->id], [
                                                'class' => 'btn btn-sm btn-success',
                                                'title' => 'Approve Transfer',
                                                'data' => [
                                                    'method' => 'post',
                                                    'params' => ['action' => 'approve']
                                                ],
                                                'onclick' => 'return confirm("Are you sure you want to approve this transfer?")'
                                            ]);
                                        }
                                        return '';
                                    },
                                    'reject' => function ($url, $model, $key) {
                                        if ($model->isPending()) {
                                            return Html::a('<i class="feather icon-x"></i>', ['approve', 'id' => $model->id], [
                                                'class' => 'btn btn-sm btn-danger',
                                                'title' => 'Reject Transfer',
                                                'data' => [
                                                    'method' => 'post',
                                                    'params' => ['action' => 'reject']
                                                ],
                                                'onclick' => 'return confirm("Are you sure you want to reject this transfer?")'
                                            ]);
                                        }
                                        return '';
                                    },
                                ],
                            ],
                        ],
                    ]) ?>

                    <?php if ($dataProvider->getTotalCount() == 0): ?>
                        <div class="text-center py-4">
                            <i class="feather icon-inbox" style="font-size: 48px; color: #ccc;"></i>
                            <p class="text-muted mt-2">No fund transfers found.</p>
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
