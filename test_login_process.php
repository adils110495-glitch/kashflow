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

echo "=== Testing Complete Login Process ===\n";

// Simulate the complete login process
try {
    // Create LoginForm
    $loginForm = Yii::createObject('dektrium\\user\\models\\LoginForm');
    $loginForm->login = 'superadmin';
    $loginForm->password = 'SuperAdmin123!';
    $loginForm->rememberMe = false;
    
    echo "1. Testing form validation...\n";
    if ($loginForm->validate()) {
        echo "   ✓ Form validation passed\n";
        
        echo "2. Testing actual login...\n";
        $loginResult = $loginForm->login();
        
        if ($loginResult) {
            echo "   ✓ Login successful\n";
            echo "   - User ID: " . Yii::$app->user->id . "\n";
            echo "   - Is Guest: " . (Yii::$app->user->isGuest ? 'Yes' : 'No') . "\n";
        } else {
            echo "   ✗ Login failed\n";
            echo "   Errors:\n";
            foreach ($loginForm->getErrors() as $attribute => $errors) {
                foreach ($errors as $error) {
                    echo "     - {$attribute}: {$error}\n";
                }
            }
        }
    } else {
        echo "   ✗ Form validation failed\n";
        foreach ($loginForm->getErrors() as $attribute => $errors) {
            foreach ($errors as $error) {
                echo "     - {$attribute}: {$error}\n";
            }
        }
    }
    
    echo "\n3. Testing SecurityController simulation...\n";
    
    // Simulate what happens in SecurityController::actionLogin()
    $model = Yii::createObject('dektrium\\user\\models\\LoginForm');
    
    // Simulate POST data
    $_POST = [
        'login-form' => [
            'login' => 'superadmin',
            'password' => 'SuperAdmin123!',
            'rememberMe' => 0
        ]
    ];
    
    // Simulate request
    Yii::$app->request->setBodyParams($_POST);
    
    if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
        echo "   ✓ SecurityController simulation successful\n";
        echo "   - User would be redirected after login\n";
    } else {
        echo "   ✗ SecurityController simulation failed\n";
        if ($model->hasErrors()) {
            foreach ($model->getErrors() as $attribute => $errors) {
                foreach ($errors as $error) {
                    echo "     - {$attribute}: {$error}\n";
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";