<?php

// Bootstrap Yii2 application
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';
new yii\web\Application($config);

use app\models\Customer;
use app\models\Package;
use app\models\CustomerPackage;
use app\models\Income;
use app\models\RoiPlan;

echo "=== Testing ROI Income Generation ===\n\n";

$transaction = Yii::$app->db->beginTransaction();

try {
    // Find a customer and assign them the Starter package
    $customer = Customer::find()->one();
    
    if ($customer) {
        echo "Assigning Starter package to customer for testing...\n";
        $customer->current_package = 2; // Starter package
        $customer->save();
    }
    
    if (!$customer) {
        echo "No customers found in database.\n";
        return;
    }
    
    echo "Using customer: {$customer->name} (ID: {$customer->id})\n";
    
    // Get the package
    $package = Package::findOne($customer->current_package);
    if (!$package) {
        echo "Package not found for customer.\n";
        return;
    }
    
    echo "Package: {$package->name} - Amount: $" . number_format($package->amount, 2) . "\n";
    
    // Create or update CustomerPackage record
    $customerPackage = CustomerPackage::find()
        ->where(['customer_id' => $customer->id, 'package_id' => $package->id])
        ->one();
    
    if (!$customerPackage) {
        $customerPackage = new CustomerPackage();
        $customerPackage->customer_id = $customer->id;
        $customerPackage->package_id = $package->id;
        $customerPackage->date = date('Y-m-d');
        $customerPackage->status = CustomerPackage::STATUS_ACTIVE;
        
        if ($customerPackage->save()) {
            echo "Created active customer package record.\n";
        } else {
            echo "Failed to create customer package: " . implode(', ', $customerPackage->getFirstErrors()) . "\n";
            return;
        }
    } else {
        $customerPackage->status = CustomerPackage::STATUS_ACTIVE;
        $customerPackage->save();
        echo "Updated existing customer package to active.\n";
    }
    
    // Check ROI plan
    $roiPlan = RoiPlan::find()
        ->where(['status' => RoiPlan::STATUS_ACTIVE])
        ->one();
    
    if (!$roiPlan) {
        echo "No active ROI plan found.\n";
        return;
    }
    
    echo "ROI Plan: Rate {$roiPlan->rate}%, Tenure: {$roiPlan->tenure}\n";
    
    // Calculate expected income
    $monthlyIncome = ($package->amount * $roiPlan->rate) / 100;
    $tenureAmount = $package->amount * $roiPlan->tenure;
    
    echo "Expected monthly income: $" . number_format($monthlyIncome, 2) . "\n";
    echo "Total tenure amount: $" . number_format($tenureAmount, 2) . "\n";
    
    // Check existing income
    $existingIncome = Income::find()
        ->where(['customer_id' => $customer->id, 'type' => Income::TYPE_ROI])
        ->sum('amount') ?: 0;
    
    echo "Existing ROI income: $" . number_format($existingIncome, 2) . "\n";
    
    // Test the command
    echo "\n=== Running ROI Generator Command ===\n";
    
    // Import the command controller
    $incomeController = new \app\commands\IncomeController('income', Yii::$app);
    $result = $incomeController->actionRoiGenerator();
    
    echo "Command result: " . ($result === 0 ? 'SUCCESS' : 'ERROR') . "\n";
    
    // Check if income was generated
    $newIncome = Income::find()
        ->where(['customer_id' => $customer->id, 'type' => Income::TYPE_ROI])
        ->orderBy(['created_at' => SORT_DESC])
        ->one();
    
    if ($newIncome) {
        echo "\nNew income generated:\n";
        echo "Amount: $" . number_format($newIncome->amount, 2) . "\n";
        echo "Date: {$newIncome->date}\n";
        echo "Status: {$newIncome->getStatusLabel()}\n";
    } else {
        echo "\nNo new income generated (may already exist for this month).\n";
    }
    
    // Show total income after generation
    $totalIncome = Income::find()
        ->where(['customer_id' => $customer->id, 'type' => Income::TYPE_ROI])
        ->sum('amount') ?: 0;
    
    echo "Total ROI income after generation: $" . number_format($totalIncome, 2) . "\n";
    
    $transaction->commit();
    echo "\n✓ ROI generation test completed successfully!\n";
    
} catch (Exception $e) {
    $transaction->rollBack();
    echo "\n✗ Error during test: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}