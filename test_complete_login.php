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

echo "=== Testing Complete Login Flow ===\n";

try {
    // Step 1: Test AJAX validation (what happens when ajax=login-form is sent)
    echo "1. Testing AJAX validation...\n";
    
    $model = Yii::createObject('dektrium\\user\\models\\LoginForm');
    $model->login = 'superadmin';
    $model->password = 'SuperAdmin123!';
    
    // Simulate AJAX validation request
    $_POST = [
        'LoginForm' => [
            'login' => 'superadmin',
            'password' => 'SuperAdmin123!',
            'rememberMe' => 0
        ],
        'ajax' => 'login-form'
    ];
    
    Yii::$app->request->setBodyParams($_POST);
    
    if ($model->load(Yii::$app->request->post())) {
        echo "   ✓ Model loaded from POST data\n";
        
        // Test validation
        $isValid = $model->validate();
        if ($isValid) {
            echo "   ✓ AJAX validation passed (this returns [])\n";
            
            // This is what gets returned as JSON for AJAX validation
            $ajaxResponse = yii\widgets\ActiveForm::validate($model);
            echo "   AJAX Response: " . json_encode($ajaxResponse) . "\n";
        } else {
            echo "   ✗ AJAX validation failed\n";
            foreach ($model->getErrors() as $attribute => $errors) {
                foreach ($errors as $error) {
                    echo "     - {$attribute}: {$error}\n";
                }
            }
        }
    }
    
    echo "\n2. Testing actual form submission (without ajax parameter)...\n";
    
    // Step 2: Test actual form submission (what happens when form is submitted normally)
    $model2 = Yii::createObject('dektrium\\user\\models\\LoginForm');
    
    // Simulate normal form submission (no ajax parameter)
    $_POST = [
        'LoginForm' => [
            'login' => 'superadmin',
            'password' => 'SuperAdmin123!',
            'rememberMe' => 0
        ]
        // No 'ajax' parameter - this triggers actual login
    ];
    
    Yii::$app->request->setBodyParams($_POST);
    
    if ($model2->load(Yii::$app->request->post())) {
        echo "   ✓ Model loaded from POST data\n";
        
        // Attempt actual login
        $loginResult = $model2->login();
        
        if ($loginResult) {
            echo "   ✓ Login successful!\n";
            echo "   - User ID: " . Yii::$app->user->id . "\n";
            echo "   - Is Guest: " . (Yii::$app->user->isGuest ? 'Yes' : 'No') . "\n";
            echo "   - Username: " . Yii::$app->user->identity->username . "\n";
        } else {
            echo "   ✗ Login failed\n";
            foreach ($model2->getErrors() as $attribute => $errors) {
                foreach ($errors as $error) {
                    echo "     - {$attribute}: {$error}\n";
                }
            }
        }
    }
    
    echo "\n3. Understanding the flow...\n";
    echo "   - AJAX validation (with 'ajax' parameter) returns [] when valid\n";
    echo "   - Form submission (without 'ajax' parameter) performs actual login\n";
    echo "   - The browser should submit the form again after AJAX validation passes\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";