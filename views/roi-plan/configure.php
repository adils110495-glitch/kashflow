<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/** @var array $roiData */
/** @var array $referralExtraBonusData */
/** @var array $referralBonusData */

$this->title = 'ROI Plan Configuration';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="roi-plan-configure">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="feather icon-settings me-2"></i>
                ROI Plan Configuration
            </h5>
        </div>
        <div class="card-body">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" id="roiTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="roi-tab" data-bs-toggle="tab" data-bs-target="#roi" type="button" role="tab" aria-controls="roi" aria-selected="true">
                        <i class="feather icon-trending-up me-1"></i> ROI Plan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="referral-extra-bonus-tab" data-bs-toggle="tab" data-bs-target="#referral-extra-bonus" type="button" role="tab" aria-controls="referral-extra-bonus" aria-selected="false">
                        <i class="feather icon-users me-1"></i> Referral Extra Bonus
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="referral-bonus-tab" data-bs-toggle="tab" data-bs-target="#referral-bonus" type="button" role="tab" aria-controls="referral-bonus" aria-selected="false">
                        <i class="feather icon-award me-1"></i> Referral Bonus
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="roiTabsContent">
                <!-- ROI Tab -->
                <div class="tab-pane fade show active" id="roi" role="tabpanel" aria-labelledby="roi-tab">
                    <div class="mt-4">
                        <?php $form = ActiveForm::begin([
                            'id' => 'roi-form',
                            'action' => ['configure'],
                            'method' => 'post'
                        ]); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <?= Html::label('ROI Rate (%)', 'roi_rate', ['class' => 'form-label']) ?>
                                <?= Html::textInput('roi[rate]', $roiData['rate'], [
                                    'class' => 'form-control',
                                    'id' => 'roi_rate',
                                    'placeholder' => 'Enter ROI rate percentage'
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= Html::label('Frequency', 'roi_frequency', ['class' => 'form-label']) ?>
                                <?= Html::dropDownList('roi[frequency]', $roiData['frequency'], 
                                    \app\models\RoiPlan::buildFrequency(),
                                    [
                                        'class' => 'form-control',
                                        'id' => 'roi_frequency',
                                        'prompt' => 'Select Frequency'
                                    ]
                                ) ?>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <?= Html::label('Tenure', 'roi_tenure', ['class' => 'form-label']) ?>
                                <?= Html::dropDownList('roi[tenure]', $roiData['tenure'], 
                                    \app\models\RoiPlan::buildTenure(),
                                    [
                                        'class' => 'form-control',
                                        'id' => 'roi_tenure',
                                        'prompt' => 'Select Tenure'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-md-6">
                                <?= Html::label('Status', 'roi_status', ['class' => 'form-label']) ?>
                                <?= Html::dropDownList('roi[status]', $roiData['status'], 
                                    \app\models\RoiPlan::buildStatus(),
                                    [
                                        'class' => 'form-control',
                                        'id' => 'roi_status',
                                        'prompt' => 'Select Status'
                                    ]
                                ) ?>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <?= Html::submitButton('<i class="feather icon-save"></i> Save ROI Plan', [
                                'class' => 'btn btn-success',
                                'name' => 'submit-roi'
                            ]) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>

                <!-- Referral Extra Bonus Tab -->
                <div class="tab-pane fade" id="referral-extra-bonus" role="tabpanel" aria-labelledby="referral-extra-bonus-tab">
                    <div class="mt-4">
                        <?php $form = ActiveForm::begin([
                            'id' => 'referral-extra-bonus-form',
                            'action' => ['configure'],
                            'method' => 'post'
                        ]); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <?= Html::label('Number of Referrals', 'referral_extra_bonus_no_of_referral', ['class' => 'form-label']) ?>
                                <?= Html::textInput('referral_extra_bonus[no_of_referral]', $referralExtraBonusData['no_of_referral'], [
                                    'class' => 'form-control',
                                    'id' => 'referral_extra_bonus_no_of_referral',
                                    'placeholder' => 'Enter number of referrals required'
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= Html::label('Referral Extra Bonus Rate (%)', 'referral_extra_bonus_rate', ['class' => 'form-label']) ?>
                                <?= Html::textInput('referral_extra_bonus[rate]', $referralExtraBonusData['rate'], [
                                    'class' => 'form-control',
                                    'id' => 'referral_extra_bonus_rate',
                                    'placeholder' => 'Enter referral rate percentage'
                                ]) ?>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <?= Html::label('Frequency', 'referral_extra_bonus_frequency', ['class' => 'form-label']) ?>
                                <?= Html::dropDownList('referral_extra_bonus[frequency]', $referralExtraBonusData['frequency'], 
                                    \app\models\RoiPlan::buildFrequency(),
                                    [
                                        'class' => 'form-control',
                                        'id' => 'referral_extra_bonus_frequency',
                                        'prompt' => 'Select Frequency'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-md-6">
                                <?= Html::label('Tenure', 'referral_extra_bonus_tenure', ['class' => 'form-label']) ?>
                                <?= Html::dropDownList('referral_extra_bonus[tenure]', $referralExtraBonusData['tenure'], 
                                    \app\models\RoiPlan::buildTenure(),
                                    [
                                        'class' => 'form-control',
                                        'id' => 'referral_extra_bonus_tenure',
                                        'prompt' => 'Select Tenure'
                                    ]
                                ) ?>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <?= Html::submitButton('<i class="feather icon-save"></i> Save Referral Extra Bonus', [
                                'class' => 'btn btn-primary',
                                'name' => 'submit-referral-extra-bonus'
                            ]) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>

                <!-- Referral Bonus Tab (single rate) -->
                <div class="tab-pane fade" id="referral-bonus" role="tabpanel" aria-labelledby="referral-bonus-tab">
                    <div class="mt-4">
                        <?php $form = ActiveForm::begin([
                            'id' => 'referral-bonus-form',
                            'action' => ['configure'],
                            'method' => 'post'
                        ]); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <?= Html::label('Referral Bonus Rate (%)', 'referral_bonus_rate', ['class' => 'form-label']) ?>
                                <?= Html::textInput('referral_bonus[rate]', $referralBonusData['rate'], [
                                    'class' => 'form-control',
                                    'id' => 'referral_bonus_rate',
                                    'placeholder' => 'Enter referral bonus rate percentage'
                                ]) ?>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <?= Html::submitButton('<i class="feather icon-save"></i> Save Referral Bonus', [
                                'class' => 'btn btn-info',
                                'name' => 'submit-referral-bonus'
                            ]) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.tab-content {
    border: 1px solid #dee2e6;
    border-top: none;
    padding: 1rem;
    background-color: #fff;
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap tab functionality
    var triggerTabList = [].slice.call(document.querySelectorAll('#roiTabs button'));
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });

});
</script>
