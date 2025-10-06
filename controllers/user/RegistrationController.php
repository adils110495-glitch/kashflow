<?php
namespace app\controllers\user;

use dektrium\user\controllers\RegistrationController as BaseRegistrationController;
use app\models\RegistrationForm;
use yii\web\NotFoundHttpException;
use Yii;

class RegistrationController extends BaseRegistrationController
{
    public $layout = '@app/views/layouts/main-login';
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Allow access to validate-referral-code action without authentication
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['validate-referral-code'],
                    'roles' => ['?'], // Allow guests
                ],
                [
                    'allow' => true,
                    'actions' => ['register'],
                    'roles' => ['?'], // Allow guests
                ],
            ],
        ];
        
        return $behaviors;
    }

    public function actionRegister()
    {
        $this->layout = '@app/views/layouts/main-login';
        
        if (!$this->module->enableRegistration) {
            throw new NotFoundHttpException();
        }

        /** @var RegistrationForm $model */
        $model = Yii::createObject(RegistrationForm::class);

        // Handle AJAX validation first to prevent issues
        if (\Yii::$app->request->isAjax) {
            if ($model->load(\Yii::$app->request->post())) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            try {
                $user = $model->register();
                if ($user) {
                    // Get the username for the success message
                    $username = $user->username;
                    Yii::$app->session->setFlash('success', "Welcome {$username}! Your account has been created successfully. Please login to continue.");
                    return $this->redirect(['/user/security/login']);
                } else {
                    // Registration failed due to validation errors
                    // Get the first error from the model to show exact error message
                    $errors = $model->getFirstErrors();
                    $errorMessage = !empty($errors) ? reset($errors) : 'Registration validation failed';
                    Yii::$app->session->setFlash('error', $errorMessage);
                }
            } catch (\yii\db\Exception $e) {
                // Database related errors
                Yii::error('Database error during registration: ' . $e->getMessage(), __METHOD__);
                Yii::$app->session->setFlash('error', 'Database Error: ' . $e->getMessage());
            } catch (\yii\base\Exception $e) {
                // General Yii exceptions
                Yii::error('Yii exception during registration: ' . $e->getMessage(), __METHOD__);
                Yii::$app->session->setFlash('error', 'System Error: ' . $e->getMessage());
            } catch (\Exception $e) {
                // Any other unexpected exceptions
                Yii::error('Unexpected error during registration: ' . $e->getMessage(), __METHOD__);
                Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
            }
        }

        return $this->render('register', [
            'model'  => $model,
            'module' => $this->module,
        ]);
    }

    /**
     * Validate referral code via AJAX
     */
    public function actionValidateReferralCode()
    {
        if (!Yii::$app->request->isAjax) {
            throw new \yii\web\BadRequestHttpException('Only AJAX requests are allowed.');
        }

        $referralCode = Yii::$app->request->post('referral_code');
        
        if (empty($referralCode)) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['valid' => true, 'message' => ''];
        }

        // Check if the referral code exists in the user table as username
        $user = \app\models\User::find()->where(['username' => $referralCode])->one();
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($user) {
            return ['valid' => true, 'message' => 'Valid referral code'];
        } else {
            return ['valid' => false, 'message' => 'Invalid referral code. Please check and try again.'];
        }
    }
}


