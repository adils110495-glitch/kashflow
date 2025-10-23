<?php
/**
 * Email Test Page
 * Simple web interface to test email credentials
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Email Credential Test';
?>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Email Credential Test</h3>
                </div>
                <div class="panel-body">
                    
                    <?php if (Yii::$app->session->hasFlash('emailTestResult')): ?>
                        <div class="alert alert-info">
                            <?= Yii::$app->session->getFlash('emailTestResult') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (Yii::$app->session->hasFlash('emailTestError')): ?>
                        <div class="alert alert-danger">
                            <?= Yii::$app->session->getFlash('emailTestError') ?>
                        </div>
                    <?php endif; ?>

                    <h4>Current Configuration:</h4>
                    <div class="well">
                        <strong>SMTP Host:</strong> <?= Yii::$app->params['mailer']['host'] ?? 'NOT SET' ?><br>
                        <strong>SMTP Username:</strong> <?= Yii::$app->params['mailer']['username'] ?? 'NOT SET' ?><br>
                        <strong>SMTP Password:</strong> <?= empty(Yii::$app->params['mailer']['password']) ? 'NOT SET' : 'SET (hidden)' ?><br>
                        <strong>SMTP Port:</strong> <?= Yii::$app->params['mailer']['port'] ?? 'NOT SET' ?><br>
                        <strong>Encryption:</strong> <?= Yii::$app->params['mailer']['encryption'] ?? 'NOT SET' ?><br>
                        <strong>From Email:</strong> <?= array_keys(Yii::$app->params['mailer']['from'])[0] ?? 'NOT SET' ?><br>
                        <strong>File Transport:</strong> <?= Yii::$app->mailer->useFileTransport ? 'YES (emails saved to files)' : 'NO (emails sent via SMTP)' ?>
                    </div>

                    <?php if (Yii::$app->mailer->useFileTransport): ?>
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> File transport is enabled. Emails are being saved to files instead of being sent via SMTP.
                            To send real emails, set <code>'useFileTransport' => false</code> in config/web.php
                        </div>
                    <?php endif; ?>

                    <h4>Test Email Sending:</h4>
                    <?php $form = ActiveForm::begin(['action' => ['site/send-email']]); ?>
                    
                    <div class="form-group">
                        <label for="to">Test Email Address:</label>
                        <input type="email" class="form-control" name="to" id="to" 
                               value="<?= Yii::$app->user->identity->email ?? '' ?>" 
                               placeholder="Enter email address to test" required>
                        <small class="help-block">Enter your email address to receive a test email</small>
                    </div>

                    <input type="hidden" name="subject" value="KashFlow Email Test - <?= date('Y-m-d H:i:s') ?>">
                    <input type="hidden" name="message" value="This is a test email from KashFlow to verify email configuration is working properly. If you receive this email, your SMTP settings are configured correctly!">

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="glyphicon glyphicon-envelope"></i> Send Test Email
                        </button>
                        <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="btn btn-default">Cancel</a>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <hr>

                    <h4>Quick Tests:</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?= \yii\helpers\Url::to(['site/email-test']) ?>" class="btn btn-info btn-block">
                                <i class="glyphicon glyphicon-cog"></i> Advanced Email Test
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?= \yii\helpers\Url::to(['site/contact']) ?>" class="btn btn-success btn-block">
                                <i class="glyphicon glyphicon-comment"></i> Contact Form Test
                            </a>
                        </div>
                    </div>

                    <hr>

                    <h4>Troubleshooting:</h4>
                    <div class="alert alert-info">
                        <strong>Common Issues:</strong>
                        <ul>
                            <li><strong>Authentication Failed:</strong> Check username/password</li>
                            <li><strong>Connection Timeout:</strong> Verify SMTP host and port</li>
                            <li><strong>SSL/TLS Issues:</strong> Ensure encryption setting matches provider</li>
                            <li><strong>Gmail Users:</strong> Use App Password, not regular password</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <strong>For Gmail:</strong>
                        <ol>
                            <li>Enable 2-factor authentication</li>
                            <li>Generate an "App Password"</li>
                            <li>Use the app password in your configuration</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
