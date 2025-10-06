<?php

// Bootstrap the Yii2 application
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';
$app = new yii\web\Application($config);

use app\models\Package;
use app\models\Customer;
use app\models\CustomerPackage;

try {
    echo "=== Available Packages ===\n";
    $packages = Package::find()->all();
    
    foreach ($packages as $package) {
        echo "ID: {$package->id}\n";
        echo "Name: {$package->name}\n";
        echo "Amount: {$package->amount}\n";
        echo "Fee: {$package->fee}\n";
        echo "Status: {$package->status}\n";
        echo "---\n";
    }
    
    echo "\n=== Package Distribution Among Customers ===\n";
    
    // Count customers by package
    $packageCounts = [];
    $customers = Customer::find()->with('currentPackage')->all();
    
    foreach ($customers as $customer) {
        if ($customer->currentPackage) {
            $packageName = $customer->currentPackage->name;
            if (!isset($packageCounts[$packageName])) {
                $packageCounts[$packageName] = 0;
            }
            $packageCounts[$packageName]++;
        } else {
            if (!isset($packageCounts['No Package'])) {
                $packageCounts['No Package'] = 0;
            }
            $packageCounts['No Package']++;
        }
    }
    
    foreach ($packageCounts as $packageName => $count) {
        echo "{$packageName}: {$count} customers\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}