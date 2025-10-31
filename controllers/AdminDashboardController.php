<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Customer;
use app\models\Withdrawal;
use app\models\Income;

/**
 * Admin dashboard controller
 */
class AdminDashboardController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            // Only allow authenticated admin users
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    return Yii::$app->response->redirect(['admin-auth/login']);
                },
            ],
        ];
    }

    /**
     * Admin dashboard index
     */
    public function actionIndex()
    {
        // Get dashboard statistics
        $stats = [
            'total_customers' => Customer::find()->count(),
            'active_customers' => Customer::find()->where(['status' => Customer::STATUS_ACTIVE])->count(),
            'pending_withdrawals' => Withdrawal::find()->where(['status' => Withdrawal::STATUS_PENDING])->count(),
            'total_withdrawals' => Withdrawal::find()->count(),
            'total_income' => Income::find()->sum('amount') ?: 0,
            'pending_income' => Income::find()->where(['status' => Income::STATUS_PENDING])->sum('amount') ?: 0,
        ];

        // Get recent activities
        $recentWithdrawals = Withdrawal::find()
            ->with(['customer'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        $recentCustomers = Customer::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        return $this->render('index', [
            'stats' => $stats,
            'recentWithdrawals' => $recentWithdrawals,
            'recentCustomers' => $recentCustomers,
        ]);
    }
}
