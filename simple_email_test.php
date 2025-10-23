<?php
/**
 * Simple Email Test
 * Test email sending with current configuration
 */

// Define YII constants
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

// Load Yii application
$config = require __DIR__ . '/config/web.php';
(new yii\web\Application($config));

echo "=== Email Test ===\n\n";

// Check configuration
$mailerConfig = Yii::$app->params['mailer'];
echo "Configuration loaded:\n";
echo "Host: " . $mailerConfig['host'] . "\n";
echo "Username: " . $mailerConfig['username'] . "\n";
echo "Password: " . (empty($mailerConfig['password']) ? 'NOT SET' : 'SET') . "\n";
echo "Port: " . $mailerConfig['port'] . "\n";
echo "Encryption: " . $mailerConfig['encryption'] . "\n";

$fromEmail = array_keys($mailerConfig['from'])[0];
$fromName = $mailerConfig['from'][$fromEmail];
echo "From: $fromEmail ($fromName)\n\n";

// Test email sending
echo "Testing email sending...\n";
try {
    $mailer = Yii::$app->mailer;
    
    $result = $mailer->compose()
        ->setFrom([$fromEmail => $fromName])
        ->setTo('test@example.com')
        ->setSubject('Test Email - ' . date('Y-m-d H:i:s'))
        ->setTextBody('This is a test email from KashFlow.')
        ->send();
    
    if ($result) {
        echo "✅ SUCCESS: Email sent successfully!\n";
        echo "✅ Your email configuration is working!\n";
    } else {
        echo "❌ FAILED: Email was not sent\n";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
