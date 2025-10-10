<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use app\models\Withdrawal;
use app\models\Customer;
use app\models\Ledger;

/**
 * AdminController handles admin functions including withdrawal management
 */
class AdminController extends Controller
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
                        'roles' => ['@'], // Only authenticated users
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays withdrawal management page
     */
    public function actionWithdrawals()
    {
        $statusFilter = Yii::$app->request->get('status', '');
        $searchTerm = Yii::$app->request->get('search', '');
        
        $query = Withdrawal::find()
            ->with(['customer', 'actionBy'])
            ->orderBy(['created_at' => SORT_DESC]);
            
        // Apply status filter
        if ($statusFilter !== '') {
            $query->andWhere(['status' => $statusFilter]);
        }
        
        // Apply search filter
        if (!empty($searchTerm)) {
            $query->andWhere(['or',
                ['like', 'customer.name', $searchTerm],
                ['like', 'customer.email', $searchTerm],
                ['like', 'customer.mobile_no', $searchTerm]
            ]);
        }
        
        $withdrawals = $query->all();
        $stats = Withdrawal::getWithdrawalStats();
        
        return $this->render('withdrawals', [
            'withdrawals' => $withdrawals,
            'stats' => $stats,
            'statusFilter' => $statusFilter,
            'searchTerm' => $searchTerm,
        ]);
    }

    /**
     * Approve withdrawal request
     */
    public function actionApproveWithdrawal($id)
    {
        $withdrawal = Withdrawal::findOne($id);
        
        if (!$withdrawal) {
            throw new NotFoundHttpException('Withdrawal request not found.');
        }
        
        if ($withdrawal->status !== Withdrawal::STATUS_PENDING) {
            Yii::$app->session->setFlash('error', 'Only pending withdrawals can be approved.');
            return $this->redirect(['withdrawals']);
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Approve withdrawal
            $withdrawal->approve(Yii::$app->user->id, 'Approved by admin');
            
            // Create ledger entry for withdrawal
            $ledger = new Ledger();
            $ledger->customer_id = $withdrawal->customer_id;
            $ledger->debit = $withdrawal->amount;
            $ledger->credit = 0;
            $ledger->type = Ledger::TYPE_WITHDRAWAL;
            $ledger->action_by = Yii::$app->user->id;
            $ledger->date = date('Y-m-d');
            $ledger->action_date_time = date('Y-m-d H:i:s');
            $ledger->status = Ledger::STATUS_ACTIVE;
            
            if (!$ledger->save()) {
                throw new \Exception('Failed to create ledger entry: ' . implode(', ', $ledger->getFirstErrors()));
            }
            
            // Log customer activity
            $customer = Customer::findOne($withdrawal->customer_id);
            if ($customer) {
                $customer->logActivity('withdrawal_approved', "Withdrawal of $" . number_format($withdrawal->amount, 2) . " approved", [
                    'amount' => $withdrawal->amount,
                    'withdrawal_id' => $withdrawal->id,
                    'ledger_id' => $ledger->id
                ]);
            }
            
            $transaction->commit();
            
            Yii::$app->session->setFlash('success', 'Withdrawal request approved successfully.');
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Failed to approve withdrawal: ' . $e->getMessage());
        }
        
        return $this->redirect(['withdrawals']);
    }

    /**
     * Reject withdrawal request
     */
    public function actionRejectWithdrawal($id)
    {
        $withdrawal = Withdrawal::findOne($id);
        
        if (!$withdrawal) {
            throw new NotFoundHttpException('Withdrawal request not found.');
        }
        
        if ($withdrawal->status !== Withdrawal::STATUS_PENDING) {
            Yii::$app->session->setFlash('error', 'Only pending withdrawals can be rejected.');
            return $this->redirect(['withdrawals']);
        }
        
        $comment = Yii::$app->request->post('comment', '');
        
        if ($withdrawal->reject(Yii::$app->user->id, $comment)) {
            // Log customer activity
            $customer = Customer::findOne($withdrawal->customer_id);
            if ($customer) {
                $customer->logActivity('withdrawal_rejected', "Withdrawal of $" . number_format($withdrawal->amount, 2) . " rejected", [
                    'amount' => $withdrawal->amount,
                    'withdrawal_id' => $withdrawal->id,
                    'reason' => $comment
                ]);
            }
            
            Yii::$app->session->setFlash('success', 'Withdrawal request rejected successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to reject withdrawal request.');
        }
        
        return $this->redirect(['withdrawals']);
    }

    /**
     * Mark withdrawal as processing
     */
    public function actionProcessWithdrawal($id)
    {
        $withdrawal = Withdrawal::findOne($id);
        
        if (!$withdrawal) {
            throw new NotFoundHttpException('Withdrawal request not found.');
        }
        
        if ($withdrawal->status !== Withdrawal::STATUS_APPROVED) {
            Yii::$app->session->setFlash('error', 'Only approved withdrawals can be marked as processing.');
            return $this->redirect(['withdrawals']);
        }
        
        $comment = Yii::$app->request->post('comment', '');
        
        if ($withdrawal->markAsProcessing(Yii::$app->user->id, $comment)) {
            Yii::$app->session->setFlash('success', 'Withdrawal marked as processing successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to mark withdrawal as processing.');
        }
        
        return $this->redirect(['withdrawals']);
    }

    /**
     * Mark withdrawal as completed
     */
    public function actionCompleteWithdrawal($id)
    {
        $withdrawal = Withdrawal::findOne($id);
        
        if (!$withdrawal) {
            throw new NotFoundHttpException('Withdrawal request not found.');
        }
        
        if ($withdrawal->status !== Withdrawal::STATUS_PROCESSING) {
            Yii::$app->session->setFlash('error', 'Only processing withdrawals can be marked as completed.');
            return $this->redirect(['withdrawals']);
        }
        
        $comment = Yii::$app->request->post('comment', '');
        
        if ($withdrawal->markAsCompleted(Yii::$app->user->id, $comment)) {
            // Log customer activity
            $customer = Customer::findOne($withdrawal->customer_id);
            if ($customer) {
                $customer->logActivity('withdrawal_completed', "Withdrawal of $" . number_format($withdrawal->amount, 2) . " completed", [
                    'amount' => $withdrawal->amount,
                    'withdrawal_id' => $withdrawal->id
                ]);
            }
            
            Yii::$app->session->setFlash('success', 'Withdrawal marked as completed successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to mark withdrawal as completed.');
        }
        
        return $this->redirect(['withdrawals']);
    }

    /**
     * View withdrawal details
     */
    public function actionViewWithdrawal($id)
    {
        $withdrawal = Withdrawal::findOne($id);
        
        if (!$withdrawal) {
            throw new NotFoundHttpException('Withdrawal request not found.');
        }
        
        return $this->render('view-withdrawal', [
            'withdrawal' => $withdrawal,
        ]);
    }

    /**
     * Displays pending withdrawals page
     */
    public function actionPendingWithdrawals()
    {
        $statusFilter = Yii::$app->request->get('status', '');
        $searchTerm = Yii::$app->request->get('search', '');
        
        $query = Withdrawal::find()
            ->with(['customer', 'actionBy'])
            ->where(['status' => Withdrawal::STATUS_PENDING])
            ->orderBy(['created_at' => SORT_DESC]);
            
        // Apply search filter
        if (!empty($searchTerm)) {
            $query->andWhere(['or',
                ['like', 'customer.name', $searchTerm],
                ['like', 'customer.email', $searchTerm],
                ['like', 'customer.mobile_no', $searchTerm]
            ]);
        }
        
        $pendingWithdrawals = $query->all();
        $stats = Withdrawal::getWithdrawalStats();
        
        return $this->render('pending-withdrawals', [
            'pendingWithdrawals' => $pendingWithdrawals,
            'stats' => $stats,
            'searchTerm' => $searchTerm,
        ]);
    }

    /**
     * Displays all withdrawals page
     */
    public function actionAllWithdrawals()
    {
        $statusFilter = Yii::$app->request->get('status', '');
        $searchTerm = Yii::$app->request->get('search', '');
        
        $query = Withdrawal::find()
            ->with(['customer', 'actionBy'])
            ->orderBy(['created_at' => SORT_DESC]);
            
        // Apply status filter
        if ($statusFilter !== '') {
            $query->andWhere(['status' => $statusFilter]);
        }
        
        // Apply search filter
        if (!empty($searchTerm)) {
            $query->andWhere(['or',
                ['like', 'customer.name', $searchTerm],
                ['like', 'customer.email', $searchTerm],
                ['like', 'customer.mobile_no', $searchTerm]
            ]);
        }
        
        $allWithdrawals = $query->all();
        $stats = Withdrawal::getWithdrawalStats();
        
        return $this->render('all-withdrawals', [
            'allWithdrawals' => $allWithdrawals,
            'stats' => $stats,
            'statusFilter' => $statusFilter,
            'searchTerm' => $searchTerm,
        ]);
    }
}
