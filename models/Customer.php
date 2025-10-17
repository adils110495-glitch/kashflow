<?php

namespace app\models;

use app\models\base\Customer as BaseCustomer;
use app\models\Package;
use app\models\CustomerPackage;
use app\models\CustomerActivity;
use app\models\Income;

/**
 * Customer model
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $email
 * @property string $mobile_no
 * @property string $referral_code
 * @property int $country_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Country $country
 * @property \dektrium\user\models\User $user
 */
class Customer extends BaseCustomer
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_PENDING = 2;
    const STATUS_EXPIRED = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_SUSPENDED = 5;

    /**
     * Generate next customer username in format KF000001
     * @return string
     */
    public static function generateCustomerUsername()
    {
        // Find the highest existing customer ID
        $lastCustomer = static::find()->orderBy(['id' => SORT_DESC])->one();
        $nextId = $lastCustomer ? $lastCustomer->id + 1 : 1;
        
        // Format as KF + 6-digit padded number
        return 'KF' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get current package relation
     */
    public function getCurrentPackage()
    {
        return $this->hasOne(Package::class, ['id' => 'current_package']);
    }



    /**
     * Get status text
     */
    public function getStatusText()
    {
        return $this->status == 1 ? 'Active' : 'Inactive';
    }

    /**
     * Get customer packages relation
     */
    public function getCustomerPackages()
    {
        return $this->hasMany(CustomerPackage::class, ['customer_id' => 'id']);
    }

    /**
     * Get active customer packages
     */
    public function getActiveCustomerPackages()
    {
        return $this->hasMany(CustomerPackage::class, ['customer_id' => 'id'])
            ->where(['status' => CustomerPackage::STATUS_ACTIVE]);
    }

    /**
     * Get customer activities relation
     */
    public function getActivities()
    {
        return $this->hasMany(CustomerActivity::class, ['customer_id' => 'id']);
    }

    /**
     * Get customer incomes relation
     */
    public function getIncomes()
    {
        return $this->hasMany(Income::class, ['customer_id' => 'id']);
    }

    /**
     * Get customer ledger entries relation
     */
    public function getLedgerEntries()
    {
        return $this->hasMany(Ledger::class, ['customer_id' => 'id']);
    }

    /**
     * Get active customer ledger entries
     */
    public function getActiveLedgerEntries()
    {
        return $this->hasMany(Ledger::class, ['customer_id' => 'id'])
            ->where(['status' => Ledger::STATUS_ACTIVE]);
    }

    /**
     * Get active customer incomes
     */
    public function getActiveIncomes()
    {
        return $this->hasMany(Income::class, ['customer_id' => 'id'])
            ->where(['status' => Income::STATUS_ACTIVE]);
    }

    /**
     * Get recent customer activities
     */
    public function getRecentActivities($limit = 10)
    {
        return $this->hasMany(CustomerActivity::class, ['customer_id' => 'id'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit);
    }

    /**
     * Get activities by type
     */
    public function getActivitiesByType($activityType, $limit = 10)
    {
        return $this->hasMany(CustomerActivity::class, ['customer_id' => 'id'])
            ->where(['activity_type' => $activityType])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit);
    }

    /**
     * Log customer activity
     */
    public function logActivity($activityType, $description = null, $metadata = null)
    {
        return CustomerActivity::logActivity($this->id, $activityType, $description, $metadata);
    }



    /**
     * Calculate package statistics for customers
     * @param array $customers
     * @return array
     */
    public static function calculatePackageStats($customers)
    {
        // Get all active packages from database
        $allPackages = \app\models\Package::find()
            ->where(['status' => [\app\models\Package::STATUS_ACTIVE, \app\models\Package::STATUS_PREMIUM]])
            ->orderBy(['amount' => SORT_ASC])
            ->all();

        $packageStats = [];
        $totalCustomers = count($customers);

        // Initialize stats for all packages
        foreach ($allPackages as $package) {
            $packageStats[$package->name] = [
                'id' => $package->id,
                'name' => $package->name,
                'amount' => $package->amount,
                'count' => 0,
                'percentage' => 0,
                'paid_count' => 0,
                'unpaid_count' => 0,
                'paid_percentage' => 0,
                'unpaid_percentage' => 0
            ];
        }

        // Add "No Package" entry
        $packageStats['No Package'] = [
            'id' => 0,
            'name' => 'No Package',
            'amount' => 0,
            'count' => 0,
            'percentage' => 0,
            'paid_count' => 0,
            'unpaid_count' => 0,
            'paid_percentage' => 0,
            'unpaid_percentage' => 0
        ];

        // Count customers by package and payment status
        $customersId = array_column($customers,'id');
        $customers = Customer::find()
            ->where(['id' => $customersId])
            ->all();
        foreach ($customers as $customer ) {
            $packageName = $customer->currentPackage->name ?? 'No Package';
            
            if (isset($packageStats[$packageName])) {
                $packageStats[$packageName]['count']++;
                
                // Check payment status from customer packages
                $customerPackage = \app\models\CustomerPackage::find()
                    ->where(['customer_id' => $customer->id, 'package_id' => $customer->currentPackage->id ?? 0])
                    ->orderBy(['created_at' => SORT_DESC])
                    ->one();
                
                if ($customerPackage) {
                    if ($customerPackage->status == \app\models\CustomerPackage::STATUS_ACTIVE) {
                        $packageStats[$packageName]['paid_count']++;
                    } else {
                        $packageStats[$packageName]['unpaid_count']++;
                    }
                } else {
                    $packageStats[$packageName]['unpaid_count']++;
                }
            }
        }

        // Calculate percentages
        foreach ($packageStats as $packageName => &$stats) {
            $stats['percentage'] = $totalCustomers > 0 ? round(($stats['count'] / $totalCustomers) * 100, 2) : 0;
            $stats['paid_percentage'] = $totalCustomers > 0 ? round(($stats['paid_count'] / $totalCustomers) * 100, 2) : 0;
            $stats['unpaid_percentage'] = $totalCustomers > 0 ? round(($stats['unpaid_count'] / $totalCustomers) * 100, 2) : 0;
        }

        // Create summary stats in the format expected by the view
        $summaryStats = [
            'free' => [
                'unpaid' => isset($packageStats['No Package']) ? $packageStats['No Package']['unpaid_count'] : 0,
                'paid' => isset($packageStats['No Package']) ? $packageStats['No Package']['paid_count'] : 0
            ],
            'paid' => [
                'paid' => 0,
                'unpaid' => 0
            ],
            'total' => [
                'paid' => 0,
                'unpaid' => 0
            ]
        ];

        // Calculate paid package totals
        foreach ($packageStats as $packageName => $stats) {
            if ($packageName !== 'No Package') {
                $summaryStats['paid']['paid'] += $stats['paid_count'];
                $summaryStats['paid']['unpaid'] += $stats['unpaid_count'];
            }
            $summaryStats['total']['paid'] += $stats['paid_count'];
            $summaryStats['total']['unpaid'] += $stats['unpaid_count'];
        }

        return $summaryStats;
    }

    /**
     * Calculate detailed package statistics for customers (for admin/customer views)
     * @param array $customers
     * @return array
     */
    public static function calculateDetailedPackageStats($customers)
    {
        // Get all active packages from database
        $allPackages = \app\models\Package::find()
            ->where(['status' => [\app\models\Package::STATUS_ACTIVE, \app\models\Package::STATUS_PREMIUM]])
            ->orderBy(['amount' => SORT_ASC])
            ->all();

        $packageStats = [];
        $totalCustomers = count($customers);

        // Initialize stats for all packages
        foreach ($allPackages as $package) {
            $packageStats[$package->name] = [
                'id' => $package->id,
                'name' => $package->name,
                'amount' => $package->amount,
                'count' => 0,
                'percentage' => 0,
                'paid_count' => 0,
                'unpaid_count' => 0,
                'paid_percentage' => 0,
                'unpaid_percentage' => 0
            ];
        }

        // Add "No Package" entry
        $packageStats['No Package'] = [
            'id' => 0,
            'name' => 'No Package',
            'amount' => 0,
            'count' => 0,
            'percentage' => 0,
            'paid_count' => 0,
            'unpaid_count' => 0,
            'paid_percentage' => 0,
            'unpaid_percentage' => 0
        ];

        // Count customers by package and payment status
        foreach ($customers as $customer) {
            $packageName = $customer->currentPackage->name ?? 'No Package';
            
            if (isset($packageStats[$packageName])) {
                $packageStats[$packageName]['count']++;
                
                // Check payment status from customer packages
                $customerPackage = \app\models\CustomerPackage::find()
                    ->where(['customer_id' => $customer->id, 'package_id' => $customer->currentPackage->id ?? 0])
                    ->orderBy(['created_at' => SORT_DESC])
                    ->one();
                
                if ($customerPackage) {
                    if ($customerPackage->status == \app\models\CustomerPackage::STATUS_ACTIVE) {
                        $packageStats[$packageName]['paid_count']++;
                    } else {
                        $packageStats[$packageName]['unpaid_count']++;
                    }
                } else {
                    $packageStats[$packageName]['unpaid_count']++;
                }
            }
        }

        // Calculate percentages
        foreach ($packageStats as $packageName => &$stats) {
            $stats['percentage'] = $totalCustomers > 0 ? round(($stats['count'] / $totalCustomers) * 100, 2) : 0;
            $stats['paid_percentage'] = $totalCustomers > 0 ? round(($stats['paid_count'] / $totalCustomers) * 100, 2) : 0;
            $stats['unpaid_percentage'] = $totalCustomers > 0 ? round(($stats['unpaid_count'] / $totalCustomers) * 100, 2) : 0;
        }

        // Remove packages with zero count
        $packageStats = array_filter($packageStats, function($stats) {
            return $stats['count'] > 0;
        });

        return $packageStats;
    }

    /**
     * Calculate dynamic package statistics for direct team
     * @param array $directTeam
     * @return array
     */
    public static function calculateDirectTeamPackageStats($directTeam)
    {
        $directTeam = Customer::find()
            ->where(['id' => array_column($directTeam, 'id')])
            ->all();
        // Get all active packages from database (including premium)
        $allPackages = \app\models\Package::find()
            ->where(['status' => [\app\models\Package::STATUS_ACTIVE, \app\models\Package::STATUS_PREMIUM]])
            ->orderBy(['amount' => SORT_ASC])
            ->all();

        $packageStats = [];
        $totalCustomers = count($directTeam);

        // Initialize stats for all packages
        foreach ($allPackages as $package) {
            $packageStats[$package->name] = [
                'id' => $package->id,
                'name' => $package->name,
                'amount' => $package->amount,
                'count' => 0,
                'percentage' => 0,
                'paid_count' => 0,
                'unpaid_count' => 0,
                'paid_percentage' => 0,
                'unpaid_percentage' => 0
            ];
        }

        // Add "No Package" entry
        $packageStats['No Package'] = [
            'id' => 0,
            'name' => 'No Package',
            'amount' => 0,
            'count' => 0,
            'percentage' => 0,
            'paid_count' => 0,
            'unpaid_count' => 0,
            'paid_percentage' => 0,
            'unpaid_percentage' => 0
        ];

        // Count customers by package and payment status
        foreach ($directTeam as $customer) {
            $packageName = $customer->currentPackage->name ?? 'No Package';
            
            if (isset($packageStats[$packageName])) {
                $packageStats[$packageName]['count']++;
                
                // Check payment status from customer packages
                $customerPackage = \app\models\CustomerPackage::find()
                    ->where(['customer_id' => $customer->id, 'package_id' => $customer->currentPackage->id ?? 0])
                    ->orderBy(['created_at' => SORT_DESC])
                    ->one();
                
                if ($customerPackage) {
                    if ($customerPackage->status == \app\models\CustomerPackage::STATUS_ACTIVE) {
                        $packageStats[$packageName]['paid_count']++;
                    } else {
                        $packageStats[$packageName]['unpaid_count']++;
                    }
                } else {
                    $packageStats[$packageName]['unpaid_count']++;
                }
            }
        }

        // Calculate percentages
        foreach ($packageStats as $packageName => &$stats) {
            $stats['percentage'] = $totalCustomers > 0 ? round(($stats['count'] / $totalCustomers) * 100, 2) : 0;
            $stats['paid_percentage'] = $totalCustomers > 0 ? round(($stats['paid_count'] / $totalCustomers) * 100, 2) : 0;
            $stats['unpaid_percentage'] = $totalCustomers > 0 ? round(($stats['unpaid_count'] / $totalCustomers) * 100, 2) : 0;
        }

        // Remove packages with zero count
        $packageStats = array_filter($packageStats, function($stats) {
            return $stats['count'] > 0;
        });

        return $packageStats;
    }



    /**
     * Get all customers at all levels under a customer (original method)
     * @param int $customerId
     * @return array
     */
    public static function getAllLevelCustomers($customerId)
    {
        $allCustomers = [];
        
        $directCustomers = static::find()
            ->select(['id', 'name', 'email', 'mobile_no', 'referral_code', 'current_package', 'created_at'])
            ->where(['referrer_id' => $customerId])
            ->with(['currentPackage'])
            ->all();

        foreach ($directCustomers as $customer) {
            $customerData = [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'mobile_no' => $customer->mobile_no,
                'referral_code' => $customer->referral_code,
                'package' => $customer->currentPackage ? $customer->currentPackage->name : 'No Package',
                'joined_date' => date('Y-m-d', $customer->created_at)
            ];
            
            $allCustomers[] = $customerData;
            
            // Recursively get customers from lower levels
            $subCustomers = static::getAllLevelCustomers($customer->id);
            $allCustomers = array_merge($allCustomers, $subCustomers);
        }

        return $allCustomers;
    }

    /**
     * Get all customers from levelTeam array structure
     * @param array $levelTeam
     * @return array
     */
    public static function getAllLevelCustomersFromTeam($levelTeam)
    {
        $allCustomers = [];
        
        if (empty($levelTeam)) {
            return $allCustomers;
        }
        
        // Add customers from current level
        if (!empty($levelTeam['customers'])) {
            foreach ($levelTeam['customers'] as $customer) {
                $customerData = [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'mobile_no' => $customer->mobile_no,
                    'referral_code' => $customer->referral_code,
                    'package' => $customer->currentPackage ? $customer->currentPackage->name : 'No Package',
                    'joined_date' => date('Y-m-d', $customer->created_at)
                ];
                $allCustomers[] = $customerData;
            }
        }
        
        // Recursively add customers from children levels
        if (!empty($levelTeam['children'])) {
            foreach ($levelTeam['children'] as $childLevel) {
                $childCustomers = static::getAllLevelCustomersFromTeam($childLevel);
                $allCustomers = array_merge($allCustomers, $childCustomers);
            }
        }
        
        return $allCustomers;
    }

    /**
     * Calculate income statistics for a customer
     * @param int $customerId
     * @return array
     */
    public static function calculateIncomeStats($customerId)
    {
        $incomes = Income::find()
            ->where(['customer_id' => $customerId])
            ->all();

        $totalIncome = 0;
        $roiIncome = 0;
        $levelIncome = 0;
        $referralIncome = 0;
        $monthlyIncome = 0;
        $pendingIncome = 0;
        $activeIncome = 0;

        $currentMonth = date('Y-m');

        foreach ($incomes as $income) {
            $totalIncome += $income->amount;
            
            // Calculate income by type
            if ($income->type == Income::TYPE_ROI) {
                $roiIncome += $income->amount;
            } elseif ($income->type == Income::TYPE_LEVEL_INCOME) {
                $levelIncome += $income->amount;
            } elseif ($income->type == Income::TYPE_REFERRAL_INCOME) {
                $referralIncome += $income->amount;
            }
            
            // Calculate income by status
            if ($income->status == Income::STATUS_PENDING) {
                $pendingIncome += $income->amount;
            } elseif ($income->status == Income::STATUS_ACTIVE) {
                $activeIncome += $income->amount;
            }
            
            // Calculate monthly income
            $incomeMonth = date('Y-m', strtotime($income->date));
            if ($incomeMonth === $currentMonth) {
                $monthlyIncome += $income->amount;
            }
        }

        return [
            'total_income' => $totalIncome,
            'roi_income' => $roiIncome,
            'level_income' => $levelIncome,
            'referral_income' => $referralIncome,
            'monthly_income' => $monthlyIncome,
            'pending_income' => $pendingIncome,
            'active_income' => $activeIncome
        ];
    }

    /**
     * Get customer's current ledger balance (Total Income - Withdrawals)
     * @return float
     */
    public function getLedgerBalance()
    {
        return Ledger::getCustomerBalance($this->id);
    }

    /**
     * Get customer's total income
     * @return float
     */
    public function getTotalIncome()
    {
        return Ledger::getCustomerTotalIncome($this->id);
    }

    /**
     * Get customer's total withdrawals
     * @return float
     */
    public function getTotalWithdrawals()
    {
        return Ledger::getCustomerTotalWithdrawals($this->id);
    }

    /**
     * Get customer's current month income
     * @return float
     */
    public function getCurrentMonthIncome()
    {
        $currentMonthStart = date('Y-m-01');
        $currentMonthEnd = date('Y-m-t');
        
        return Income::find()
            ->where(['customer_id' => $this->id])
            ->andWhere(['>=', 'date', $currentMonthStart])
            ->andWhere(['<=', 'date', $currentMonthEnd])
            ->sum('amount') ?: 0;
    }

    /**
     * Get customer's total withdrawal amount from withdrawal table
     * @return float
     */
    public function getTotalWithdrawalAmount()
    {
        return \app\models\Withdrawal::find()
            ->where(['customer_id' => $this->id])
            ->andWhere(['in', 'status', [
                \app\models\Withdrawal::STATUS_APPROVED,
                \app\models\Withdrawal::STATUS_PROCESSING,
                \app\models\Withdrawal::STATUS_COMPLETED
            ]])
            ->sum('amount') ?: 0;
    }

    /**
     * Get count of direct referrals for a customer
     * @param int $customerId
     * @return int
     */
    public static function getDirectReferralsCount($customerId)
    {
        $customer = static::findOne($customerId);
        if (!$customer || !$customer->user) {
            return 0;
        }

        return static::find()
            ->joinWith('user')
            ->where(['customer.referral_code' => $customer->user->username])
            ->count();
    }

    /**
     * Get count of all members in level team for a customer
     * @param int $customerId
     * @return int
     */
    public static function getLevelTeamCount($customerId)
    {
        $customer = static::findOne($customerId);
        if (!$customer || !$customer->user) {
            return 0;
        }

        // Build level team structure
        $levelTeam = static::buildLevelTeam($customer->user->username);
        
        // Get all customers from the level team structure
        $allCustomers = static::getAllLevelCustomersFromTeam($levelTeam);
        
        return count($allCustomers);
    }

    /**
     * Add debit entry to customer's ledger
     * @param float $amount
     * @param int $actionBy
     * @param int $type
     * @param string $date
     * @return bool
     */
    public function addDebitEntry($amount, $actionBy, $type = Ledger::TYPE_TOPUP, $date = null)
    {
        return Ledger::createDebit($this->id, $amount, $actionBy, $type, $date);
    }

    /**
     * Add credit entry to customer's ledger
     * @param float $amount
     * @param int $actionBy
     * @param int $type
     * @param string $date
     * @return bool
     */
    public function addCreditEntry($amount, $actionBy, $type = Ledger::TYPE_TOPUP, $date = null)
    {
        return Ledger::createCredit($this->id, $amount, $actionBy, $type, $date);
    }

    /**
     * Get customer's recent ledger entries
     * @param int $limit
     * @return \yii\db\ActiveQuery
     */
    public function getRecentLedgerEntries($limit = 10)
    {
        return Ledger::getCustomerLedger($this->id, $limit);
    }

    /**
     * Check if customer can upgrade to a specific package
     * @param int $customerId
     * @param int|null $packageId
     * @return bool
     */
    public static function canCustomerUpgrade($customerId, $packageId = null)
    {
        $customer = static::findOne($customerId);
        if (!$customer) {
            return false;
        }

        // If no specific package ID provided, check general upgrade eligibility
        if ($packageId === null) {
            // Customer can upgrade only if:
            // 1. They have a free package
            // 2. They don't have any paid package history
            
            if (!$customer->currentPackage) {
                return false; // No package assigned
            }
            
            // Check if current package is free
            $isFreePackage = (strtolower($customer->currentPackage->name) === 'free');
            
            if (!$isFreePackage) {
                return false; // Already has paid package
            }
            
            // Check if customer has ever had a paid package
            $hasPaidPackageHistory = CustomerPackage::find()
                ->joinWith('package')
                ->where(['customer_id' => $customer->id])
                ->andWhere(['!=', 'package.name', 'Free'])
                ->exists();
            
            return !$hasPaidPackageHistory;
        }

        $targetPackage = Package::findOne($packageId);
        if (!$targetPackage) {
            return false;
        }

        $currentPackage = $customer->getCurrentPackage()->one();
        if (!$currentPackage) {
            return true; // Can upgrade from no package
        }

        // Check if target package is higher level than current
        return $targetPackage->level > $currentPackage->level;
    }

    /**
     * Get available packages for upgrade for a customer
     * @param int $customerId
     * @return Package[]
     */
    public static function getAvailablePackagesForUpgrade($customerId)
    {
        $customer = static::findOne($customerId);
        if (!$customer) {
            return [];
        }

        // Get all active paid packages (exclude free package)
        return Package::find()
            ->where(['status' => [Package::STATUS_ACTIVE, Package::STATUS_PREMIUM]])
            ->andWhere(['!=', 'name', 'Free'])
            ->orderBy('amount ASC')
            ->all();
    }

    /**
     * Process package upgrade for a customer
     * @param int $customerId
     * @param int $packageId
     * @param string $paymentMethod
     * @param array $paymentData
     * @return array
     */
    public static function processPackageUpgrade($customerId, $packageId, $paymentMethod = 'manual', $paymentData = [])
    {
        $customer = static::findOne($customerId);
        if (!$customer) {
            return ['success' => false, 'message' => 'Customer not found'];
        }

        $newPackage = Package::findOne($packageId);
        if (!$newPackage) {
            return ['success' => false, 'message' => 'Selected package not found.'];
        }

        // Validate it's a paid package
        if (strtolower($newPackage->name) === 'free') {
            return ['success' => false, 'message' => 'Cannot upgrade to free package.'];
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            // Update customer's current package
            $customer->current_package = $newPackage->id;
            
            if (!$customer->save()) {
                throw new \Exception('Failed to update customer package.');
            }
            
            // Create customer package record
            $customerPackage = new CustomerPackage();
            $customerPackage->customer_id = $customer->id;
            $customerPackage->package_id = $newPackage->id;
            $customerPackage->date = date('Y-m-d');
            $customerPackage->status = CustomerPackage::STATUS_PENDING; // Pending payment
            
            if (!$customerPackage->save()) {
                throw new \Exception('Failed to create customer package record.');
            }

            $transaction->commit();
            
            return [
                'success' => true,
                'message' => "Successfully upgraded to {$newPackage->name} package. Please complete the payment to activate your package."
            ];
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Upgrade failed: ' . $e->getMessage()];
        }
    }

    /**
     * Build level team hierarchy with filtering
     * @param string $username Root customer username
     * @param string $usernameFilter Filter by username
     * @param string $fromDate Filter from date
     * @param string $toDate Filter to date
     * @param string $level Filter by level
     * @return array
     */
    public static function buildLevelTeam($username, $usernameFilter = '', $fromDate = '', $toDate = '', $level = '')
    {
        // Find the root customer by username
        $rootCustomer = static::find()
            ->joinWith('user')
            ->where(['user.username' => $username])
            ->one();

        if (!$rootCustomer) {
            return [];
        }

        // Build the team hierarchy
        return static::buildLevelTeamRecursive($rootCustomer->id, 1, $usernameFilter, $fromDate, $toDate, $level);
    }

    /**
     * Recursively build level team hierarchy
     * @param int $customerId
     * @param int $currentLevel
     * @param string $usernameFilter
     * @param string $fromDate
     * @param string $toDate
     * @param string $levelFilter
     * @param int $maxLevel
     * @return array
     */
    private static function buildLevelTeamRecursive($customerId, $currentLevel, $usernameFilter = '', $fromDate = '', $toDate = '', $levelFilter = '', $maxLevel = 10)
    {
        if ($currentLevel > $maxLevel) {
            return [];
        }

        // Get the current customer to find their username for referral tracking
        $currentCustomer = static::findOne($customerId);
        if (!$currentCustomer || !$currentCustomer->user) {
            return [];
        }

        // Build query for direct referrals using referral_code system
        $query = static::find()
            ->joinWith(['user', 'currentPackage'])
            ->where(['customer.referral_code' => $currentCustomer->user->username]);

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
        
        $levelData = [
            'level' => $currentLevel,
            'customers' => $includeCurrentLevel ? $customers : [],
            'count' => $includeCurrentLevel ? count($customers) : 0,
            'children' => []
        ];

        // Recursively get children for each customer
        foreach ($customers as $customer) {
            $childLevel = static::buildLevelTeamRecursive(
                $customer->id, 
                $currentLevel + 1, 
                $usernameFilter, 
                $fromDate, 
                $toDate, 
                $levelFilter, 
                $maxLevel
            );
            
            if (!empty($childLevel)) {
                $levelData['children'][] = $childLevel;
            }
        }

        // Return data only if there are customers at this level or children levels
        if (!empty($levelData['customers']) || !empty($levelData['children'])) {
            return $levelData;
        }

        return [];
    }

    /**
     * Generate level income when a customer registers
     * 
     * @param int $customerId The ID of the newly registered customer
     * @return array Result array with success status and details
     */
    public static function generateLevelIncome($customerId)
    {
        $result = [
            'success' => false,
            'generated_count' => 0,
            'error_count' => 0,
            'errors' => []
        ];
        
        // Find the customer
        $customer = Customer::findOne($customerId);
        if (!$customer) {
            $result['errors'][] = "Customer not found with ID: {$customerId}";
            return $result;
        }
        
        // Get customer's package
        $package = $customer->currentPackage;
            
        if (!$package) {
            $result['errors'][] = "Customer has no active package assigned";
            return $result;
        }
        
        $transaction = \Yii::$app->db->beginTransaction();
        
        try {
            // Get active level plans
            $levelPlans = LevelPlan::find()
                ->where(['status' => 1])
                ->orderBy('level ASC')
                ->all();
            
            if (empty($levelPlans)) {
                $result['errors'][] = "No active level plans found";
                $transaction->rollBack();
                return $result;
            }
            
            // Start from the customer's referrer and go up the hierarchy
            $currentReferralCode = $customer->referral_code;
            $currentLevel = 1;
            
            while ($currentReferralCode && $currentLevel <= count($levelPlans)) {
                // Find the referrer by referral code
                $referrer = Customer::find()
                    ->joinWith('user')
                    ->where(['user.username' => $currentReferralCode])
                    ->one();
                if( $referrer->status == self::STATUS_INACTIVE ){
                    break; // No more referrers in the chain
                }
                if (!$referrer) {
                    break; // No more referrers in the chain
                }
                
                // Get level plan for current level
                $levelPlan = null;
                foreach ($levelPlans as $plan) {
                    if ($plan->level == $currentLevel) {
                        $levelPlan = $plan;
                        break;
                    }
                }
                
                if (!$levelPlan) {
                    break; // No level plan for this level
                }
                
                // Check if referrer meets the direct referral requirement
                if ($levelPlan->no_of_directs > 0) {
                    $directReferrals = Customer::find()
                        ->joinWith('user')
                        ->where(['referral_code' => $referrer->user->username])
                        ->count();
                    
                    if ($directReferrals < $levelPlan->no_of_directs) {
                        // Move to next level without generating income
                        $currentReferralCode = $referrer->referral_code;
                        $currentLevel++;
                        continue;
                    }
                }
                
                // Calculate level income using level plan rate
                $levelIncome = ($package->amount * $levelPlan->rate) / 100;
                
                // Check if income already exists for this customer and referrer at this level
                $existingIncome = Income::find()
                    ->where([
                        'customer_id' => $referrer->id,
                        'type' => Income::TYPE_LEVEL_INCOME,
                        'level' => $currentLevel,
                        'meta' => $customer->user->username, // Validate for registered user in meta
                    ])
                    ->andWhere(['like', 'date', date('Y-m')])
                    ->exists();
                
                if (!$existingIncome) {
                    // Create level income record
                    $income = new Income();
                    $income->customer_id = $referrer->id;
                    $income->amount = $levelIncome;
                    $income->type = Income::TYPE_LEVEL_INCOME;
                    $income->level = $currentLevel;
                    $income->date = date('Y-m-d');
                    $income->status = Income::STATUS_PENDING;
                    $income->meta = $customer->user->username; // Store registered user's username
                    
                    if ($income->save()) {
                        $result['generated_count']++;
                        
                        // Create notification for level income generation
                        $activity = new CustomerActivity();
                        $activity->customer_id = $referrer->id;
                        $activity->activity_type = CustomerActivity::TYPE_INCOME_GENERATED;
                        $activity->activity_description = "Level {$currentLevel} income of $" . number_format($levelIncome, 2) . " generated from {$customer->user->username}";
                        $activity->metadata = ['amount' => $levelIncome, 'income_type' => 'Level', 'level' => $currentLevel, 'from_user' => $customer->user->username];
                        $activity->save();
                    } else {
                        $result['error_count']++;
                        $result['errors'][] = "Failed to save Level {$currentLevel} income for {$referrer->name}: " . implode(', ', $income->getFirstErrors());
                    }
                }
                
                // Move to next level
                $currentReferralCode = $referrer->referral_code;
                $currentLevel++;
            }
            
            $transaction->commit();
            $result['success'] = true;
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            $result['errors'][] = "Error during level income generation: " . $e->getMessage();
        }
        
        return $result;
    }
}