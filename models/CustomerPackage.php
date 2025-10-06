<?php

namespace app\models;

use Yii;
use app\models\base\CustomerPackage as BaseCustomerPackage;

/**
 * This is the model class for table "customer_package".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $package_id
 * @property string $date
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Customer $customer
 * @property Package $package
 */
class CustomerPackage extends BaseCustomerPackage
{
    // Status constants
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_PENDING = 2;
    const STATUS_EXPIRED = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_SUSPENDED = 5;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        // Add custom validation rules if needed
        $rules[] = [['status'], 'in', 'range' => [
            self::STATUS_INACTIVE,
            self::STATUS_ACTIVE,
            self::STATUS_PENDING,
            self::STATUS_EXPIRED,
            self::STATUS_CANCELLED,
            self::STATUS_SUSPENDED
        ]];
        return $rules;
    }

    /**
     * Get status text
     * @return string
     */
    public function getStatusText()
    {
        $statusMap = [
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_SUSPENDED => 'Suspended'
        ];
        
        return $statusMap[$this->status] ?? 'Unknown';
    }

    /**
     * Get all status options
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_SUSPENDED => 'Suspended'
        ];
    }

    /**
     * Check if the customer package is active
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the customer package is expired
     * @return bool
     */
    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    /**
     * Check if the customer package is pending
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Activate the customer package
     * @return bool
     */
    public function activate()
    {
        $this->status = self::STATUS_ACTIVE;
        return $this->save(false);
    }

    /**
     * Deactivate the customer package
     * @return bool
     */
    public function deactivate()
    {
        $this->status = self::STATUS_INACTIVE;
        return $this->save(false);
    }

    /**
     * Expire the customer package
     * @return bool
     */
    public function expire()
    {
        $this->status = self::STATUS_EXPIRED;
        return $this->save(false);
    }

    /**
     * Cancel the customer package
     * @return bool
     */
    public function cancel()
    {
        $this->status = self::STATUS_CANCELLED;
        return $this->save(false);
    }

    /**
     * Suspend the customer package
     * @return bool
     */
    public function suspend()
    {
        $this->status = self::STATUS_SUSPENDED;
        return $this->save(false);
    }
}