<?php
require 'vendor/autoload.php';
require 'vendor/yiisoft/yii2/Yii.php';

$config = require 'config/web.php';
new yii\web\Application($config);

use app\models\Customer;
use dektrium\user\models\User;

// Check for customers
$customers = Customer::find()->joinWith('user')->limit(5)->all();
echo "Found " . count($customers) . " customers:\n";
foreach($customers as $customer) {
    $username = $customer->user ? $customer->user->username : 'No user';
    echo "- ID: {$customer->id}, Name: {$customer->name}, Username: {$username}\n";
}

// Check for users with customer role using raw SQL
$customerUsers = Yii::$app->db->createCommand(
    "SELECT u.id, u.username, u.email FROM user u 
     JOIN auth_assignment aa ON u.id = aa.user_id 
     WHERE aa.item_name = 'customer' LIMIT 5"
)->queryAll();
    
echo "\nFound " . count($customerUsers) . " users with customer role:\n";
foreach($customerUsers as $user) {
    echo "- ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}\n";
}
?>