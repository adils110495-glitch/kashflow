<?php

// Define constants
define('YII_DEBUG', true);
define('YII_ENV', 'dev');
define('YII_ENV_DEV', true);

// Include Yii
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// Load configuration
$config = require __DIR__ . '/../config/web.php';

// Create application
$app = new yii\web\Application($config);

use app\models\Customer;
use app\models\User;

echo "=== Creating Team Sample Data ===\n\n";

$transaction = Yii::$app->db->beginTransaction();
$level1Customers = [];
$level2Customers = [];

try {
    // Check if we have existing customers with usernames to use as referrers
    $existingCustomers = Customer::find()
        ->joinWith('user')
        ->where(['not', ['user.username' => null]])
        ->andWhere(['!=', 'user.username', 'superadmin'])
        ->limit(1)
        ->all();
    
    if (!empty($existingCustomers)) {
        $mainReferrer = $existingCustomers[0];
        $mainReferralCode = $mainReferrer->user->username;
        echo "Found existing customer referrer: {$mainReferrer->name} (Username: {$mainReferralCode})\n\n";
    } else {
        echo "No suitable customer referrer found. Using KF000001 as default.\n\n";
        $mainReferralCode = 'KF000001';
    }
    // Create Level 1 customers (direct referrals)
    echo "Creating Level 1 customers...\n";
    
    for ($i = 1; $i <= 3; $i++) {
        // Create user first
        $user = \Yii::createObject([
            'class' => \dektrium\user\models\User::class,
            'scenario' => 'create',
        ]);
        $user->username = "TL1U" . str_pad($i, 6, '0', STR_PAD_LEFT);
        $user->email = "teamuser{$i}@example.com";
        $user->password = 'password123';
        $user->confirmed_at = time();
        
        if ($user->save()) {
            // Create customer
            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->name = "Team Member Level 1 - {$i}";
            $customer->email = "teamuser{$i}@example.com";
            $customer->mobile_no = "123456789{$i}";
            $customer->country_id = 1;
            $customer->referral_code = $mainReferralCode;
            $customer->current_package = 1;
            $customer->status = 1;
            $customer->created_at = time() - (86400 * $i * 5); // Different creation dates
            $customer->updated_at = time();
            
            if ($customer->save()) {
                $level1Customers[] = $customer;
                echo "✓ Created Level 1 customer: {$customer->name} (Username: {$user->username})\n";
            } else {
                echo "✗ Failed to create Level 1 customer {$i}: " . implode(', ', $customer->getFirstErrors()) . "\n";
            }
        } else {
            echo "✗ Failed to create user for Level 1 customer {$i}: " . implode(', ', $user->getFirstErrors()) . "\n";
        }
    }
    
    echo "\n";
    
    // Create Level 2 customers (referred by Level 1 customers)
    echo "Creating Level 2 customers...\n";
    
    if (empty($level1Customers)) {
        $level1Customers = Customer::find()
            ->joinWith('user')
            ->where(['referral_code' => 'KF000001'])
            ->all();
    }
    
    $level2Count = 1;
    foreach ($level1Customers as $level1Customer) {
        if ($level1Customer->user) {
            // Create 1-2 Level 2 customers for each Level 1 customer
            $numLevel2 = rand(1, 2);
            
            for ($j = 1; $j <= $numLevel2; $j++) {
                // Create user first
                $user = \Yii::createObject([
                    'class' => \dektrium\user\models\User::class,
                    'scenario' => 'create',
                ]);
                $user->username = "TL2U" . str_pad($level2Count, 6, '0', STR_PAD_LEFT);
                $user->email = "level2user{$level2Count}@example.com";
                $user->password = 'password123';
                $user->confirmed_at = time();
                
                if ($user->save()) {
                    // Create customer
                    $customer = new Customer();
                    $customer->user_id = $user->id;
                    $customer->name = "Team Member Level 2 - {$level2Count}";
                    $customer->email = "level2user{$level2Count}@example.com";
                    $customer->mobile_no = "223456789{$level2Count}";
                    $customer->country_id = 1;
                    $customer->referral_code = $level1Customer->user->username; // Use Level 1 customer's username
                    $customer->current_package = 1;
                    $customer->status = 1;
                    $customer->created_at = time() - (86400 * $level2Count * 2);
                    $customer->updated_at = time();
                    
                    if ($customer->save()) {
                        $level2Customers[] = $customer;
                        echo "✓ Created Level 2 customer: {$customer->name} (Username: {$user->username}, Referrer: {$level1Customer->user->username})\n";
                    } else {
                        echo "✗ Failed to create Level 2 customer {$level2Count}: " . implode(', ', $customer->getFirstErrors()) . "\n";
                    }
                } else {
                    echo "✗ Failed to create user for Level 2 customer {$level2Count}: " . implode(', ', $user->getFirstErrors()) . "\n";
                }
                
                $level2Count++;
            }
        }
    }
    
    echo "\n";
    
    // Create Level 3 customers (referred by Level 2 customers)
    echo "Creating Level 3 customers...\n";
    
    // Create Level 3 customers (referrals from Level 2)
     echo "\nCreating Level 3 customers...\n";
     
     $level3Count = 1;
     foreach ($level2Customers as $level2Customer) {
         if ($level3Count <= 3) { // Limit to 3 Level 3 customers
             // Create user first
             $user = \Yii::createObject([
                 'class' => \dektrium\user\models\User::class,
                 'scenario' => 'create',
             ]);
             $user->username = "TL3U" . str_pad($level3Count, 6, '0', STR_PAD_LEFT);
             $user->email = "level3user{$level3Count}@example.com";
             $user->password = 'password123';
             $user->confirmed_at = time();
             
             if ($user->save()) {
                 // Create customer
                 $customer = new Customer();
                 $customer->user_id = $user->id;
                 $customer->name = "Team Member Level 3 - {$level3Count}";
                 $customer->email = "level3user{$level3Count}@example.com";
                 $customer->mobile_no = "323456789{$level3Count}";
                 $customer->country_id = 1;
                 $customer->referral_code = $level2Customer->user->username; // Use Level 2 customer's username
                 $customer->current_package = 1;
                 $customer->status = 1;
                 $customer->created_at = time() - (86400 * $level3Count * 3);
                 $customer->updated_at = time();
                 
                 if ($customer->save()) {
                     echo "✓ Created Level 3 customer: {$customer->name} (Username: {$user->username}, Referrer: {$level2Customer->user->username})\n";
                 } else {
                     echo "✗ Failed to create Level 3 customer {$level3Count}: " . implode(', ', $customer->getFirstErrors()) . "\n";
                 }
             } else {
                 echo "✗ Failed to create user for Level 3 customer {$level3Count}: " . implode(', ', $user->getFirstErrors()) . "\n";
             }
             
             $level3Count++;
         }
     }
    
    // Commit transaction
    $transaction->commit();
    
    echo "\n=== Sample Data Creation Complete ===\n";
    echo "Summary:\n";
    
    // Count Level 1 customers (those with main referral code)
    $level1Count = Customer::find()->where(['referral_code' => $mainReferralCode])->count();
    
    // Count Level 2 customers (those referred by Level 1 usernames)
    $level1Usernames = [];
    foreach($level1Customers as $l1) {
        if ($l1->user) {
            $level1Usernames[] = $l1->user->username;
        }
    }
    $level2Count = !empty($level1Usernames) ? Customer::find()->where(['in', 'referral_code', $level1Usernames])->count() : 0;
    
    // Count Level 3 customers (those referred by Level 2 usernames)
    $level2Usernames = [];
    foreach($level2Customers as $l2) {
        if ($l2->user) {
            $level2Usernames[] = $l2->user->username;
        }
    }
    $level3Count = !empty($level2Usernames) ? Customer::find()->where(['in', 'referral_code', $level2Usernames])->count() : 0;
    
    echo "- Level 1 customers: {$level1Count}\n";
    echo "- Level 2 customers: {$level2Count}\n";
    echo "- Level 3 customers: {$level3Count}\n";
    echo "Total customers created: " . ($level1Count + $level2Count + $level3Count) . "\n";
    
} catch (Exception $e) {
    $transaction->rollBack();
    echo "\n✗ Error creating sample data: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}