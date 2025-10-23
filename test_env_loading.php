<?php
/**
 * Quick Environment Test
 * Test if .env file is loading properly
 */

// Define YII constants
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

// Load Yii application
$config = require __DIR__ . '/config/web.php';
(new yii\web\Application($config));

echo "=== Environment Variables Test ===\n\n";

echo "Environment variables from .env file:\n";
echo "MAILER_HOST: " . (getenv('MAILER_HOST') ?: 'NOT SET') . "\n";
echo "MAILER_USERNAME: " . (getenv('MAILER_USERNAME') ?: 'NOT SET') . "\n";
echo "MAILER_PASSWORD: " . (empty(getenv('MAILER_PASSWORD')) ? 'NOT SET' : 'SET (hidden)') . "\n";
echo "MAILER_PORT: " . (getenv('MAILER_PORT') ?: 'NOT SET') . "\n";
echo "MAILER_ENCRYPTION: " . (getenv('MAILER_ENCRYPTION') ?: 'NOT SET') . "\n";
echo "MAILER_FROM_EMAIL: " . (getenv('MAILER_FROM_EMAIL') ?: 'NOT SET') . "\n";
echo "MAILER_FROM_NAME: " . (getenv('MAILER_FROM_NAME') ?: 'NOT SET') . "\n\n";

echo "Yii application mailer configuration:\n";
$mailerConfig = Yii::$app->params['mailer'];
echo "Host: " . ($mailerConfig['host'] ?? 'NOT SET') . "\n";
echo "Username: " . ($mailerConfig['username'] ?? 'NOT SET') . "\n";
echo "Password: " . (empty($mailerConfig['password']) ? 'NOT SET' : 'SET (hidden)') . "\n";
echo "Port: " . ($mailerConfig['port'] ?? 'NOT SET') . "\n";
echo "Encryption: " . ($mailerConfig['encryption'] ?? 'NOT SET') . "\n";

$fromEmail = array_keys($mailerConfig['from'])[0] ?? 'NOT SET';
echo "From Email: " . $fromEmail . "\n";
echo "From Name: " . ($mailerConfig['from'][$fromEmail] ?? 'NOT SET') . "\n\n";

echo "=== Test Complete ===\n";
echo "If all values show your Mailtrap settings, the .env file is loading correctly!\n";
