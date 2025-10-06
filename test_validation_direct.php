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

echo "=== Testing Login Form Validation ===\n";

try {
    // Test 1: Completely empty form
    echo "\n1. Testing completely empty form...\n";
    $loginForm = Yii::createObject('dektrium\\user\\models\\LoginForm');
    $loginForm->login = '';
    $loginForm->password = '';
    $loginForm->rememberMe = false;
    
    $isValid = $loginForm->validate();
    echo "   Validation result: " . ($isValid ? 'PASSED' : 'FAILED') . "\n";
    
    if (!$isValid) {
        echo "   Validation errors:\n";
        foreach ($loginForm->getErrors() as $attribute => $errors) {
            foreach ($errors as $error) {
                echo "     - {$attribute}: {$error}\n";
            }
        }
    } else {
        echo "   ✗ No validation errors found for empty form!\n";
    }
    
    // Test AJAX validation response
    echo "\n   Testing AJAX validation response...\n";
    $ajaxResponse = yii\widgets\ActiveForm::validate($loginForm);
    echo "   AJAX Response: " . json_encode($ajaxResponse) . "\n";
    
    // Test 2: Only login field empty
    echo "\n2. Testing with only login field empty...\n";
    $loginForm2 = Yii::createObject('dektrium\\user\\models\\LoginForm');
    $loginForm2->login = '';
    $loginForm2->password = 'somepassword';
    $loginForm2->rememberMe = false;
    
    $isValid2 = $loginForm2->validate();
    echo "   Validation result: " . ($isValid2 ? 'PASSED' : 'FAILED') . "\n";
    
    if (!$isValid2) {
        echo "   Validation errors:\n";
        foreach ($loginForm2->getErrors() as $attribute => $errors) {
            foreach ($errors as $error) {
                echo "     - {$attribute}: {$error}\n";
            }
        }
    }
    
    $ajaxResponse2 = yii\widgets\ActiveForm::validate($loginForm2);
    echo "   AJAX Response: " . json_encode($ajaxResponse2) . "\n";
    
    // Test 3: Only password field empty
    echo "\n3. Testing with only password field empty...\n";
    $loginForm3 = Yii::createObject('dektrium\\user\\models\\LoginForm');
    $loginForm3->login = 'testuser';
    $loginForm3->password = '';
    $loginForm3->rememberMe = false;
    
    $isValid3 = $loginForm3->validate();
    echo "   Validation result: " . ($isValid3 ? 'PASSED' : 'FAILED') . "\n";
    
    if (!$isValid3) {
        echo "   Validation errors:\n";
        foreach ($loginForm3->getErrors() as $attribute => $errors) {
            foreach ($errors as $error) {
                echo "     - {$attribute}: {$error}\n";
            }
        }
    }
    
    $ajaxResponse3 = yii\widgets\ActiveForm::validate($loginForm3);
    echo "   AJAX Response: " . json_encode($ajaxResponse3) . "\n";
    
    // Test 4: Check form name
    echo "\n4. Checking form configuration...\n";
    $loginForm4 = Yii::createObject('dektrium\\user\\models\\LoginForm');
    echo "   Form name: " . $loginForm4->formName() . "\n";
    echo "   Expected form name in view: login-form\n";
    
    // Test 5: Check validation rules
    echo "\n5. Checking validation rules...\n";
    $rules = $loginForm4->rules();
    echo "   Number of rules: " . count($rules) . "\n";
    foreach ($rules as $i => $rule) {
        if (is_array($rule) && isset($rule[0])) {
            $attributes = is_array($rule[0]) ? implode(', ', $rule[0]) : $rule[0];
            $validator = isset($rule[1]) ? $rule[1] : 'unknown';
            echo "   Rule {$i}: [{$attributes}] => {$validator}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";