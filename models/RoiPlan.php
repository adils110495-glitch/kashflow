<?php

namespace app\models;

use \app\models\base\RoiPlan as BaseRoiPlan;

/**
 * This is the model class for table "roi_plan".
 */
class RoiPlan extends BaseRoiPlan
{
    // Frequency constants
    const FREQUENCY_DAILY = 1;
    const FREQUENCY_WEEKLY = 2;
    const FREQUENCY_MONTHLY = 3;
    const FREQUENCY_YEARLY = 4;

    // Tenure constants
    const TENURE_TWICE = 2;
    const TENURE_THRICE = 3;

    // Status constants
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_PENDING = 2;
    const STATUS_COMPLETED = 3;

    /**
     * Get frequency options
     * @return array
     */
    public static function getFrequencyOptions()
    {
        return [
            self::FREQUENCY_DAILY => 'Daily',
            self::FREQUENCY_WEEKLY => 'Weekly',
            self::FREQUENCY_MONTHLY => 'Monthly',
            self::FREQUENCY_YEARLY => 'Yearly',
        ];
    }

    /**
     * Get tenure options
     * @return array
     */
    public static function getTenureOptions()
    {
        return [
            self::TENURE_TWICE => 'Twice',
            self::TENURE_THRICE => 'Thrice',
        ];
    }

    /**
     * Get frequency label
     * @return string
     */
    public function getFrequencyLabel()
    {
        $options = self::getFrequencyOptions();
        return isset($options[$this->frequency]) ? $options[$this->frequency] : 'Unknown';
    }

    /**
     * Get tenure label
     * @return string
     */
    public function getTenureLabel()
    {
        $options = self::getTenureOptions();
        return isset($options[$this->tenure]) ? $options[$this->tenure] : 'Unknown';
    }

    /**
     * Build frequency dropdown options
     * @return array
     */
    public static function buildFrequency()
    {
        return self::getFrequencyOptions();
    }

    /**
     * Build tenure dropdown options
     * @return array
     */
    public static function buildTenure()
    {
        return self::getTenureOptions();
    }

    /**
     * Get status options
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    /**
     * Get status label
     * @return string
     */
    public function getStatusLabel()
    {
        $options = self::getStatusOptions();
        return isset($options[$this->status]) ? $options[$this->status] : 'Unknown';
    }

    /**
     * Build status dropdown options
     * @return array
     */
    public static function buildStatus()
    {
        return self::getStatusOptions();
    }
}
