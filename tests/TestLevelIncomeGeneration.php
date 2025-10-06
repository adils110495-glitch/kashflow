<?php

// Include Yii framework
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// Load application configuration
$config = require __DIR__ . '/../config/console.php';
new yii\console\Application($config);

// Import required models
use app\models\Customer;
use app\models\Package;
use app\models\CustomerPackage;
use app\models\Income;
use app\models\LevelPlan;
use app\models\User;

echo "Testing Level Income Generation\n";
echo "================================\n\n";

// Check if level plans exist
$levelPlans = LevelPlan::find()->where(['status' => 1])->orderBy('level ASC')->all();
if (empty($levelPlans)) {
    echo "❌ No active level plans found. Please create level plans first.\n";
    exit(1);
}

echo "✓ Found " . count($levelPlans) . " active level plans:\n";
foreach ($levelPlans as $plan) {
    echo "  Level {$plan->level}: {$plan->rate}% rate, {$plan->no_of_directs} direct referrals required\n";
}
echo "\n";

// Find or create a test referrer (Level 1)
$timestamp = time();
$referrer1 = Customer::find()->joinWith('user')->where(['user.username' => 'testref1_' . $timestamp])->one();
if (!$referrer1) {
    // Create test referrer 1
    $user1 = new User(['scenario' => 'register']);
    $user1->username = 'testref1_' . $timestamp;
    $user1->email = 'testref1_' . $timestamp . '@example.com';
    $user1->password_hash = Yii::$app->security->generatePasswordHash('password123');
    $user1->confirmed_at = time();
    $user1->created_at = time();
    $user1->updated_at = time();
    
    if ($user1->save(false)) {
        $referrer1 = new Customer();
        $referrer1->user_id = $user1->id;
        $referrer1->name = 'Test Referrer 1';
        $referrer1->email = $user1->email;
        $referrer1->mobile_no = '1' . $timestamp;
        $referrer1->country_id = 1;
        $referrer1->referral_code = null; // Top level referrer
        $referrer1->save();
        
        // Generate referral code for referrer1
        $referrer1->referral_code = $referrer1->generateCustomerUsername();
        $referrer1->save(false);
        echo "✓ Created test referrer 1: {$referrer1->name}\n";
    }
} else {
    echo "✓ Found existing test referrer 1: {$referrer1->name}\n";
}

// Find or create a test referrer (Level 2)
$referrer2 = Customer::find()->joinWith('user')->where(['user.username' => 'testref2_' . $timestamp])->one();
if (!$referrer2) {
    // Create test referrer 2
    $user2 = new User(['scenario' => 'register']);
    $user2->username = 'testref2_' . $timestamp;
    $user2->email = 'testref2_' . $timestamp . '@example.com';
    $user2->password_hash = Yii::$app->security->generatePasswordHash('password123');
    $user2->confirmed_at = time();
    $user2->created_at = time();
    $user2->updated_at = time();
    
    if ($user2->save(false)) {
        $referrer2 = new Customer();
        $referrer2->user_id = $user2->id;
        $referrer2->name = 'Test Referrer 2';
        $referrer2->email = $user2->email;
        $referrer2->mobile_no = '2' . $timestamp;
        $referrer2->country_id = 1;
        $referrer2->referral_code = $user1->username; // Referred by referrer1's username
        $referrer2->save();
         
         echo "✓ Created test referrer 2: {$referrer2->name} referred by: {$user1->username}\n";
    }
} else {
    echo "✓ Found existing test referrer 2: {$referrer2->name}\n";
}

// Create a new test customer
$testUser = new User(['scenario' => 'register']);
$testUser->username = 'testcustomer_' . time();
$testUser->email = 'testcustomer_' . time() . '@example.com';
$testUser->password_hash = Yii::$app->security->generatePasswordHash('password123');
$testUser->confirmed_at = time();
$testUser->created_at = time();
$testUser->updated_at = time();

if (!$testUser->save(false)) {
    echo "❌ Failed to create test user: " . implode(', ', $testUser->getFirstErrors()) . "\n";
    exit(1);
}

$testCustomer = new Customer();
$testCustomer->user_id = $testUser->id;
$testCustomer->name = 'Test Customer ' . time();
$testCustomer->email = $testUser->email;
$testCustomer->mobile_no = '3' . $timestamp;
$testCustomer->country_id = 1;
$testCustomer->referral_code = $user2->username; // Referred by referrer2's username

if (!$testCustomer->save()) {
    echo "❌ Failed to create test customer: " . implode(', ', $testCustomer->getFirstErrors()) . "\n";
    exit(1);
}

echo "✓ Created test customer: {$testCustomer->name} (ID: {$testCustomer->id})\n";
echo "  Referral code: {$testCustomer->referral_code}\n\n";

// Assign a package to the test customer
$package = Package::find()->where(['status' => Package::STATUS_ACTIVE])->andWhere(['>', 'amount', 0])->one();
if (!$package) {
    echo "❌ No active paid packages found.\n";
    exit(1);
}

$customerPackage = new CustomerPackage();
$customerPackage->customer_id = $testCustomer->id;
$customerPackage->package_id = $package->id;
$customerPackage->status = CustomerPackage::STATUS_ACTIVE;
$customerPackage->date = date('Y-m-d');

if (!$customerPackage->save()) {
    echo "❌ Failed to assign package: " . implode(', ', $customerPackage->getFirstErrors()) . "\n";
    exit(1);
}

echo "✓ Assigned package: {$package->name} - Amount: $" . number_format($package->amount, 2) . "\n\n";

// Count existing level income records before generation
$existingIncomeCount = Income::find()->where(['type' => Income::TYPE_LEVEL_INCOME])->count();
echo "Existing level income records: {$existingIncomeCount}\n\n";

// Run level income generation
echo "Running level income generation...\n";
$command = "php yii income/level-generator {$testCustomer->id}";
echo "Command: {$command}\n\n";

$output = [];
$returnCode = 0;
exec($command . ' 2>&1', $output, $returnCode);

echo "Command output:\n";
echo implode("\n", $output) . "\n\n";

if ($returnCode !== 0) {
    echo "❌ Level income generation failed with return code: {$returnCode}\n";
    exit(1);
}

// Check if level income was generated
$newIncomeCount = Income::find()->where(['type' => Income::TYPE_LEVEL_INCOME])->count();
$generatedCount = $newIncomeCount - $existingIncomeCount;

echo "Level income generation results:\n";
echo "Generated income records: {$generatedCount}\n";

if ($generatedCount > 0) {
    echo "\n✓ Level income generation successful!\n\n";
    
    // Show generated income records
    $generatedIncomes = Income::find()
        ->where(['type' => Income::TYPE_LEVEL_INCOME])
        ->andWhere(['like', 'date', date('Y-m')])
        ->with('customer')
        ->orderBy(['level' => SORT_ASC])
        ->all();
    
    echo "Generated income records:\n";
    foreach ($generatedIncomes as $income) {
        echo "  Level {$income->level}: {$income->customer->name} - $" . number_format($income->amount, 2) . "\n";
        echo "    Date: {$income->date}\n";
    }
} else {
    echo "\n⚠️  No level income was generated. This might be expected if:\n";
    echo "   - Referrers don't meet direct referral requirements\n";
    echo "   - Income already exists for this customer\n";
    echo "   - No referral hierarchy exists\n";
}

echo "\n✅ Level income generation test completed successfully!\n";