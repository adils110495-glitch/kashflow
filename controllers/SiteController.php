<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\mail\MailerInterface;
use yii\web\BadRequestHttpException;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        // Use a minimal layout for the login page
        $this->layout = '@app/views/layouts/main-login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Email test page for testing email functionality.
     *
     * @return string
     */
    public function actionEmailTest()
    {
        $model = new ContactForm();
        return $this->render('email-test', [
            'model' => $model,
        ]);
    }

    /**
     * Email credential test page for testing SMTP configuration.
     *
     * @return string
     */
    public function actionEmailCredentialTest()
    {
        return $this->render('email-credential-test');
    }

    /**
     * Send email action.
     * This action can be used to send emails for various purposes.
     *
     * @return Response|string
     */
    public function actionSendEmail()
    {

        // Get email parameters from request
        $to = 'adils335@gmail.com';
        $subject = 'registration email';
        $message = 'This is a test email';
        $from = Yii::$app->request->post('from', Yii::$app->params['adminEmail'] ?? 'noreply@kashflow.com');
        $fromName = Yii::$app->request->post('fromName', 'KashFlow System');

        // Validate required parameters
        if (empty($to) || empty($subject) || empty($message)) {
            Yii::$app->session->setFlash('error', 'Missing required email parameters: to, subject, and message are required.');
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }

        // Validate email format
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            Yii::$app->session->setFlash('error', 'Invalid email address format.');
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }

        try {
            // Send email using Yii2 mailer
            $mailer = Yii::$app->mailer;
            
            $result = $mailer->compose()
                ->setFrom([$from => $fromName])
                ->setTo($to)
                ->setSubject($subject)
                ->setHtmlBody($message)
                ->send();

            if ($result) {
                Yii::$app->session->setFlash('success', 'Email sent successfully to ' . $to);
                Yii::info("Email sent successfully to: {$to}, Subject: {$subject}", 'email');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to send email. Please try again.');
                Yii::error("Failed to send email to: {$to}, Subject: {$subject}", 'email');
            }

        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'An error occurred while sending the email: ' . $e->getMessage());
            Yii::error("Email sending error: " . $e->getMessage(), 'email');
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Send bulk email action.
     * This action can be used to send emails to multiple recipients.
     *
     * @return Response|string
     */
    public function actionSendBulkEmail()
    {
        // Only allow POST requests for security
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException('Only POST requests are allowed.');
        }

        // Get email parameters from request
        $recipients = Yii::$app->request->post('recipients', []);
        $subject = Yii::$app->request->post('subject');
        $message = Yii::$app->request->post('message');
        $from = Yii::$app->request->post('from', Yii::$app->params['adminEmail'] ?? 'noreply@kashflow.com');
        $fromName = Yii::$app->request->post('fromName', 'KashFlow System');

        // Validate required parameters
        if (empty($recipients) || empty($subject) || empty($message)) {
            Yii::$app->session->setFlash('error', 'Missing required email parameters: recipients, subject, and message are required.');
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }

        // Ensure recipients is an array
        if (!is_array($recipients)) {
            $recipients = [$recipients];
        }

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            $mailer = Yii::$app->mailer;
            
            foreach ($recipients as $recipient) {
                // Validate email format
                if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Invalid email format: {$recipient}";
                    $errorCount++;
                    continue;
                }

                try {
                    $result = $mailer->compose()
                        ->setFrom([$from => $fromName])
                        ->setTo($recipient)
                        ->setSubject($subject)
                        ->setHtmlBody($message)
                        ->send();

                    if ($result) {
                        $successCount++;
                        Yii::info("Bulk email sent successfully to: {$recipient}", 'email');
                    } else {
                        $errorCount++;
                        $errors[] = "Failed to send email to: {$recipient}";
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Error sending to {$recipient}: " . $e->getMessage();
                    Yii::error("Bulk email error for {$recipient}: " . $e->getMessage(), 'email');
                }
            }

            // Set flash message with results
            if ($successCount > 0) {
                Yii::$app->session->setFlash('success', "Bulk email sent successfully to {$successCount} recipient(s).");
            }
            if ($errorCount > 0) {
                Yii::$app->session->setFlash('error', "Failed to send email to {$errorCount} recipient(s). " . implode(', ', $errors));
            }

        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'An error occurred while sending bulk emails: ' . $e->getMessage());
            Yii::error("Bulk email sending error: " . $e->getMessage(), 'email');
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Send notification email action.
     * This action can be used to send notification emails to users.
     *
     * @return Response|string
     */
    public function actionSendNotification()
    {
        // Only allow POST requests for security
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException('Only POST requests are allowed.');
        }

        // Get notification parameters from request
        $userId = Yii::$app->request->post('userId');
        $type = Yii::$app->request->post('type', 'general');
        $data = Yii::$app->request->post('data', []);

        // Validate required parameters
        if (empty($userId)) {
            Yii::$app->session->setFlash('error', 'User ID is required for notification.');
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }

        try {
            // Get user information
            $user = \dektrium\user\models\User::findOne($userId);
            if (!$user) {
                Yii::$app->session->setFlash('error', 'User not found.');
                return $this->redirect(Yii::$app->request->referrer ?: ['index']);
            }

            // Get customer information if available
            $customer = \app\models\Customer::find()->where(['user_id' => $userId])->one();

            // Prepare email content based on notification type
            $emailContent = $this->prepareNotificationEmail($type, $data, $user, $customer);

            if (!$emailContent) {
                Yii::$app->session->setFlash('error', 'Invalid notification type.');
                return $this->redirect(Yii::$app->request->referrer ?: ['index']);
            }

            // Send notification email
            $mailer = Yii::$app->mailer;
            $result = $mailer->compose()
                ->setFrom([Yii::$app->params['adminEmail'] ?? 'noreply@kashflow.com' => 'KashFlow Notifications'])
                ->setTo($user->email)
                ->setSubject($emailContent['subject'])
                ->setHtmlBody($emailContent['body'])
                ->send();

            if ($result) {
                Yii::$app->session->setFlash('success', 'Notification email sent successfully to ' . $user->email);
                Yii::info("Notification email sent to user {$userId} ({$user->email}), Type: {$type}", 'email');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to send notification email.');
                Yii::error("Failed to send notification email to user {$userId}", 'email');
            }

        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'An error occurred while sending notification: ' . $e->getMessage());
            Yii::error("Notification email error: " . $e->getMessage(), 'email');
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Prepare notification email content based on type.
     *
     * @param string $type
     * @param array $data
     * @param \dektrium\user\models\User $user
     * @param \app\models\Customer|null $customer
     * @return array|null
     */
    private function prepareNotificationEmail($type, $data, $user, $customer = null)
    {
        $customerName = $customer ? $customer->name : $user->username;
        
        switch ($type) {
            case 'welcome':
                return [
                    'subject' => 'Welcome to KashFlow!',
                    'body' => $this->renderPartial('@app/views/mail/welcome', [
                        'user' => $user,
                        'customer' => $customer,
                        'data' => $data
                    ])
                ];
                
            case 'withdrawal_approved':
                return [
                    'subject' => 'Withdrawal Request Approved - KashFlow',
                    'body' => $this->renderPartial('@app/views/mail/withdrawal-approved', [
                        'user' => $user,
                        'customer' => $customer,
                        'data' => $data
                    ])
                ];
                
            case 'withdrawal_rejected':
                return [
                    'subject' => 'Withdrawal Request Update - KashFlow',
                    'body' => $this->renderPartial('@app/views/mail/withdrawal-rejected', [
                        'user' => $user,
                        'customer' => $customer,
                        'data' => $data
                    ])
                ];
                
            case 'kyc_approved':
                return [
                    'subject' => 'KYC Verification Approved - KashFlow',
                    'body' => $this->renderPartial('@app/views/mail/kyc-approved', [
                        'user' => $user,
                        'customer' => $customer,
                        'data' => $data
                    ])
                ];
                
            case 'kyc_rejected':
                return [
                    'subject' => 'KYC Verification Update - KashFlow',
                    'body' => $this->renderPartial('@app/views/mail/kyc-rejected', [
                        'user' => $user,
                        'customer' => $customer,
                        'data' => $data
                    ])
                ];
                
            case 'package_upgrade':
                return [
                    'subject' => 'Package Upgrade Successful - KashFlow',
                    'body' => $this->renderPartial('@app/views/mail/package-upgrade', [
                        'user' => $user,
                        'customer' => $customer,
                        'data' => $data
                    ])
                ];
                
            case 'general':
            default:
                return [
                    'subject' => $data['subject'] ?? 'Notification from KashFlow',
                    'body' => $data['message'] ?? 'You have a new notification from KashFlow.'
                ];
        }
    }

    /**
     * Static method to send email from anywhere in the application.
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string|null $from
     * @param string|null $fromName
     * @return bool
     */
    public static function sendEmail($to, $subject, $message, $from = null, $fromName = null)
    {
        try {
            $from = $from ?: (Yii::$app->params['adminEmail'] ?? 'noreply@kashflow.com');
            $fromName = $fromName ?: 'KashFlow System';

            $mailer = Yii::$app->mailer;
            $result = $mailer->compose()
                ->setFrom([$from => $fromName])
                ->setTo($to)
                ->setSubject($subject)
                ->setHtmlBody($message)
                ->send();

            if ($result) {
                Yii::info("Email sent successfully to: {$to}, Subject: {$subject}", 'email');
            } else {
                Yii::error("Failed to send email to: {$to}, Subject: {$subject}", 'email');
            }

            return $result;
        } catch (\Exception $e) {
            Yii::error("Email sending error: " . $e->getMessage(), 'email');
            return false;
        }
    }

    /**
     * Static method to send notification email to a user.
     *
     * @param int $userId
     * @param string $type
     * @param array $data
     * @return bool
     */
    public static function sendNotificationEmail($userId, $type = 'general', $data = [])
    {
        try {
            $user = \dektrium\user\models\User::findOne($userId);
            if (!$user) {
                Yii::error("User not found for notification: {$userId}", 'email');
                return false;
            }

            $customer = \app\models\Customer::find()->where(['user_id' => $userId])->one();
            
            // Create a temporary controller instance to use the prepareNotificationEmail method
            $controller = new self('site', Yii::$app);
            $emailContent = $controller->prepareNotificationEmail($type, $data, $user, $customer);

            if (!$emailContent) {
                Yii::error("Invalid notification type: {$type}", 'email');
                return false;
            }

            $result = self::sendEmail(
                $user->email,
                $emailContent['subject'],
                $emailContent['body'],
                Yii::$app->params['adminEmail'] ?? 'noreply@kashflow.com',
                'KashFlow Notifications'
            );

            if ($result) {
                Yii::info("Notification email sent to user {$userId} ({$user->email}), Type: {$type}", 'email');
            }

            return $result;
        } catch (\Exception $e) {
            Yii::error("Notification email error: " . $e->getMessage(), 'email');
            return false;
        }
    }

    /**
     * Static method to send bulk emails.
     *
     * @param array $recipients
     * @param string $subject
     * @param string $message
     * @param string|null $from
     * @param string|null $fromName
     * @return array ['success_count' => int, 'error_count' => int, 'errors' => array]
     */
    public static function sendBulkEmail($recipients, $subject, $message, $from = null, $fromName = null)
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        if (!is_array($recipients)) {
            $recipients = [$recipients];
        }

        foreach ($recipients as $recipient) {
            if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format: {$recipient}";
                $errorCount++;
                continue;
            }

            $result = self::sendEmail($recipient, $subject, $message, $from, $fromName);
            
            if ($result) {
                $successCount++;
            } else {
                $errorCount++;
                $errors[] = "Failed to send email to: {$recipient}";
            }
        }

        return [
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors
        ];
    }
}
