<?php

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "customer".
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $email
 * @property string $mobile_no
 * @property string|null $referral_code
 * @property int $country_id
 * @property int|null $current_package
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property \app\models\Country $country
 * @property \app\models\Package $currentPackage
 * @property \dektrium\user\models\User $user
 * @property \app\models\Customer[] $referredCustomers
 * @property \app\models\CustomerPackage[] $customerPackages
 * @property \app\models\CustomerActivity[] $activities
 * @property \app\models\Income[] $incomes
 * @property \app\models\Ledger[] $ledgerEntries
 * @property \app\models\Withdrawal[] $withdrawals
 */
class Customer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%customer}}';
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
            [['user_id', 'name', 'email', 'mobile_no', 'country_id'], 'required'],
            [['user_id', 'country_id', 'status', 'current_package'], 'integer'],
            [['name', 'email'], 'string', 'max' => 255],
            [['mobile_no'], 'string', 'max' => 20],
            [['referral_code'], 'string', 'max' => 50],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['mobile_no'], 'unique'],
            [['status'], 'default', 'value' => 1],
            [['status'], 'in', 'range' => [0, 1]],
            [['current_package'], 'default', 'value' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Full Name',
            'email' => 'Email Address',
            'mobile_no' => 'Mobile Number',
            'referral_code' => 'Referral Code',
            'country_id' => 'Country',
            'current_package' => 'Current Package',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(\app\models\Country::class, ['id' => 'country_id']);
    }

    /**
     * Gets query for [[CurrentPackage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPackage()
    {
        return $this->hasOne(\app\models\Package::class, ['id' => 'current_package']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\dektrium\user\models\User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[ReferredCustomers]] - customers who used this customer's referral code.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReferredCustomers()
    {
        return $this->hasMany(\app\models\Customer::class, ['id' => 'id'])
            ->viaTable('{{%customer}}', ['referral_code' => 'referral_code'], function ($query) {
                $query->andWhere(['!=', 'id', $this->id]);
            });
    }

    /**
     * Gets the referrer customer based on referral code used during registration.
     * This would need to be implemented based on how referral tracking is stored.
     *
     * @return \yii\db\ActiveQuery|null
     */
    public function getReferrer()
    {
        // This would need additional logic to track which referral code was used
        // For now, returning null as the relationship needs to be defined based on business logic
        return null;
    }

    /**
     * Gets query for [[CustomerPackages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerPackages()
    {
        return $this->hasMany(\app\models\CustomerPackage::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[Activities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActivities()
    {
        return $this->hasMany(\app\models\CustomerActivity::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[Incomes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIncomes()
    {
        return $this->hasMany(\app\models\Income::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[LedgerEntries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLedgerEntries()
    {
        return $this->hasMany(\app\models\Ledger::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[Withdrawals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWithdrawals()
    {
        return $this->hasMany(\app\models\Withdrawal::class, ['customer_id' => 'id']);
    }
}