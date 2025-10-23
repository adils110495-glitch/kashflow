<?php
/**
 * Web Environment Test
 * Test environment variables in web context
 */

// Define YII constants
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

// Load Yii application
$config = require __DIR__ . '/config/web.php';
(new yii\web\Application($config));

echo "<h2>Environment Variables Test</h2>";

echo "<h3>Direct Environment Variables:</h3>";
echo "MAILER_HOST: " . (getenv('MAILER_HOST') ?: 'NOT SET') . "<br>";
echo "MAILER_USERNAME: " . (getenv('MAILER_USERNAME') ?: 'NOT SET') . "<br>";
echo "MAILER_PASSWORD: " . (empty(getenv('MAILER_PASSWORD')) ? 'NOT SET' : 'SET (hidden)') . "<br>";
echo "MAILER_PORT: " . (getenv('MAILER_PORT') ?: 'NOT SET') . "<br>";
echo "MAILER_ENCRYPTION: " . (getenv('MAILER_ENCRYPTION') ?: 'NOT SET') . "<br>";
echo "MAILER_FROM_EMAIL: " . (getenv('MAILER_FROM_EMAIL') ?: 'NOT SET') . "<br>";
echo "MAILER_FROM_NAME: " . (getenv('MAILER_FROM_NAME') ?: 'NOT SET') . "<br><br>";

echo "<h3>Yii Application Configuration:</h3>";
$mailerConfig = Yii::$app->params['mailer'];
echo "Host: " . ($mailerConfig['host'] ?? 'NOT SET') . "<br>";
echo "Username: " . ($mailerConfig['username'] ?? 'NOT SET') . "<br>";
echo "Password: " . (empty($mailerConfig['password']) ? 'NOT SET' : 'SET (hidden)') . "<br>";
echo "Port: " . ($mailerConfig['port'] ?? 'NOT SET') . "<br>";
echo "Encryption: " . ($mailerConfig['encryption'] ?? 'NOT SET') . "<br>";

$fromEmail = array_keys($mailerConfig['from'])[0] ?? 'NOT SET';
echo "From Email: " . $fromEmail . "<br>";
echo "From Name: " . ($mailerConfig['from'][$fromEmail] ?? 'NOT SET') . "<br><br>";

echo "<h3>Test Email Sending:</h3>";
try {
    $mailer = Yii::$app->mailer;
    $fromEmail = array_keys($mailerConfig['from'])[0];
    $fromName = $mailerConfig['from'][$fromEmail];
    
    $result = $mailer->compose()
        ->setFrom([$fromEmail => $fromName])
        ->setTo('test@example.com')
        ->setSubject('Test Email - ' . date('Y-m-d H:i:s'))
        ->setTextBody('This is a test email to verify configuration.')
        ->send();
    
    if ($result) {
        echo "✅ Email sent successfully!<br>";
    } else {
        echo "❌ Failed to send email<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Debug Information:</h3>";
echo "Current working directory: " . getcwd() . "<br>";
echo ".env file exists: " . (file_exists('.env') ? 'YES' : 'NO') . "<br>";
echo "load_env.php exists: " . (file_exists('load_env.php') ? 'YES' : 'NO') . "<br>";
echo "config/web.php exists: " . (file_exists('config/web.php') ? 'YES' : 'NO') . "<br>";
