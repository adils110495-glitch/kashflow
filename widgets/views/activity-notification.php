<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\ActivityNotificationWidget;
use app\models\CustomerActivity;

/* @var $this yii\web\View */
/* @var $activities app\models\CustomerActivity[] */
/* @var $customer app\models\Customer */
/* @var $unreadCount int */
/* @var $showBadge bool */
/* @var $containerClass string */
/* @var $autoRefresh bool */
/* @var $refreshInterval int */

?>

<div class="<?= Html::encode($containerClass) ?>">
    <!-- Notification Bell -->
    <div class="activity-bell" title="View Activities">
        <i class="fas fa-bell"></i>
        <?php if ($showBadge && $unreadCount > 0): ?>
            <span class="activity-badge"><?= $unreadCount > 99 ? '99+' : $unreadCount ?></span>
        <?php endif; ?>
    </div>
    
    <!-- Notification Dropdown -->
    <div class="activity-dropdown">
        <!-- Header -->
        <div class="activity-header">
            <h6>
                <i class="fas fa-history"></i>
                Recent Activities
                <?php if ($unreadCount > 0): ?>
                    <span class="badge badge-primary ml-2"><?= $unreadCount ?></span>
                <?php endif; ?>
            </h6>
        </div>
        
        <!-- Activity List -->
        <div class="activity-list">
            <?php if (empty($activities)): ?>
                <div class="activity-empty">
                    <i class="fas fa-inbox fa-2x mb-2" style="color: #dee2e6;"></i>
                    <div>No recent activities</div>
                </div>
            <?php else: ?>
                <?php foreach ($activities as $activity): ?>
                    <div class="activity-item d-flex">
                        <!-- Activity Icon -->
                        <div class="activity-icon <?= ActivityNotificationWidget::getActivityIconClass($activity->activity_type) ?>">
                            <i class="<?= ActivityNotificationWidget::getActivityIcon($activity->activity_type) ?>"></i>
                        </div>
                        
                        <!-- Activity Content -->
                        <div class="activity-content">
                            <div class="activity-title">
                                <?= Html::encode($activity->getActivityTypeLabel()) ?>
                            </div>
                            
                            <?php if ($activity->activity_description): ?>
                                <div class="activity-description">
                                    <?= Html::encode($activity->activity_description) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="activity-time">
                                <i class="fas fa-clock"></i>
                                <?= Yii::$app->formatter->asRelativeTime($activity->created_at) ?>
                                
                                <?php if ($activity->ip_address): ?>
                                    <span class="ml-2">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= Html::encode($activity->ip_address) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Additional metadata display -->
                            <?php if ($activity->metadata && is_array($activity->metadata)): ?>
                                <?php 
                                $metadata = $activity->metadata;
                                $displayMetadata = [];
                                
                                // Show relevant metadata based on activity type
                                if (isset($metadata['amount']) && $activity->activity_type === CustomerActivity::TYPE_PAYMENT_SUCCESS) {
                                    $displayMetadata[] = 'Amount: $' . number_format($metadata['amount'], 2);
                                }
                                
                                if (isset($metadata['transaction_id'])) {
                                    $displayMetadata[] = 'Transaction: ' . substr($metadata['transaction_id'], 0, 10) . '...';
                                }
                                
                                if (isset($metadata['changed_fields']) && is_array($metadata['changed_fields'])) {
                                    $displayMetadata[] = 'Updated: ' . implode(', ', $metadata['changed_fields']);
                                }
                                
                                if (isset($metadata['old_package_id']) && isset($metadata['new_package_id'])) {
                                    $displayMetadata[] = 'Package: ' . $metadata['old_package_id'] . ' → ' . $metadata['new_package_id'];
                                }
                                ?>
                                
                                <?php if (!empty($displayMetadata)): ?>
                                    <div class="activity-metadata mt-1">
                                        <small class="text-muted">
                                            <?= Html::encode(implode(' | ', $displayMetadata)) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Footer -->
        <?php if (!empty($activities)): ?>
            <div class="activity-footer">
                <?= Html::a(
                    '<i class="fas fa-list"></i> View All Activities',
                    Url::to(['/customer-dashboard/activities']),
                    ['class' => 'btn btn-link btn-sm p-0']
                ) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Additional JavaScript for enhanced functionality
$this->registerJs("
// Enhanced notification interactions
$('.activity-item').on('click', function() {
    // Mark as read or perform action
    $(this).addClass('read');
});

// Keyboard navigation
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        $('.activity-dropdown').removeClass('show');
    }
});

// Auto-hide after period of inactivity
let hideTimeout;
$('.activity-bell').on('click', function() {
    clearTimeout(hideTimeout);
    hideTimeout = setTimeout(function() {
        $('.activity-dropdown').removeClass('show');
    }, 10000); // Hide after 10 seconds of inactivity
});
");

// Add some additional CSS for better UX
$this->registerCss("
.activity-item.read {
    opacity: 0.7;
}

.activity-metadata {
    font-size: 11px;
    color: #868e96;
}

.activity-dropdown {
    animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.activity-bell:active {
    transform: scale(0.95);
}

.activity-item:hover .activity-icon {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}
");
?>