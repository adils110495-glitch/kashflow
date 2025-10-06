<?php

// Define constants
define('YII_DEBUG', true);
define('YII_ENV', 'dev');

// Include Yii
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// Load application config
$config = require __DIR__ . '/../config/web.php';

// Create application
(new yii\web\Application($config));

use app\models\Customer;
use app\models\Package;

echo "Testing Package Statistics Functionality\n";
echo "==========================================\n\n";

// Test the calculatePackageStats method logic
function testCalculatePackageStats($customers) {
    $packageStats = [
        'free' => ['unpaid' => 0, 'paid' => 0],
        'paid' => ['unpaid' => 0, 'paid' => 0],
        'total' => ['unpaid' => 0, 'paid' => 0]
    ];
    
    foreach ($customers as $customer) {
        $package = $customer->currentPackage;
        
        if (!$package) {
            // No package - count as unpaid
            $packageStats['total']['unpaid']++;
            continue;
        }
        
        $isFreePackage = (strtolower($package->name) === 'free');
        
        // Check if customer has active package through CustomerPackage relation
        $activePackage = $customer->getActiveCustomerPackages()->one();
        $isPaid = ($activePackage && $activePackage->isActive());
        
        if ($isFreePackage) {
            if ($isPaid) {
                $packageStats['free']['paid']++;
            } else {
                $packageStats['free']['unpaid']++;
            }
        } else {
            if ($isPaid) {
                $packageStats['paid']['paid']++;
            } else {
                $packageStats['paid']['unpaid']++;
            }
        }
        
        // Update totals
        if ($isPaid) {
            $packageStats['total']['paid']++;
        } else {
            $packageStats['total']['unpaid']++;
        }
    }
    
    return $packageStats;
}

// Get some sample customers with packages
$customers = Customer::find()
    ->with(['currentPackage', 'activeCustomerPackages'])
    ->limit(20)
    ->all();

echo "Found " . count($customers) . " customers for testing\n\n";

// Test the package stats calculation
$packageStats = testCalculatePackageStats($customers);

echo "Package Statistics Results:\n";
echo "---------------------------\n";
echo "Free Package (Unpaid): " . $packageStats['free']['unpaid'] . "\n";
echo "Free Package (Paid): " . $packageStats['free']['paid'] . "\n";
echo "Paid Packages (Unpaid): " . $packageStats['paid']['unpaid'] . "\n";
echo "Paid Packages (Paid): " . $packageStats['paid']['paid'] . "\n";
echo "Total Unpaid: " . $packageStats['total']['unpaid'] . "\n";
echo "Total Paid: " . $packageStats['total']['paid'] . "\n\n";

// Show sample customer data
echo "Sample Customer Data:\n";
echo "--------------------\n";
foreach (array_slice($customers, 0, 5) as $customer) {
    $packageName = $customer->currentPackage ? $customer->currentPackage->name : 'No Package';
    $activePackage = $customer->getActiveCustomerPackages()->one();
    $status = ($activePackage && $activePackage->isActive()) ? 'active' : 'inactive';
    echo "Customer: {$customer->user->username} | Package: {$packageName} | Status: {$status}\n";
}

echo "\n✓ Package statistics functionality test completed!\n";