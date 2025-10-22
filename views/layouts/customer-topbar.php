<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Customer;
use app\widgets\ActivityNotificationWidget;

/* @var $this yii\web\View */

// Get current customer data
$customer = Customer::find()
    ->where(['user_id' => Yii::$app->user->id])
    ->with(['currentPackage', 'user'])
    ->one();
?>

<header class="navbar pcoded-header navbar-expand-lg navbar-light">
    <div class="m-header">
        <a class="mobile-menu" id="mobile-collapse1" href="#!"><span></span></a>
        <a href="<?= Url::to(['/customer-dashboard/index']) ?>" class="b-brand">
            <div class="b-bg">
                <i class="feather icon-trending-up"></i>
            </div>
            <span class="b-title">KashFlow</span>
        </a>
    </div>
    <a class="mobile-menu" id="mobile-header" href="#!">
        <i class="feather icon-more-horizontal"></i>
    </a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li><a href="#!" class="full-screen" onclick="javascript:toggleFullScreen()"><i class="feather icon-maximize"></i></a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <!-- Notification -->
            <li class="nav-item">
                <div class="dropdown drp-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="feather icon-bell"></i>
                        <span class="badge">5</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-notification">
                        <div class="pro-head">
                            <span>Notifications</span>
                        </div>
                        <ul class="pro-body">
                            <li><a href="#" class="dropdown-item"><i class="feather icon-info"></i> System Update Available</a></li>
                            <li><a href="#" class="dropdown-item"><i class="feather icon-package"></i> New Package Features</a></li>
                            <li><a href="#" class="dropdown-item"><i class="feather icon-bell"></i> View All Notifications</a></li>
                        </ul>
                    </div>
                </div>
            </li>
            
            <!-- Activity Notifications -->
            <li class="nav-item">
                <?= ActivityNotificationWidget::widget([
                    'limit' => 10,
                    'showBadge' => true,
                    'containerClass' => 'activity-notification-topbar',
                    'autoRefresh' => true,
                    'refreshInterval' => 30000 // 30 seconds
                ]) ?>
            </li>

            <!-- Copy Icon -->
            <?php if ($customer): ?>
            <?php 
            $referralCode = $customer->user ? $customer->user->username : 'KF' . str_pad($customer->id, 6, '0', STR_PAD_LEFT);
            $registrationLink = Url::to(['/user/registration/register', 'ref' => $referralCode], true);
            ?>
            <li class="nav-item">
                <a href="#" id="copy-registration-link" data-link="<?= $registrationLink ?>" title="Copy registration link: <?= $registrationLink ?>">
                    <i class="fas fa-copy"></i>
                </a>
            </li>
            <?php endif; ?>

            <!-- WhatsApp Icon -->
            <?php if ($customer): ?>
            <li class="nav-item">
                <a href="#" id="whatsapp-share-link" data-link="<?= $registrationLink ?>" title="Share registration link on WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </li>
            <?php endif; ?>

            <!-- User Profile Dropdown -->
            <li class="nav-item">
                <div class="dropdown drp-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <div class="user-avatar">
                            <?php if ($customer): ?>
                            <div class="avatar-circle" style="width: 32px; height: 32px; background: #007bff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">
                                <?= strtoupper(substr($customer->name, 0, 1)) ?>
                            </div>
                            <?php else: ?>
                            <i class="feather icon-user"></i>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-notification">
                        <div class="pro-head">
                            <?php if ($customer): ?>
                            <img src="data:image/svg+xml;base64,<?= base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><rect width="40" height="40" fill="#007bff" rx="20"/><text x="20" y="26" text-anchor="middle" fill="white" font-family="Arial" font-size="16" font-weight="bold">' . strtoupper(substr($customer->name, 0, 1)) . '</text></svg>') ?>" class="img-radius" alt="User-Profile-Image">
                            <span><?= Html::encode($customer->name) ?></span>
                            <small><?= Html::encode($customer->email) ?></small>
                            <?php else: ?>
                            <span>Customer</span>
                            <?php endif; ?>
                        </div>
                        <ul class="pro-body">
                            <li><a href="<?= Url::to(['/customer-dashboard/profile']) ?>" class="dropdown-item"><i class="feather icon-user"></i> Profile</a></li>
                            <li><a href="<?= Url::to(['/customer-dashboard/kyc']) ?>" class="dropdown-item"><i class="feather icon-shield"></i> KYC Profile</a></li>
                            <li><a href="#" class="dropdown-item"><i class="feather icon-settings"></i> Settings</a></li>
                            <li><a href="#" class="dropdown-item"><i class="feather icon-package"></i> My Package</a></li>
                            <li><a href="#" class="dropdown-item"><i class="feather icon-credit-card"></i> Billing</a></li>
                            <li><a href="#" class="dropdown-item"><i class="feather icon-help-circle"></i> Support</a></li>
                            <li>
                                <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'dropdown-item-form']) ?>
                                <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left;">
                                    <i class="feather icon-log-out"></i> Logout
                                </button>
                                <?= Html::endForm() ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>

<!-- Copy and WhatsApp functionality is handled in custom.js -->

<style>
/* Copy and WhatsApp icons styling */
#copy-registration-link,
#whatsapp-share-link {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 40px !important;
    height: 40px !important;
    background: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    border-radius: 50% !important;
    color: white !important;
    text-decoration: none !important;
}

#copy-registration-link:hover,
#whatsapp-share-link:hover {
    background: rgba(255, 255, 255, 0.2) !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
}

#copy-registration-link i {
    color: #17a2b8 !important;
}

#whatsapp-share-link i {
    color: #25D366 !important;
}
</style>

<script>
// Copy and WhatsApp functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    
    // Copy functionality
    const copyBtn = document.getElementById('copy-registration-link');
    if (copyBtn) {
        copyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Copy button clicked');
            const link = this.getAttribute('data-link');
            console.log('Link to copy:', link);
            
            // Copy to clipboard
            navigator.clipboard.writeText(link).then(function() {
                alert('Copied!');
            }).catch(function() {
                alert('Copy failed');
            });
        });
    }
    
    // WhatsApp functionality
    const whatsappBtn = document.getElementById('whatsapp-share-link');
    if (whatsappBtn) {
        whatsappBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('WhatsApp button clicked');
            const link = this.getAttribute('data-link');
            const message = 'Join me on KashFlow! Use my referral link to register: ' + link;
            const whatsappUrl = 'https://wa.me/?text=' + encodeURIComponent(message);
            console.log('WhatsApp URL:', whatsappUrl);
            window.open(whatsappUrl, '_blank');
        });
    }
});
</script>
