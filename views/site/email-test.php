<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ContactForm */

$this->title = 'Email Test';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-email-test">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Send Single Email</h3>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['action' => ['/site/send-email']]); ?>

                    <?= $form->field($model, 'email')->textInput(['placeholder' => 'recipient@example.com']) ?>

                    <?= $form->field($model, 'subject')->textInput(['placeholder' => 'Email Subject']) ?>

                    <?= $form->field($model, 'body')->textarea(['rows' => 6, 'placeholder' => 'Email message content...']) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Send Email', ['class' => 'btn btn-primary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Send Bulk Email</h3>
                </div>
                <div class="card-body">
                    <?php $bulkForm = ActiveForm::begin(['action' => ['/site/send-bulk-email']]); ?>

                    <?= $bulkForm->field($model, 'email')->textarea(['rows' => 4, 'placeholder' => 'recipient1@example.com,recipient2@example.com,recipient3@example.com']) ?>

                    <?= $bulkForm->field($model, 'subject')->textInput(['placeholder' => 'Bulk Email Subject']) ?>

                    <?= $bulkForm->field($model, 'body')->textarea(['rows' => 6, 'placeholder' => 'Bulk email message content...']) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Send Bulk Email', ['class' => 'btn btn-success']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Send Notification Email</h3>
                </div>
                <div class="card-body">
                    <?php $notificationForm = ActiveForm::begin(['action' => ['/site/send-notification']]); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $notificationForm->field($model, 'email')->textInput(['name' => 'userId', 'placeholder' => 'User ID']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= Html::dropDownList('type', 'general', [
                                'welcome' => 'Welcome Email',
                                'withdrawal_approved' => 'Withdrawal Approved',
                                'withdrawal_rejected' => 'Withdrawal Rejected',
                                'kyc_approved' => 'KYC Approved',
                                'kyc_rejected' => 'KYC Rejected',
                                'package_upgrade' => 'Package Upgrade',
                                'general' => 'General Notification'
                            ], ['class' => 'form-control']) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Additional Data (JSON format)</label>
                        <textarea name="data" class="form-control" rows="4" placeholder='{"amount": "1000", "method": "UPI", "transaction_id": "TXN123456"}'></textarea>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton('Send Notification', ['class' => 'btn btn-info']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Email Usage Examples</h3>
                </div>
                <div class="card-body">
                    <h4>1. Send Single Email via AJAX:</h4>
                    <pre><code>$.post('/site/send-email', {
    to: 'user@example.com',
    subject: 'Test Email',
    message: 'This is a test email message'
});</code></pre>

                    <h4>2. Send Bulk Email:</h4>
                    <pre><code>$.post('/site/send-bulk-email', {
    recipients: ['user1@example.com', 'user2@example.com'],
    subject: 'Bulk Email Subject',
    message: 'Bulk email message content'
});</code></pre>

                    <h4>3. Send Notification Email:</h4>
                    <pre><code>$.post('/site/send-notification', {
    userId: 1,
    type: 'withdrawal_approved',
    data: {
        amount: '1000',
        method: 'UPI',
        transaction_id: 'TXN123456'
    }
});</code></pre>

                    <h4>4. Send Email from Controller:</h4>
                    <pre><code>// In any controller
Yii::$app->runAction('site/send-email', [
    'to' => 'user@example.com',
    'subject' => 'Test Subject',
    'message' => 'Test Message'
]);</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
