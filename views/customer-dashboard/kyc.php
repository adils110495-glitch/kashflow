<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\UploadedFile;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'KYC Profile';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['dashboard']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="customer-kyc">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-id-card"></i> Know Your Customer (KYC) Profile
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?= Yii::$app->session->getFlash('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?= Yii::$app->session->getFlash('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin([
                        'options' => ['enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate' => true]
                    ]); ?>

                    <!-- Required Documents Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-id-card"></i> Required Documents (Mandatory)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'aadhar_number')->textInput([
                                            'maxlength' => 12,
                                            'placeholder' => 'Enter 12-digit Aadhar number',
                                            'class' => 'form-control',
                                            'pattern' => '[0-9]{12}',
                                            'title' => 'Please enter a valid 12-digit Aadhar number'
                                        ])->label('Aadhar Card Number') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-id-card"></i> 
                                            Enter your 12-digit Aadhar card number.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'pan_number')->textInput([
                                            'maxlength' => 10,
                                            'placeholder' => 'Enter PAN number (e.g., ABCDE1234F)',
                                            'class' => 'form-control',
                                            'style' => 'text-transform: uppercase;',
                                            'title' => 'Please enter a valid PAN number'
                                        ])->label('PAN Card Number') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-credit-card"></i> 
                                            Enter your PAN card number (10 characters).
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'aadhar_card_image')->fileInput([
                                            'accept' => 'image/*',
                                            'class' => 'form-control-file'
                                        ])->label('Aadhar Card Image') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-upload"></i> 
                                            Upload a clear image of your Aadhar card. Supported formats: JPG, PNG, GIF (Max: 2MB)
                                        </small>
                                        
                                        <?php if ($model->aadhar_card_image && file_exists(Yii::getAlias('@webroot') . '/uploads/kyc-documents/' . $model->aadhar_card_image)): ?>
                                            <div class="mt-3">
                                                <label class="form-label">Current Aadhar Card:</label>
                                                <div class="text-center">
                                                    <img src="<?= Url::to('@web/uploads/kyc-documents/' . $model->aadhar_card_image) ?>" 
                                                         alt="Aadhar Card" 
                                                         class="img-thumbnail" 
                                                         style="max-width: 200px; max-height: 200px;">
                                                    <div class="mt-2">
                                                        <a href="<?= Url::to('@web/uploads/kyc-documents/' . $model->aadhar_card_image) ?>" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-external-link-alt"></i> View Full Size
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'pan_card_image')->fileInput([
                                            'accept' => 'image/*',
                                            'class' => 'form-control-file'
                                        ])->label('PAN Card Image') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-upload"></i> 
                                            Upload a clear image of your PAN card. Supported formats: JPG, PNG, GIF (Max: 2MB)
                                        </small>
                                        
                                        <?php if ($model->pan_card_image && file_exists(Yii::getAlias('@webroot') . '/uploads/kyc-documents/' . $model->pan_card_image)): ?>
                                            <div class="mt-3">
                                                <label class="form-label">Current PAN Card:</label>
                                                <div class="text-center">
                                                    <img src="<?= Url::to('@web/uploads/kyc-documents/' . $model->pan_card_image) ?>" 
                                                         alt="PAN Card" 
                                                         class="img-thumbnail" 
                                                         style="max-width: 200px; max-height: 200px;">
                                                    <div class="mt-2">
                                                        <a href="<?= Url::to('@web/uploads/kyc-documents/' . $model->pan_card_image) ?>" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-external-link-alt"></i> View Full Size
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Currency Preference Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-coins"></i> Currency Preference (Optional)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'currency_id')->dropDownList(
                                            \app\models\Customer::getCurrencyOptionsForCustomer(),
                                            [
                                                'prompt' => 'Select preferred currency',
                                                'class' => 'form-control'
                                            ]
                                        )->label('Preferred Currency') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-coins"></i> 
                                            Choose your preferred currency for transactions and displays.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Current Currency</label>
                                        <div class="form-control-plaintext">
                                            <?php if ($model->currency): ?>
                                                <span class="badge badge-info">
                                                    <i class="fas fa-coins"></i>
                                                    <?= Html::encode($model->currency->getDisplayName()) ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <?= Html::encode($model->currency->getExchangeRateInfo()) ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-rupee-sign"></i>
                                                    INR - Indian Rupee (₹) - Base Currency
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> Currency preference affects how amounts are displayed throughout the system. 
                                All transactions will be converted to your preferred currency based on current exchange rates.
                            </div>
                        </div>
                    </div>

                    <!-- Bank Account Details Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-university"></i> Bank Account Details (Optional)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'bank_account_number')->textInput([
                                            'maxlength' => 20,
                                            'placeholder' => 'Enter bank account number',
                                            'class' => 'form-control',
                                            'pattern' => '[0-9]{9,18}',
                                            'title' => 'Please enter a valid bank account number (9-18 digits)'
                                        ])->label('Bank Account Number') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-credit-card"></i> 
                                            Enter your bank account number (9-18 digits).
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'bank_account_holder_name')->textInput([
                                            'maxlength' => 255,
                                            'placeholder' => 'Enter account holder name',
                                            'class' => 'form-control'
                                        ])->label('Account Holder Name') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-user"></i> 
                                            Name as it appears on your bank account.
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'bank_name')->textInput([
                                            'maxlength' => 255,
                                            'placeholder' => 'Enter bank name (e.g., State Bank of India)',
                                            'class' => 'form-control'
                                        ])->label('Bank Name') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-university"></i> 
                                            Name of your bank.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'bank_ifsc_code')->textInput([
                                            'maxlength' => 11,
                                            'placeholder' => 'Enter IFSC code (e.g., SBIN0001234)',
                                            'class' => 'form-control',
                                            'style' => 'text-transform: uppercase;',
                                            'title' => 'Please enter a valid IFSC code'
                                        ])->label('IFSC Code') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-code"></i> 
                                            IFSC code of your bank branch (11 characters).
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'bank_branch_name')->textInput([
                                            'maxlength' => 255,
                                            'placeholder' => 'Enter branch name',
                                            'class' => 'form-control'
                                        ])->label('Branch Name') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-map-marker-alt"></i> 
                                            Name of your bank branch.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'bank_account_type')->dropDownList(
                                            \app\models\Customer::getBankAccountTypeOptions(),
                                            [
                                                'prompt' => 'Select account type',
                                                'class' => 'form-control'
                                            ]
                                        )->label('Account Type') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-list"></i> 
                                            Type of your bank account.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Payment Methods Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-plus-circle"></i> Additional Payment Methods (Optional)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'crypto_wallet_address')->textInput([
                                            'maxlength' => true,
                                            'placeholder' => 'Enter your crypto wallet address (Bitcoin, Ethereum, etc.)',
                                            'class' => 'form-control'
                                        ])->label('Crypto Wallet Address') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> 
                                            Provide your cryptocurrency wallet address for digital currency transactions.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'upi_id')->textInput([
                                            'maxlength' => true,
                                            'placeholder' => 'Enter your UPI ID (e.g., username@paytm, username@phonepe)',
                                            'class' => 'form-control'
                                        ])->label('UPI ID') ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-mobile-alt"></i> 
                                            Your UPI ID for instant payments and transfers.
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?= $form->field($model, 'qr_code_image')->fileInput([
                                    'accept' => 'image/*',
                                    'class' => 'form-control-file'
                                ])->label('QR Code Image') ?>
                                <small class="form-text text-muted">
                                    <i class="fas fa-qrcode"></i> 
                                    Upload a QR code image for easy payment scanning. Supported formats: JPG, PNG, GIF (Max: 2MB)
                                </small>
                                
                                <?php if ($model->qr_code_image && file_exists(Yii::getAlias('@webroot') . '/uploads/qr-codes/' . $model->qr_code_image)): ?>
                                    <div class="mt-3">
                                        <label class="form-label">Current QR Code:</label>
                                        <div class="text-center">
                                            <img src="<?= Url::to('@web/uploads/qr-codes/' . $model->qr_code_image) ?>" 
                                                 alt="QR Code" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px; max-height: 200px;">
                                            <div class="mt-2">
                                                <a href="<?= Url::to('@web/uploads/qr-codes/' . $model->qr_code_image) ?>" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt"></i> View Full Size
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <?= Html::checkbox('kyc_terms', false, [
                                'class' => 'form-check-input',
                                'required' => true,
                                'id' => 'kyc_terms'
                            ]) ?>
                            <?= Html::label('I confirm that the information provided is accurate and I agree to the KYC verification process.', 'kyc_terms', ['class' => 'form-check-label']) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton('Update KYC Profile', [
                            'class' => 'btn btn-primary btn-lg',
                            'data-confirm' => 'Are you sure you want to update your KYC profile?'
                        ]) ?>
                        
                        <?= Html::a('Cancel', ['dashboard'], [
                            'class' => 'btn btn-secondary btn-lg ml-2'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> KYC Status
                    </h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <?= $model->getKycStatusBadge() ?>
                    </div>
                    
                    <div class="kyc-info">
                        <div class="info-item">
                            <strong>Status:</strong> <?= Html::encode($model->getKycStatusText()) ?>
                        </div>
                        
                        <?php if ($model->kyc_verified_at): ?>
                            <div class="info-item">
                                <strong>Verified On:</strong> <?= Html::encode($model->getFormattedKycVerifiedAt()) ?>
                            </div>
                            
                            <?php if ($model->kycVerifiedBy): ?>
                                <div class="info-item">
                                    <strong>Verified By:</strong> <?= Html::encode($model->kycVerifiedBy->username) ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Document Completion Status -->
                    <div class="mt-4">
                        <h6 class="text-muted mb-3">Document Status:</h6>
                        <?php $completionStatus = $model->getKycCompletionStatus(); ?>
                        
                        <div class="document-status">
                            <div class="status-item <?= $completionStatus['aadhar_number'] ? 'completed' : 'pending' ?>">
                                <i class="fas <?= $completionStatus['aadhar_number'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' ?>"></i>
                                <span>Aadhar Number</span>
                            </div>
                            
                            <div class="status-item <?= $completionStatus['aadhar_card_image'] ? 'completed' : 'pending' ?>">
                                <i class="fas <?= $completionStatus['aadhar_card_image'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' ?>"></i>
                                <span>Aadhar Card Image</span>
                            </div>
                            
                            <div class="status-item <?= $completionStatus['pan_number'] ? 'completed' : 'pending' ?>">
                                <i class="fas <?= $completionStatus['pan_number'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' ?>"></i>
                                <span>PAN Number</span>
                            </div>
                            
                            <div class="status-item <?= $completionStatus['pan_card_image'] ? 'completed' : 'pending' ?>">
                                <i class="fas <?= $completionStatus['pan_card_image'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' ?>"></i>
                                <span>PAN Card Image</span>
                            </div>
                        </div>
                        
                        <?php if (!$completionStatus['all_required_docs']): ?>
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Action Required:</strong> Please upload both Aadhar and PAN card documents to complete your KYC verification.
                            </div>
                        <?php endif; ?>
                        
                        <!-- Bank Account Status -->
                        <div class="mt-4">
                            <h6 class="text-muted mb-3">Bank Account Status:</h6>
                            <?php $bankStatus = $model->getBankAccountCompletionStatus(); ?>
                            
                            <div class="document-status">
                                <div class="status-item <?= $bankStatus['bank_account_number'] ? 'completed' : 'pending' ?>">
                                    <i class="fas <?= $bankStatus['bank_account_number'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' ?>"></i>
                                    <span>Account Number</span>
                                </div>
                                
                                <div class="status-item <?= $bankStatus['bank_account_holder_name'] ? 'completed' : 'pending' ?>">
                                    <i class="fas <?= $bankStatus['bank_account_holder_name'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' ?>"></i>
                                    <span>Account Holder Name</span>
                                </div>
                                
                                <div class="status-item <?= $bankStatus['bank_name'] ? 'completed' : 'pending' ?>">
                                    <i class="fas <?= $bankStatus['bank_name'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' ?>"></i>
                                    <span>Bank Name</span>
                                </div>
                                
                                <div class="status-item <?= $bankStatus['bank_ifsc_code'] ? 'completed' : 'pending' ?>">
                                    <i class="fas <?= $bankStatus['bank_ifsc_code'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' ?>"></i>
                                    <span>IFSC Code</span>
                                </div>
                                
                                <div class="status-item <?= $bankStatus['bank_branch_name'] ? 'completed' : 'pending' ?>">
                                    <i class="fas <?= $bankStatus['bank_branch_name'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' ?>"></i>
                                    <span>Branch Name</span>
                                </div>
                                
                                <div class="status-item <?= $bankStatus['bank_account_type'] ? 'completed' : 'pending' ?>">
                                    <i class="fas <?= $bankStatus['bank_account_type'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' ?>"></i>
                                    <span>Account Type</span>
                                </div>
                            </div>
                            
                            <?php if ($bankStatus['all_bank_details']): ?>
                                <div class="alert alert-success mt-3">
                                    <i class="fas fa-check-circle"></i>
                                    <strong>Complete:</strong> All bank account details are provided.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Optional:</strong> Bank account details are optional but recommended for faster withdrawals.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt"></i> KYC Benefits
                    </h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i> 
                            Higher withdrawal limits
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i> 
                            Faster transaction processing
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i> 
                            Enhanced account security
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i> 
                            Priority customer support
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i> 
                            Access to premium features
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-question-circle"></i> Need Help?
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        If you need assistance with your KYC verification, please contact our support team.
                    </p>
                    <a href="<?= Url::to(['/customer-dashboard/support']) ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-headset"></i> Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kyc-info .info-item {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.kyc-info .info-item:last-child {
    border-bottom: none;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-group {
    margin-bottom: 1.5rem;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
}

.img-thumbnail {
    border: 2px solid #dee2e6;
    border-radius: 8px;
}

.list-unstyled li {
    padding: 4px 0;
}

.text-success {
    color: #28a745 !important;
}

.document-status {
    margin-bottom: 1rem;
}

.status-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.status-item:last-child {
    border-bottom: none;
}

.status-item i {
    margin-right: 10px;
    width: 20px;
}

.status-item.completed span {
    color: #28a745;
    font-weight: 500;
}

.status-item.pending span {
    color: #dc3545;
    font-weight: 500;
}

.card-header.bg-primary {
    background-color: #007bff !important;
}

.card-header.bg-info {
    background-color: #17a2b8 !important;
}

.card-header.bg-success {
    background-color: #28a745 !important;
}

.card-header.bg-warning {
    background-color: #ffc107 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format PAN number to uppercase
    const panInput = document.querySelector('input[name="Customer[pan_number]"]');
    if (panInput) {
        panInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        // Validate PAN format on blur
        panInput.addEventListener('blur', function() {
            const panValue = this.value.trim();
            if (panValue && !/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(panValue)) {
                this.setCustomValidity('Please enter a valid PAN number (e.g., ABCDE1234F)');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Validate Aadhar number
    const aadharInput = document.querySelector('input[name="Customer[aadhar_number]"]');
    if (aadharInput) {
        aadharInput.addEventListener('blur', function() {
            const aadharValue = this.value.trim();
            if (aadharValue && !/^\d{12}$/.test(aadharValue)) {
                this.setCustomValidity('Please enter a valid 12-digit Aadhar number');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Auto-format IFSC code to uppercase
    const ifscInput = document.querySelector('input[name="Customer[bank_ifsc_code]"]');
    if (ifscInput) {
        ifscInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        // Validate IFSC format on blur
        ifscInput.addEventListener('blur', function() {
            const ifscValue = this.value.trim();
            if (ifscValue && !/^[A-Z]{4}[0-9]{7}$/.test(ifscValue)) {
                this.setCustomValidity('Please enter a valid IFSC code (e.g., SBIN0001234)');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Validate bank account number
    const bankAccountInput = document.querySelector('input[name="Customer[bank_account_number]"]');
    if (bankAccountInput) {
        bankAccountInput.addEventListener('blur', function() {
            const accountValue = this.value.trim();
            if (accountValue && !/^\d{9,18}$/.test(accountValue)) {
                this.setCustomValidity('Please enter a valid bank account number (9-18 digits)');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // File upload validation
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Check file size (2MB limit)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    return;
                }
                
                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please upload only JPG, PNG, or GIF images');
                    this.value = '';
                    return;
                }
            }
        });
    });
});
</script>
