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

echo "=== Debugging HTML Element IDs ===\n\n";

try {
    // Simulate a web request to render the login form
    Yii::$app->request->setUrl('/user/security/login');
    
    // Create the login form model
    $model = Yii::createObject('dektrium\\user\\models\\LoginForm');
    
    echo "1. Model Information:\n";
    echo "   Form name: " . $model->formName() . "\n";
    echo "   Class: " . get_class($model) . "\n\n";
    
    // Create ActiveForm widget to see what IDs it generates
    echo "2. ActiveForm Field Configuration:\n";
    
    // Start output buffering to capture the form HTML
    ob_start();
    
    // Create a mock view
    $view = new yii\web\View();
    Yii::$app->set('view', $view);
    
    // Create ActiveForm
    $form = yii\widgets\ActiveForm::begin([
        'id' => 'login-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
    ]);
    
    // Generate login field
    $loginField = $form->field($model, 'login', [
        'template' => "{input}",
        'inputOptions' => [
            'class' => 'form-control',
            'placeholder' => 'Email or Username',
        ],
    ]);
    
    // Generate password field
    $passwordField = $form->field($model, 'password', [
        'template' => "{input}",
        'inputOptions' => [
            'class' => 'form-control',
            'placeholder' => 'Password',
        ],
    ])->passwordInput();
    
    echo $loginField;
    echo $passwordField;
    
    yii\widgets\ActiveForm::end();
    
    $formHtml = ob_get_clean();
    
    echo "3. Generated HTML:\n";
    echo $formHtml . "\n\n";
    
    // Parse the HTML to extract IDs
    echo "4. Extracted Element Information:\n";
    
    // Use DOMDocument to parse HTML
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($formHtml);
    libxml_clear_errors();
    
    $inputs = $dom->getElementsByTagName('input');
    
    foreach ($inputs as $input) {
        $id = $input->getAttribute('id');
        $name = $input->getAttribute('name');
        $type = $input->getAttribute('type');
        
        if ($type !== 'hidden' && !empty($id)) {
            echo "   Input ID: {$id}\n";
            echo "   Input Name: {$name}\n";
            echo "   Input Type: {$type}\n\n";
        }
    }
    
    // Check error containers
    echo "5. Error Container Information:\n";
    $divs = $dom->getElementsByTagName('div');
    
    foreach ($divs as $div) {
        $class = $div->getAttribute('class');
        if (strpos($class, 'help-block') !== false || strpos($class, 'invalid-feedback') !== false) {
            echo "   Error container class: {$class}\n";
        }
    }
    
    echo "\n6. ActiveForm JavaScript Configuration:\n";
    
    // Get the ActiveForm widget's client options
    $clientOptions = $form->getClientOptions();
    echo "   Client options: " . json_encode($clientOptions, JSON_PRETTY_PRINT) . "\n\n";
    
    echo "7. Analysis:\n";
    echo "   The issue might be that the HTML input IDs don't match\n";
    echo "   the AJAX validation response keys, or the error containers\n";
    echo "   are not properly configured.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Debug Complete ===\n";