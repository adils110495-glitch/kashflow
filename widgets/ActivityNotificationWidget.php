<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\CustomerActivity;
use app\models\Customer;

/**
 * Activity Notification Widget
 * 
 * Displays customer activities as notifications
 */
class ActivityNotificationWidget extends Widget
{
    /**
     * @var int Number of activities to display
     */
    public $limit = 10;
    
    /**
     * @var Customer Customer model instance
     */
    public $customer;
    
    /**
     * @var string CSS class for the notification container
     */
    public $containerClass = 'activity-notifications';
    
    /**
     * @var bool Whether to show notification count badge
     */
    public $showBadge = true;
    
    /**
     * @var bool Whether to auto-refresh notifications
     */
    public $autoRefresh = false;
    
    /**
     * @var int Auto-refresh interval in seconds
     */
    public $refreshInterval = 30;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        if (!$this->customer) {
            // Try to get customer from current user
            if (!Yii::$app->user->isGuest) {
                $identity = Yii::$app->user->identity;
                if ($identity && method_exists($identity, 'getCustomer')) {
                    $this->customer = $identity->customer;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!$this->customer) {
            return '';
        }
        
        $activities = CustomerActivity::getRecentActivities($this->customer->id, $this->limit);
        $unreadCount = count($activities); // In a real app, you'd track read/unread status
        
        $this->registerAssets();
        
        return $this->render('activity-notification', [
            'activities' => $activities,
            'customer' => $this->customer,
            'unreadCount' => $unreadCount,
            'showBadge' => $this->showBadge,
            'containerClass' => $this->containerClass,
            'autoRefresh' => $this->autoRefresh,
            'refreshInterval' => $this->refreshInterval,
        ]);
    }
    
    /**
     * Register widget assets
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        
        // Register CSS
        $css = "
        .activity-notifications {
            position: relative;
            display: inline-block;
        }
        
        /* Topbar specific styling */
        .activity-notification-topbar .activity-bell {
            color: #6c757d;
            font-size: 18px;
            padding: 8px;
            border-radius: 4px;
            transition: all 0.2s ease;
            background: transparent;
            border: none;
        }
        
        .activity-notification-topbar .activity-bell:hover {
            color: #007bff;
            background-color: rgba(0,123,255,0.1);
        }
        
        .activity-bell {
            position: relative;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .activity-bell:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }
        
        .activity-bell i {
            font-size: 18px;
            color: #6c757d;
        }
        
        .activity-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid white;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .activity-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .activity-dropdown.show {
            display: block;
        }
        
        .activity-header {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }
        
        .activity-header h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #495057;
        }
        
