<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
defined('YII_ENV_DEV') or define('YII_ENV_DEV', true);

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/config/console.php');

$application = new yii\console\Application($config);
$application->run();

use app\models\Customer;
use app\models\Income;
use dektrium\user\models\User;

echo "Cleaning up test data...\n";

// Delete test customers
$deletedCustomers = Customer::deleteAll(['like', 'name', 'Test Referrer']);
echo "Deleted {$deletedCustomers} test referrer customers\n";

$deletedCustomers = Customer::deleteAll(['like', 'name', 'Test Customer']);
echo "Deleted {$deletedCustomers} test customers\n";

// Delete test users
$deletedUsers = User::deleteAll(['like', 'username', 'testref']);
echo "Deleted {$deletedUsers} test referrer users\n";

$deletedUsers = User::deleteAll(['like', 'username', 'testcustomer']);
echo "Deleted {$deletedUsers} test customer users\n";

// Delete level income records
$deletedIncomes = Income::deleteAll(['type' => 2]);
echo "Deleted {$deletedIncomes} level income records\n";

echo "Cleanup completed!\n";