<?php
namespace app\controllers\user;

use dektrium\user\controllers\SecurityController as BaseSecurityController;
use dektrium\user\controllers\RegistrationController as BaseRegistrationController;

use dektrium\user\models\LoginForm;
use app\models\Customer;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use Yii;

class AdminController extends BaseSecurityController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Override VerbFilter to allow both GET and POST for logout
        if (isset($behaviors['verbs'])) {
            $behaviors['verbs']['actions']['logout'] = ['get', 'post'];
        }
        
        // Add access control for admin-only actions
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['customer', 'referred-team', 'level-team', 'income'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['customer', 'referred-team', 'level-team', 'income'],
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
        ];
        
        return $behaviors;
    }
    public function actionLogin()
    {
        $this->layout = '@app/views/layouts/main-login';
        
        // Handle AJAX validation first to prevent null appending
        if (\Yii::$app->request->isAjax) {
            $model = \Yii::createObject(LoginForm::class);
            if ($model->load(\Yii::$app->request->post())) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return \yii\widgets\ActiveForm::validate($model);
            }
        }
        
        // Check if user is already logged in
        if (!\Yii::$app->user->isGuest) {
            return $this->redirectAfterLogin();
        }
        
        // Handle login form submission
        $model = \Yii::createObject(LoginForm::class);
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->redirectAfterLogin();
        }
        
        return parent::actionLogin();
    }
    
    /**
     * Redirect user after successful login based on their role
     * @return \yii\web\Response
     */
    protected function redirectAfterLogin()
    {
        // Check if the logged-in user is a customer
        $customer = Customer::find()->where(['user_id' => \Yii::$app->user->id])->one();
        
        if ($customer) {
            // Redirect customers to their dashboard
            return $this->redirect(['/customer-dashboard/index']);
        }
        
        // For non-customers (admins, etc.), use default redirect
        return $this->goBack();
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionRegister()
    {
        $this->layout = '@app/views/layouts/main-login';
        // Delegate to Dektrium registration action to keep logic
        /** @var BaseRegistrationController $controller */
        $controller = Yii::createObject(BaseRegistrationController::class, ['registration', Yii::$app->getModule('user')]);
        $controller->layout = $this->layout;
        $controller->action->id = 'register';
        return $controller->runAction('register');
    }

    /**
     * Display all customers
     */
    public function actionCustomer()
    {
        $this->layout = '@app/views/layouts/sidebar';
        
        $dataProvider = new ActiveDataProvider([
            'query' => Customer::find()->with('user'),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('@app/views/user/admin/customer', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Display referred team with filters
     */
    public function actionReferredTeam()
    {
        $this->layout = '@app/views/layouts/sidebar';
        
        $selectedCustomer = Yii::$app->request->get('customer');
        $dateFrom = Yii::$app->request->get('date_from');
        $dateTo = Yii::$app->request->get('date_to');
        
        $query = Customer::find()->with('user');
        
        if ($selectedCustomer) {
            // TODO: Implement referral_code-based filtering
            // $query->where(['referral_code' => $selectedCustomer]);
        }
        
        if ($dateFrom) {
            $query->andWhere(['>=', 'created_at', $dateFrom]);
        }
        
        if ($dateTo) {
            $query->andWhere(['<=', 'created_at', $dateTo]);
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        
        $customers = Customer::find()->with('user')->all();

        return $this->render('@app/views/user/admin/referred-team', [
            'dataProvider' => $dataProvider,
            'customers' => $customers,
            'selectedCustomer' => $selectedCustomer,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Display level team for selected customer
     */
    public function actionLevelTeam()
    {
        $this->layout = '@app/views/layouts/sidebar';
        
        // Get filter parameters
        $usernameFilter = Yii::$app->request->get('username', '');
        $fromDate = Yii::$app->request->get('from_date', '');
        $toDate = Yii::$app->request->get('to_date', '');
        $levelFilter = Yii::$app->request->get('level', '');
        
        $levelTeam = [];
        $levelPlans = [];
        
        // Get all level plans to determine max levels
        $levelPlans = \app\models\LevelPlan::find()
            ->where(['status' => 1])
            ->orderBy('level ASC')
            ->all();
        
        if (!empty($usernameFilter)) {
            // Build level team hierarchy based on referral_code
            $levelTeam = $this->buildLevelTeamHierarchy($usernameFilter, $usernameFilter, $fromDate, $toDate, $levelFilter, $levelPlans);
        }
        
        return $this->render('@app/views/user/admin/level-team', [
            'levelTeam' => $levelTeam,
            'levelPlans' => $levelPlans,
            'usernameFilter' => $usernameFilter,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'levelFilter' => $levelFilter,
        ]);
    }

    /**
     * Display income for selected customer
     */
    public function actionIncome()
    {
        $this->layout = '@app/views/layouts/sidebar';
        
        $selectedCustomer = Yii::$app->request->get('customer');
        $incomeData = [];
        
        if ($selectedCustomer) {
            // Get income data for the selected customer
            $incomeData = $this->getIncomeData($selectedCustomer);
        }
        
        $customers = Customer::find()->with('user')->all();
        
        return $this->render('@app/views/user/admin/income', [
            'incomeData' => $incomeData,
            'customers' => $customers,
            'selectedCustomer' => $selectedCustomer,
        ]);
    }

    /**
     * Build level team hierarchy based on referral_code
     */
    private function buildLevelTeamHierarchy($rootUsername, $usernameFilter, $fromDate, $toDate, $levelFilter, $levelPlans)
    {
        $levelTeam = [];
        $maxLevel = count($levelPlans);
        
        // Find the root customer by username
        $rootCustomer = Customer::find()
            ->joinWith('user')
            ->where(['user.username' => $rootUsername])
            ->one();
        
        if (!$rootCustomer) {
            return $levelTeam;
        }
        
        // Build level team recursively
        $this->buildLevelTeamRecursive($rootCustomer->user->username, 1, $maxLevel, $usernameFilter, $fromDate, $toDate, $levelFilter, $levelTeam);
        
        return $levelTeam;
    }
    
    /**
     * Recursively build level team hierarchy
     */
    private function buildLevelTeamRecursive($currentUsername, $currentLevel, $maxLevel, $usernameFilter, $fromDate, $toDate, $levelFilter, &$levelTeam)
    {
        if ($currentLevel > $maxLevel) {
            return;
        }
        
        // Build query for customers referred by current username
        $query = Customer::find()
            ->joinWith(['user', 'currentPackage'])
            ->where(['customer.referral_code' => $currentUsername]);
        
        // Apply filters
        if (!empty($usernameFilter)) {
            $query->andWhere(['like', 'user.username', $usernameFilter]);
        }
        
        if (!empty($fromDate)) {
            $fromTimestamp = strtotime($fromDate . ' 00:00:00');
            if ($fromTimestamp !== false) {
                $query->andWhere(['>=', 'customer.created_at', $fromTimestamp]);
            }
        }
        
        if (!empty($toDate)) {
            $toTimestamp = strtotime($toDate . ' 23:59:59');
            if ($toTimestamp !== false) {
                $query->andWhere(['<=', 'customer.created_at', $toTimestamp]);
            }
        }
        
        $customers = $query->all();
        
        // If level filter is specified and doesn't match current level, skip customers but continue recursion
        $includeCurrentLevel = empty($levelFilter) || $levelFilter == $currentLevel;
        
        if ($includeCurrentLevel && !empty($customers)) {
            $levelTeam[$currentLevel] = [
                'level' => $currentLevel,
                'customers' => $customers,
                'count' => count($customers)
            ];
        }
        
        // Recursively get customers from next level
        foreach ($customers as $customer) {
            if ($customer->user) {
                $this->buildLevelTeamRecursive(
                    $customer->user->username, 
                    $currentLevel + 1, 
                    $maxLevel, 
                    $usernameFilter, 
                    $fromDate, 
                    $toDate, 
                    $levelFilter, 
                    $levelTeam
                );
            }
        }
    }

    /**
     * Get income data for a customer
     */
    private function getIncomeData($customerId)
    {
        $incomeData = [
            'total_income' => 0,
            'referral_income' => 0,
            'level_income' => 0,
            'monthly_breakdown' => [],
        ];
        
        $customer = Customer::findOne($customerId);
        
        if ($customer) {
            // Calculate referral income (direct referrals)
            $directReferrals = Customer::find()->where(['referrer_id' => $customerId])->count();
            $incomeData['referral_income'] = $directReferrals * 100; // Assuming $100 per referral
            
            // Calculate level income
            $levelTeam = $this->getLevelTeamData($customerId);
            $level2Count = count($levelTeam[2] ?? []);
            $level3Count = count($levelTeam[3] ?? []);
            $incomeData['level_income'] = ($level2Count * 50) + ($level3Count * 25); // Level 2: $50, Level 3: $25
            
            $incomeData['total_income'] = $incomeData['referral_income'] + $incomeData['level_income'];
            
            // Monthly breakdown (simplified)
            $incomeData['monthly_breakdown'] = [
                date('Y-m') => $incomeData['total_income'],
            ];
        }
        
        return $incomeData;
    }
}