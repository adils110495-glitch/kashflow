<?php

namespace app\models;

use app\models\base\RewardPlan as BaseRewardPlan;

class RewardPlan extends BaseRewardPlan
{
    public static function getStatusOptions()
    {
        return [
            1 => 'Active',
            0 => 'Inactive',
        ];
    }

    public function getStatusLabel()
    {
        $options = self::getStatusOptions();
        return isset($options[$this->status]) ? $options[$this->status] : 'Unknown';
    }
}


