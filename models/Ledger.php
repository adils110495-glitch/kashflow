<?php

namespace app\models;

use Yii;
use app\models\base\Ledger as BaseLedger;

/**
 * This is the model class for table "ledger".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $date
 * @property float $debit
 * @property float $credit
 * @property int $type
 * @property int $status
 * @property int $action_by
 * @property string $action_date_time
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Customer $customer
 * @property \dektrium\user\models\User $actionBy
 */
class Ledger extends BaseLedger
{
    // Type constants
    const TYPE_TOPUP = 1;
    const TYPE_WITHDRAWAL = 2;
    const TYPE_TOPUP_REFUND = 3;
    const TYPE_WITHDRAWAL_REFUND = 4;

    // Status constants
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        // Override parent rules to use constants
        $rules[] = [['type'], 'in', 'range' => [self::TYPE_TOPUP, self::TYPE_WITHDRAWAL, self::TYPE_TOPUP_REFUND, self::TYPE_WITHDRAWAL_REFUND]];
        $rules[] = [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]];
        return $rules;
    }

    /**
     * Get type label with Bootstrap styling
     * @return string
     */
    public function getTypeLabel()
    {
        $typeLabels = [
            self::TYPE_TOPUP => '<span class="label label-default">Topup</span>',
            self::TYPE_WITHDRAWAL => '<span class="label label-warning">Withdrawal</span>',
            self::TYPE_TOPUP_REFUND => '<span class="label label-success">Topup Refund</span>',
            self::TYPE_WITHDRAWAL_REFUND => '<span class="label label-info">Withdrawal Refund</span>',
        ];
        
        return $typeLabels[$this->type] ?? '<span class="label label-info">' . $this->getTypeText() . '</span>';
    }

    /**
     * Get status label with Bootstrap styling
     * @return string
     */
    public function getStatusLabel()
    {
        $statusLabels = [
            self::STATUS_ACTIVE => '<span class="label label-success">Active</span>',
            self::STATUS_INACTIVE => '<span class="label label-danger">Inactive</span>',
        ];
        
        return $statusLabels[$this->status] ?? '<span class="label label-info">' . $this->getStatusText() . '</span>';
    }

    /**
     * Create a debit entry
     * @param int $customerId
     * @param float $amount
     * @param int $actionBy
     * @param int $type
     * @param string $date
     * @return bool
     */
    public static function createDebit($customerId, $amount, $actionBy, $type = self::TYPE_TOPUP, $date = null)
    {
        $ledger = new self();
        $ledger->customer_id = $customerId;
        $ledger->debit = $amount;
        $ledger->credit = 0;
        $ledger->type = $type;
        $ledger->action_by = $actionBy;
        $ledger->date = $date ?: date('Y-m-d');
        $ledger->action_date_time = date('Y-m-d H:i:s');
        $ledger->status = self::STATUS_ACTIVE;
        
        return $ledger->save();
    }

    /**
     * Create a credit entry
     * @param int $customerId
     * @param float $amount
     * @param int $actionBy
     * @param int $type
     * @param string $date
     * @return bool
     */
    public static function createCredit($customerId, $amount, $actionBy, $type = self::TYPE_TOPUP, $date = null)
    {
        $ledger = new self();
        $ledger->customer_id = $customerId;
        $ledger->debit = 0;
        $ledger->credit = $amount;
        $ledger->type = $type;
        $ledger->action_by = $actionBy;
        $ledger->date = $date ?: date('Y-m-d');
        $ledger->action_date_time = date('Y-m-d H:i:s');
        $ledger->status = self::STATUS_ACTIVE;
        
        return $ledger->save();
    }

    /**
     * Get customer's current balance (Total Income - Withdrawals)
     * @param int $customerId
     * @return float
     */
    public static function getCustomerBalance($customerId)
    {
        // Get total income using same logic as Customer::calculateIncomeStats
        $totalIncome = self::getCustomerTotalIncome($customerId);
            
        // Get total withdrawals from Ledger table
        $totalWithdrawals = self::find()
            ->where([
                'customer_id' => $customerId, 
                'status' => self::STATUS_ACTIVE,
                'type' => self::TYPE_WITHDRAWAL
            ])
            ->sum('debit') ?: 0;
            
        return $totalIncome - $totalWithdrawals;
    }

    /**
     * Get customer's total income (using same logic as Customer::calculateIncomeStats)
     * @param int $customerId
     * @return float
     */
    public static function getCustomerTotalIncome($customerId)
    {
        $incomes = \app\models\Income::find()
            ->where(['customer_id' => $customerId])
            ->all();

        $totalIncome = 0;
        foreach ($incomes as $income) {
            $totalIncome += $income->amount;
        }
        
        return $totalIncome;
    }

    /**
     * Get customer's total withdrawals
     * @param int $customerId
     * @return float
     */
    public static function getCustomerTotalWithdrawals($customerId)
    {
        return self::find()
            ->where([
                'customer_id' => $customerId, 
                'status' => self::STATUS_ACTIVE,
                'type' => self::TYPE_WITHDRAWAL
            ])
            ->sum('debit') ?: 0;
    }

    /**
     * Get customer's ledger entries
     * @param int $customerId
     * @param int $limit
     * @return \yii\db\ActiveQuery
     */
    public static function getCustomerLedger($customerId, $limit = null)
    {
        $query = self::find()
            ->where(['customer_id' => $customerId])
            ->orderBy(['date' => SORT_DESC, 'created_at' => SORT_DESC]);
            
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query;
    }

    /**
     * Get ledger entries by date range
     * @param string $fromDate
     * @param string $toDate
     * @param int $customerId
     * @return \yii\db\ActiveQuery
     */
    public static function getLedgerByDateRange($fromDate, $toDate, $customerId = null)
    {
        $query = self::find()
            ->where(['>=', 'date', $fromDate])
            ->andWhere(['<=', 'date', $toDate])
            ->orderBy(['date' => SORT_DESC, 'created_at' => SORT_DESC]);
            
        if ($customerId) {
            $query->andWhere(['customer_id' => $customerId]);
        }
        
        return $query;
    }

    /**
     * Get ledger entries by type
     * @param int $type
     * @param int $customerId
     * @param int $limit
     * @return \yii\db\ActiveQuery
     */
    public static function getLedgerByType($type, $customerId = null, $limit = null)
    {
        $query = self::find()
            ->where(['type' => $type])
            ->orderBy(['date' => SORT_DESC, 'created_at' => SORT_DESC]);
            
        if ($customerId) {
            $query->andWhere(['customer_id' => $customerId]);
        }
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query;
    }
}
