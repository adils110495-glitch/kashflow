<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "customer_activity".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $activity_type
 * @property string|null $activity_description
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array|null $metadata
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Customer $customer
 */
class CustomerActivity extends ActiveRecord
{
    // Activity type constants
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_REGISTRATION = 'registration';
    const TYPE_PROFILE_UPDATE = 'profile_update';
    const TYPE_PASSWORD_CHANGE = 'password_change';
    const TYPE_PACKAGE_CHANGE = 'package_change';
    const TYPE_PACKAGE_UPGRADE = 'package_upgrade';
    const TYPE_PACKAGE_DOWNGRADE = 'package_downgrade';
    const TYPE_BILLING_UPDATE = 'billing_update';
    const TYPE_PAYMENT_SUCCESS = 'payment_success';
    const TYPE_PAYMENT_FAILED = 'payment_failed';
    const TYPE_INVOICE_GENERATED = 'invoice_generated';
    const TYPE_SUPPORT_TICKET = 'support_ticket';
    const TYPE_SETTINGS_UPDATE = 'settings_update';
    const TYPE_EMAIL_VERIFICATION = 'email_verification';
    const TYPE_PASSWORD_RESET = 'password_reset';
    const TYPE_ACCOUNT_SUSPENSION = 'account_suspension';
    const TYPE_ACCOUNT_REACTIVATION = 'account_reactivation';
    const TYPE_DATA_EXPORT = 'data_export';
    const TYPE_ACCOUNT_DELETION = 'account_deletion';
    const TYPE_INCOME_GENERATED = 'income_generated';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'activity_type'], 'required'],
            [['customer_id'], 'integer'],
            [['activity_description', 'user_agent'], 'string'],
            [['metadata'], 'safe'],
            [['created_at', 'updated_at'], 'safe'],
            [['activity_type'], 'string', 'max' => 50],
            [['ip_address'], 'string', 'max' => 45],
            [['ip_address'], 'ip'],
            [['activity_type'], 'in', 'range' => self::getActivityTypes()],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'activity_type' => 'Activity Type',
            'activity_description' => 'Activity Description',
            'ip_address' => 'IP Address',
            'user_agent' => 'User Agent',
            'metadata' => 'Metadata',
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
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * Get all available activity types
     *
     * @return array
     */
    public static function getActivityTypes()
    {
        return [
            self::TYPE_LOGIN,
            self::TYPE_LOGOUT,
            self::TYPE_REGISTRATION,
            self::TYPE_PROFILE_UPDATE,
            self::TYPE_PASSWORD_CHANGE,
            self::TYPE_PACKAGE_CHANGE,
            self::TYPE_PACKAGE_UPGRADE,
            self::TYPE_PACKAGE_DOWNGRADE,
            self::TYPE_BILLING_UPDATE,
            self::TYPE_PAYMENT_SUCCESS,
            self::TYPE_PAYMENT_FAILED,
            self::TYPE_INVOICE_GENERATED,
            self::TYPE_SUPPORT_TICKET,
            self::TYPE_SETTINGS_UPDATE,
            self::TYPE_EMAIL_VERIFICATION,
            self::TYPE_PASSWORD_RESET,
            self::TYPE_ACCOUNT_SUSPENSION,
            self::TYPE_ACCOUNT_REACTIVATION,
            self::TYPE_DATA_EXPORT,
            self::TYPE_ACCOUNT_DELETION,
            self::TYPE_INCOME_GENERATED,
        ];
    }

    /**
     * Get activity type labels
     *
     * @return array
     */
    public static function getActivityTypeLabels()
    {
        return [
            self::TYPE_LOGIN => 'Login',
            self::TYPE_LOGOUT => 'Logout',
            self::TYPE_REGISTRATION => 'Registration',
            self::TYPE_PROFILE_UPDATE => 'Profile Update',
            self::TYPE_PASSWORD_CHANGE => 'Password Change',
            self::TYPE_PACKAGE_CHANGE => 'Package Change',
            self::TYPE_PACKAGE_UPGRADE => 'Package Upgrade',
            self::TYPE_PACKAGE_DOWNGRADE => 'Package Downgrade',
            self::TYPE_BILLING_UPDATE => 'Billing Update',
            self::TYPE_PAYMENT_SUCCESS => 'Payment Success',
            self::TYPE_PAYMENT_FAILED => 'Payment Failed',
            self::TYPE_INVOICE_GENERATED => 'Invoice Generated',
            self::TYPE_SUPPORT_TICKET => 'Support Ticket',
            self::TYPE_SETTINGS_UPDATE => 'Settings Update',
            self::TYPE_EMAIL_VERIFICATION => 'Email Verification',
            self::TYPE_PASSWORD_RESET => 'Password Reset',
            self::TYPE_ACCOUNT_SUSPENSION => 'Account Suspension',
            self::TYPE_ACCOUNT_REACTIVATION => 'Account Reactivation',
            self::TYPE_DATA_EXPORT => 'Data Export',
            self::TYPE_ACCOUNT_DELETION => 'Account Deletion',
        ];
    }

    /**
     * Get activity type label
     *
     * @return string
     */
    public function getActivityTypeLabel()
    {
        $labels = self::getActivityTypeLabels();
        return isset($labels[$this->activity_type]) ? $labels[$this->activity_type] : $this->activity_type;
    }

    /**
     * Log customer activity
     *
     * @param int $customerId
     * @param string $activityType
     * @param string|null $description
     * @param array|null $metadata
     * @return bool
     */
    public static function logActivity($customerId, $activityType, $description = null, $metadata = null)
    {
        $activity = new self();
        $activity->customer_id = $customerId;
        $activity->activity_type = $activityType;
        $activity->activity_description = $description;
        $activity->ip_address = Yii::$app->request->getUserIP();
        $activity->user_agent = Yii::$app->request->getUserAgent();
        $activity->metadata = $metadata;

        return $activity->save();
    }

    /**
     * Get recent activities for a customer
     *
     * @param int $customerId
     * @param int $limit
     * @return CustomerActivity[]
     */
    public static function getRecentActivities($customerId, $limit = 10)
    {
        return self::find()
            ->where(['customer_id' => $customerId])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Get activities by type for a customer
     *
     * @param int $customerId
     * @param string $activityType
     * @param int $limit
     * @return CustomerActivity[]
     */
    public static function getActivitiesByType($customerId, $activityType, $limit = 10)
    {
        return self::find()
            ->where(['customer_id' => $customerId, 'activity_type' => $activityType])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Get activity count by type for a customer
     *
     * @param int $customerId
     * @param string|null $activityType
     * @return int
     */
    public static function getActivityCount($customerId, $activityType = null)
    {
        $query = self::find()->where(['customer_id' => $customerId]);
        
        if ($activityType !== null) {
            $query->andWhere(['activity_type' => $activityType]);
        }
        
        return $query->count();
    }



    /**
     * Get activities within date range
     *
     * @param int $customerId
     * @param string $startDate
     * @param string $endDate
     * @return CustomerActivity[]
     */
    public static function getActivitiesInDateRange($customerId, $startDate, $endDate)
    {
        return self::find()
            ->where(['customer_id' => $customerId])
            ->andWhere(['>=', 'created_at', $startDate])
            ->andWhere(['<=', 'created_at', $endDate])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }
}