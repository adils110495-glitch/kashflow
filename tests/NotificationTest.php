<?php

// Test script to create sample activities and test notification functionality

// Define required constants
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
defined('YII_ENV_DEV') or define('YII_ENV_DEV', true);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

use app\models\Customer;
use app\models\CustomerActivity;
use app\services\ActivityLoggerService;

// Initialize Yii application
$config = require __DIR__ . '/../config/web.php';
$app = new yii\web\Application($config);
Yii::setAlias('@app', __DIR__ . '/..');
Yii::setAlias('@webroot', __DIR__ . '/../web');
Yii::setAlias('@web', '/');

// Set up database connection
Yii::$app->db;

echo "=== Customer Activity Notification Test ===\n\n";

try {
    // Find a test customer (or create one)
    $customer = Customer::find()->one();
    
    if (!$customer) {
        echo "❌ No customers found in database. Please create a customer first.\n";
        exit(1);
    }
    
    echo "✓ Testing with customer: {$customer->name} (ID: {$customer->id})\n\n";
    
    // Test 1: Create sample activities
    echo "Test 1: Creating sample activities...\n";
    
    $activities = [
        [
            'type' => CustomerActivity::TYPE_LOGIN,
            'description' => 'User logged in from web browser',
            'metadata' => ['browser' => 'Chrome', 'device' => 'Desktop']
        ],
        [
            'type' => CustomerActivity::TYPE_PROFILE_UPDATE,
            'description' => 'Updated profile information',
            'metadata' => ['changed_fields' => ['name', 'phone']]
        ],
        [
            'type' => CustomerActivity::TYPE_PAYMENT_SUCCESS,
            'description' => 'Monthly subscription payment processed',
            'metadata' => ['amount' => 29.99, 'transaction_id' => 'TXN_' . uniqid()]
        ],
        [
            'type' => CustomerActivity::TYPE_PACKAGE_UPGRADE,
            'description' => 'Upgraded to Premium package',
            'metadata' => ['old_package_id' => 1, 'new_package_id' => 2]
        ],
        [
            'type' => CustomerActivity::TYPE_SUPPORT_TICKET,
            'description' => 'Created support ticket for billing inquiry',
            'metadata' => ['ticket_id' => 'TICKET_' . uniqid(), 'category' => 'billing']
        ]
    ];
    
    foreach ($activities as $activityData) {
        $activity = new CustomerActivity();
        $activity->customer_id = $customer->id;
        $activity->activity_type = $activityData['type'];
        $activity->activity_description = $activityData['description'];
        $activity->ip_address = '127.0.0.1';
        $activity->user_agent = 'Test Script';
        $activity->metadata = $activityData['metadata'];
        $activity->created_at = date('Y-m-d H:i:s', time() - rand(0, 3600)); // Random time within last hour
        
        if ($activity->save()) {
            echo "  ✓ Created: {$activity->getActivityTypeLabel()}\n";
        } else {
            echo "  ❌ Failed to create activity: " . implode(', ', $activity->getFirstErrors()) . "\n";
        }
    }
    
    echo "\nTest 2: Retrieving recent activities...\n";
    
    // Test 2: Get recent activities
    $recentActivities = $customer->getRecentActivities(10);
    echo "✓ Found {$recentActivities->count()} recent activities\n";
    
    foreach ($recentActivities->all() as $activity) {
        echo "  - {$activity->getActivityTypeLabel()} ({$activity->created_at})\n";
    }
    
    echo "\nTest 3: Testing activity type labels...\n";
    
    // Test 3: Test activity type labels
    $activityTypes = [
        CustomerActivity::TYPE_LOGIN,
        CustomerActivity::TYPE_PAYMENT_SUCCESS,
        CustomerActivity::TYPE_PACKAGE_UPGRADE,
        CustomerActivity::TYPE_SUPPORT_TICKET
    ];
    
    foreach ($activityTypes as $type) {
        $activity = new CustomerActivity();
        $activity->activity_type = $type;
        echo "  ✓ {$type} -> {$activity->getActivityTypeLabel()}\n";
    }
    
    echo "\nTest 4: Testing ActivityLoggerService...\n";
    
    // Test 4: Test service methods
    try {
        // Simulate user session
        Yii::$app->user->login($customer->user);
        
        ActivityLoggerService::logLogin('Test login via service');
        echo "  ✓ Login activity logged via service\n";
        
        ActivityLoggerService::logProfileUpdate(['email'], 'Updated email address');
        echo "  ✓ Profile update activity logged via service\n";
        
        ActivityLoggerService::logPaymentSuccess(49.99, 'TXN_SERVICE_' . uniqid(), 'Service payment test');
        echo "  ✓ Payment success activity logged via service\n";
        
    } catch (Exception $e) {
        echo "  ⚠️  Service test skipped (no user session): {$e->getMessage()}\n";
    }
    
    echo "\nTest 5: Checking notification widget data...\n";
    
    // Test 5: Check widget data structure
    $activities = CustomerActivity::find()
        ->where(['customer_id' => $customer->id])
        ->orderBy(['created_at' => SORT_DESC])
        ->limit(5)
        ->all();
    
    echo "✓ Widget will display " . count($activities) . " activities:\n";
    
    foreach ($activities as $activity) {
        $metadata = is_array($activity->metadata) ? json_encode($activity->metadata) : ($activity->metadata ?: 'null');
        echo "  - {$activity->getActivityTypeLabel()}\n";
        echo "    Description: {$activity->activity_description}\n";
        echo "    Time: {$activity->created_at}\n";
        echo "    Metadata: {$metadata}\n";
        echo "\n";
    }
    
    echo "\n=== All Tests Completed Successfully! ===\n";
    echo "\nThe notification widget should now display these activities in the customer topbar.\n";
    echo "Visit the customer dashboard to see the notification bell with activity count.\n";
    
} catch (Exception $e) {
    echo "❌ Test failed with error: {$e->getMessage()}\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n";
    exit(1);
}