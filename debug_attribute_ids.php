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

echo "=== Debugging Attribute ID Mismatch ===\n\n";

try {
    // Create the login form model
    $model = Yii::createObject('dektrium\\user\\models\\LoginForm');
    
    echo "1. Form Configuration:\n";
    echo "   Form name: " . $model->formName() . "\n";
    echo "   Expected form ID in HTML: login-form\n\n";
    
    // Test validation with empty fields
    $model->login = '';
    $model->password = '';
    $model->rememberMe = false;
    
    $isValid = $model->validate();
    
    echo "2. Validation Results:\n";
    echo "   Is valid: " . ($isValid ? 'Yes' : 'No') . "\n";
    
    if (!$isValid) {
        echo "   Raw validation errors:\n";
        foreach ($model->getErrors() as $attribute => $errors) {
            foreach ($errors as $error) {
                echo "     - {$attribute}: {$error}\n";
            }
        }
    }
    
    echo "\n3. AJAX Validation Response:\n";
    $ajaxResponse = yii\widgets\ActiveForm::validate($model);
    echo "   AJAX Response: " . json_encode($ajaxResponse, JSON_PRETTY_PRINT) . "\n\n";
    
    echo "4. Expected JavaScript Attribute IDs:\n";
    
    // Simulate what the JavaScript expects
    $formName = strtolower($model->formName());
    $expectedLoginId = $formName . '-login';
    $expectedPasswordId = $formName . '-password';
    
    echo "   Expected login attribute ID: {$expectedLoginId}\n";
    echo "   Expected password attribute ID: {$expectedPasswordId}\n\n";
    
    echo "5. Actual AJAX Response Keys:\n";
    if (!empty($ajaxResponse)) {
        foreach (array_keys($ajaxResponse) as $key) {
            echo "   Actual key: {$key}\n";
        }
    } else {
        echo "   No keys in AJAX response\n";
    }
    
    echo "\n6. Key Matching Analysis:\n";
    if (!empty($ajaxResponse)) {
        $actualKeys = array_keys($ajaxResponse);
        
        echo "   Login field match: ";
        if (in_array($expectedLoginId, $actualKeys)) {
            echo "✓ MATCH\n";
        } else {
            echo "✗ MISMATCH\n";
            echo "     Expected: {$expectedLoginId}\n";
            echo "     Available: " . implode(', ', $actualKeys) . "\n";
        }
        
        echo "   Password field match: ";
        if (in_array($expectedPasswordId, $actualKeys)) {
            echo "✓ MATCH\n";
        } else {
            echo "✗ MISMATCH\n";
            echo "     Expected: {$expectedPasswordId}\n";
            echo "     Available: " . implode(', ', $actualKeys) . "\n";
        }
    }
    
    echo "\n7. Solution Analysis:\n";
    echo "   The issue is that the JavaScript yii.activeForm.js expects attribute IDs\n";
    echo "   to match the form field names, but the server returns different keys.\n";
    echo "   \n";
    echo "   Server returns: " . implode(', ', array_keys($ajaxResponse)) . "\n";
    echo "   JavaScript expects: {$expectedLoginId}, {$expectedPasswordId}\n";
    echo "   \n";
    echo "   This mismatch prevents the JavaScript from finding and displaying\n";
    echo "   the validation errors in the UI.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Debug Complete ===\n";