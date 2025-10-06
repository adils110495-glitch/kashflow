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

echo "=== Testing CSRF Token Fix ===\n";

try {
    // Get CSRF token
    $csrfToken = Yii::$app->request->getCsrfToken();
    echo "1. CSRF Token: " . substr($csrfToken, 0, 20) . "...\n";
    
    // Test 1: Form submission WITHOUT CSRF token (should fail)
    echo "\n2. Testing without CSRF token...\n";
    $_POST = [
        'RegistrationForm' => [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'country_id' => 1,
            'mobile_no' => '1234567890',
            'password' => 'password123',
            'password_repeat' => 'password123'
        ]
    ];
    
    Yii::$app->request->setBodyParams($_POST);
    
    try {
        $model1 = new app\models\RegistrationForm();
        if ($model1->load(Yii::$app->request->post())) {
            echo "   Model loaded: YES\n";
            if ($model1->validate()) {
                echo "   Validation: PASSED\n";
            } else {
                echo "   Validation: FAILED\n";
                foreach ($model1->getErrors() as $attr => $errors) {
                    echo "     - $attr: " . implode(', ', $errors) . "\n";
                }
            }
        } else {
            echo "   Model loaded: NO\n";
        }
    } catch (Exception $e) {
        echo "   Error: " . $e->getMessage() . "\n";
    }
    
    // Test 2: Form submission WITH CSRF token (should work)
    echo "\n3. Testing with CSRF token...\n";
    $_POST = [
        'RegistrationForm' => [
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'country_id' => 1,
            'mobile_no' => '1234567891',
            'password' => 'password123',
            'password_repeat' => 'password123'
        ],
        '_csrf' => $csrfToken
    ];
    
    Yii::$app->request->setBodyParams($_POST);
    
    try {
        $model2 = new app\models\RegistrationForm();
        if ($model2->load(Yii::$app->request->post())) {
            echo "   Model loaded: YES\n";
            if ($model2->validate()) {
                echo "   Validation: PASSED\n";
                echo "   ✓ CSRF token fixes the validation issue!\n";
            } else {
                echo "   Validation: FAILED\n";
                foreach ($model2->getErrors() as $attr => $errors) {
                    echo "     - $attr: " . implode(', ', $errors) . "\n";
                }
            }
        } else {
            echo "   Model loaded: NO\n";
        }
    } catch (Exception $e) {
        echo "   Error: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}