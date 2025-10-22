<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $customer app\models\Customer */
/* @var $fundRequest app\models\FundRequest */
/* @var $fundRequests app\models\FundRequest[] */

$this->title = 'Fund Request';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="customer-dashboard-fund-request">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Submit Fund Request</h5>
                </div>
                <div class="card-body">
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= Yii::$app->session->getFlash('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= Yii::$app->session->getFlash('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin([
                        'options' => ['enctype' => 'multipart/form-data']
                    ]); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($fundRequest, 'amount')->textInput([
                                'type' => 'number',
                                'step' => '0.01',
                                'min' => '0.01',
                                'placeholder' => 'Enter amount'
                            ])->label('Request Amount ($)') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($fundRequest, 'attachment_file')->fileInput([
                                'accept' => '.pdf,.jpg,.jpeg,.png,.doc,.docx',
                                'class' => 'form-control',
                                'required' => true
                            ])->label('Attachment <span class="text-danger">*</span>') ?>
                            <small class="form-text text-muted">
                                <i class="feather icon-info"></i> Supported formats: PDF, JPG, PNG, DOC, DOCX (Max: 5MB)
                            </small>
                        </div>
                    </div>

                    <?= $form->field($fundRequest, 'comment')->textarea([
                        'rows' => 4,
                        'placeholder' => 'Please provide details about your fund request...'
                    ])->label('Comment') ?>

                    <div class="form-group">
                        <?= Html::submitButton('<i class="feather icon-send"></i> Submit Request', [
                            'class' => 'btn btn-primary btn-lg'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Fund Request History -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Fund Request History</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($fundRequests)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Request Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Attachment</th>
                                        <th>Comment</th>
                                        <th>Admin Comment</th>
                                        <th>Processed Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fundRequests as $request): ?>
                                        <tr>
                                            <td><?= Html::encode($request->getFormattedRequestDate()) ?></td>
                                            <td>
                                                <strong class="text-success"><?= Html::encode($request->getFormattedAmount()) ?></strong>
                                            </td>
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
                                                    <small class="text-muted"><?= Html::encode($request->comment) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($request->admin_comment)): ?>
                                                    <small class="text-info"><?= Html::encode($request->admin_comment) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= Html::encode($request->getFormattedProcessedDate()) ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
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

.btn-lg {
    padding: 12px 30px;
    font-size: 16px;
}
</style>