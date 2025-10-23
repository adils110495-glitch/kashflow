<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user \dektrium\user\models\User */
/* @var $customer \app\models\Customer|null */
/* @var $data array */

$this->title = 'Welcome to KashFlow!';
?>

<h2>Welcome to KashFlow, <?= Html::encode($customer ? $customer->name : $user->username) ?>!</h2>

<p>We're excited to have you join our community of successful investors and entrepreneurs. Your account has been successfully created and you're ready to start your financial journey with us.</p>

<div class="info-box">
    <h3>Your Account Details:</h3>
    <ul>
        <li><strong>Username:</strong> <?= Html::encode($user->username) ?></li>
        <li><strong>Email:</strong> <?= Html::encode($user->email) ?></li>
        <?php if ($customer): ?>
        <li><strong>Name:</strong> <?= Html::encode($customer->name) ?></li>
        <li><strong>Referral Code:</strong> <?= Html::encode($customer->referral_code ?? 'Not generated yet') ?></li>
        <?php endif; ?>
    </ul>
</div>

<h3>What's Next?</h3>
<p>Here are some important steps to get you started:</p>

<ol>
    <li><strong>Complete Your Profile:</strong> Add your personal information and preferences</li>
    <li><strong>KYC Verification:</strong> Complete your Know Your Customer verification for secure transactions</li>
    <li><strong>Choose Your Package:</strong> Select an investment package that suits your goals</li>
    <li><strong>Start Earning:</strong> Begin your journey to financial growth</li>
</ol>

<div class="success-box">
    <h3>🎉 Special Welcome Bonus!</h3>
    <p>As a new member, you're eligible for our welcome bonus. Log in to your dashboard to claim it!</p>
</div>

<p>
    <?= Html::a('Access Your Dashboard', Url::to(['/customer-dashboard/index'], true), [
        'class' => 'button',
        'style' => 'color: white; text-decoration: none;'
    ]) ?>
</p>

<h3>Need Help?</h3>
<p>Our support team is here to help you every step of the way. Don't hesitate to reach out if you have any questions.</p>

<p>Thank you for choosing KashFlow. We look forward to being part of your financial success story!</p>

<p>Best regards,<br>
<strong>The KashFlow Team</strong></p>
