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

echo "=== Checking CSRF Configuration ===\n";

try {
    // Check if CSRF validation is enabled
    $request = Yii::$app->request;
    echo "1. CSRF Validation Enabled: " . ($request->enableCsrfValidation ? 'Yes' : 'No') . "\n";
    
    if ($request->enableCsrfValidation) {
        echo "2. CSRF Param Name: " . $request->csrfParam . "\n";
        echo "3. CSRF Token: " . $request->getCsrfToken() . "\n";
        echo "4. CSRF Cookie Name: " . $request->csrfCookie['name'] . "\n";
    }
    
    // Test form submission without CSRF token
    echo "\n=== Testing Form Submission ===\n";
    
    // Simulate POST data without CSRF token
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
    
    $request->setBodyParams($_POST);
    
    // Create registration form
    $model = new app\models\RegistrationForm();
    
    if ($model->load(Yii::$app->request->post())) {
        echo "5. Model loaded successfully\n";
        
        if ($model->validate()) {
            echo "6. Validation passed\n";
        } else {
            echo "6. Validation failed:\n";
            foreach ($model->getErrors() as $attribute => $errors) {
                foreach ($errors as $error) {
                    echo "   - {$attribute}: {$error}\n";
                }
            }
        }
    } else {
        echo "5. Model failed to load from POST data\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}