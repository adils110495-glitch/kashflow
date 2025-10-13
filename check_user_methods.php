<?php

// Define constants
define('YII_DEBUG', true);
define('YII_ENV', 'dev');
define('YII_ENV_DEV', true);

// Include Yii
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

// Load configuration
$config = require __DIR__ . '/config/web.php';

// Create application
$app = new yii\web\Application($config);

echo "=== Checking User Model Methods ===\n";

try {
    $user = app\models\User::findOne(1);
    if ($user) {
        echo "User found: {$user->username}\n";
        echo "Available methods:\n";
        
        $methods = get_class_methods($user);
        foreach ($methods as $method) {
            if (strpos($method, 'validate') !== false || strpos($method, 'password') !== false) {
                echo "  - {$method}\n";
            }
        }
        
        // Test different password validation methods
        echo "\nTesting password validation methods:\n";
        
        if (method_exists($user, 'validatePassword')) {
            echo "  validatePassword method exists\n";
            $result = $user->validatePassword('SuperAdmin123!');
            echo "  validatePassword result: " . ($result ? 'true' : 'false') . "\n";
        }
        
        if (method_exists($user, 'checkPassword')) {
            echo "  checkPassword method exists\n";
            $result = $user->checkPassword('SuperAdmin123!');
            echo "  checkPassword result: " . ($result ? 'true' : 'false') . "\n";
        }
        
        // Check parent class methods
        echo "\nParent class: " . get_parent_class($user) . "\n";
        $parentMethods = get_class_methods(get_parent_class($user));
        foreach ($parentMethods as $method) {
            if (strpos($method, 'validate') !== false || strpos($method, 'password') !== false) {
                echo "  Parent method: {$method}\n";
            }
        }
        
    } else {
        echo "User not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Complete ===\n";
