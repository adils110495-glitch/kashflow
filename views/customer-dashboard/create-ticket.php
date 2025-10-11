<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\ActiveForm;
use app\models\Ticket;

$this->title = 'Create Support Ticket';
$this->params['breadcrumbs'][] = ['label' => 'Support Tickets', 'url' => ['tickets']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="customer-dashboard-create-ticket">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <i class="feather icon-plus-circle me-2"></i>
                                Create New Support Ticket
                            </h5>
                        </div>
                        <div class="col-auto">
                            <a href="<?= Url::to(['tickets']) ?>" class="btn btn-outline-secondary">
                                <i class="feather icon-arrow-left me-1"></i>
                                Back to Tickets
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="feather icon-info me-2"></i>
                        <strong>Need Help?</strong> Please provide as much detail as possible about your issue or complaint. This will help us assist you more effectively.
                    </div>

                    <?php $form = ActiveForm::begin([
                        'id' => 'create-ticket-form',
                        'options' => ['class' => 'needs-validation'],
                        'fieldConfig' => [
                            'template' => '<div class="mb-3">{label}{input}{error}</div>',
                            'labelOptions' => ['class' => 'form-label fw-bold'],
                            'inputOptions' => ['class' => 'form-control'],
                            'errorOptions' => ['class' => 'invalid-feedback']
                        ]
                    ]); ?>

                    <div class="row">
                        <div class="col-md-8">
                            <?= $form->field($model, 'subject')->textInput([
                                'placeholder' => 'Brief description of your issue',
                                'maxlength' => 255,
                                'required' => true
                            ])->label('Subject *') ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'priority')->dropDownList([
                                Ticket::PRIORITY_LOW => 'Low',
                                Ticket::PRIORITY_MEDIUM => 'Medium',
                                Ticket::PRIORITY_HIGH => 'High',
                                Ticket::PRIORITY_URGENT => 'Urgent'
                            ], [
                                'class' => 'form-select',
                                'prompt' => 'Select Priority'
                            ])->label('Priority *') ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'description')->textarea([
                        'rows' => 8,
                        'placeholder' => 'Please provide detailed information about your issue, complaint, or question. Include any relevant details that might help us understand and resolve your concern.',
                        'required' => true
                    ])->label('Description *') ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="<?= Url::to(['tickets']) ?>" class="btn btn-outline-secondary me-md-2">
                                    <i class="feather icon-x me-1"></i>
                                    Cancel
                                </a>
                                <?= Html::submitButton('<i class="feather icon-send me-1"></i> Create Ticket', [
                                    'class' => 'btn btn-primary',
                                    'name' => 'create-ticket-button'
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="feather icon-help-circle me-2"></i>
                        What to include in your ticket?
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">For Technical Issues:</h6>
                            <ul class="list-unstyled">
                                <li><i class="feather icon-check text-success me-2"></i> Steps to reproduce the issue</li>
                                <li><i class="feather icon-check text-success me-2"></i> Error messages (if any)</li>
                                <li><i class="feather icon-check text-success me-2"></i> Browser and device information</li>
                                <li><i class="feather icon-check text-success me-2"></i> Screenshots (if applicable)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">For Account Issues:</h6>
                            <ul class="list-unstyled">
                                <li><i class="feather icon-check text-success me-2"></i> Account details (username/email)</li>
                                <li><i class="feather icon-check text-success me-2"></i> Specific problem description</li>
                                <li><i class="feather icon-check text-success me-2"></i> When the issue started</li>
                                <li><i class="feather icon-check text-success me-2"></i> Any relevant transaction details</li>
                            </ul>
                        </div>
                    </div>
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

.form-label {
    color: #495057;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.text-primary {
    color: #007bff !important;
}

.text-success {
    color: #28a745 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('create-ticket-form');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }

    // Character counter for subject
    const subjectInput = document.querySelector('input[name="Ticket[subject]"]');
    if (subjectInput) {
        const maxLength = 255;
        const counter = document.createElement('small');
        counter.className = 'text-muted';
        counter.style.float = 'right';
        subjectInput.parentNode.appendChild(counter);
        
        function updateCounter() {
            const remaining = maxLength - subjectInput.value.length;
            counter.textContent = `${remaining} characters remaining`;
            if (remaining < 50) {
                counter.className = 'text-warning';
            } else {
                counter.className = 'text-muted';
            }
        }
        
        subjectInput.addEventListener('input', updateCounter);
        updateCounter();
    }
});
</script>
