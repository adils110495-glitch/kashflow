<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\FundRequest;
use app\models\Customer;

/**
 * FundRequestController handles fund request management for admins
 */
class FundRequestController extends Controller
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
                            // Only allow admin users (not customers)
                            if (Yii::$app->user->isGuest) {
                                return false;
                            }
                            
                            // Check if user is a customer - if yes, deny access
                            $customer = Customer::find()->where(['user_id' => Yii::$app->user->id])->one();
                            if ($customer) {
                                return false; // Customers are not allowed
                            }
                            
                            // Allow non-customer users (admins)
                            return true;
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    // Redirect customers to their dashboard
                    $customer = Customer::find()->where(['user_id' => Yii::$app->user->id])->one();
                    if ($customer) {
                        return Yii::$app->response->redirect(['/customer-dashboard/index']);
                    }
                    // Redirect guests to login
                    return Yii::$app->response->redirect(['/user/login']);
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'approve' => ['POST'],
                    'reject' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all fund requests
     */
    public function actionIndex()
    {
        // Apply filters
        $statusFilter = Yii::$app->request->get('status', '');
        $customerFilter = Yii::$app->request->get('customer', '');

        // Build query
        $query = FundRequest::find()->with(['customer', 'processedBy']);

        // Apply status filter
        if (!empty($statusFilter)) {
            $query->andWhere(['status' => $statusFilter]);
        }

        // Apply customer filter
        if (!empty($customerFilter)) {
            $query->andWhere(['customer_id' => $customerFilter]);
        }

        $fundRequests = $query->orderBy(['created_at' => SORT_DESC])->all();

        // Get statistics
        $stats = [
            'total' => FundRequest::find()->count(),
            'pending' => FundRequest::find()->where(['status' => FundRequest::STATUS_PENDING])->count(),
            'approved' => FundRequest::find()->where(['status' => FundRequest::STATUS_APPROVED])->count(),
            'rejected' => FundRequest::find()->where(['status' => FundRequest::STATUS_REJECTED])->count(),
            'total_amount' => FundRequest::find()->where(['status' => FundRequest::STATUS_APPROVED])->sum('amount') ?: 0,
            'pending_amount' => FundRequest::find()->where(['status' => FundRequest::STATUS_PENDING])->sum('amount') ?: 0,
        ];

        // Get customers for filter dropdown
        $customers = Customer::find()->with('user')->all();

        return $this->render('index', [
            'fundRequests' => $fundRequests,
            'stats' => $stats,
            'customers' => $customers,
            'statusFilter' => $statusFilter,
            'customerFilter' => $customerFilter,
        ]);
    }

    /**
     * Displays a single fund request
     */
    public function actionView($id)
    {
        $fundRequest = $this->findModel($id);

        return $this->render('view', [
            'fundRequest' => $fundRequest,
        ]);
    }

    /**
     * Approve a fund request
     */
    public function actionApprove($id)
    {
        $fundRequest = $this->findModel($id);
        
        if (Yii::$app->request->isPost) {
            $adminComment = Yii::$app->request->post('admin_comment', '');
            $action = Yii::$app->request->post('action', '');

            if ($action === 'approve') {
                if ($fundRequest->processRequest(FundRequest::STATUS_APPROVED, $adminComment)) {
                    Yii::$app->session->setFlash('success', 'Fund request approved successfully.');
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to approve fund request.');
                }
            } elseif ($action === 'reject') {
                if ($fundRequest->processRequest(FundRequest::STATUS_REJECTED, $adminComment)) {
                    Yii::$app->session->setFlash('success', 'Fund request rejected successfully.');
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to reject fund request.');
                }
            }

            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Reject a fund request
     */
    public function actionReject($id)
    {
        $fundRequest = $this->findModel($id);
        
        if (Yii::$app->request->isPost) {
            $adminComment = Yii::$app->request->post('admin_comment', '');

            if ($fundRequest->processRequest(FundRequest::STATUS_REJECTED, $adminComment)) {
                Yii::$app->session->setFlash('success', 'Fund request rejected successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to reject fund request.');
            }

            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Bulk action for fund requests (approve/reject multiple)
     */
    public function actionBulkAction()
    {
        if (Yii::$app->request->isPost) {
            $action = Yii::$app->request->post('bulk_action');
            $ids = Yii::$app->request->post('selected_ids', []);
            $adminComment = Yii::$app->request->post('admin_comment', '');

            if (empty($ids)) {
                Yii::$app->session->setFlash('error', 'Please select at least one fund request.');
                return $this->redirect(['index']);
            }

            $successCount = 0;
            $errorCount = 0;

            foreach ($ids as $id) {
                $fundRequest = FundRequest::findOne($id);
                if ($fundRequest && $fundRequest->isPending()) {
                    $status = ($action === 'approve') ? FundRequest::STATUS_APPROVED : FundRequest::STATUS_REJECTED;
                    if ($fundRequest->processRequest($status, $adminComment)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                }
            }

            if ($successCount > 0) {
                Yii::$app->session->setFlash('success', "Successfully processed {$successCount} fund request(s).");
            }
            if ($errorCount > 0) {
                Yii::$app->session->setFlash('error', "Failed to process {$errorCount} fund request(s).");
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Export fund requests to CSV
     */
    public function actionExport()
    {
        $fundRequests = FundRequest::find()->with(['customer', 'processedBy'])->all();
        
        $filename = 'fund_requests_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'ID', 'Customer Name', 'Customer Email', 'Amount', 'Request Date', 
            'Status', 'Comment', 'Admin Comment', 'Processed By', 'Processed Date'
        ]);
        
        // CSV data
        foreach ($fundRequests as $request) {
            fputcsv($output, [
                $request->id,
                $request->customer ? $request->customer->name : 'N/A',
                $request->customer && $request->customer->user ? $request->customer->user->email : 'N/A',
                $request->amount,
                $request->request_date,
                $request->getStatusLabel(),
                $request->comment ?: '',
                $request->admin_comment ?: '',
                $request->processedBy ? $request->processedBy->username : 'N/A',
                $request->getFormattedProcessedDate()
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Finds the FundRequest model based on its primary key value
     */
    protected function findModel($id)
    {
        if (($model = FundRequest::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested fund request does not exist.');
    }
}
