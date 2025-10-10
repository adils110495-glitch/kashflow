<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use Yii;
use app\models\Customer;
use app\models\Package;
use app\models\CustomerPackage;
use yii\helpers\Url;

/**
 * CustomerDashboardController handles customer dashboard functionality
 */
class CustomerDashboardController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        // Set customer-specific layout
        $this->layout = 'customer-main';
    }

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
                        'roles' => ['customer'], // Only customers can access
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
     * Displays the customer dashboard
     * @return string
     */
    public function actionIndex()
    {
        // Get current customer data
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->with(['country', 'currentPackage'])
            ->one();

        if (!$customer) {
            Yii::$app->session->setFlash('error', 'Customer profile not found.');
            return $this->goHome();
        }

        return $this->render('index', [
            'customer' => $customer,
        ]);
    }

    /**
     * Displays customer profile page
     */
    public function actionProfile()
    {
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->with(['country', 'currentPackage'])
            ->one();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        return $this->render('profile', [
            'customer' => $customer,
        ]);
    }

    /**
     * Displays direct team members
     */
    public function actionDirectTeam()
    {
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        // Get current user's username
        $currentUser = Yii::$app->user->identity;
        $username = $currentUser->username;

        // Apply filters
        $usernameFilter = Yii::$app->request->get('username', '');
        $fromDate = Yii::$app->request->get('from_date', '');
        $toDate = Yii::$app->request->get('to_date', '');

        // Build query for direct team (customers with current user's username in referral_code)
        $query = Customer::find()
            ->joinWith('user')
            ->with(['currentPackage', 'activeCustomerPackages'])
            ->where(['referral_code' => $username])
            ->orderBy(['created_at' => SORT_DESC]);

        // Apply username filter
        if (!empty($usernameFilter)) {
            $query->andWhere(['like', 'user.username', $usernameFilter]);
        }

        // Apply date filters
        if (!empty($fromDate)) {
            $query->andWhere(['>=', 'customer.created_at', strtotime($fromDate)]);
        }
        if (!empty($toDate)) {
            $query->andWhere(['<=', 'customer.created_at', strtotime($toDate . ' 23:59:59')]);
        }

        $directTeam = $query->all();
        
        // Calculate package statistics
        $packageStats = Customer::calculatePackageStats($directTeam);

        return $this->render('direct-team', [
            'customer' => $customer,
            'directTeam' => $directTeam,
            'packageStats' => $packageStats,
            'usernameFilter' => $usernameFilter,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);
    }

    /**
     * Displays level team with hierarchy
     */
    public function actionLevelTeam()
    {
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        // Get current user's username
        $currentUser = Yii::$app->user->identity;
        $username = $currentUser->username;

        // Apply filters
        $usernameFilter = Yii::$app->request->get('username', '');
        $fromDate = Yii::$app->request->get('from_date', '');
        $toDate = Yii::$app->request->get('to_date', '');
        $level = Yii::$app->request->get('level', '');
        $status = Yii::$app->request->get('status', '');

        // Build level team hierarchy (up to 10 levels) with all filters
        $levelTeam = Customer::buildLevelTeam($username, $usernameFilter, $fromDate, $toDate, $level, $status);
        
        // Calculate package statistics for all levels using new method
        $allLevelCustomers = Customer::getAllLevelCustomersFromTeam($levelTeam);
        $packageStats = Customer::calculatePackageStats($allLevelCustomers);

        return $this->render('level-team', [
            'customer' => $customer,
            'levelTeam' => $levelTeam,
            'packageStats' => $packageStats,
            'usernameFilter' => $usernameFilter,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'level' => $level,
            'status' => $status,
        ]);
    }

    /**
     * Displays customer income history
     */
    public function actionIncome()
    {
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        // Apply filters
        $typeFilter = Yii::$app->request->get('type', '');
        $fromDate = Yii::$app->request->get('from_date', '');
        $toDate = Yii::$app->request->get('to_date', '');
        $statusFilter = Yii::$app->request->get('status', '');

        // Build query for customer incomes
        $query = \app\models\Income::find()
            ->where(['customer_id' => $customer->id])
            ->orderBy(['created_at' => SORT_DESC]);

        // Apply type filter
        if (!empty($typeFilter)) {
            $query->andWhere(['type' => $typeFilter]);
        }

        // Apply status filter
        if (!empty($statusFilter)) {
            $query->andWhere(['status' => $statusFilter]);
        }

        // Apply date filters
        if (!empty($fromDate)) {
            $query->andWhere(['>=', 'date', $fromDate]);
        }
        if (!empty($toDate)) {
            $query->andWhere(['<=', 'date', $toDate]);
        }

        $incomes = $query->all();
        
        // Calculate income statistics
        $stats = Customer::calculateIncomeStats($customer->id);

        return $this->render('income', [
            'customer' => $customer,
            'incomes' => $incomes,
            'stats' => $stats,
            'typeFilter' => $typeFilter,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'statusFilter' => $statusFilter,
        ]);
    }

    /**
     * Displays withdrawal page
     */
    public function actionWithdrawal()
    {
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        // Get customer's current balance
        $currentBalance = $customer->getLedgerBalance();
        
        // Get recent withdrawal history from withdrawal table
        $withdrawalHistory = \app\models\Withdrawal::getCustomerWithdrawals($customer->id, 10)->all();

        if (Yii::$app->request->isPost) {
            $amount = Yii::$app->request->post('amount');
            $accountDetails = Yii::$app->request->post('account_details');
            
            // Validate withdrawal request
            if (empty($amount) || $amount <= 0) {
                Yii::$app->session->setFlash('error', 'Please enter a valid withdrawal amount.');
            } elseif ($amount > $currentBalance) {
                Yii::$app->session->setFlash('error', 'Insufficient balance for withdrawal.');
            } elseif (empty($accountDetails)) {
                Yii::$app->session->setFlash('error', 'Please provide account details.');
            } else {
                // Create withdrawal request
                $result = $this->processWithdrawalRequest($customer, $amount, $accountDetails);
                
                if ($result['success']) {
                    Yii::$app->session->setFlash('success', $result['message']);
                    return $this->redirect(['withdrawal']);
                } else {
                    Yii::$app->session->setFlash('error', $result['message']);
                }
            }
        }

        return $this->render('withdrawal', [
            'customer' => $customer,
            'currentBalance' => $currentBalance,
            'withdrawalHistory' => $withdrawalHistory,
        ]);
    }

    /**
     * Process withdrawal request
     * @param Customer $customer
     * @param float $amount
     * @param string $accountDetails
     * @return array
     */
    private function processWithdrawalRequest($customer, $amount, $accountDetails)
    {
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Create withdrawal request in withdrawal table
            $withdrawal = new \app\models\Withdrawal();
            $withdrawal->customer_id = $customer->id;
            $withdrawal->amount = $amount;
            $withdrawal->date = date('Y-m-d');
            $withdrawal->status = \app\models\Withdrawal::STATUS_PENDING;
            $withdrawal->comment = "Account Details: {$accountDetails}";
            $withdrawal->action_by = Yii::$app->user->id;
            $withdrawal->action_date_time = date('Y-m-d H:i:s');
            
            if (!$withdrawal->save()) {
                throw new \Exception('Failed to create withdrawal request: ' . implode(', ', $withdrawal->getFirstErrors()));
            }
            
            // Log activity
            $customer->logActivity('withdrawal_request', "Withdrawal request of $" . number_format($amount, 2), [
                'amount' => $amount,
                'account_details' => $accountDetails,
                'withdrawal_id' => $withdrawal->id
            ]);
            
            $transaction->commit();
            
            return [
                'success' => true,
                'message' => 'Withdrawal request submitted successfully. Your request is pending admin approval.'
            ];
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            
            return [
                'success' => false,
                'message' => 'Failed to process withdrawal request: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Upgrade package action
     */
    public function actionUpgrade()
    {
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();
            
        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }
        
        // Check if customer can upgrade
        $canUpgrade = Customer::canCustomerUpgrade($customer->id, null);
        
        if (Yii::$app->request->isPost) {
            $packageId = Yii::$app->request->post('package_id');
            
            if (!$canUpgrade) {
                Yii::$app->session->setFlash('error', 'You are not eligible for package upgrade.');
                return $this->redirect(['upgrade']);
            }
            
            $result = Customer::processPackageUpgrade($customer->id, $packageId, 'manual');
            
            if ($result['success']) {
                Yii::$app->session->setFlash('success', $result['message']);
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', $result['message']);
            }
        }
        
        // Get available packages for upgrade
        $availablePackages = Customer::getAvailablePackagesForUpgrade($customer->id);
        
        return $this->render('upgrade', [
            'customer' => $customer,
            'availablePackages' => $availablePackages,
            'canUpgrade' => $canUpgrade,
        ]);
    }

    /**
     * AJAX endpoint to get upgrade data for modal
     */
    public function actionGetUpgradeData()
     {
         Yii::$app->response->format = Response::FORMAT_JSON;
         
         $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->with(['currentPackage'])
            ->one();
            
        if (!$customer) {
            return [
                'success' => false,
                'message' => 'Customer profile not found.'
            ];
        }
         
         if (!Customer::canCustomerUpgrade($customer->id, null)) {
             return [
                 'success' => false,
                 'message' => 'You are not eligible for package upgrade.',
                 'canUpgrade' => false
             ];
         }
         
         $availablePackages = Customer::getAvailablePackagesForUpgrade($customer->id);
         
         return [
             'success' => true,
             'canUpgrade' => true,
             'currentPackage' => [
                 'name' => $customer->currentPackage->name,
                 'price' => $customer->currentPackage->amount
             ],
             'packages' => array_map(function($package) {
                 return [
                     'id' => $package->id,
                     'name' => $package->name,
                     'price' => $package->amount,
                     'description' => $package->description ?? ''
                 ];
             }, $availablePackages)
         ];
     }
    
    /**
     * AJAX endpoint to process upgrade form submission
     */
    public function actionProcessUpgrade()
     {
         Yii::$app->response->format = Response::FORMAT_JSON;
         
         if (!Yii::$app->request->isPost) {
             return ['success' => false, 'message' => 'Invalid request method.'];
         }
         
         $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->with(['currentPackage'])
            ->one();
            
        if (!$customer) {
            return ['success' => false, 'message' => 'Customer profile not found.'];
        }
         
         if (!Customer::canCustomerUpgrade($customer->id, null)) {
             return ['success' => false, 'message' => 'You are not eligible for package upgrade.'];
         }
         
         $packageId = Yii::$app->request->post('package_id');
         
         // Start database transaction
         $transaction = Yii::$app->db->beginTransaction();
         
         try {
             // Process package upgrade
             $result = Customer::processPackageUpgrade($customer->id, $packageId, 'manual');
             
             if (!$result['success']) {
                 $transaction->rollBack();
                 return ['success' => false, 'message' => $result['message']];
             }
             
             // Generate level income for the upgraded customer
             $levelIncomeResult = Customer::generateLevelIncome($customer->id);
             
             if (!$levelIncomeResult['success']) {
                 // If level income generation fails, rollback the entire transaction
                 $transaction->rollBack();
                 return [
                     'success' => false, 
                     'message' => 'Package upgrade failed due to level income generation error: ' . implode(', ', $levelIncomeResult['errors'])
                 ];
             }
             
             // If we reach here, both operations succeeded
             $transaction->commit();
             
             // Prepare success message
             $result['message'] .= " Level income generation: {$levelIncomeResult['generated_count']} records created.";
             if ($levelIncomeResult['error_count'] > 0) {
                 $result['message'] .= " {$levelIncomeResult['error_count']} errors occurred.";
             }
             
             return [
                 'success' => true,
                 'message' => $result['message'],
                 'redirect' => Url::to(['index'])
             ];
             
         } catch (\Exception $e) {
             // Rollback transaction on any exception
             $transaction->rollBack();
             
             return [
                 'success' => false,
                 'message' => 'An error occurred during package upgrade: ' . $e->getMessage()
             ];
         }
     }


    

}