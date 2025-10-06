<?php

// Bootstrap Yii2 application
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';
new yii\web\Application($config);

use app\models\Customer;
use dektrium\user\models\User;

echo "=== Cleaning up existing sample data ===\n";

// Delete sample customers
$deletedCustomers = Customer::deleteAll(['like', 'name', 'Team Member Level%', false]);
echo "Deleted {$deletedCustomers} sample customers\n";

// Delete sample users
$deletedUsers = User::deleteAll(['like', 'username', 'TL%U%', false]);
echo "Deleted {$deletedUsers} sample users\n";

echo "Sample data cleanup complete.\n";