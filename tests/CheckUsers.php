<?php

// Bootstrap Yii2 application
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';
new yii\web\Application($config);

use dektrium\user\models\User;
use app\models\Customer;

echo "=== Users in Database ===\n";
$users = User::find()->all();
foreach($users as $user) {
    echo "- ID: {$user->id}, Username: {$user->username}, Email: {$user->email}\n";
}

echo "\n=== Customers and their User relationships ===\n";
$customers = Customer::find()->joinWith('user')->all();
foreach($customers as $customer) {
    $username = $customer->user ? $customer->user->username : 'No user';
    echo "- Customer ID: {$customer->id}, Name: {$customer->name}, User ID: {$customer->user_id}, Username: {$username}, Referral Code: {$customer->referral_code}\n";
}

echo "\n=== Testing Team Relationships ===\n";
// Find customers who could be referrers (have usernames)
$potentialReferrers = Customer::find()
    ->joinWith('user')
    ->where(['not', ['user.username' => null]])
    ->all();

foreach($potentialReferrers as $referrer) {
    $username = $referrer->user->username;
    $directTeam = Customer::find()
        ->where(['referral_code' => $username])
        ->all();
    
    if (count($directTeam) > 0) {
        echo "Referrer: {$referrer->name} (Username: {$username}) has " . count($directTeam) . " direct team members:\n";
        foreach($directTeam as $member) {
            echo "  - {$member->name}\n";
        }
    }
}