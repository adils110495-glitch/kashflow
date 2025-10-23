# Email Functionality Documentation

## Overview
The SiteController has been enhanced with comprehensive email functionality that allows sending emails in various ways throughout the KashFlow application.

## Available Actions

### 1. `actionSendEmail()`
Sends a single email to one recipient.

**URL:** `/site/send-email`  
**Method:** POST  
**Parameters:**
- `to` (required): Recipient email address
- `subject` (required): Email subject
- `message` (required): Email content (HTML supported)
- `from` (optional): Sender email (defaults to admin email)
- `fromName` (optional): Sender name (defaults to "KashFlow System")

**Example Usage:**
```php
// Via form submission
<form method="post" action="/site/send-email">
    <input type="email" name="to" required>
    <input type="text" name="subject" required>
    <textarea name="message" required></textarea>
    <button type="submit">Send Email</button>
</form>

// Via AJAX
$.post('/site/send-email', {
    to: 'user@example.com',
    subject: 'Test Email',
    message: 'This is a test email message'
});
```

### 2. `actionSendBulkEmail()`
Sends emails to multiple recipients.

**URL:** `/site/send-bulk-email`  
**Method:** POST  
**Parameters:**
- `recipients` (required): Array of email addresses or comma-separated string
- `subject` (required): Email subject
- `message` (required): Email content
- `from` (optional): Sender email
- `fromName` (optional): Sender name

**Example Usage:**
```php
// Via form submission
<form method="post" action="/site/send-bulk-email">
    <textarea name="recipients" placeholder="email1@example.com,email2@example.com"></textarea>
    <input type="text" name="subject" required>
    <textarea name="message" required></textarea>
    <button type="submit">Send Bulk Email</button>
</form>

// Via AJAX
$.post('/site/send-bulk-email', {
    recipients: ['user1@example.com', 'user2@example.com'],
    subject: 'Bulk Email Subject',
    message: 'Bulk email message content'
});
```

### 3. `actionSendNotification()`
Sends pre-formatted notification emails to users.

**URL:** `/site/send-notification`  
**Method:** POST  
**Parameters:**
- `userId` (required): User ID to send notification to
- `type` (optional): Notification type (default: 'general')
- `data` (optional): Additional data for the notification

**Supported Notification Types:**
- `welcome`: Welcome email for new users
- `withdrawal_approved`: Withdrawal request approved
- `withdrawal_rejected`: Withdrawal request rejected
- `kyc_approved`: KYC verification approved
- `kyc_rejected`: KYC verification rejected
- `package_upgrade`: Package upgrade successful
- `general`: General notification

**Example Usage:**
```php
// Via form submission
<form method="post" action="/site/send-notification">
    <input type="number" name="userId" required>
    <select name="type">
        <option value="welcome">Welcome Email</option>
        <option value="withdrawal_approved">Withdrawal Approved</option>
        <!-- other options -->
    </select>
    <textarea name="data" placeholder='{"amount": "1000", "method": "UPI"}'></textarea>
    <button type="submit">Send Notification</button>
</form>

// Via AJAX
$.post('/site/send-notification', {
    userId: 1,
    type: 'withdrawal_approved',
    data: {
        amount: '1000',
        method: 'UPI',
        transaction_id: 'TXN123456'
    }
});
```

### 4. `actionEmailTest()`
Test page for email functionality.

**URL:** `/site/email-test`  
**Method:** GET  
**Description:** Provides a user interface to test all email functionality.

## Static Helper Methods

### `SiteController::sendEmail()`
Static method to send emails from anywhere in the application.

```php
$result = SiteController::sendEmail(
    'user@example.com',
    'Test Subject',
    'Test message content',
    'sender@example.com',  // optional
    'Sender Name'           // optional
);

if ($result) {
    echo "Email sent successfully";
} else {
    echo "Failed to send email";
}
```

### `SiteController::sendNotificationEmail()`
Static method to send notification emails.

```php
$result = SiteController::sendNotificationEmail(
    1,  // userId
    'withdrawal_approved',  // type
    [   // data
        'amount' => '1000',
        'method' => 'UPI',
        'transaction_id' => 'TXN123456'
    ]
);
```

### `SiteController::sendBulkEmail()`
Static method to send bulk emails.

```php
$result = SiteController::sendBulkEmail(
    ['user1@example.com', 'user2@example.com'],  // recipients
    'Bulk Email Subject',
    'Bulk email message content'
);

echo "Success: " . $result['success_count'];
echo "Errors: " . $result['error_count'];
```

## Email Templates

### Template Structure
Email templates are located in `views/mail/` directory and use the layout in `views/mail/layout.php`.

