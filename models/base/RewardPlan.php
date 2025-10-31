<?php

namespace app\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "reward_plan".
 *
 * @property integer $id
 * @property string $business_amount
 * @property string $reward
 * @property string $reward_amount
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
abstract class RewardPlan extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'reward_plan';
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::class,
        ];
        return $behaviors;
    }

    public function rules()
    {
        $parentRules = parent::rules();
        return ArrayHelper::merge($parentRules, [
            [['business_amount', 'reward', 'reward_amount'], 'required'],
            [['business_amount', 'reward_amount'], 'number', 'min' => 0],
            [['reward'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => 1],
            [['status'], 'in', 'range' => [0, 1]],
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => 'ID',
            'business_amount' => 'Business Amount',
            'reward' => 'Reward',
            'reward_amount' => 'Reward Amount',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ]);
    }
}


