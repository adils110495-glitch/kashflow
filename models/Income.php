<?php

namespace app\models;

use Yii;
use app\models\base\Income as BaseIncome;

/**
 * This is the model class for table "income".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $date
 * @property int $type
 * @property int $level
 * @property string $amount
 * @property int $status
 * @property string $meta
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Customer $customer
 */
class Income extends BaseIncome
{
    // Income type constants
    const TYPE_ROI = 1;
    const TYPE_LEVEL_INCOME = 2;
    const TYPE_REFERRAL_INCOME = 3;

    // Status constants
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_PROCESSED = 2;
    const STATUS_CANCELLED = 3;

    /**
     * Get income type labels
     * @return array
     */
    public static function getTypeLabels()
    {
        return [
            self::TYPE_ROI => 'ROI Earnings',
            self::TYPE_LEVEL_INCOME => 'Network Earnings',
            self::TYPE_REFERRAL_INCOME => 'Referral Bonus',
        ];
    }

    /**
     * Get status labels
     * @return array
     */
    public static function getStatusLabels()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PROCESSED => 'Processed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * Get type label
     * @return string
     */
    public function getTypeLabel()
    {
        $labels = self::getTypeLabels();
        return isset($labels[$this->type]) ? $labels[$this->type] : 'Unknown';
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
     * Check if income is ROI type
     * @return bool
     */
    public function isROI()
    {
        return $this->type === self::TYPE_ROI;
    }

    /**
     * Check if income is level type
     * @return bool
     */
    public function isLevelIncome()
    {
        return $this->type === self::TYPE_LEVEL_INCOME;
    }

    /**
     * Check if income is referral type
     * @return bool
     */
    public function isReferralIncome()
    {
        return $this->type === self::TYPE_REFERRAL_INCOME;
    }

    /**
     * Check if income is active
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if income is processed
     * @return bool
     */
    public function isProcessed()
    {
        return $this->status === self::STATUS_PROCESSED;
    }
}