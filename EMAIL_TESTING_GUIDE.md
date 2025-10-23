# How to Test Email Credentials

## Current Test Results
Based on the test I just ran, here are the issues found:

### ✅ **Working:**
- Email configuration is loaded correctly
- SMTP transport is being used
- File transport is disabled (emails will be sent via SMTP)

### ❌ **Issues Found:**
1. **SSL Certificate Error**: `SSL operation failed with code 1. OpenSSL Error messages: error:0A000086:SSL routines::certificate verify failed`
2. **Invalid From Email**: `Email "KashFlow System" does not comply with addr-spec of RFC 2822`

## How to Test Your Credentials

### **Method 1: Command Line Test (Recommended)**
```bash
php test_email_credentials.php
```

### **Method 2: Web Interface Test**
1. Visit: `http://your-domain/site/email-credential-test`
2. Enter your email address
3. Click "Send Test Email"

### **Method 3: Using the Existing Email Test Page**
1. Visit: `http://your-domain/site/email-test`
2. Use the form to send a test email

## Step-by-Step Testing Process

### **Step 1: Update Your Credentials**
Edit `config/params.php` and replace the placeholder values:

```php
'mailer' => [
    'host' => 'smtp.gmail.com',  // Your SMTP server
    'username' => 'your-actual-email@gmail.com',  // Your email
    'password' => 'your-actual-app-password',  // Your app password
    'port' => '587',
    'encryption' => 'tls',
    'from' => [
        'noreply@kashflow.com' => 'KashFlow System',  // Fixed format
    ],
],
```

### **Step 2: Test Configuration**
Run the test script:
```bash
php test_email_credentials.php
```

### **Step 3: Fix SSL Issues (if needed)**
If you get SSL certificate errors, you can temporarily disable SSL verification for testing:

Add this to your `config/web.php` mailer configuration:
```php
'mailer' => [
    'class' => \yii\symfonymailer\Mailer::class,
    'viewPath' => '@app/mail',
    'useFileTransport' => false,
    'transport' => [
        'scheme' => 'smtp',
        'host' => $params['mailer']['host'],
        'username' => $params['mailer']['username'],
        'password' => $params['mailer']['password'],
        'port' => $params['mailer']['port'],
        'stream_options' => [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ],
    ],
],
```

**⚠️ Warning**: Only use this for testing. Enable SSL verification in production.

### **Step 4: Test with Real Email**
1. Update the test email address in `test_email_credentials.php` (line 45)
2. Run the test again
3. Check your email inbox

## Gmail Setup (Most Common)

### **For Gmail Users:**
1. **Enable 2-Factor Authentication** on your Gmail account
2. **Generate App Password**:
   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate password for "Mail"
   - Use this password (not your regular password)

3. **Update Configuration**:
```php
'mailer' => [
    'host' => 'smtp.gmail.com',
    'username' => 'your-email@gmail.com',
    'password' => 'your-16-character-app-password',
    'port' => '587',
    'encryption' => 'tls',
],
```

## Other SMTP Providers

### **SendGrid:**
```php
'mailer' => [
    'host' => 'smtp.sendgrid.net',
    'username' => 'apikey',
    'password' => 'your-sendgrid-api-key',
    'port' => '587',
    'encryption' => 'tls',
],
```

### **Mailgun:**
```php
'mailer' => [
    'host' => 'smtp.mailgun.org',
    'username' => 'your-mailgun-smtp-username',
    'password' => 'your-mailgun-smtp-password',
    'port' => '587',
    'encryption' => 'tls',
],
```

## Troubleshooting Common Issues

### **1. "Authentication Failed"**
- Check username and password
- For Gmail: Use App Password, not regular password
- Ensure 2FA is enabled for Gmail

### **2. "Connection Timeout"**
- Verify SMTP host and port
- Check firewall settings
- Try different ports (587, 465, 25)

### **3. "SSL Certificate Error"**
- Update OpenSSL on your server
- Use `verify_peer => false` for testing only
- Check if your server has proper SSL certificates

### **4. "Invalid Email Format"**
- Ensure from email is a valid email address
- Check the from configuration format

### **5. "File Transport Enabled"**
- Set `'useFileTransport' => false` in config/web.php
- Check if emails are being saved to files instead of sent

## Testing Checklist

- [ ] Updated email credentials in `config/params.php`
- [ ] Ran command line test: `php test_email_credentials.php`
- [ ] Checked web interface: `/site/email-credential-test`
- [ ] Verified SSL/TLS settings
- [ ] Tested with real email address
- [ ] Checked email inbox for test email
- [ ] Reviewed application logs for errors

## Next Steps After Testing

Once your credentials are working:

1. **Remove test files** (optional):
   ```bash
   rm test_email_credentials.php
   ```

2. **Update production settings**:
   - Use environment variables for production
   - Enable SSL verification
   - Set proper error handling

3. **Test all email functions**:
   - Contact form
   - User notifications
   - Bulk emails
   - System notifications

## Quick Test Commands

```bash
# Test email configuration
php test_email_credentials.php

# Check if mailer is working
php -r "require 'vendor/autoload.php'; require 'vendor/yiisoft/yii2/Yii.php'; defined('YII_DEBUG') or define('YII_DEBUG', true); \$config = require 'config/web.php'; new yii\web\Application(\$config); echo 'Mailer class: ' . get_class(Yii::\$app->mailer) . PHP_EOL;"

# Test SMTP connection
telnet smtp.gmail.com 587
```
