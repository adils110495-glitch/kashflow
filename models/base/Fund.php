<?php

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "fund".
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
 * @property \app\models\Customer $customer
 * @property \dektrium\user\models\User $actionBy
 * @property \dektrium\user\models\User $updatedBy
 */
class Fund extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%fund}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'amount', 'date', 'action_by', 'action_time'], 'required'],
            [['customer_id', 'status', 'action_by', 'action_time', 'updated_by'], 'integer'],
            [['amount'], 'number'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['comment'], 'string'],
            [['attachment_file'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => 1],
            [['status'], 'in', 'range' => [0, 1]],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['action_by'], 'exist', 'skipOnError' => true, 'targetClass' => \dektrium\user\models\User::class, 'targetAttribute' => ['action_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => \dektrium\user\models\User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer',
            'amount' => 'Amount',
            'date' => 'Date',
            'attachment_file' => 'Attachment File',
            'comment' => 'Comment',
            'status' => 'Status',
            'action_by' => 'Action By',
            'action_time' => 'Action Time',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(\app\models\Customer::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[ActionBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActionBy()
    {
        return $this->hasOne(\dektrium\user\models\User::class, ['id' => 'action_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(\dektrium\user\models\User::class, ['id' => 'updated_by']);
    }
}
