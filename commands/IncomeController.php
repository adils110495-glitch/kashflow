<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Customer;
use app\models\CustomerPackage;
use app\models\Package;
use app\models\RoiPlan;
use app\models\Income;
use app\models\LevelPlan;
use app\models\CustomerActivity;
use yii\db\Expression;

/**
 * Income command controller for generating ROI income for customers
 *
 * @author Kashflow System
 */
class IncomeController extends Controller
{
    /**
     * Generate ROI income for customers based on their active packages and ROI plans
     * This action should be run monthly on the release date
     * 
     * @return int Exit code
     */
    public function actionRoiGenerator()
    {
        $this->stdout("Starting ROI income generation...\n");
        
        $transaction = Yii::$app->db->beginTransaction();
        $generatedCount = 0;
        $errorCount = 0;
        
        try {
            // Get active ROI plan
            $roiPlan = RoiPlan::find()
                ->where(['status' => RoiPlan::STATUS_ACTIVE])
                ->andWhere(['frequency' => RoiPlan::FREQUENCY_MONTHLY])
                ->one();
            
            if (!$roiPlan) {
                $this->stdout("No active monthly ROI plan found.\n");
                return ExitCode::OK;
            }
            
            $this->stdout("Using ROI Plan - Rate: {$roiPlan->rate}%, Tenure: {$roiPlan->tenure}, Release Date: {$roiPlan->release_date}\n");
            
            // Get customers with active packages
            $activeCustomerPackages = CustomerPackage::find()
                ->with(['customer', 'package'])
                ->where(['status' => CustomerPackage::STATUS_ACTIVE])
                ->all();
            
            $this->stdout("Found " . count($activeCustomerPackages) . " active customer packages.\n");
            
            foreach ($activeCustomerPackages as $customerPackage) {
                try {
                    $customer = $customerPackage->customer;
                    $package = $customerPackage->package;
                    
                    if (!$customer || !$package) {
                        $this->stdout("Skipping invalid customer package ID: {$customerPackage->id}\n");
                        continue;
                    }
                    
                    // Calculate total tenure amount (package_amount * tenure)
                    $tenureAmount = $package->amount * $roiPlan->tenure;
                    
                    // Check how much income has already been generated for this customer
                    $totalGenerated = Income::find()
                        ->where(['customer_id' => $customer->id, 'type' => Income::TYPE_ROI])
                        ->sum('amount') ?: 0;
                    
                    // Check if tenure amount is already completed
                    if ($totalGenerated >= $tenureAmount) {
                        $this->stdout("Customer {$customer->name} (ID: {$customer->id}) has completed tenure amount. Skipping.\n");
                        continue;
                    }
                    
                    // Calculate monthly ROI income: package_amount * rate / 100
                    $monthlyIncome = ($package->amount * $roiPlan->rate) / 100;
                    
                    // Ensure we don't exceed the tenure amount
                    $remainingAmount = $tenureAmount - $totalGenerated;
                    if ($monthlyIncome > $remainingAmount) {
                        $monthlyIncome = $remainingAmount;
                    }
                    
                    // Check if income for this month already exists
                    $currentMonth = date('Y-m');
                    $existingIncome = Income::find()
                        ->where([
                            'customer_id' => $customer->id,
                            'type' => Income::TYPE_ROI,
                        ])
                        ->andWhere(['like', 'date', $currentMonth])
                        ->exists();
                    
                    if ($existingIncome) {
                        $this->stdout("Income for {$customer->name} already generated for {$currentMonth}. Skipping.\n");
                        continue;
                    }
                    
                    // Create new income record
                    $income = new Income();
                    $income->customer_id = $customer->id;
                    $income->date = date('Y-m-d');
                    $income->type = Income::TYPE_ROI;
                    $income->level = 0; // ROI is not level-based
                    $income->amount = $monthlyIncome;
                    $income->status = Income::STATUS_PENDING;
                    
                    if ($income->save()) {
                        $generatedCount++;
                        $this->stdout("Generated ROI income for {$customer->name}: $" . number_format($monthlyIncome, 2) . "\n");
                        
                        // Create notification for ROI income generation
                        $activity = new CustomerActivity();
                        $activity->customer_id = $customer->id;
                        $activity->activity_type = CustomerActivity::TYPE_INCOME_GENERATED;
                        $activity->activity_description = "ROI income of $" . number_format($monthlyIncome, 2) . " generated";
                        $activity->metadata = ['amount' => $monthlyIncome, 'income_type' => 'ROI'];
                        $activity->save();
                    } else {
                        $errorCount++;
                        $this->stdout("Failed to save income for {$customer->name}: " . implode(', ', $income->getFirstErrors()) . "\n");
                    }
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->stdout("Error processing customer package ID {$customerPackage->id}: " . $e->getMessage() . "\n");
                }
            }
            
            $transaction->commit();
            
            $this->stdout("\nROI income generation completed.\n");
            $this->stdout("Generated: {$generatedCount} income records\n");
            $this->stdout("Errors: {$errorCount}\n");
            
            return ExitCode::OK;
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->stdout("Error during ROI generation: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
    
    /**
     * Generate level income when a customer registers
     * 
     * @param int $customerId The ID of the newly registered customer
     * @return int Exit code
     */
    public function actionLevelGenerator($customerId = null)
    {
        $this->stdout("Starting level income generation...\n");
        
        if (!$customerId) {
            $this->stdout("Error: Customer ID is required.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
        
        // Find the customer
        $customer = Customer::findOne($customerId);
        if (!$customer) {
            $this->stdout("Error: Customer not found.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
        
        $this->stdout("Processing level income for customer: {$customer->name} (ID: {$customerId})\n");
        
        // Get customer's package
        $customerPackage = CustomerPackage::find()
            ->where(['customer_id' => $customerId, 'status' => CustomerPackage::STATUS_ACTIVE])
            ->one();
            
        if (!$customerPackage || !$customerPackage->package) {
            $this->stdout("Error: Customer has no active package assigned.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
        
        $package = $customerPackage->package;
        $this->stdout("Customer package: {$package->name} - Amount: $" . number_format($package->amount, 2) . "\n");
        
        $transaction = Yii::$app->db->beginTransaction();
        $generatedCount = 0;
        $errorCount = 0;
        
        try {
            // Get active level plans
            $levelPlans = LevelPlan::find()
                ->where(['status' => 1])
                ->orderBy('level ASC')
                ->all();
            
            if (empty($levelPlans)) {
                $this->stdout("No active level plans found.\n");
                return ExitCode::OK;
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
                
                if (!$referrer) {
                    $this->stdout("Referrer not found for referral code: {$currentReferralCode}\n");
                    break;
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
                    $this->stdout("Level plan not found for level {$currentLevel}\n");
                    break;
                }
                
                // Check if referrer meets the direct referral requirement
                if ($levelPlan->no_of_directs > 0) {
                    $directReferrals = Customer::find()
                        ->joinWith('user')
                        ->where(['referral_code' => $referrer->user->username])
                        ->count();
                    
                    if ($directReferrals < $levelPlan->no_of_directs) {
                        $this->stdout("Referrer {$referrer->name} (Level {$currentLevel}) doesn't meet direct referral requirement ({$directReferrals}/{$levelPlan->no_of_directs})\n");
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
                
                if ($existingIncome) {
                    $this->stdout("Level {$currentLevel} income already exists for {$referrer->name}\n");
                } else {
                    // Create level income record
                    $income = new Income();
                    $income->customer_id = $referrer->id;
                    $income->amount = $levelIncome;
                    $income->type = Income::TYPE_LEVEL_INCOME;
                    $income->level = $currentLevel;
                    $income->date = date('Y-m-d');
                    $income->status = Income::STATUS_PENDING;
                    $income->meta = $customer->user->username; // Store registered user's username
                    // Note: Level income for customer ID {$customerId}
                    
                    if ($income->save()) {
                        $this->stdout("Generated Level {$currentLevel} income for {$referrer->name}: $" . number_format($levelIncome, 2) . "\n");
                        $generatedCount++;
                        
                        // Create notification for level income generation
                        $activity = new CustomerActivity();
                        $activity->customer_id = $referrer->id;
                        $activity->activity_type = CustomerActivity::TYPE_INCOME_GENERATED;
                        $activity->activity_description = "Level {$currentLevel} income of $" . number_format($levelIncome, 2) . " generated from {$customer->user->username}";
                        $activity->metadata = ['amount' => $levelIncome, 'income_type' => 'Level', 'level' => $currentLevel, 'from_user' => $customer->user->username];
                        $activity->save();
                    } else {
                        $this->stdout("Failed to save Level {$currentLevel} income for {$referrer->name}: " . implode(', ', $income->getFirstErrors()) . "\n");
                        $errorCount++;
                    }
                }
                
                // Move to next level
                $currentReferralCode = $referrer->referral_code;
                $currentLevel++;
            }
            
            $transaction->commit();
            
            $this->stdout("\nLevel income generation completed.\n");
            $this->stdout("Generated: {$generatedCount} income records\n");
            $this->stdout("Errors: {$errorCount}\n");
            
            return ExitCode::OK;
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->stdout("Error during level income generation: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
    
    /**
     * Display ROI generation statistics
     * 
     * @return int Exit code
     */
    public function actionStats()
    {
        $this->stdout("Income Generation Statistics\n");
        $this->stdout("============================\n\n");
        
        // Active customers with packages
        $activeCustomers = CustomerPackage::find()
            ->where(['status' => CustomerPackage::STATUS_ACTIVE])
            ->count();
        
        $this->stdout("Active customers with packages: {$activeCustomers}\n");
        
        // Total ROI income generated
        $totalROI = Income::find()
            ->where(['type' => Income::TYPE_ROI])
            ->sum('amount') ?: 0;
        
        $this->stdout("Total ROI income generated: $" . number_format($totalROI, 2) . "\n");
        
        // ROI income this month
        $currentMonth = date('Y-m');
        $monthlyROI = Income::find()
            ->where(['type' => Income::TYPE_ROI])
            ->andWhere(['like', 'date', $currentMonth])
            ->sum('amount') ?: 0;
        
        $this->stdout("ROI income this month ({$currentMonth}): $" . number_format($monthlyROI, 2) . "\n");
        
        // Total Level income generated
         $totalLevel = Income::find()
             ->where(['type' => Income::TYPE_LEVEL_INCOME])
             ->sum('amount') ?: 0;
        
        $this->stdout("Total Level income generated: $" . number_format($totalLevel, 2) . "\n");
        
        // Level income this month
         $monthlyLevel = Income::find()
             ->where(['type' => Income::TYPE_LEVEL_INCOME])
             ->andWhere(['like', 'date', $currentMonth])
             ->sum('amount') ?: 0;
        
        $this->stdout("Level income this month ({$currentMonth}): $" . number_format($monthlyLevel, 2) . "\n");
        
        // Active ROI plan
        $roiPlan = RoiPlan::find()
            ->where(['status' => RoiPlan::STATUS_ACTIVE])
            ->one();
        
        if ($roiPlan) {
            $this->stdout("\nActive ROI Plan:\n");
            $this->stdout("Rate: {$roiPlan->rate}%\n");
            $this->stdout("Frequency: {$roiPlan->getFrequencyLabel()}\n");
            $this->stdout("Tenure: {$roiPlan->getTenureLabel()}\n");
            $this->stdout("Release Date: {$roiPlan->release_date}\n");
        } else {
            $this->stdout("\nNo active ROI plan found.\n");
        }
        
        return ExitCode::OK;
    }
}