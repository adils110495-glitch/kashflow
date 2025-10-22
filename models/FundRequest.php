<?php

namespace app\models;

use app\models\base\FundRequest as BaseFundRequest;
use Yii;

/**
 * FundRequest model
 *
 * @property int $id
 * @property int $customer_id
 * @property string $amount
 * @property string $request_date
 * @property string|null $attachment_file
 * @property string|null $comment
 * @property int $status
 * @property string|null $admin_comment
 * @property int|null $processed_by
 * @property int|null $processed_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Customer $customer
 * @property \dektrium\user\models\User $processedBy
 */
class FundRequest extends BaseFundRequest
{
    // Status constants
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    /**
     * Get status labels
     * @return array
     */
    public static function getStatusLabels()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    /**
     * Get status label
     * @return string
     */
    public function getStatusLabel()
    {
        $labels = self::getStatusLabels();
        return isset($labels[$this->status]) ? $labels[$this->status] : 'Unknown';
    }

    /**
     * Check if request is pending
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request is approved
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if request is rejected
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Get formatted amount
     * @return string
     */
    public function getFormattedAmount()
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get formatted request date
     * @return string
     */
    public function getFormattedRequestDate()
    {
        return date('M d, Y', strtotime($this->request_date));
    }

    /**
     * Get formatted processed date
     * @return string
     */
    public function getFormattedProcessedDate()
    {
        return $this->processed_at ? date('M d, Y H:i', $this->processed_at) : 'Not Processed';
    }

    /**
     * Process fund request (approve or reject)
     * @param int $status
     * @param string $adminComment
     * @return bool
     */
    public function processRequest($status, $adminComment = '')
    {
        if (!$this->isPending()) {
            return false; // Can only process pending requests
        }

        $this->status = $status;
        $this->admin_comment = $adminComment;
        $this->processed_by = Yii::$app->user->id;
        $this->processed_at = time();

        if ($this->save()) {
            // If approved, add credit to ledger
            if ($status === self::STATUS_APPROVED) {
                $ledgerResult = $this->addLedgerCredit();
                if (!$ledgerResult) {
                    // If ledger entry fails, log error but don't fail the approval
                    Yii::error("Failed to create ledger entry for approved fund request {$this->id}");
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Add credit entry to customer's ledger
     * @return bool
     */
    private function addLedgerCredit()
    {
        try {
            // Create ledger entry for approved fund request using the static method
            $result = \app\models\Ledger::createCredit(
                $this->customer_id,
                $this->amount,
                $this->processed_by,
                \app\models\Ledger::TYPE_TOPUP,
                date('Y-m-d')
            );
            
            if ($result) {
                Yii::info("Successfully created ledger credit entry for fund request {$this->id}: Amount {$this->amount} for customer {$this->customer_id}");
            } else {
                Yii::error("Failed to create ledger credit entry for fund request {$this->id}: Amount {$this->amount} for customer {$this->customer_id}");
            }
            
            return $result;
        } catch (\Exception $e) {
            Yii::error("Exception while creating ledger entry for fund request {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get customer's fund requests
     * @param int $customerId
     * @param int $limit
     * @return static[]
     */
    public static function getCustomerRequests($customerId, $limit = 10)
    {
        return static::find()
            ->where(['customer_id' => $customerId])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Get pending fund requests for admin
     * @return static[]
     */
    public static function getPendingRequests()
    {
        return static::find()
            ->where(['status' => self::STATUS_PENDING])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
    }

    /**
     * Get all fund requests for admin
     * @param int $limit
     * @return static[]
     */
    public static function getAllRequests($limit = 50)
    {
        return static::find()
            ->with(['customer', 'processedBy'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Get customer's total requested amount
     * @param int $customerId
     * @return float
     */
    public static function getCustomerTotalRequested($customerId)
    {
        return static::find()
            ->where(['customer_id' => $customerId, 'status' => self::STATUS_APPROVED])
            ->sum('amount') ?: 0;
    }

    /**
     * Get customer's pending request count
     * @param int $customerId
     * @return int
     */
    public static function getCustomerPendingCount($customerId)
    {
        return static::find()
            ->where(['customer_id' => $customerId, 'status' => self::STATUS_PENDING])
            ->count();
    }
}

