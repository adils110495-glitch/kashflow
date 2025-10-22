<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use Yii;
use app\models\Customer;
use app\models\Package;
use app\models\CustomerPackage;
use app\models\Ticket;
use app\models\TicketChat;
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

        // Calculate financial data
        $financialData = $this->calculateFinancialData($customer->id);
        
        // Calculate additional metrics (investment, referrals, network, profit)
        $additionalMetrics = $this->calculateAdditionalMetrics($customer->id);

        return $this->render('index', [
            'customer' => $customer,
            'financialData' => $financialData,
            'additionalMetrics' => $additionalMetrics,
        ]);
    }

    /**
     * Calculate financial data for customer dashboard
     * @param int $customerId
     * @return array
     */
    private function calculateFinancialData($customerId)
    {
        $customer = Customer::findOne($customerId);
        
        if (!$customer) {
            return [
                'currentMonthIncome' => 0,
                'totalIncome' => 0,
                'totalWithdrawal' => 0,
                'currentBalance' => 0,
            ];
        }

        return [
            'currentMonthIncome' => $customer->getCurrentMonthIncome(),
            'totalIncome' => $customer->getTotalIncome(),
            'totalWithdrawal' => $customer->getTotalWithdrawalAmount(),
            'currentBalance' => $customer->getLedgerBalance(),
        ];
    }

    /**
     * Calculate additional metrics for customer dashboard
     * @param int $customerId
     * @return array
     */
    private function calculateAdditionalMetrics($customerId)
    {
        $customer = Customer::findOne($customerId);
        
        if (!$customer) {
            return [
                'investment' => 0,
                'referrals' => 0,
                'network' => 0,
                'profit' => 0,
            ];
        }

        // Investment: Selected package amount
        $investment = $customer->currentPackage ? $customer->currentPackage->amount : 0;

        // Referrals: Number of total direct referrals
        $referrals = Customer::getDirectReferralsCount($customerId);

        // Network: Number of members in level team
        $network = Customer::getLevelTeamCount($customerId);

        // Profit: Income - Investment
        $totalIncome = $customer->getTotalIncome();
        $profit = $totalIncome - $investment;

        return [
            'investment' => $investment,
            'referrals' => $referrals,
            'network' => $network,
            'profit' => $profit,
        ];
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

        // Build query for customer incomes
        $query = \app\models\Income::find()
            ->where(['customer_id' => $customer->id])
            ->orderBy(['created_at' => SORT_DESC]);

        // Apply type filter
        if (!empty($typeFilter)) {
            $query->andWhere(['type' => $typeFilter]);
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
            $withdrawalMethod = Yii::$app->request->post('withdrawal_method');
            $accountDetails = Yii::$app->request->post('account_details');
            
            // Validate withdrawal request
            if (empty($amount) || $amount <= 0) {
                Yii::$app->session->setFlash('error', 'Please enter a valid withdrawal amount.');
            } elseif ($amount < \app\models\Withdrawal::MIN_WITHDRAWAL_AMOUNT) {
                Yii::$app->session->setFlash('error', 'Minimum withdrawal amount is ₹' . number_format(\app\models\Withdrawal::MIN_WITHDRAWAL_AMOUNT, 2) . '.');
            } elseif ($amount > $currentBalance) {
                Yii::$app->session->setFlash('error', 'Insufficient balance for withdrawal.');
            } elseif (empty($withdrawalMethod)) {
                Yii::$app->session->setFlash('error', 'Please select a withdrawal method.');
            } elseif (empty($accountDetails)) {
                Yii::$app->session->setFlash('error', 'Please provide account details.');
            } else {
                // Create withdrawal request
                $result = $this->processWithdrawalRequest($customer, $amount, $withdrawalMethod, $accountDetails);
                
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
     * @param string $withdrawalMethod
     * @param string $accountDetails
     * @return array
     */
    private function processWithdrawalRequest($customer, $amount, $withdrawalMethod, $accountDetails)
    {
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Create withdrawal request in withdrawal table
            $withdrawal = new \app\models\Withdrawal();
            $withdrawal->customer_id = $customer->id;
            $withdrawal->amount = $amount;
            $withdrawal->withdrawal_method = $withdrawalMethod;
            $withdrawal->date = date('Y-m-d');
            $withdrawal->status = \app\models\Withdrawal::STATUS_PENDING;
            $withdrawal->comment = "Account Details: {$accountDetails}";
            $withdrawal->action_by = Yii::$app->user->id;
            $withdrawal->action_date_time = date('Y-m-d H:i:s');
            
            if (!$withdrawal->save()) {
                throw new \Exception('Failed to create withdrawal request: ' . implode(', ', $withdrawal->getFirstErrors()));
            }
            
            // Log activity
            $customer->logActivity('withdrawal_request', "Withdrawal request of ₹" . number_format($amount, 2) . " via " . $withdrawalMethod, [
                'amount' => $amount,
                'withdrawal_method' => $withdrawalMethod,
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

    /**
     * Fund request action
     */
    public function actionFundRequest()
    {
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        $fundRequest = new \app\models\FundRequest();

        if (Yii::$app->request->isPost) {
            $fundRequest->load(Yii::$app->request->post());
            $fundRequest->customer_id = $customer->id;
            $fundRequest->request_date = date('Y-m-d');

            // Handle file uploads
            $uploadedFile = \yii\web\UploadedFile::getInstance($fundRequest, 'attachment_file');
            
            // Validate attachment file is required
            if (!$uploadedFile) {
                Yii::$app->session->setFlash('error', 'Attachment file is required.');
            } else {
                // Validate file size (5MB limit)
                if ($uploadedFile->size > 5 * 1024 * 1024) {
                    Yii::$app->session->setFlash('error', 'Attachment file size must not exceed 5MB.');
                } else {
                    $fileName = 'attachment_' . time() . '_' . $uploadedFile->baseName . '.' . $uploadedFile->extension;
                    $filePath = 'uploads/fund_requests/' . $fileName;
                    if (!is_dir('uploads/fund_requests/')) {
                        mkdir('uploads/fund_requests/', 0755, true);
                    }
                    if ($uploadedFile->saveAs($filePath)) {
                        $fundRequest->attachment_file = $filePath;
                        
                        if ($fundRequest->save()) {
                            Yii::$app->session->setFlash('success', 'Fund request submitted successfully.');
                            return $this->redirect(['fund-request']);
                        } else {
                            Yii::$app->session->setFlash('error', 'Failed to submit fund request.');
                        }
                    } else {
                        Yii::$app->session->setFlash('error', 'Failed to upload attachment file.');
                    }
                }
            }
        }

        // Get customer's fund request history
        $fundRequests = \app\models\FundRequest::getCustomerRequests($customer->id, 10);

        return $this->render('fund-request', [
            'customer' => $customer,
            'fundRequest' => $fundRequest,
            'fundRequests' => $fundRequests,
        ]);
    }

    /**
     * Displays customer tickets/complaints
     */
    public function actionTickets()
    {
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        // Apply filters
        $statusFilter = Yii::$app->request->get('status', '');
        $priorityFilter = Yii::$app->request->get('priority', '');

        // Build query for customer tickets
        $query = Ticket::getCustomerTickets($customer->id);

        // Apply status filter
        if (!empty($statusFilter)) {
            $query->andWhere(['status' => $statusFilter]);
        }

        // Apply priority filter
        if (!empty($priorityFilter)) {
            $query->andWhere(['priority' => $priorityFilter]);
        }

        $tickets = $query->all();
        
        // Get ticket statistics
        $stats = Ticket::getCustomerTicketStats($customer->id);

        return $this->render('tickets', [
            'customer' => $customer,
            'tickets' => $tickets,
            'stats' => $stats,
            'statusFilter' => $statusFilter,
            'priorityFilter' => $priorityFilter,
        ]);
    }

    /**
     * Create a new ticket/complaint
     */
    public function actionCreateTicket()
    {
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        $model = new Ticket();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->customer_id = $customer->id;
            
            if ($model->validate()) {
                // Create ticket using the model
                if ($model->save()) {
                    // Log activity
                    $customer->logActivity('support_ticket', "Support ticket created: {$model->subject}", [
                        'ticket_id' => $model->id,
                        'subject' => $model->subject,
                        'priority' => $model->priority
                    ]);

                    Yii::$app->session->setFlash('success', 'Ticket created successfully. We will respond to you soon.');
                    return $this->redirect(['tickets']);
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to create ticket: ' . implode(', ', $model->getFirstErrors()));
                }
            } else {
                Yii::$app->session->setFlash('error', 'Please fill in all required fields correctly.');
            }
        }

        return $this->render('create-ticket', [
            'customer' => $customer,
            'model' => $model,
        ]);
    }

    /**
     * View a specific ticket
     */
    public function actionViewTicket($id)
    {
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        $ticket = Ticket::find()
            ->where(['id' => $id, 'customer_id' => $customer->id])
            ->one();

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket not found.');
        }

        // Get chat messages for this ticket
        $chatMessages = TicketChat::getTicketChats($ticket->id)->all();
        
        // Mark admin messages as read
        TicketChat::markAsRead($ticket->id, TicketChat::SENDER_ADMIN);

        return $this->render('view-ticket', [
            'customer' => $customer,
            'ticket' => $ticket,
            'chatMessages' => $chatMessages,
        ]);
    }

    /**
     * Send a chat message to a ticket
     */
    public function actionSendMessage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method.'];
        }

        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();

        if (!$customer) {
            return ['success' => false, 'message' => 'Customer not found.'];
        }

        $ticketId = Yii::$app->request->post('ticket_id');
        $message = Yii::$app->request->post('message');

        if (empty($ticketId) || empty($message)) {
            return ['success' => false, 'message' => 'Please provide ticket ID and message.'];
        }

        $ticket = Ticket::find()
            ->where(['id' => $ticketId, 'customer_id' => $customer->id])
            ->one();

        if (!$ticket) {
            return ['success' => false, 'message' => 'Ticket not found.'];
        }

        // Check if customer can communicate
        if (!$ticket->canCustomerCommunicate()) {
            return ['success' => false, 'message' => 'This ticket is closed. You cannot send messages.'];
        }

        // Add message to chat
        $result = TicketChat::addMessage($ticket->id, TicketChat::SENDER_CUSTOMER, $customer->id, $message);

        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Message sent successfully.',
                'chat' => [
                    'id' => $result['chat']->id,
                    'message' => $result['chat']->message,
                    'sender_name' => $result['chat']->getSenderName(),
                    'formatted_time' => $result['chat']->getFormattedTime(),
                    'sender_type' => $result['chat']->sender_type
                ]
            ];
        }

        return ['success' => false, 'message' => $result['message']];
    }

    /**
     * Get chat messages for a ticket (AJAX)
     */
    public function actionGetChatMessages($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();

        if (!$customer) {
            return ['success' => false, 'message' => 'Customer not found.'];
        }

        $ticket = Ticket::find()
            ->where(['id' => $id, 'customer_id' => $customer->id])
            ->one();

        if (!$ticket) {
            return ['success' => false, 'message' => 'Ticket not found.'];
        }

        $chatMessages = TicketChat::getTicketChats($ticket->id)->all();
        
        // Mark admin messages as read
        TicketChat::markAsRead($ticket->id, TicketChat::SENDER_ADMIN);

        $messages = [];
        foreach ($chatMessages as $chat) {
            $messages[] = [
                'id' => $chat->id,
                'message' => $chat->message,
                'sender_name' => $chat->getSenderName(),
                'sender_type' => $chat->sender_type,
                'formatted_time' => $chat->getFormattedTime(),
                'is_read' => $chat->is_read
            ];
        }

        return [
            'success' => true,
            'messages' => $messages,
            'can_communicate' => $ticket->canCustomerCommunicate()
        ];
    }

    /**
     * Fund transfer functionality for customers
     */
    public function actionFundTransfer()
    {
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        $fundTransfer = new \app\models\FundTransfer();

        if (Yii::$app->request->isPost) {
            $fundTransfer->load(Yii::$app->request->post());
            $fundTransfer->from_customer_id = $customer->id;
            $fundTransfer->transfer_date = date('Y-m-d');
            $fundTransfer->transfer_type = \app\models\FundTransfer::TYPE_CUSTOMER_TO_CUSTOMER;
            $fundTransfer->status = \app\models\FundTransfer::STATUS_PENDING;

            // Validate sufficient balance
            if (!\app\models\FundTransfer::hasSufficientBalance($customer->id, $fundTransfer->amount)) {
                Yii::$app->session->setFlash('error', 'Insufficient balance for this transfer.');
            } else {
                if ($fundTransfer->save()) {
                    Yii::$app->session->setFlash('success', 'Fund transfer request submitted successfully. It will be processed by admin.');
                    return $this->redirect(['fund-transfer']);
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to submit fund transfer request.');
                }
            }
        }

        // Get customer's transfer history
        $outgoingTransfers = \app\models\FundTransfer::getCustomerOutgoingTransfers($customer->id, 10);
        $incomingTransfers = \app\models\FundTransfer::getCustomerIncomingTransfers($customer->id, 10);
        
        // Get transfers pending receiver approval
        $pendingReceiverApprovalTransfers = \app\models\FundTransfer::getCustomerPendingReceiverApprovalTransfers($customer->id);

        // Get all customers for transfer dropdown
        $customers = Customer::find()
            ->with('user')
            ->where(['!=', 'id', $customer->id])
            ->all();

        return $this->render('fund-transfer', [
            'customer' => $customer,
            'fundTransfer' => $fundTransfer,
            'outgoingTransfers' => $outgoingTransfers,
            'incomingTransfers' => $incomingTransfers,
            'pendingReceiverApprovalTransfers' => $pendingReceiverApprovalTransfers,
            'customers' => $customers,
        ]);
    }

    /**
     * Receiver approval for fund transfers
     */
    public function actionFundTransferApproval($id)
    {
        $customer = Customer::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->one();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        $fundTransfer = \app\models\FundTransfer::find()
            ->where(['id' => $id, 'to_customer_id' => $customer->id])
            ->one();

        if (!$fundTransfer) {
            throw new NotFoundHttpException('Fund transfer not found or you are not authorized to approve this transfer.');
        }

        if (!$fundTransfer->canBeApprovedByReceiver()) {
            Yii::$app->session->setFlash('error', 'This transfer cannot be approved at this time.');
            return $this->redirect(['fund-transfer']);
        }

        if (Yii::$app->request->isPost) {
            $action = Yii::$app->request->post('action', '');
            $comment = Yii::$app->request->post('receiver_comment', '');

            if ($action === 'approve') {
                $status = \app\models\FundTransfer::STATUS_RECEIVER_APPROVED;
                $message = 'Fund transfer approved successfully.';
            } elseif ($action === 'reject') {
                $status = \app\models\FundTransfer::STATUS_RECEIVER_REJECTED;
                $message = 'Fund transfer rejected successfully.';
            } else {
                Yii::$app->session->setFlash('error', 'Invalid action.');
                return $this->redirect(['fund-transfer']);
            }

            if ($fundTransfer->processReceiverApproval($status, $comment)) {
                Yii::$app->session->setFlash('success', $message);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to process fund transfer approval.');
            }

            return $this->redirect(['fund-transfer']);
        }

        return $this->render('fund-transfer-approval', [
            'customer' => $customer,
            'fundTransfer' => $fundTransfer,
        ]);
    }

    /**
     * KYC Profile action
     * @return string|Response
     */
    public function actionKyc()
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        if (Yii::$app->request->isPost) {
            $customerData = Yii::$app->request->post('Customer');
            $cryptoWalletAddress = $customerData['crypto_wallet_address'] ?? '';
            $upiId = $customerData['upi_id'] ?? '';
            $aadharNumber = $customerData['aadhar_number'] ?? '';
            $panNumber = $customerData['pan_number'] ?? '';
            $bankAccountNumber = $customerData['bank_account_number'] ?? '';
            $bankAccountHolderName = $customerData['bank_account_holder_name'] ?? '';
            $bankName = $customerData['bank_name'] ?? '';
            $bankIfscCode = $customerData['bank_ifsc_code'] ?? '';
            $bankBranchName = $customerData['bank_branch_name'] ?? '';
            $bankAccountType = $customerData['bank_account_type'] ?? '';
            $currencyId = $customerData['currency_id'] ?? null;
            $kycTerms = Yii::$app->request->post('kyc_terms');
            
            // Validate KYC terms acceptance
            if (!$kycTerms) {
                Yii::$app->session->setFlash('error', 'You must accept the KYC terms and conditions.');
                return $this->render('kyc', ['model' => $customer]);
            }
            
            // Validate Aadhar number format
            if (!empty($aadharNumber) && !Customer::validateAadharNumber($aadharNumber)) {
                Yii::$app->session->setFlash('error', 'Please enter a valid 12-digit Aadhar number.');
                return $this->render('kyc', ['model' => $customer]);
            }
            
            // Validate PAN number format
            if (!empty($panNumber) && !Customer::validatePanNumber($panNumber)) {
                Yii::$app->session->setFlash('error', 'Please enter a valid PAN number (e.g., ABCDE1234F).');
                return $this->render('kyc', ['model' => $customer]);
            }
            
            // Validate bank account number format
            if (!empty($bankAccountNumber) && !Customer::validateBankAccountNumber($bankAccountNumber)) {
                Yii::$app->session->setFlash('error', 'Please enter a valid bank account number (9-18 digits).');
                return $this->render('kyc', ['model' => $customer]);
            }
            
            // Validate IFSC code format
            if (!empty($bankIfscCode) && !Customer::validateIfscCode($bankIfscCode)) {
                Yii::$app->session->setFlash('error', 'Please enter a valid IFSC code (e.g., SBIN0001234).');
                return $this->render('kyc', ['model' => $customer]);
            }
            
            // Handle file uploads
            $uploadPath = Yii::getAlias('@webroot/uploads/kyc-documents/');
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Handle Aadhar card image upload
            $aadharCardFile = UploadedFile::getInstance($customer, 'aadhar_card_image');
            if ($aadharCardFile) {
                $fileName = 'aadhar_' . $customer->id . '_' . time() . '.' . $aadharCardFile->extension;
                $filePath = $uploadPath . $fileName;
                
                if ($aadharCardFile->saveAs($filePath)) {
                    // Delete old Aadhar card if exists
                    if ($customer->aadhar_card_image && file_exists($uploadPath . $customer->aadhar_card_image)) {
                        unlink($uploadPath . $customer->aadhar_card_image);
                    }
                    $customer->aadhar_card_image = $fileName;
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to upload Aadhar card image.');
                    return $this->render('kyc', ['model' => $customer]);
                }
            }
            
            // Handle PAN card image upload
            $panCardFile = UploadedFile::getInstance($customer, 'pan_card_image');
            if ($panCardFile) {
                $fileName = 'pan_' . $customer->id . '_' . time() . '.' . $panCardFile->extension;
                $filePath = $uploadPath . $fileName;
                
                if ($panCardFile->saveAs($filePath)) {
                    // Delete old PAN card if exists
                    if ($customer->pan_card_image && file_exists($uploadPath . $customer->pan_card_image)) {
                        unlink($uploadPath . $customer->pan_card_image);
                    }
                    $customer->pan_card_image = $fileName;
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to upload PAN card image.');
                    return $this->render('kyc', ['model' => $customer]);
                }
            }
            
            // Handle QR code image upload
            $qrCodeFile = UploadedFile::getInstance($customer, 'qr_code_image');
            if ($qrCodeFile) {
                $qrUploadPath = Yii::getAlias('@webroot/uploads/qr-codes/');
                
                // Create directory if it doesn't exist
                if (!is_dir($qrUploadPath)) {
                    mkdir($qrUploadPath, 0755, true);
                }
                
                // Generate unique filename
                $fileName = 'qr_' . $customer->id . '_' . time() . '.' . $qrCodeFile->extension;
                $filePath = $qrUploadPath . $fileName;
                
                if ($qrCodeFile->saveAs($filePath)) {
                    // Delete old QR code if exists
                    if ($customer->qr_code_image && file_exists($qrUploadPath . $customer->qr_code_image)) {
                        unlink($qrUploadPath . $customer->qr_code_image);
                    }
                    $customer->qr_code_image = $fileName;
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to upload QR code image.');
                    return $this->render('kyc', ['model' => $customer]);
                }
            }
            
            // Update KYC information
            $customer->crypto_wallet_address = $cryptoWalletAddress;
            $customer->upi_id = $upiId;
            $customer->aadhar_number = $aadharNumber;
            $customer->pan_number = strtoupper($panNumber); // Store PAN in uppercase
            $customer->bank_account_number = $bankAccountNumber;
            $customer->bank_account_holder_name = $bankAccountHolderName;
            $customer->bank_name = $bankName;
            $customer->bank_ifsc_code = strtoupper($bankIfscCode); // Store IFSC in uppercase
            $customer->bank_branch_name = $bankBranchName;
            $customer->bank_account_type = $bankAccountType;
            $customer->currency_id = $currencyId;
            
            // Set KYC status to pending for review
            $customer->kyc_status = Customer::KYC_STATUS_PENDING;
            
            if ($customer->save()) {
                // Log activity
                $customer->logActivity('kyc_updated', 'KYC profile updated', [
                    'crypto_wallet_address' => $cryptoWalletAddress,
                    'upi_id' => $upiId,
                    'aadhar_number' => $customer->getMaskedAadharNumber(),
                    'pan_number' => $customer->getMaskedPanNumber(),
                    'bank_account_number' => $customer->getMaskedBankAccountNumber(),
                    'bank_name' => $bankName,
                    'bank_ifsc_code' => $bankIfscCode,
                    'bank_account_type' => $bankAccountType,
                    'currency_id' => $currencyId,
                    'aadhar_card_uploaded' => $aadharCardFile ? true : false,
                    'pan_card_uploaded' => $panCardFile ? true : false,
                    'qr_code_uploaded' => $qrCodeFile ? true : false
                ]);
                
                Yii::$app->session->setFlash('success', 'KYC profile updated successfully. Your information is under review.');
                return $this->redirect(['kyc']);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update KYC profile: ' . implode(', ', $customer->getFirstErrors()));
            }
        }

        return $this->render('kyc', ['model' => $customer]);
    }

    /**
     * Get current customer
     * @return Customer|null
     */
    private function getCurrentCustomer()
    {
        if (!Yii::$app->user->isGuest) {
            return Customer::find()
                ->where(['user_id' => Yii::$app->user->id])
                ->with(['currentPackage', 'user', 'kycVerifiedBy'])
                ->one();
        }
        return null;
    }

}