### Available Templates
- `welcome.php`: Welcome email for new users
- `withdrawal-approved.php`: Withdrawal approval notification
- `withdrawal-rejected.php`: Withdrawal rejection notification
- `kyc-approved.php`: KYC approval notification
- `kyc-rejected.php`: KYC rejection notification
- `package-upgrade.php`: Package upgrade notification

### Creating New Templates
1. Create a new PHP file in `views/mail/` directory
2. Use the following structure:

```php
<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user \dektrium\user\models\User */
/* @var $customer \app\models\Customer|null */
/* @var $data array */

$this->title = 'Your Email Title';
?>

<h2>Your Email Heading</h2>
<p>Your email content here...</p>

<div class="info-box">
    <h3>Important Information:</h3>
    <p>Additional details...</p>
</div>
```

## Integration Examples

### 1. Send Welcome Email After Registration
```php
// In your registration controller
public function actionRegister()
{
    // ... registration logic ...
    
    if ($user->save()) {
        // Send welcome email
        SiteController::sendNotificationEmail($user->id, 'welcome');
        
        return $this->redirect(['login']);
    }
}
```

### 2. Send Withdrawal Approval Email
```php
// In your withdrawal controller
public function actionApproveWithdrawal($id)
{
    $withdrawal = Withdrawal::findOne($id);
    
    if ($withdrawal && $withdrawal->approve()) {
        // Send approval email
        SiteController::sendNotificationEmail(
            $withdrawal->customer->user_id,
            'withdrawal_approved',
            [
                'amount' => $withdrawal->amount,
                'method' => $withdrawal->withdrawal_method,
                'transaction_id' => $withdrawal->transaction_id
            ]
        );
        
        Yii::$app->session->setFlash('success', 'Withdrawal approved and email sent.');
    }
}
```

### 3. Send Bulk Newsletter
```php
// In your admin controller
public function actionSendNewsletter()
{
    $customers = Customer::find()->with('user')->all();
    $recipients = [];
    
    foreach ($customers as $customer) {
        if ($customer->user && $customer->user->email) {
            $recipients[] = $customer->user->email;
        }
    }
    
    $result = SiteController::sendBulkEmail(
        $recipients,
        'Monthly Newsletter - KashFlow',
        '<h1>Monthly Newsletter</h1><p>Your newsletter content...</p>'
    );
    
    echo "Newsletter sent to {$result['success_count']} recipients";
}
```

## Security Features

### 1. POST-Only Actions
All email actions only accept POST requests to prevent CSRF attacks.

### 2. Email Validation
All email addresses are validated using PHP's `filter_var()` function.

### 3. Error Handling
Comprehensive error handling with logging for debugging.

### 4. Rate Limiting
Consider implementing rate limiting for production use.

## Configuration

### Mail Configuration
Ensure your `config/web.php` has proper mail configuration:

```php
'components' => [
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'useFileTransport' => false, // Set to true for testing
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => 'your-smtp-host',
            'username' => 'your-username',
            'password' => 'your-password',
            'port' => '587',
            'encryption' => 'tls',
        ],
    ],
],
```

### Admin Email Configuration
Set the admin email in `config/params.php`:

```php
return [
    'adminEmail' => 'admin@kashflow.com',
    'supportEmail' => 'support@kashflow.com',
];
```

## Testing

### 1. Use the Test Page
Visit `/site/email-test` to test all email functionality through the web interface.

### 2. Enable File Transport
For testing, set `useFileTransport => true` in mail configuration. Emails will be saved as files instead of being sent.

### 3. Check Logs
Email sending is logged in the application logs. Check `runtime/logs/app.log` for email-related entries.

## Best Practices

### 1. Use Templates
Always use email templates for consistent branding and formatting.

### 2. Handle Errors Gracefully
Always check the return value of email sending methods and handle errors appropriately.

### 3. Log Email Activities
Email sending is automatically logged, but you can add additional logging for specific use cases.

### 4. Test Email Content
Always test email content in different email clients before sending to users.

### 5. Respect User Preferences
Consider implementing email preferences for users to opt-out of certain types of emails.

## Troubleshooting

### Common Issues

1. **Emails not being sent**
   - Check mail configuration
   - Verify SMTP credentials
   - Check application logs

2. **Template not found errors**
   - Ensure template files exist in `views/mail/`
   - Check file permissions

3. **Invalid email format errors**
   - Validate email addresses before sending
   - Use proper email validation

4. **Permission errors**
   - Check file permissions for mail templates
   - Ensure web server can read template files

### Debug Mode
Enable debug mode in `config/web.php` to see detailed error messages:

```php
'components' => [
    'log' => [
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning', 'info'],
            ],
        ],
    ],
],
```
