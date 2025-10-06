<?php

namespace app\controllers;

use Yii;
use app\models\Customer;
use app\models\CustomerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'referred-team', 'level-team', 'income'],
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
                    $customer = Customer::find()->where(['user_id' => Yii::$app->user->id])->one();
                    
                    // For admin-only actions, redirect customers to their dashboard
                    if ($customer) {
                        return Yii::$app->response->redirect(['/customer-dashboard/index']);
                    }
                    // Redirect guests to login
                    return Yii::$app->response->redirect(['/user/login']);
                },
            ],
        ];
    }

    /**
     * Lists all Customer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Customer model.
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
     * Creates a new Customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customer();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Customer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Displays referred team with filters
     */
    public function actionReferredTeam()
    {
        // Get filter parameters
        $selectedCustomer = Yii::$app->request->get('customer', '');
        $dateFrom = Yii::$app->request->get('date_from', '');
        $dateTo = Yii::$app->request->get('date_to', '');
        
        // Build query for referred team based on referral_code
        $query = Customer::find()
            ->joinWith(['user', 'currentPackage'])
            ->where(['not', ['referral_code' => null]])
            ->andWhere(['!=', 'referral_code', ''])
            ->orderBy(['created_at' => SORT_DESC]);
        
        // Apply customer filter - search in referral_code field
        if (!empty($selectedCustomer)) {
            // Find the selected customer and get their username
            $customer = Customer::findOne($selectedCustomer);
            if ($customer && $customer->user) {
                $query->andWhere(['like', 'referral_code', $customer->user->username]);
            }
        }
        
        // Apply date filters
        if (!empty($dateFrom)) {
            $fromTimestamp = strtotime($dateFrom . ' 00:00:00');
            if ($fromTimestamp !== false) {
                $query->andWhere(['>=', 'customer.created_at', $fromTimestamp]);
            }
        }
        
        if (!empty($dateTo)) {
            $toTimestamp = strtotime($dateTo . ' 23:59:59');
            if ($toTimestamp !== false) {
                $query->andWhere(['<=', 'customer.created_at', $toTimestamp]);
            }
        }
        
        // Create data provider
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
        
        // Get all customers for dropdown
        $customers = Customer::find()->with('user')->all();
        
        return $this->render('referred-team', [
            'dataProvider' => $dataProvider,
            'customers' => $customers,
            'selectedCustomer' => $selectedCustomer,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Displays level team with hierarchy
     */
    public function actionLevelTeam()
    {
        // Get filter parameters
        $usernameFilter = Yii::$app->request->get('username', '');
        $fromDate = Yii::$app->request->get('from_date', '');
        $toDate = Yii::$app->request->get('to_date', '');
        $level = Yii::$app->request->get('level', '');
        $status = Yii::$app->request->get('status', '');
        
        $levelTeam = [];
        $packageStats = [];
        $customer = null;
        
        // If no username filter provided, show empty state
        if (empty($usernameFilter)) {
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
        
        // Find the customer by username
        $customer = Customer::find()
            ->joinWith('user')
            ->where(['user.username' => $usernameFilter])
            ->one();
        
        if (!$customer) {
            // Customer not found, show empty state
            return $this->render('level-team', [
                'customer' => null,
                'levelTeam' => [],
                'packageStats' => [],
                'usernameFilter' => $usernameFilter,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'level' => $level,
                'status' => $status,
            ]);
        }
        
        // Build level team hierarchy using the same method as customer-dashboard
        $levelTeam = Customer::buildLevelTeam($usernameFilter, $usernameFilter, $fromDate, $toDate, $level, $status);
        
        // Calculate package statistics using the same method as customer-dashboard
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
     * Displays customer income
     */
    public function actionIncome()
    {
        $selectedCustomer = Yii::$app->request->get('customer');
        $incomeData = [];
        
        if ($selectedCustomer) {
            $incomeData = $this->getIncomeData($selectedCustomer);
        }
        
        $customers = Customer::find()->with('user')->all();
        
        return $this->render('income', [
            'incomeData' => $incomeData,
            'customers' => $customers,
            'selectedCustomer' => $selectedCustomer,
        ]);
    }


    /**
     * Get income data for selected customer
     */
    private function getIncomeData($customerId)
    {
        // This is a placeholder - implement based on your income logic
        return [
            'total_income' => 0,
            'monthly_income' => 0,
            'referral_income' => 0,
            'level_income' => 0,
        ];
    }

    /**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}