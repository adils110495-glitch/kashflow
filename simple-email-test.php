<?php
/**
 * Email Test Page
 * Access this via: http://your-domain/simple-email-test.php
 */

// Define YII constants
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

// Load Yii application
$config = require __DIR__ . '/config/web.php';
(new yii\web\Application($config));

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Configuration Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .config-box { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .test-form { background: #e8f4fd; padding: 15px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Email Configuration Test</h1>
    
    <div class="config-box">
        <h3>Current Configuration:</h3>
        <?php
        $mailerConfig = Yii::$app->params['mailer'];
        echo "<strong>Host:</strong> " . $mailerConfig['host'] . "<br>";
        echo "<strong>Username:</strong> " . $mailerConfig['username'] . "<br>";
        echo "<strong>Password:</strong> " . (empty($mailerConfig['password']) ? 'NOT SET' : 'SET (hidden)') . "<br>";
        echo "<strong>Port:</strong> " . $mailerConfig['port'] . "<br>";
        echo "<strong>Encryption:</strong> " . $mailerConfig['encryption'] . "<br>";
        
        $fromEmail = array_keys($mailerConfig['from'])[0];
        $fromName = $mailerConfig['from'][$fromEmail];
        echo "<strong>From Email:</strong> " . $fromEmail . "<br>";
        echo "<strong>From Name:</strong> " . $fromName . "<br>";
        ?>
    </div>

    <div class="test-form">
        <h3>Test Email Sending:</h3>
        <form method="post">
            <label>Test Email Address:</label><br>
            <input type="email" name="test_email" placeholder="your-email@example.com" required style="width: 300px; padding: 5px;"><br><br>
            <button type="submit" name="send_test" style="padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 3px;">Send Test Email</button>
        </form>
    </div>

    <?php
    if (isset($_POST['send_test']) && !empty($_POST['test_email'])) {
        $testEmail = $_POST['test_email'];
        echo "<div class='config-box'>";
        echo "<h3>Test Result:</h3>";
        
        try {
            $mailer = Yii::$app->mailer;
            
            $result = $mailer->compose()
                ->setFrom([$fromEmail => $fromName])
                ->setTo($testEmail)
                ->setSubject('KashFlow Test Email - ' . date('Y-m-d H:i:s'))
                ->setTextBody('This is a test email from KashFlow to verify your email configuration is working properly.')
                ->send();
            
            if ($result) {
                echo "<p class='success'>✅ SUCCESS: Test email sent successfully to: " . htmlspecialchars($testEmail) . "</p>";
                echo "<p class='info'>Check your email inbox (and spam folder) for the test email.</p>";
            } else {
                echo "<p class='error'>❌ FAILED: Email was not sent</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p class='error'>Stack trace:</p><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
        
        echo "</div>";
    }
    ?>

    <div class="config-box">
        <h3>Environment Variables Check:</h3>
        <?php
        echo "<strong>MAILER_HOST:</strong> " . (getenv('MAILER_HOST') ?: 'NOT SET') . "<br>";
        echo "<strong>MAILER_USERNAME:</strong> " . (getenv('MAILER_USERNAME') ?: 'NOT SET') . "<br>";
        echo "<strong>MAILER_PASSWORD:</strong> " . (empty(getenv('MAILER_PASSWORD')) ? 'NOT SET' : 'SET (hidden)') . "<br>";
        echo "<strong>MAILER_PORT:</strong> " . (getenv('MAILER_PORT') ?: 'NOT SET') . "<br>";
        echo "<strong>MAILER_ENCRYPTION:</strong> " . (getenv('MAILER_ENCRYPTION') ?: 'NOT SET') . "<br>";
        echo "<strong>MAILER_FROM_EMAIL:</strong> " . (getenv('MAILER_FROM_EMAIL') ?: 'NOT SET') . "<br>";
        echo "<strong>MAILER_FROM_NAME:</strong> " . (getenv('MAILER_FROM_NAME') ?: 'NOT SET') . "<br>";
        ?>
    </div>

    <div class="config-box">
        <h3>File System Check:</h3>
        <?php
        echo "<strong>.env file exists:</strong> " . (file_exists('.env') ? 'YES' : 'NO') . "<br>";
        echo "<strong>load_env.php exists:</strong> " . (file_exists('load_env.php') ? 'YES' : 'NO') . "<br>";
        echo "<strong>Current working directory:</strong> " . getcwd() . "<br>";
        ?>
    </div>

    <div class="config-box">
        <h3>Next Steps:</h3>
        <ul>
            <li>If you see "NOT SET" for any environment variables, the .env file is not loading properly</li>
            <li>If all variables show your Mailtrap settings, the configuration is working</li>
            <li>Try sending a test email using the form above</li>
            <li>Check your Mailtrap inbox at <a href="https://mailtrap.io" target="_blank">mailtrap.io</a></li>
        </ul>
    </div>
</body>
</html>
