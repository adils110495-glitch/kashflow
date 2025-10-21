<?php

namespace app\controllers;

use Yii;
use app\models\FundTransfer;
use app\models\Customer;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;

/**
 * FundTransferController implements the CRUD actions for FundTransfer model.
 */
class FundTransferController extends Controller
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
                    'bulk-action' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all FundTransfer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $statusFilter = Yii::$app->request->get('status', '');
        $customerFilter = Yii::$app->request->get('customer', '');

        $query = FundTransfer::find()
            ->with(['fromCustomer', 'toCustomer', 'processedBy']);

        // Apply filters
        if ($statusFilter !== '') {
            $query->andWhere(['status' => $statusFilter]);
        }

        if ($customerFilter !== '') {
            $query->andWhere(['or', 
                ['from_customer_id' => $customerFilter],
                ['to_customer_id' => $customerFilter]
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        // Get statistics
        $stats = [
            'total' => FundTransfer::find()->count(),
            'pending' => FundTransfer::find()->where(['status' => FundTransfer::STATUS_PENDING])->count(),
            'pending_receiver' => FundTransfer::find()->where(['status' => FundTransfer::STATUS_PENDING_RECEIVER_APPROVAL])->count(),
            'approved' => FundTransfer::find()->where(['status' => FundTransfer::STATUS_APPROVED])->count(),
            'receiver_approved' => FundTransfer::find()->where(['status' => FundTransfer::STATUS_RECEIVER_APPROVED])->count(),
            'rejected' => FundTransfer::find()->where(['status' => FundTransfer::STATUS_REJECTED])->count(),
            'receiver_rejected' => FundTransfer::find()->where(['status' => FundTransfer::STATUS_RECEIVER_REJECTED])->count(),
            'pending_amount' => FundTransfer::find()->where(['status' => FundTransfer::STATUS_PENDING])->sum('amount') ?: 0,
            'pending_receiver_amount' => FundTransfer::find()->where(['status' => FundTransfer::STATUS_PENDING_RECEIVER_APPROVAL])->sum('amount') ?: 0,
            'total_amount' => FundTransfer::find()->where(['status' => FundTransfer::STATUS_APPROVED])->sum('amount') ?: 0,
            'receiver_approved_amount' => FundTransfer::find()->where(['status' => FundTransfer::STATUS_RECEIVER_APPROVED])->sum('amount') ?: 0,
        ];

        // Get customers for filter
        $customers = Customer::find()->with('user')->all();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'stats' => $stats,
            'customers' => $customers,
            'statusFilter' => $statusFilter,
            'customerFilter' => $customerFilter,
        ]);
    }

    /**
     * Displays a single FundTransfer model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new FundTransfer model for admin.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FundTransfer();
        $model->transfer_type = FundTransfer::TYPE_ADMIN_TRANSFER;
        $model->transfer_date = date('Y-m-d');

        if ($model->load(Yii::$app->request->post())) {
            $model->status = FundTransfer::STATUS_APPROVED; // Admin transfers are auto-approved
            $model->processed_by = Yii::$app->user->id;
            $model->processed_at = time();

            if ($model->save()) {
                // Execute the transfer immediately for admin transfers
                $model->executeTransfer();
                Yii::$app->session->setFlash('success', 'Fund transfer created and executed successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to create fund transfer.');
            }
        }

        $customers = Customer::find()->with('user')->all();

        return $this->render('create', [
            'model' => $model,
            'customers' => $customers,
        ]);
    }

    /**
     * Approve or reject a fund transfer
     * @param integer $id
     * @return mixed
     */
    public function actionApprove($id)
    {
        $model = $this->findModel($id);
        
        if (!$model->isPending()) {
            Yii::$app->session->setFlash('error', 'This transfer has already been processed.');
            return $this->redirect(['view', 'id' => $id]);
        }

        if (Yii::$app->request->isPost) {
            $action = Yii::$app->request->post('action', '');
            $adminComment = Yii::$app->request->post('admin_comment', '');

            if ($action === 'approve') {
                $status = FundTransfer::STATUS_PENDING_RECEIVER_APPROVAL;
                $message = 'Fund transfer approved by admin. Waiting for receiver approval.';
            } elseif ($action === 'reject') {
                $status = FundTransfer::STATUS_REJECTED;
                $message = 'Fund transfer rejected.';
            } else {
                Yii::$app->session->setFlash('error', 'Invalid action.');
                return $this->redirect(['view', 'id' => $id]);
            }

            if ($model->processTransfer($status, $adminComment)) {
                Yii::$app->session->setFlash('success', $message);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to process fund transfer.');
            }

            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Bulk action for processing multiple transfers
     * @return mixed
     */
    public function actionBulkAction()
    {
        if (Yii::$app->request->isPost) {
            $action = Yii::$app->request->post('bulk_action');
            $ids = Yii::$app->request->post('selected_ids', []);
            $adminComment = Yii::$app->request->post('admin_comment', '');

            if (empty($ids)) {
                Yii::$app->session->setFlash('error', 'Please select at least one fund transfer.');
                return $this->redirect(['index']);
            }

            $successCount = 0;
            $errorCount = 0;

            foreach ($ids as $id) {
                $transfer = FundTransfer::findOne($id);
                if ($transfer && $transfer->isPending()) {
                    $status = ($action === 'approve') ? FundTransfer::STATUS_APPROVED : FundTransfer::STATUS_REJECTED;
                    if ($transfer->processTransfer($status, $adminComment)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                }
            }

            if ($successCount > 0) {
                Yii::$app->session->setFlash('success', "Successfully processed {$successCount} fund transfer(s).");
            }
            if ($errorCount > 0) {
                Yii::$app->session->setFlash('error', "Failed to process {$errorCount} fund transfer(s).");
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Export fund transfers to CSV
     * @return mixed
     */
    public function actionExport()
    {
        $transfers = FundTransfer::find()
            ->with(['fromCustomer', 'toCustomer', 'processedBy'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $filename = 'fund_transfers_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'ID',
            'From Customer',
            'To Customer',
            'Amount',
            'Transfer Date',
            'Status',
            'Transfer Type',
            'Comment',
            'Admin Comment',
            'Processed By',
            'Processed Date',
            'Created Date'
        ]);
        
        // CSV data
        foreach ($transfers as $transfer) {
            fputcsv($output, [
                $transfer->id,
                $transfer->fromCustomer->name,
                $transfer->toCustomer->name,
                $transfer->amount,
                $transfer->transfer_date,
                $transfer->getStatusLabel(),
                $transfer->getTransferTypeLabel(),
                $transfer->comment,
                $transfer->admin_comment,
                $transfer->processedBy ? $transfer->processedBy->username : '',
                $transfer->getFormattedProcessedDate(),
                date('Y-m-d H:i:s', $transfer->created_at)
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Finds the FundTransfer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FundTransfer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FundTransfer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
