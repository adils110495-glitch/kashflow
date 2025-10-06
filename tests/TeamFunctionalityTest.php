<?php

/**
 * Team Functionality Test
 * Tests the direct team and level team functionality
 */

// Bootstrap Yii2 application
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';
new yii\web\Application($config);

use app\models\Customer;
use dektrium\user\models\User;

echo "=== Team Functionality Test ===\n\n";

// Test 1: Check if we can find customers with referral codes
echo "1. Testing Customer-User relationship and referral codes...\n";

$customers = Customer::find()
    ->joinWith('user')
    ->where(['not', ['referral_code' => null]])
    ->andWhere(['not', ['referral_code' => '']])
    ->limit(5)
    ->all();

echo "Found " . count($customers) . " customers with referral codes:\n";
foreach ($customers as $customer) {
    echo "- Customer ID: {$customer->id}, Name: {$customer->name}, Referral Code: {$customer->referral_code}";
    if ($customer->user) {
        echo ", Username: {$customer->user->username}";
    } else {
        echo ", No user associated";
    }
    echo "\n";
}

echo "\n";

// Test 2: Test direct team functionality
echo "2. Testing direct team functionality...\n";

// Find a customer with a username to use as test
$testCustomer = Customer::find()
    ->joinWith('user')
    ->where(['not', ['user.username' => null]])
    ->one();

if ($testCustomer && $testCustomer->user) {
    $testUsername = $testCustomer->user->username;
    echo "Using test username: {$testUsername}\n";
    
    // Find direct team members
    $directTeam = Customer::find()
        ->joinWith('user')
        ->where(['referral_code' => $testUsername])
        ->all();
    
    echo "Direct team members for {$testUsername}: " . count($directTeam) . "\n";
    foreach ($directTeam as $member) {
        $username = $member->user ? $member->user->username : 'No username';
        echo "- {$member->name} ({$username})\n";
    }
} else {
    echo "No customer with username found for testing\n";
}

echo "\n";

// Test 3: Test level team hierarchy building
echo "3. Testing level team hierarchy...\n";

function buildTestLevelTeam($username, $currentLevel = 1, $maxLevel = 3) {
    if ($currentLevel > $maxLevel) {
        return [];
    }
    
    $customers = Customer::find()
        ->joinWith('user')
        ->where(['referral_code' => $username])
        ->all();
    
    $levelData = [
        'level' => $currentLevel,
        'customers' => $customers,
        'count' => count($customers),
        'children' => []
    ];
    
    // Recursively build next levels (limited to 3 for testing)
    foreach ($customers as $customer) {
        if ($customer->user && $customer->user->username) {
            $childLevels = buildTestLevelTeam(
                $customer->user->username, 
                $currentLevel + 1, 
                $maxLevel
            );
            if (!empty($childLevels) && $childLevels['count'] > 0) {
                $levelData['children'][$customer->user->username] = $childLevels;
            }
        }
    }
    
    return $levelData;
}

if ($testCustomer && $testCustomer->user) {
    $levelTeam = buildTestLevelTeam($testCustomer->user->username);
    
    function displayTestLevelTeam($levelData, $indent = '') {
        if (empty($levelData) || $levelData['count'] == 0) {
            return;
        }
        
        echo "{$indent}Level {$levelData['level']}: {$levelData['count']} members\n";
        
        foreach ($levelData['customers'] as $customer) {
            $username = $customer->user ? $customer->user->username : 'No username';
            echo "{$indent}  - {$customer->name} ({$username})\n";
        }
        
        foreach ($levelData['children'] as $username => $childLevel) {
            displayTestLevelTeam($childLevel, $indent . '  ');
        }
    }
    
    displayTestLevelTeam($levelTeam);
} else {
    echo "No test customer available\n";
}

echo "\n";

// Test 4: Test filter functionality
echo "4. Testing filter functionality...\n";

$fromDate = date('Y-m-d', strtotime('-30 days'));
$toDate = date('Y-m-d');

echo "Testing date filter from {$fromDate} to {$toDate}\n";

$filteredCustomers = Customer::find()
    ->where(['>=', 'created_at', strtotime($fromDate)])
    ->andWhere(['<=', 'created_at', strtotime($toDate . ' 23:59:59')])
    ->count();

echo "Found {$filteredCustomers} customers in date range\n";

echo "\n=== Test Complete ===\n";