        .activity-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .activity-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f3f4;
            transition: background-color 0.2s ease;
        }
        
        .activity-item:hover {
            background: #f8f9fa;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .activity-icon.login { background: #d4edda; color: #155724; }
        .activity-icon.logout { background: #f8d7da; color: #721c24; }
        .activity-icon.registration { background: #d1ecf1; color: #0c5460; }
        .activity-icon.profile_update { background: #fff3cd; color: #856404; }
        .activity-icon.password_change { background: #f0d0ff; color: #6f42c1; }
        .activity-icon.payment_success { background: #d4edda; color: #155724; }
        .activity-icon.payment_failed { background: #f8d7da; color: #721c24; }
        .activity-icon.default { background: #e2e3e5; color: #6c757d; }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-size: 13px;
            font-weight: 500;
            color: #495057;
            margin: 0 0 4px 0;
        }
        
        .activity-description {
            font-size: 12px;
            color: #6c757d;
            margin: 0 0 4px 0;
        }
        
        .activity-time {
            font-size: 11px;
            color: #adb5bd;
        }
        
        .activity-empty {
            padding: 30px 15px;
            text-align: center;
            color: #6c757d;
            font-size: 13px;
        }
        
        .activity-footer {
            padding: 10px 15px;
            border-top: 1px solid #dee2e6;
            background: #f8f9fa;
            text-align: center;
            border-radius: 0 0 8px 8px;
        }
        
        .activity-footer a {
            font-size: 12px;
            color: #007bff;
            text-decoration: none;
        }
        
        .activity-footer a:hover {
            text-decoration: underline;
        }
        ";
        
        $view->registerCss($css);
        
        // Register JavaScript
        $js = "
        $(document).ready(function() {
            // Toggle notification dropdown
            $('.activity-bell').on('click', function(e) {
                e.stopPropagation();
                $('.activity-dropdown').toggleClass('show');
            });
            
            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.activity-notifications').length) {
                    $('.activity-dropdown').removeClass('show');
                }
            });
            
            // Auto-refresh functionality
            if (" . ($this->autoRefresh ? 'true' : 'false') . ") {
                setInterval(function() {
                    // In a real implementation, you'd make an AJAX call to refresh activities
                    console.log('Auto-refreshing activities...');
                }, " . ($this->refreshInterval * 1000) . ");
            }
        });
        ";
        
        $view->registerJs($js);
    }
    
    /**
     * Get activity icon class
     */
    public static function getActivityIcon($activityType)
    {
        $icons = [
            CustomerActivity::TYPE_LOGIN => 'fas fa-sign-in-alt',
            CustomerActivity::TYPE_LOGOUT => 'fas fa-sign-out-alt',
            CustomerActivity::TYPE_REGISTRATION => 'fas fa-user-plus',
            CustomerActivity::TYPE_PROFILE_UPDATE => 'fas fa-user-edit',
            CustomerActivity::TYPE_PASSWORD_CHANGE => 'fas fa-key',
            CustomerActivity::TYPE_PACKAGE_CHANGE => 'fas fa-box',
            CustomerActivity::TYPE_PACKAGE_UPGRADE => 'fas fa-arrow-up',
            CustomerActivity::TYPE_PACKAGE_DOWNGRADE => 'fas fa-arrow-down',
            CustomerActivity::TYPE_BILLING_UPDATE => 'fas fa-credit-card',
            CustomerActivity::TYPE_PAYMENT_SUCCESS => 'fas fa-check-circle',
            CustomerActivity::TYPE_PAYMENT_FAILED => 'fas fa-times-circle',
            CustomerActivity::TYPE_INVOICE_GENERATED => 'fas fa-file-invoice',
            CustomerActivity::TYPE_SUPPORT_TICKET => 'fas fa-life-ring',
            CustomerActivity::TYPE_SETTINGS_UPDATE => 'fas fa-cog',
            CustomerActivity::TYPE_EMAIL_VERIFICATION => 'fas fa-envelope-check',
            CustomerActivity::TYPE_PASSWORD_RESET => 'fas fa-unlock-alt',
            CustomerActivity::TYPE_ACCOUNT_SUSPENSION => 'fas fa-ban',
            CustomerActivity::TYPE_ACCOUNT_REACTIVATION => 'fas fa-check',
            CustomerActivity::TYPE_DATA_EXPORT => 'fas fa-download',
            CustomerActivity::TYPE_ACCOUNT_DELETION => 'fas fa-trash',
        ];
        
        return isset($icons[$activityType]) ? $icons[$activityType] : 'fas fa-bell';
    }
    
    /**
     * Get activity icon CSS class
     */
    public static function getActivityIconClass($activityType)
    {
        $classes = [
            CustomerActivity::TYPE_LOGIN => 'login',
            CustomerActivity::TYPE_LOGOUT => 'logout',
            CustomerActivity::TYPE_REGISTRATION => 'registration',
            CustomerActivity::TYPE_PROFILE_UPDATE => 'profile_update',
            CustomerActivity::TYPE_PASSWORD_CHANGE => 'password_change',
            CustomerActivity::TYPE_PAYMENT_SUCCESS => 'payment_success',
            CustomerActivity::TYPE_PAYMENT_FAILED => 'payment_failed',
        ];
        
        return isset($classes[$activityType]) ? $classes[$activityType] : 'default';
    }
}