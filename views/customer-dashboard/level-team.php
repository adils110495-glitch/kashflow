<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $customer app\models\Customer */
/* @var $levelTeam array */
/* @var $packageStats array */
/* @var $usernameFilter string */
/* @var $fromDate string */
/* @var $toDate string */
/* @var $level string */
/* @var $status string */

$this->title = 'Level Team';
$this->params['breadcrumbs'][] = $this->title;

/**
 * Recursive function to display level team hierarchy
 */
function displayLevelTeam($levelData, $parentLevel = 0) {
    if (empty($levelData)) {
        return;
    }
    
    $level = $levelData['level'];
    $customers = $levelData['customers'];
    $count = $levelData['count'];
    $children = $levelData['children'];
    
    echo '<div class="level-section mb-4" data-level="' . $level . '">';
    echo '<div class="level-header">';
    echo '<h4 class="level-title">';
    echo '<i class="fas fa-layer-group"></i> Level ' . $level;
    echo '<span class="badge badge-primary ml-2">' . $count . ' members</span>';
    echo '</h4>';
    echo '</div>';
    
    if (!empty($customers)) {
        echo '<div class="table-responsive mb-3">';
        echo '<table class="table table-sm table-striped">';
        echo '<thead class="thead-light">';
        echo '<tr>';
        echo '<th>#</th>';
        echo '<th>Username</th>';
        echo '<th>Name</th>';
        echo '<th>Email</th>';
        echo '<th>Joined Date</th>';
        echo '<th>Status</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($customers as $index => $member) {
            echo '<tr>';
            echo '<td>' . ($index + 1) . '</td>';
            echo '<td><strong>' . Html::encode($member->user ? $member->user->username : 'N/A') . '</strong></td>';
            echo '<td>' . Html::encode($member->name) . '</td>';
            echo '<td>' . Html::encode($member->email) . '</td>';
            echo '<td>' . date('Y-m-d H:i', $member->created_at) . '</td>';
            echo '<td>';
            echo '<span class="badge badge-' . ($member->status == 1 ? 'success' : 'secondary') . '">';
            echo $member->getStatusText();
            echo '</span>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
    
    // Display children levels
    if (!empty($children)) {
        echo '<div class="children-levels ml-4">';
        foreach ($children as $username => $childLevel) {
            displayLevelTeam($childLevel, $level);
        }
        echo '</div>';
    }
    
    echo '</div>';
}

/**
 * Function to calculate total counts for all levels
 */
function calculateLevelCounts($levelData, &$levelCounts = []) {
    if (empty($levelData)) {
        return $levelCounts;
    }
    
    $level = $levelData['level'];
    $count = $levelData['count'];
    $children = $levelData['children'];
    
    if (!isset($levelCounts[$level])) {
        $levelCounts[$level] = 0;
    }
    $levelCounts[$level] += $count;
    
    // Process children
    if (!empty($children)) {
        foreach ($children as $childLevel) {
            calculateLevelCounts($childLevel, $levelCounts);
        }
    }
    
    return $levelCounts;
}

$levelCounts = calculateLevelCounts($levelTeam);
?>

<div class="level-team-index">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sitemap"></i> Level Team Hierarchy
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
                        
                        <div class="col-md-2">
                            <?= Html::label('From Date', 'from_date', ['class' => 'form-label']) ?>
                            <?= Html::input('date', 'from_date', $fromDate, [
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                        
                        <div class="col-md-2">
                            <?= Html::label('To Date', 'to_date', ['class' => 'form-label']) ?>
                            <?= Html::input('date', 'to_date', $toDate, [
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                        
                        <div class="col-md-2">
                            <?= Html::label('Level', 'level', ['class' => 'form-label']) ?>
                            <?= Html::dropDownList('level', $level, \app\models\LevelPlan::getLevelOptions(), ['class' => 'form-select', 'prompt' => 'All Levels']) ?>
                        </div>
                        
                        <div class="col-md-2">
                            <?= Html::label('Status', 'status', ['class' => 'form-label']) ?>
                            <?= Html::dropDownList('status', $status, [
                                '1' => 'Active',
                                '0' => 'Inactive'
                            ], ['class' => 'form-select', 'prompt' => 'All Status']) ?>
                        </div>
                        
                        <div class="col-md-1">
                            <?= Html::label('&nbsp;', '', ['class' => 'form-label d-block']) ?>
                            <div class="d-flex flex-column gap-2">
                                <?= Html::submitButton('Filter', ['class' => 'btn btn-primary btn-sm']) ?>
                                <?= Html::a('Clear', Url::to(['level-team']), ['class' => 'btn btn-secondary btn-sm']) ?>
                            </div>
                        </div>
                        
                        <?php ActiveForm::end(); ?>
                    </div>

                    <!-- Level Statistics -->
                    <div class="level-stats mb-4">
                        <h5><i class="fas fa-chart-bar"></i> Level-wise Statistics</h5>
                        <div class="row">
                            <?php 
                            // Get all active levels from LevelPlan table
                            $activeLevels = \app\models\LevelPlan::getActiveLevels()->all();
                            $colors = ['primary', 'success', 'warning', 'danger', 'info', 'secondary', 'dark', 'light'];
                            ?>
                            <?php foreach ($activeLevels as $index => $levelPlan): ?>
                                <div class="col-md-2 col-sm-4 col-6 mb-3">
                                    <div class="info-box bg-<?= $colors[$index % count($colors)] ?>">
                                        <span class="info-box-icon"><i class="fas fa-layer-group"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Level <?= $levelPlan->level ?></span>
                                            <span class="info-box-number"><?= isset($levelCounts[$levelPlan->level]) ? $levelCounts[$levelPlan->level] : 0 ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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
                                        <span class="info-box-text">Free Package (Unpaid)</span>
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

                    <!-- Team Hierarchy -->
                    <div class="team-hierarchy">
                        <?php if (!empty($levelTeam)): ?>
                            <h5><i class="fas fa-sitemap"></i> Team Hierarchy</h5>
                            <?php displayLevelTeam($levelTeam); ?>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i>
                                No team members found in the hierarchy.
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
</div>

<style>
.filter-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}

.level-section {
    border-left: 4px solid #007bff;
    padding-left: 15px;
    margin-bottom: 20px;
}

/* Dynamic level colors */
.level-section[data-level="1"] { border-left-color: #007bff; }
.level-section[data-level="2"] { border-left-color: #28a745; }
.level-section[data-level="3"] { border-left-color: #ffc107; }
.level-section[data-level="4"] { border-left-color: #dc3545; }
.level-section[data-level="5"] { border-left-color: #6f42c1; }
.level-section[data-level="6"] { border-left-color: #fd7e14; }
.level-section[data-level="7"] { border-left-color: #20c997; }
.level-section[data-level="8"] { border-left-color: #e83e8c; }
.level-section[data-level="9"] { border-left-color: #6c757d; }
.level-section[data-level="10"] { border-left-color: #343a40; }
.level-section[data-level="11"] { border-left-color: #17a2b8; }
.level-section[data-level="12"] { border-left-color: #6610f2; }
.level-section[data-level="13"] { border-left-color: #e83e8c; }
.level-section[data-level="14"] { border-left-color: #fd7e14; }
.level-section[data-level="15"] { border-left-color: #20c997; }

.level-header {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
}

.level-title {
    margin: 0;
    color: #495057;
}

.children-levels {
    border-left: 2px dashed #dee2e6;
    padding-left: 20px;
}

.info-box {
    display: block;
    min-height: 70px;
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
    height: 70px;
    width: 70px;
    text-align: center;
    font-size: 30px;
    line-height: 70px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 70px;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 11px;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 16px;
}

.bg-primary {
    background-color: #007bff !important;
    color: #fff;
}

.bg-info {
    background-color: #17a2b8 !important;
    color: #fff;
}

.bg-secondary {
    background-color: #6c757d !important;
    color: #fff;
}

.team-hierarchy {
    max-height: 800px;
    overflow-y: auto;
}
</style>