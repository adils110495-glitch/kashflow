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

// Test login functionality
echo "=== Testing Login Functionality ===\n";

// Test 1: Check if user module is loaded
echo "1. Checking user module...\n";
$userModule = Yii::$app->getModule('user');
if ($userModule) {
    echo "   ✓ User module loaded successfully\n";
    echo "   - Enable confirmation: " . ($userModule->enableConfirmation ? 'Yes' : 'No') . "\n";
    echo "   - Enable unconfirmed login: " . ($userModule->enableUnconfirmedLogin ? 'Yes' : 'No') . "\n";
} else {
    echo "   ✗ User module not found\n";
}

// Test 2: Check finder service
echo "\n2. Testing user finder...\n";
try {
    $finder = Yii::$container->get('dektrium\\user\\Finder');
    echo "   ✓ Finder service available\n";
    
    // Test finding superadmin user
    $user = $finder->findUserByUsernameOrEmail('superadmin');
    if ($user) {
        echo "   ✓ Found superadmin user (ID: {$user->id})\n";
        echo "   - Username: {$user->username}\n";
        echo "   - Email: {$user->email}\n";
        echo "   - Confirmed: " . ($user->getIsConfirmed() ? 'Yes' : 'No') . "\n";
        echo "   - Blocked: " . ($user->getIsBlocked() ? 'Yes' : 'No') . "\n";
    } else {
        echo "   ✗ Superadmin user not found\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error with finder: " . $e->getMessage() . "\n";
}

// Test 3: Test LoginForm validation
echo "\n3. Testing LoginForm validation...\n";
try {
    $loginForm = Yii::createObject('dektrium\\user\\models\\LoginForm');
    $loginForm->login = 'superadmin';
    $loginForm->password = 'SuperAdmin123!';
    
    echo "   Testing validation...\n";
    $isValid = $loginForm->validate();
    
    if ($isValid) {
        echo "   ✓ LoginForm validation passed\n";
    } else {
        echo "   ✗ LoginForm validation failed:\n";
        foreach ($loginForm->getErrors() as $attribute => $errors) {
            foreach ($errors as $error) {
                echo "     - {$attribute}: {$error}\n";
            }
        }
    }
    
    // Test AJAX validation response
    echo "\n   Testing AJAX validation response...\n";
    $ajaxResponse = yii\widgets\ActiveForm::validate($loginForm);
    echo "   AJAX Response: " . json_encode($ajaxResponse) . "\n";
    
} catch (Exception $e) {
    echo "   ✗ Error testing LoginForm: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";