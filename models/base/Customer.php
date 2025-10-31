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
 * @property string|null $crypto_wallet_address
 * @property string|null $upi_id
 * @property string|null $qr_code_image
 * @property string|null $aadhar_number
 * @property string|null $aadhar_card_image
 * @property string|null $pan_number
 * @property string|null $pan_card_image
 * @property string|null $bank_account_number
 * @property string|null $bank_account_holder_name
 * @property string|null $bank_name
 * @property string|null $bank_ifsc_code
 * @property string|null $bank_branch_name
 * @property string|null $bank_account_type
 * @property string|null $balance
 * @property int|null $currency_id
 * @property int $kyc_status
 * @property string|null $kyc_verified_at
 * @property int|null $kyc_verified_by
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
 * @property \dektrium\user\models\User $kycVerifiedBy
 * @property \app\models\Currency $currency
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
            [['user_id', 'name', 'email', 'mobile_no', 'country_id', 'referral_code'], 'required'],
            [['user_id', 'country_id', 'status', 'current_package', 'kyc_status', 'kyc_verified_by', 'currency_id'], 'integer'],
            [['name', 'email', 'crypto_wallet_address', 'qr_code_image', 'aadhar_card_image', 'pan_card_image', 'bank_account_holder_name', 'bank_name', 'bank_branch_name'], 'string', 'max' => 255],
            [['balance'], 'safe'],
            [['mobile_no'], 'string', 'max' => 20],
            [['referral_code'], 'string', 'max' => 50],
            [['upi_id'], 'string', 'max' => 100],
            [['aadhar_number'], 'string', 'max' => 12],
            [['pan_number'], 'string', 'max' => 10],
            [['bank_account_number'], 'string', 'max' => 20],
            [['bank_ifsc_code'], 'string', 'max' => 11],
            [['bank_account_type'], 'string', 'max' => 20],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['mobile_no'], 'unique'],
            [['upi_id'], 'unique'],
            [['aadhar_number'], 'unique'],
            [['pan_number'], 'unique'],
            [['bank_account_number'], 'unique'],
            [['status'], 'default', 'value' => 1],
            [['status'], 'in', 'range' => [0, 1]],
            [['kyc_status'], 'default', 'value' => 0],
            [['kyc_status'], 'in', 'range' => [0, 1, 2]],
            [['current_package'], 'default', 'value' => 1],
            [['kyc_verified_at'], 'safe'],
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
            'crypto_wallet_address' => 'Crypto Wallet Address',
            'upi_id' => 'UPI ID',
            'qr_code_image' => 'QR Code Image',
            'aadhar_number' => 'Aadhar Card Number',
            'aadhar_card_image' => 'Aadhar Card Image',
            'pan_number' => 'PAN Card Number',
            'pan_card_image' => 'PAN Card Image',
            'bank_account_number' => 'Bank Account Number',
            'bank_account_holder_name' => 'Account Holder Name',
            'bank_name' => 'Bank Name',
            'bank_ifsc_code' => 'IFSC Code',
            'bank_branch_name' => 'Branch Name',
            'bank_account_type' => 'Account Type',
            'currency_id' => 'Preferred Currency',
            'kyc_status' => 'KYC Status',
            'kyc_verified_at' => 'KYC Verified At',
            'kyc_verified_by' => 'KYC Verified By',
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

    /**
     * Gets query for [[KycVerifiedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKycVerifiedBy()
    {
        return $this->hasOne(\dektrium\user\models\User::class, ['id' => 'kyc_verified_by']);
    }

    /**
     * Gets query for [[Currency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(\app\models\Currency::class, ['id' => 'currency_id']);
    }
}