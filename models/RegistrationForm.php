<?php

namespace app\models;

use dektrium\user\models\RegistrationForm as BaseRegistrationForm;
use yii\helpers\ArrayHelper;
use app\models\Country;
use Yii;

/**
 * Custom Registration Form with additional fields
 */
class RegistrationForm extends BaseRegistrationForm
{
    public $name;
    public $mobile_no;
    public $country_id;
    public $referral_code;
    public $password_repeat;
    public $username; // Override parent username property

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        
        // Remove username required validation since we auto-generate it
        foreach ($rules as $key => $rule) {
            if (isset($rule[0]) && $rule[0] === 'username' && isset($rule[1]) && $rule[1] === 'required') {
                unset($rules[$key]);
            }
        }
        
        $rules[] = [['name', 'mobile_no', 'country_id', 'referral_code'], 'required'];
        $rules[] = [['name'], 'string', 'max' => 255];
        $rules[] = [['mobile_no'], 'string', 'max' => 20];
        $rules[] = [['referral_code'], 'string', 'max' => 50];
        $rules[] = [['country_id'], 'integer'];
        $rules[] = [['password_repeat'], 'required'];
        $rules[] = [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match.'];
        
        // Custom validation for email uniqueness in customer table
        $rules[] = [['email'], 'validateEmailUnique'];
        
        // Custom validation for mobile number uniqueness in customer table
        $rules[] = [['mobile_no'], 'validateMobileUnique'];
        
        // Validate country exists
        $rules[] = [['country_id'], 'exist', 'targetClass' => Country::class, 'targetAttribute' => 'id'];
        
        // Validate referral code exists in user table
        $rules[] = [['referral_code'], 'validateReferralCode'];
        
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        
        $labels['name'] = 'Full Name';
        $labels['mobile_no'] = 'Mobile Number';
        $labels['country_id'] = 'Country';
        $labels['referral_code'] = 'Referral Code';
        $labels['password_repeat'] = 'Confirm Password';
        
        return $labels;
    }

    /**
     * Validate email uniqueness in customer table
     */
    public function validateEmailUnique($attribute, $params)
    {
        $customer = \app\models\Customer::find()->where(['email' => $this->$attribute])->one();
        if ($customer) {
            $this->addError($attribute, 'This email address is already registered.');
        }
    }

    /**
     * Validate mobile number uniqueness in customer table
     */
    public function validateMobileUnique($attribute, $params)
    {
        $customer = \app\models\Customer::find()->where(['mobile_no' => $this->$attribute])->one();
        if ($customer) {
            $this->addError($attribute, 'This mobile number is already registered.');
        }
    }

    /**
     * Validate referral code exists in user table
     */
    public function validateReferralCode($attribute, $params)
    {
        // Referral code is now mandatory
        if (empty($this->$attribute)) {
            $this->addError($attribute, 'Referral code is required.');
            return;
        }

        // Check if the referral code exists in the user table as username
        $user = \app\models\User::find()->where(['username' => $this->$attribute])->one();
        
        if (!$user) {
            $this->addError($attribute, 'Invalid referral code. Please check and try again.');
        }
    }

    /**
     * Get countries for dropdown
     * @return array
     */
    public function getCountries()
    {
        return ArrayHelper::map(Country::find()->where(['status' => 1])->all(), 'id', 'name');
    }

    /**
     * Get countries data formatted for Select2 with flags
     * @return array
     */
    public function getCountriesForSelect2()
    {
        $countries = Country::find()->where(['status' => 1])->all();
        $data[] = ['id' => '', 'text' => 'Select Country', 'flag' => '', 'mobile_code' => ''];
        
        foreach ($countries as $country) {
            $data[] = [
                'id' => $country->id,
                'text' => $country->name,
                'flag' => $country->country_code, // Use country code instead of emoji
                'mobile_code' => $country->mobile_code
            ];
        }
        
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        // Generate custom username for customer (KF000001 format) before validation
        if (empty($this->username)) {
            $this->username = \app\models\Customer::generateCustomerUsername();
        }
        
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            
            // Create user using parent method
            $registrationResult = parent::register();
            
            if (!$registrationResult) {
                $transaction->rollBack();
                $this->addError('email', 'Failed to create user account. Please try again.');
                return false;
            }

            // Get the user object from the form's user property
            $user = $this->user;
            
            // Create customer record
            $customer = new \app\models\Customer();
            $customer->user_id = $user->id;
            $customer->name = $this->name;
            $customer->email = $this->email;
            $customer->mobile_no = $this->mobile_no;
            $customer->country_id = $this->country_id;
            $customer->referral_code = $this->referral_code;
            $customer->current_package = 1; // Default to Free package
            $customer->status = 1; // Active
            
            if (!$customer->save()) {
                $transaction->rollBack();
                // Add customer validation errors to the form
                foreach ($customer->errors as $attribute => $errors) {
                    foreach ($errors as $error) {
                        $this->addError($attribute, $error);
                    }
                }
                $this->addError('name', 'Failed to create customer profile. Please check your information and try again.');
                return false;
            }

            // Create customer package record for the free package
            $freePackage = \app\models\Package::findByName('Free');
            if ($freePackage) {
                $customerPackage = new \app\models\CustomerPackage();
                $customerPackage->customer_id = $customer->id;
                $customerPackage->package_id = $freePackage->id;
                $customerPackage->date = date('Y-m-d H:i:s');
                $customerPackage->status = \app\models\CustomerPackage::STATUS_ACTIVE;
                
                if (!$customerPackage->save()) {
                    $transaction->rollBack();
                    Yii::error('Failed to create customer package record: ' . json_encode($customerPackage->errors), __METHOD__);
                    $this->addError('email', 'Failed to assign package. Please try again.');
                    return false;
                }
            }

            // Assign customer role to user
            try {
                $auth = Yii::$app->authManager;
                $customerRole = $auth->getRole('customer');
                if ($customerRole) {
                    $auth->assign($customerRole, $user->id);
                }
            } catch (\Exception $e) {
                // Log role assignment error but don't fail registration
                Yii::error('Failed to assign customer role to user ' . $user->id . ': ' . $e->getMessage(), __METHOD__);
            }
            
            $transaction->commit();
            return $user;
            
        } catch (\yii\db\Exception $e) {
                $transaction->rollBack();
                Yii::error('Database error during registration: ' . $e->getMessage(), __METHOD__);
                $this->addError('email', 'Database Error: ' . $e->getMessage());
                return false;
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error('Unexpected error during registration: ' . $e->getMessage(), __METHOD__);
                $this->addError('email', 'Error: ' . $e->getMessage());
                return false;
        }
    }
}