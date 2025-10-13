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

echo "=== Final Login Test ===\n";

try {
    // Test 1: AJAX Validation (what happens when user types in fields)
    echo "1. Testing AJAX validation...\n";
    
    $model = Yii::createObject('dektrium\\user\\models\\LoginForm');
    $model->login = 'superadmin';
    $model->password = 'SuperAdmin123!';
    
    // Test validation
    $isValid = $model->validate();
    echo "   Validation result: " . ($isValid ? 'PASSED' : 'FAILED') . "\n";
    
    if (!$isValid) {
        echo "   Validation errors:\n";
        foreach ($model->getErrors() as $attribute => $errors) {
            foreach ($errors as $error) {
                echo "     - {$attribute}: {$error}\n";
            }
        }
    }
    
    // Test AJAX response format
    $ajaxResponse = yii\widgets\ActiveForm::validate($model);
    echo "   AJAX Response: " . json_encode($ajaxResponse) . "\n";
    
    // Test 2: Check if user exists
    echo "\n2. Testing user lookup...\n";
    $finder = Yii::$container->get('dektrium\\user\\Finder');
    $user = $finder->findUserByUsernameOrEmail('superadmin');
    
    if ($user) {
        echo "   ✓ User found: {$user->username} (ID: {$user->id})\n";
        echo "   - Email: {$user->email}\n";
        echo "   - Confirmed: " . ($user->getIsConfirmed() ? 'Yes' : 'No') . "\n";
        echo "   - Blocked: " . ($user->getIsBlocked() ? 'Yes' : 'No') . "\n";
        
        // Test password validation
        echo "\n3. Testing password validation...\n";
        $passwordValid = $user->validatePassword('SuperAdmin123!');
        echo "   Password validation: " . ($passwordValid ? 'PASSED' : 'FAILED') . "\n";
        
        if ($passwordValid) {
            echo "   ✓ Login credentials are correct\n";
        } else {
            echo "   ✗ Password validation failed\n";
        }
    } else {
        echo "   ✗ User not found\n";
    }
    
    echo "\n4. Summary:\n";
    echo "   - Form validation: " . ($isValid ? '✓ Working' : '✗ Failed') . "\n";
    echo "   - AJAX response: " . (empty($ajaxResponse) ? '✓ Valid (no errors)' : '✗ Has errors') . "\n";
    echo "   - User lookup: " . ($user ? '✓ Found' : '✗ Not found') . "\n";
    echo "   - Password check: " . (isset($passwordValid) && $passwordValid ? '✓ Valid' : '✗ Invalid') . "\n";
    
    if ($isValid && $user && isset($passwordValid) && $passwordValid) {
        echo "\n🎉 All tests passed! Login should work correctly.\n";
    } else {
        echo "\n❌ Some tests failed. Check the issues above.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
