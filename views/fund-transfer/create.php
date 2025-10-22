<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FundTransfer */
/* @var $customers app\models\Customer[] */

$this->title = 'Create Fund Transfer';
$this->params['breadcrumbs'][] = ['label' => 'Fund Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="fund-transfer-create">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Create New Fund Transfer</h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'from_customer_id')->dropDownList(
                                \yii\helpers\ArrayHelper::map($customers, 'id', function($customer) {
                                    return $customer->name . ' (' . ($customer->user ? $customer->user->username : 'N/A') . ')';
                                }),
                                ['prompt' => 'Select From Customer', 'class' => 'form-control']
                            )->label('From Customer <span class="text-danger">*</span>') ?>
                        </div>
                        
                        <div class="col-md-6">
                            <?= $form->field($model, 'to_customer_id')->dropDownList(
                                \yii\helpers\ArrayHelper::map($customers, 'id', function($customer) {
                                    return $customer->name . ' (' . ($customer->user ? $customer->user->username : 'N/A') . ')';
                                }),
                                ['prompt' => 'Select To Customer', 'class' => 'form-control']
                            )->label('To Customer <span class="text-danger">*</span>') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'amount')->textInput([
                                'type' => 'number',
                                'step' => '0.01',
                                'min' => '0.01',
                                'class' => 'form-control'
                            ])->label('Amount <span class="text-danger">*</span>') ?>
                        </div>
                        
                        <div class="col-md-6">
                            <?= $form->field($model, 'transfer_date')->textInput([
                                'type' => 'date',
                                'value' => date('Y-m-d'),
                                'class' => 'form-control'
                            ])->label('Transfer Date <span class="text-danger">*</span>') ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'comment')->textarea([
                        'rows' => 3,
                        'placeholder' => 'Enter transfer description or reason...',
                        'class' => 'form-control'
                    ])->label('Transfer Comment') ?>

                    <div class="form-group">
                        <?= Html::submitButton('<i class="feather icon-check"></i> Create Transfer', [
                            'class' => 'btn btn-success btn-lg'
                        ]) ?>
                        
                        <?= Html::a('<i class="feather icon-arrow-left"></i> Back to List', ['index'], [
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
                    <h5 class="card-title">Transfer Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="feather icon-info"></i>
                        <strong>Admin Transfer</strong><br>
                        <small>This transfer will be executed immediately upon creation. No approval is required.</small>
                    </div>
                    
                    <h6>Transfer Process:</h6>
                    <ol class="small">
                        <li>Debit amount from sender's account</li>
                        <li>Credit amount to receiver's account</li>
                        <li>Create ledger entries for both customers</li>
                        <li>Mark transfer as approved</li>
                    </ol>
                    
                    <h6>Requirements:</h6>
                    <ul class="small">
                        <li>Sender must have sufficient balance</li>
                        <li>Both customers must be active</li>
                        <li>Amount must be greater than $0.01</li>
                        <li>Cannot transfer to same customer</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical w-100" role="group">
                        <?= Html::a('<i class="feather icon-list"></i> View All Transfers', ['index'], [
                            'class' => 'btn btn-outline-primary'
                        ]) ?>
                        
                        <?= Html::a('<i class="feather icon-download"></i> Export CSV', ['export'], [
                            'class' => 'btn btn-outline-info'
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Prevent selecting same customer for both fields
    $('#fundtransfer-from_customer_id, #fundtransfer-to_customer_id').on('change', function() {
        const fromCustomer = $('#fundtransfer-from_customer_id').val();
        const toCustomer = $('#fundtransfer-to_customer_id').val();
        
        if (fromCustomer && toCustomer && fromCustomer === toCustomer) {
            alert('Cannot transfer to the same customer. Please select different customers.');
            $(this).val('');
        }
    });
    
    // Validate amount
    $('#fundtransfer-amount').on('input', function() {
        const amount = parseFloat($(this).val());
        if (amount <= 0) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>

<style>
.is-invalid {
    border-color: #dc3545;
}

.card-header h5 {
    margin-bottom: 0;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}
</style>
