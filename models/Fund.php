<?php

namespace app\models;

use app\models\base\Fund as BaseFund;

/**
 * Fund model
 *
 * @property int $id
 * @property int $customer_id
 * @property string $amount
 * @property string $date
 * @property string|null $attachment_file
 * @property string|null $comment
 * @property int $status
 * @property int $action_by
 * @property int $action_time
 * @property int $updated_at
 * @property int|null $updated_by
 *
 * @property Customer $customer
 * @property \dektrium\user\models\User $actionBy
 * @property \dektrium\user\models\User $updatedBy
 */
class Fund extends BaseFund
{
    // Status constants
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * Get status labels
     * @return array
     */
    public static function getStatusLabels()
    {
        return [
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
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
     * Check if fund is active
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
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
     * Get formatted date
     * @return string
     */
    public function getFormattedDate()
    {
        return date('M d, Y', strtotime($this->date));
    }

    /**
     * Get customer's total fund amount
     * @param int $customerId
     * @return float
     */
    public static function getCustomerTotalFunds($customerId)
    {
        return static::find()
            ->where(['customer_id' => $customerId, 'status' => self::STATUS_ACTIVE])
            ->sum('amount') ?: 0;
    }

    /**
     * Get customer's fund count
     * @param int $customerId
     * @return int
     */
    public static function getCustomerFundCount($customerId)
    {
        return static::find()
            ->where(['customer_id' => $customerId])
            ->count();
    }

    /**
     * Get recent funds for a customer
     * @param int $customerId
     * @param int $limit
     * @return static[]
     */
    public static function getCustomerRecentFunds($customerId, $limit = 10)
    {
        return static::find()
            ->where(['customer_id' => $customerId])
            ->orderBy(['date' => SORT_DESC, 'id' => SORT_DESC])
            ->limit($limit)
            ->all();
    }
}
