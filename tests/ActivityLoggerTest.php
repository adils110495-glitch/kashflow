<?php

namespace app\tests;

use Yii;
use app\models\Customer;
use app\models\CustomerActivity;
use app\services\ActivityLoggerService;
use yii\db\Connection;

/**
 * Test class for Customer Activity Logging functionality
 */
class ActivityLoggerTest
{
    /**
     * Test basic activity logging
     */
    public static function testBasicActivityLogging()
    {
        echo "\n=== Testing Basic Activity Logging ===\n";
        
        // Find a customer to test with
        $customer = Customer::find()->one();
        if (!$customer) {
            echo "❌ No customer found for testing\n";
            return false;
        }
        
        echo "✓ Using customer ID: {$customer->id} ({$customer->name})\n";
        
        // Test direct activity logging
        $result = CustomerActivity::logActivity(
            $customer->id,
            CustomerActivity::TYPE_LOGIN,
            'Test login activity',
            ['test' => true, 'ip' => '127.0.0.1']
        );
        
        if ($result) {
            echo "✓ Direct activity logging successful\n";
        } else {
            echo "❌ Direct activity logging failed\n";
            return false;
        }
        
        // Test service-based logging
        $result = ActivityLoggerService::logLogin(
            $customer->id,
            ['test_service' => true]
        );
        
        if ($result) {
            echo "✓ Service-based activity logging successful\n";
        } else {
            echo "❌ Service-based activity logging failed\n";
            return false;
        }
        
        // Test customer model logging
        $result = $customer->logActivity(
            CustomerActivity::TYPE_PROFILE_UPDATE,
            'Test profile update via customer model',
            ['updated_fields' => ['name', 'email']]
        );
        
        if ($result) {
            echo "✓ Customer model activity logging successful\n";
        } else {
            echo "❌ Customer model activity logging failed\n";
            return false;
        }
        
        return true;
    }
    
    /**
     * Test activity retrieval
     */
    public static function testActivityRetrieval()
    {
        echo "\n=== Testing Activity Retrieval ===\n";
        
        $customer = Customer::find()->one();
        if (!$customer) {
            echo "❌ No customer found for testing\n";
            return false;
        }
        
        // Test getting recent activities
        $recentActivities = CustomerActivity::getRecentActivities($customer->id, 5);
        echo "✓ Found " . count($recentActivities) . " recent activities\n";
        
        // Test getting activities by type
        $loginActivities = CustomerActivity::getActivitiesByType(
            $customer->id,
            CustomerActivity::TYPE_LOGIN,
            3
        );
        echo "✓ Found " . count($loginActivities) . " login activities\n";
        
        // Test activity count
        $totalCount = CustomerActivity::getActivityCount($customer->id);
        echo "✓ Total activities for customer: $totalCount\n";
        
        // Test customer relationship methods
        $customerActivities = $customer->getActivities()->limit(3)->all();
        echo "✓ Found " . count($customerActivities) . " activities via customer relationship\n";
        
        // Display some activity details
        if (!empty($recentActivities)) {
            echo "\n--- Recent Activity Details ---\n";
            foreach (array_slice($recentActivities, 0, 3) as $activity) {
                echo "- {$activity->getActivityTypeLabel()}: {$activity->activity_description} ({$activity->created_at})\n";
            }
        }
        
        return true;
    }
    
    /**
     * Test various activity types
     */
    public static function testVariousActivityTypes()
    {
        echo "\n=== Testing Various Activity Types ===\n";
        
        $customer = Customer::find()->one();
        if (!$customer) {
            echo "❌ No customer found for testing\n";
            return false;
        }
        
        $testActivities = [
            ['service' => 'logRegistration', 'type' => 'Registration'],
            ['service' => 'logProfileUpdate', 'type' => 'Profile Update', 'params' => [['name', 'email']]],
            ['service' => 'logPasswordChange', 'type' => 'Password Change'],
            ['service' => 'logPayment', 'type' => 'Payment', 'params' => ['success', 100.00, 'TXN123']],
            ['service' => 'logEmailVerification', 'type' => 'Email Verification', 'params' => ['test@example.com']],
        ];
        
        foreach ($testActivities as $test) {
            $method = $test['service'];
            $params = [$customer->id];
            
            if (isset($test['params'])) {
                $params = array_merge($params, $test['params']);
            }
            
            $result = call_user_func_array([ActivityLoggerService::class, $method], $params);
            
            if ($result) {
                echo "✓ {$test['type']} logging successful\n";
            } else {
                echo "❌ {$test['type']} logging failed\n";
            }
        }
        
        return true;
    }
    
    /**
     * Test activity type constants and labels
     */
    public static function testActivityTypeConstants()
    {
        echo "\n=== Testing Activity Type Constants ===\n";
        
        $types = CustomerActivity::getActivityTypes();
        $labels = CustomerActivity::getActivityTypeLabels();
        
        echo "✓ Found " . count($types) . " activity types\n";
        echo "✓ Found " . count($labels) . " activity labels\n";
        
        // Verify all types have labels
        $missingLabels = array_diff($types, array_keys($labels));
        if (empty($missingLabels)) {
            echo "✓ All activity types have corresponding labels\n";
        } else {
            echo "❌ Missing labels for: " . implode(', ', $missingLabels) . "\n";
            return false;
        }
        
        // Display some types and labels
        echo "\n--- Sample Activity Types ---\n";
        foreach (array_slice($types, 0, 5) as $type) {
            echo "- $type: {$labels[$type]}\n";
        }
        
        return true;
    }
    
    /**
     * Run all tests
     */
    public static function runAllTests()
    {
        echo "\n🚀 Starting Customer Activity Logger Tests\n";
        echo "==========================================\n";
        
        $tests = [
            'testBasicActivityLogging',
            'testActivityRetrieval',
            'testVariousActivityTypes',
            'testActivityTypeConstants'
        ];
        
        $passed = 0;
        $total = count($tests);
        
        foreach ($tests as $test) {
            if (self::$test()) {
                $passed++;
            }
        }
        
        echo "\n==========================================\n";
        echo "📊 Test Results: $passed/$total tests passed\n";
        
        if ($passed === $total) {
            echo "🎉 All tests passed! Customer Activity logging is working correctly.\n";
            return true;
        } else {
            echo "❌ Some tests failed. Please check the implementation.\n";
            return false;
        }
    }
}

// Run tests if this file is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    // Initialize Yii application for testing
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
    
    $config = require __DIR__ . '/../config/web.php';
    new \yii\web\Application($config);
    
    ActivityLoggerTest::runAllTests();
}