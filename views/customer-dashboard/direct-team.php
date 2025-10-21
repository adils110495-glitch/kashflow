<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $customer app\models\Customer */
/* @var $directTeam app\models\Customer[] */
/* @var $packageStats array */
/* @var $usernameFilter string */
/* @var $fromDate string */
/* @var $toDate string */

$this->title = 'Referrals';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="direct-team-index">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Referrals
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->

                    <div class="filter-section mb-4">
                        <?php $form = ActiveForm::begin([
                            'method' => 'get',
                            'options' => ['class' => 'row g-3']
                        ]); ?>
                        
                        <div class="col-md-3">
                            <?= Html::label('Username', 'username', ['class' => 'form-label']) ?>
                            <?= Html::textInput('username', $usernameFilter, [
                                'class' => 'form-control',
                                'placeholder' => 'Search by username'
                            ]) ?>
                        </div>
                        
                        <div class="col-md-3">
                            <?= Html::label('From Date', 'from_date', ['class' => 'form-label']) ?>
                            <?= Html::input('date', 'from_date', $fromDate, [
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                        
                        <div class="col-md-3">
                            <?= Html::label('To Date', 'to_date', ['class' => 'form-label']) ?>
                            <?= Html::input('date', 'to_date', $toDate, [
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <?= Html::submitButton('<i class="feather icon-search"></i> Filter', ['class' => 'btn btn-primary']) ?>
                                    <?= Html::a('<i class="feather icon-refresh-cw"></i> Reset', ['direct-team'], ['class' => 'btn btn-secondary ml-2']) ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php ActiveForm::end(); ?>
                    </div>

                    <!-- Team Statistics -->
                    <div class="team-stats mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Referrals Team</span>
                                        <span class="info-box-number"><?= count($directTeam) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Package Statistics -->
                    <div class="package-stats mb-4">
                        <h5><i class="fas fa-box"></i> Package Statistics</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-gift"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Open Package (Unpaid)</span>
                                        <span class="info-box-number"><?= $packageStats['free']['unpaid'] ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-credit-card"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Paid Packages</span>
                                        <span class="info-box-number"><?= $packageStats['paid']['paid'] ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-secondary">
                                    <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Unpaid</span>
                                        <span class="info-box-number"><?= $packageStats['total']['unpaid'] ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-primary">
                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Paid</span>
                                        <span class="info-box-number"><?= $packageStats['total']['paid'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Members List -->
                    <?php if (!empty($directTeam)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Joined Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($directTeam as $index => $member): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <strong><?= Html::encode($member->user ? $member->user->username : 'N/A') ?></strong>
                                            </td>
                                            <td><?= Html::encode($member->name) ?></td>
                                            <td><?= Html::encode($member->email) ?></td>
                                            <td><?= Html::encode($member->mobile_no) ?></td>
                                            <td>
                                                <?= date('Y-m-d H:i', $member->created_at) ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $member->status == 1 ? 'success' : 'secondary' ?>">
                                                    <?= $member->getStatusText() ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i>
                            No Referral found.
                            <?php if ($usernameFilter || $fromDate || $toDate): ?>
                                Try adjusting your filters.
                            <?php endif; ?>
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

.bg-info {
    background-color: #17a2b8 !important;
    color: #fff;
}
</style>