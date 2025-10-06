<?php

namespace app\models;

use Yii;
use app\models\base\Package as BasePackage;
use app\models\CustomerPackage;

/**
 * This is the model class for table "package".
 *
 * @property int $id
 * @property string $name
 * @property string $amount
 * @property string $fee
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Package extends BasePackage
{
    // Status constants
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_PREMIUM = 2;
    const STATUS_PENDING = 3;
    const STATUS_EXPIRED = 4;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        // Add custom validation rule for unique name
        $rules[] = [['name'], 'unique'];
        return $rules;
    }

    /**
     * Get status text
     */
    public function getStatusText()
    {
        $statusMap = [
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PREMIUM => 'Premium',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_EXPIRED => 'Expired'
        ];
        
        return isset($statusMap[$this->status]) ? $statusMap[$this->status] : 'Unknown';
    }

    /**
     * Get status label with Bootstrap styling
     */
    public function getStatusLabel()
    {
        $statusLabels = [
            self::STATUS_ACTIVE => '<span class="label label-success">Active</span>',
            self::STATUS_PREMIUM => '<span class="label label-primary">Premium</span>',
            self::STATUS_INACTIVE => '<span class="label label-danger">Inactive</span>',
            self::STATUS_PENDING => '<span class="label label-warning">Pending</span>',
            self::STATUS_EXPIRED => '<span class="label label-default">Expired</span>',
        ];
        
        return isset($statusLabels[$this->status]) ? $statusLabels[$this->status] : '<span class="label label-info">' . $this->getStatusText() . '</span>';
    }

    /**
     * Build status dropdown options
     */
    public static function buildStatus()
    {
        return [
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PREMIUM => 'Premium',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_EXPIRED => 'Expired'
        ];
    }

    /**
     * Get active packages
     */
    public static function getActivePackages()
    {
        return static::find()->where(['status' => [1, 2]])->all();
    }

    /**
     * Get package by name
     */
    public static function findByName($name)
    {
        return static::findOne(['name' => $name]);
    }

    /**
     * Get customer packages relation
     */
    public function getCustomerPackages()
    {
        return $this->hasMany(CustomerPackage::class, ['package_id' => 'id']);
    }

    /**
     * Get active customer packages
     */
    public function getActiveCustomerPackages()
    {
        return $this->hasMany(CustomerPackage::class, ['package_id' => 'id'])
            ->where(['status' => CustomerPackage::STATUS_ACTIVE]);
    }
}