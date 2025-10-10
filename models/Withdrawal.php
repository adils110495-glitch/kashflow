<?php

namespace app\models;

use Yii;
use app\models\base\Withdrawal as BaseWithdrawal;

/**
 * This is the model class for table "withdrawal".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $date
 * @property string $amount
 * @property int $status
 * @property string $comment
 * @property int $action_by
 * @property string $action_date_time
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Customer $customer
 * @property \dektrium\user\models\User $actionBy
 */
class Withdrawal extends BaseWithdrawal
{
    // Status constants
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_PROCESSING = 3;
    const STATUS_COMPLETED = 4;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        // Override parent rules to use constants
        $rules[] = [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_PROCESSING, self::STATUS_COMPLETED]];
        return $rules;
    }

    /**
     * Get status label with Bootstrap styling
     * @return string
     */
    public function getStatusLabel()
    {
        $statusLabels = [
            self::STATUS_PENDING => '<span class="label label-warning">Pending</span>',
            self::STATUS_APPROVED => '<span class="label label-success">Approved</span>',
            self::STATUS_REJECTED => '<span class="label label-danger">Rejected</span>',
            self::STATUS_PROCESSING => '<span class="label label-info">Processing</span>',
            self::STATUS_COMPLETED => '<span class="label label-primary">Completed</span>',
        ];
        
        return $statusLabels[$this->status] ?? '<span class="label label-info">' . $this->getStatusText() . '</span>';
    }

    /**
     * Get status text
     * @return string
     */
    public function getStatusText()
    {
        $statusMap = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
        ];
        
        return $statusMap[$this->status] ?? 'Unknown';
    }

    /**
     * Get status options
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    /**
     * Create a new withdrawal request
     * @param int $customerId
     * @param float $amount
     * @param string $comment
     * @param int $actionBy
     * @param string $date
     * @return bool
     */
    public static function createWithdrawalRequest($customerId, $amount, $comment = '', $actionBy, $date = null)
    {
        $withdrawal = new self();
        $withdrawal->customer_id = $customerId;
        $withdrawal->date = $date ?: date('Y-m-d');
        $withdrawal->amount = $amount;
        $withdrawal->status = self::STATUS_PENDING;
        $withdrawal->comment = $comment;
        $withdrawal->action_by = $actionBy;
        $withdrawal->action_date_time = date('Y-m-d H:i:s');
        
        return $withdrawal->save();
    }

    /**
     * Approve withdrawal request
     * @param int $actionBy
     * @param string $comment
     * @return bool
     */
    public function approve($actionBy, $comment = '')
    {
        $this->status = self::STATUS_APPROVED;
        $this->action_by = $actionBy;
        $this->action_date_time = date('Y-m-d H:i:s');
        
        if (!empty($comment)) {
            $this->comment = $this->comment ? $this->comment . "\n\nApproval: " . $comment : "Approval: " . $comment;
        }
        
        return $this->save();
    }

    /**
     * Reject withdrawal request
     * @param int $actionBy
     * @param string $comment
     * @return bool
     */
    public function reject($actionBy, $comment = '')
    {
        $this->status = self::STATUS_REJECTED;
        $this->action_by = $actionBy;
        $this->action_date_time = date('Y-m-d H:i:s');
        
        if (!empty($comment)) {
            $this->comment = $this->comment ? $this->comment . "\n\nRejection: " . $comment : "Rejection: " . $comment;
        }
        
        return $this->save();
    }

    /**
     * Mark withdrawal as processing
     * @param int $actionBy
     * @param string $comment
     * @return bool
     */
    public function markAsProcessing($actionBy, $comment = '')
    {
        $this->status = self::STATUS_PROCESSING;
        $this->action_by = $actionBy;
        $this->action_date_time = date('Y-m-d H:i:s');
        
        if (!empty($comment)) {
            $this->comment = $this->comment ? $this->comment . "\n\nProcessing: " . $comment : "Processing: " . $comment;
        }
        
        return $this->save();
    }

    /**
     * Mark withdrawal as completed
     * @param int $actionBy
     * @param string $comment
     * @return bool
     */
    public function markAsCompleted($actionBy, $comment = '')
    {
        $this->status = self::STATUS_COMPLETED;
        $this->action_by = $actionBy;
        $this->action_date_time = date('Y-m-d H:i:s');
        
        if (!empty($comment)) {
            $this->comment = $this->comment ? $this->comment . "\n\nCompleted: " . $comment : "Completed: " . $comment;
        }
        
        return $this->save();
    }

    /**
     * Get withdrawals by status
     * @param int $status
     * @param int $limit
     * @return \yii\db\ActiveQuery
     */
    public static function getWithdrawalsByStatus($status, $limit = null)
    {
        $query = self::find()
            ->where(['status' => $status])
            ->orderBy(['created_at' => SORT_DESC]);
            
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query;
    }

    /**
     * Get customer withdrawals
     * @param int $customerId
     * @param int $limit
     * @return \yii\db\ActiveQuery
     */
    public static function getCustomerWithdrawals($customerId, $limit = null)
    {
        $query = self::find()
            ->where(['customer_id' => $customerId])
            ->orderBy(['created_at' => SORT_DESC]);
            
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query;
    }

    /**
     * Get pending withdrawals count
     * @return int
     */
    public static function getPendingCount()
    {
        return self::find()
            ->where(['status' => self::STATUS_PENDING])
            ->count();
    }

    /**
     * Get withdrawals statistics
     * @return array
     */
    public static function getWithdrawalStats()
    {
        return [
            'total' => self::find()->count(),
            'pending' => self::find()->where(['status' => self::STATUS_PENDING])->count(),
            'approved' => self::find()->where(['status' => self::STATUS_APPROVED])->count(),
            'rejected' => self::find()->where(['status' => self::STATUS_REJECTED])->count(),
            'processing' => self::find()->where(['status' => self::STATUS_PROCESSING])->count(),
            'completed' => self::find()->where(['status' => self::STATUS_COMPLETED])->count(),
            'total_amount' => self::find()->sum('amount') ?: 0,
            'pending_amount' => self::find()->where(['status' => self::STATUS_PENDING])->sum('amount') ?: 0,
            'approved_amount' => self::find()->where(['status' => self::STATUS_APPROVED])->sum('amount') ?: 0,
            'completed_amount' => self::find()->where(['status' => self::STATUS_COMPLETED])->sum('amount') ?: 0,
        ];
    }

    /**
     * Get customer total withdrawal amount
     * @param int $customerId
     * @return float
     */
    public static function getCustomerTotalWithdrawalAmount($customerId)
    {
        return self::find()
            ->where(['customer_id' => $customerId])
            ->sum('amount') ?: 0;
    }

    /**
     * Get customer pending withdrawal amount
     * @param int $customerId
     * @return float
     */
    public static function getCustomerPendingWithdrawalAmount($customerId)
    {
        return self::find()
            ->where(['customer_id' => $customerId, 'status' => self::STATUS_PENDING])
            ->sum('amount') ?: 0;
    }
}
