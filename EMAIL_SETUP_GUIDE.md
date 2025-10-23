# Email Configuration Guide

## Current Issue
Your email sending is failing because the SMTP configuration is missing. The application expects environment variables that are not set.

## Quick Fix Applied
I've updated `config/params.php` to include default values that will work with Gmail SMTP.

## Next Steps

### 1. Update Email Credentials
Edit `config/params.php` and replace these placeholder values with your actual email settings:

```php
'mailer' => [
    'host' => 'smtp.gmail.com',  // Your SMTP host
    'username' => 'your-email@gmail.com',  // Your email
    'password' => 'your-app-password',  // Your app password
    'port' => '587',
    'encryption' => 'tls',
    'from' => [
        'noreply@kashflow.com' => 'KashFlow System',
    ],
],
```

### 2. Gmail Setup (if using Gmail)
1. Enable 2-factor authentication on your Gmail account
2. Generate an "App Password" for this application
3. Use the app password (not your regular password) in the configuration

### 3. Alternative SMTP Providers
You can use other SMTP providers like:
- **SendGrid**: smtp.sendgrid.net (port 587)
- **Mailgun**: smtp.mailgun.org (port 587)
- **Amazon SES**: email-smtp.us-east-1.amazonaws.com (port 587)

### 4. Test Email Functionality
After updating the configuration:
1. Visit `/site/email-test` to test email sending
2. Check the application logs in `runtime/logs/` for any errors
3. Try sending a test email using the `actionSendEmail` method

### 5. Environment Variables (Optional)
For better security, you can set environment variables instead of hardcoding values:

**Windows (PowerShell):**
```powershell
$env:MAILER_HOST="smtp.gmail.com"
$env:MAILER_USERNAME="your-email@gmail.com"
$env:MAILER_PASSWORD="your-app-password"
$env:MAILER_PORT="587"
$env:MAILER_ENCRYPTION="tls"
$env:MAILER_FROM_EMAIL="noreply@kashflow.com"
$env:MAILER_FROM_NAME="KashFlow System"
```

**Linux/Mac:**
```bash
export MAILER_HOST="smtp.gmail.com"
export MAILER_USERNAME="your-email@gmail.com"
export MAILER_PASSWORD="your-app-password"
export MAILER_PORT="587"
export MAILER_ENCRYPTION="tls"
export MAILER_FROM_EMAIL="noreply@kashflow.com"
export MAILER_FROM_NAME="KashFlow System"
```

## Troubleshooting

### Common Issues:
1. **Authentication Failed**: Check username/password
2. **Connection Timeout**: Verify SMTP host and port
3. **SSL/TLS Issues**: Ensure encryption setting matches provider
4. **App Password Required**: Gmail requires app passwords for SMTP

### Debug Mode:
To see detailed error messages, enable debug logging in `config/web.php`:

```php
'log' => [
    'traceLevel' => YII_DEBUG ? 3 : 0,
    'targets' => [
        [
            'class' => 'yii\log\FileTarget',
            'levels' => ['error', 'warning', 'info'],
        ],
    ],
],
```

## Security Notes
- Never commit real email credentials to version control
- Use environment variables for production
- Consider using a dedicated email service for production
- Regularly rotate email passwords/app passwords
