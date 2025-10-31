<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\AdminLoginForm;

/**
 * AdminAuthController handles admin authentication
 */
class AdminAuthController extends Controller
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
                        'roles' => ['@'], // Only authenticated admin users
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
    public function init()
    {
        parent::init();
        
        // Set admin layout
        $this->layout = 'admin-login';
    }

    /**
     * Admin login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()) {
            return $this->redirect(['/admin-dashboard/index']);
        }

        $model = new AdminLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Admin logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(['login']);
    }

    /**
     * Admin dashboard redirect
     *
     * @return Response
     */
    public function actionDashboard()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin()) {
            return $this->redirect(['login']);
        }

        return $this->redirect(['/admin-dashboard/index']);
    }
}
