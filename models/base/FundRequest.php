<?php

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "fund_request".
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
 * @property \app\models\Customer $customer
 * @property \dektrium\user\models\User $processedBy
 */
class FundRequest extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%fund_request}}';
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
            [['customer_id', 'amount', 'request_date', 'attachment_file'], 'required'],
            [['customer_id', 'status', 'processed_by', 'processed_at'], 'integer'],
            [['amount'], 'number'],
            [['request_date'], 'date', 'format' => 'php:Y-m-d'],
            [['comment', 'admin_comment'], 'string'],
            [['attachment_file'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => 0],
            [['status'], 'in', 'range' => [0, 1, 2]],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['processed_by'], 'exist', 'skipOnError' => true, 'targetClass' => \dektrium\user\models\User::class, 'targetAttribute' => ['processed_by' => 'id']],
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
            'request_date' => 'Request Date',
            'attachment_file' => 'Attachment File *',
            'comment' => 'Comment',
            'status' => 'Status',
            'admin_comment' => 'Admin Comment',
            'processed_by' => 'Processed By',
            'processed_at' => 'Processed At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
     * Gets query for [[ProcessedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcessedBy()
    {
        return $this->hasOne(\dektrium\user\models\User::class, ['id' => 'processed_by']);
    }
}

