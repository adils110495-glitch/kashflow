<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\LevelPlan;

/* @var $this yii\web\View */
/* @var $levelTeam array */
/* @var $levelPlans app\models\LevelPlan[] */
/* @var $usernameFilter string */
/* @var $fromDate string */
/* @var $toDate string */
/* @var $levelFilter string */

$this->title = 'Level Team';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="giiant-crud level-team-index card">

    <div class="card-header">
        <h5><i class="feather icon-layers"></i> <?= Html::encode($this->title) ?></h5>
        <p class="text-muted mb-0">View level team hierarchy based on referral structure</p>
        <div class="card-header-right">
            <div class="btn-group card-option">
                <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="feather icon-more-horizontal"></i>
                </button>
                <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                    <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>
                    <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                    <li class="dropdown-item reload-card"><a href="#!"><i class="feather icon-refresh-cw"></i> reload</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Filter Form -->
    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'options' => ['class' => 'form-inline mb-3']
        ]); ?>
        
        <div class="row">
            <div class="col-md-3">
                <?= Html::label('Username (Root)', 'username', ['class' => 'form-label']) ?>
                <?= Html::textInput('username', $usernameFilter, [
                    'class' => 'form-control',
                    'placeholder' => 'Enter root username',
                    'id' => 'username-filter',
                    'required' => true
                ]) ?>
                <small class="form-text text-muted">Enter the root username to build level team</small>
            </div>
            
            <div class="col-md-2">
                <?= Html::label('Level', 'level', ['class' => 'form-label']) ?>
                <?= Html::dropDownList('level', $levelFilter, ArrayHelper::map($levelPlans, 'level', function($model) {
                    return 'Level ' . $model->level . ' (' . $model->rate . '%)';
                }), [
                    'class' => 'form-control',
                    'prompt' => 'All Levels',
                    'id' => 'level-filter'
                ]) ?>
            </div>
            
            <div class="col-md-2">
                <?= Html::label('From Date', 'from_date', ['class' => 'form-label']) ?>
                <?= Html::textInput('from_date', $fromDate, [
                    'class' => 'form-control',
                    'placeholder' => 'From Date',
                    'id' => 'from-date',
                    'type' => 'date'
                ]) ?>
            </div>
            
            <div class="col-md-2">
                <?= Html::label('To Date', 'to_date', ['class' => 'form-label']) ?>
                <?= Html::textInput('to_date', $toDate, [
                    'class' => 'form-control',
                    'placeholder' => 'To Date',
                    'id' => 'to-date',
                    'type' => 'date'
                ]) ?>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <label>&nbsp;</label><br>
                    <?= Html::submitButton('Filter', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Reset', ['level-team'], ['class' => 'btn btn-secondary ml-2']) ?>
                </div>
            </div>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>

    <!-- Results Section -->
    <div class="card-body">
        <?php if (empty($usernameFilter)): ?>
            <div class="alert alert-info text-center">
                <i class="feather icon-info"></i>
                <h4>Enter Root Username</h4>
                <p>Please enter a username to view the level team hierarchy.</p>
            </div>
        <?php elseif (empty($levelTeam)): ?>
            <div class="alert alert-warning text-center">
                <i class="feather icon-alert-triangle"></i>
                <h4>No Level Team Found</h4>
                <p>No level team members found for username: <strong><?= Html::encode($usernameFilter) ?></strong></p>
                <small class="text-muted">This user may not have any referrals or the filters may be too restrictive.</small>
            </div>
        <?php else: ?>
            <div class="alert alert-success mb-3">
                <i class="feather icon-info"></i>
                Level team hierarchy for: <strong><?= Html::encode($usernameFilter) ?></strong>
                <?php if ($levelFilter): ?>
                    (Filtered by Level <?= $levelFilter ?>)
                <?php endif; ?>
            </div>
            
            <!-- Level Team Hierarchy -->
            <div class="level-team-hierarchy">
                <?php foreach ($levelTeam as $levelData): ?>
                    <div class="level-section mb-4" data-level="<?= $levelData['level'] ?>">
                        <div class="level-header">
                            <h4 class="level-title">
                                <i class="feather icon-layers"></i> Level <?= $levelData['level'] ?>
                                <span class="badge badge-primary ml-2"><?= $levelData['count'] ?> members</span>
                                <?php 
                                $levelPlan = ArrayHelper::getValue($levelPlans, $levelData['level'] - 1);
                                if ($levelPlan): ?>
                                    <span class="badge badge-info ml-1"><?= $levelPlan->rate ?>% rate</span>
                                <?php endif; ?>
                            </h4>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Package</th>
                                        <th>Package Value</th>
                                        <th>Joined Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($levelData['customers'] as $index => $customer): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <strong><?= $customer->user ? Html::encode($customer->user->username) : 'N/A' ?></strong>
                                            </td>
                                            <td><?= Html::encode($customer->name) ?></td>
                                            <td><?= $customer->user ? Html::encode($customer->user->email) : 'N/A' ?></td>
                                            <td><?= Html::encode($customer->mobile_no) ?></td>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    <?= $customer->currentPackage ? Html::encode($customer->currentPackage->name) : 'No Package' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $customer->currentPackage ? '$' . number_format($customer->currentPackage->amount, 2) : 'N/A' ?>
                                            </td>
                                            <td><?= date('Y-m-d H:i', $customer->created_at) ?></td>
                                            <td>
                                                <?php 
                                                $class = $customer->status == 1 ? 'badge-success' : 'badge-danger';
                                                $text = $customer->status == 1 ? 'Active' : 'Inactive';
                                                ?>
                                                <span class="badge <?= $class ?>"><?= $text ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

