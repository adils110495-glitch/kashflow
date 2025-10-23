<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user \dektrium\user\models\User */
/* @var $customer \app\models\Customer|null */
/* @var $data array */

$this->title = 'Withdrawal Request Approved';
?>

<h2>Great News! Your Withdrawal Has Been Approved</h2>

<p>Dear <?= Html::encode($customer ? $customer->name : $user->username) ?>,</p>

<p>We're pleased to inform you that your withdrawal request has been approved and processed successfully.</p>

<div class="success-box">
    <h3>Withdrawal Details:</h3>
    <ul>
        <li><strong>Amount:</strong> <?= Html::encode($data['amount'] ?? 'N/A') ?></li>
        <li><strong>Method:</strong> <?= Html::encode($data['method'] ?? 'N/A') ?></li>
        <li><strong>Request Date:</strong> <?= Html::encode($data['request_date'] ?? 'N/A') ?></li>
        <li><strong>Approval Date:</strong> <?= Html::encode($data['approval_date'] ?? date('Y-m-d H:i:s')) ?></li>
        <li><strong>Transaction ID:</strong> <?= Html::encode($data['transaction_id'] ?? 'N/A') ?></li>
    </ul>
</div>

<?php if (!empty($data['account_details'])): ?>
<div class="info-box">
    <h3>Account Details:</h3>
    <p><?= Html::encode($data['account_details']) ?></p>
</div>
<?php endif; ?>

<p>The funds have been transferred to your specified account. Please allow 1-3 business days for the transaction to reflect in your account, depending on your bank's processing time.</p>

<div class="info-box">
    <h3>Important Notes:</h3>
    <ul>
        <li>Keep this email as proof of your transaction</li>
        <li>Contact your bank if you don't see the funds within 3 business days</li>
        <li>Always verify account details before making withdrawal requests</li>
    </ul>
</div>

<p>
    <?= Html::a('View Transaction History', Url::to(['/customer-dashboard/withdrawal'], true), [
        'class' => 'button',
        'style' => 'color: white; text-decoration: none;'
    ]) ?>
</p>

<p>If you have any questions about this transaction, please don't hesitate to contact our support team.</p>

<p>Thank you for choosing KashFlow!</p>

<p>Best regards,<br>
<strong>The KashFlow Team</strong></p>
