<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * AdminLoginForm is the model behind the admin login form.
 *
 * @property-read User|null $admin
 */
class AdminLoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_admin = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $admin = $this->getAdmin();

            if (!$admin || !$admin->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in an admin using the provided username and password.
     * @return bool whether the admin is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $admin = $this->getAdmin();
            if ($admin) {
                // Update last login information
                $admin->updateLastLogin();
                
                // Login the admin using the regular user component
                return Yii::$app->user->login($admin, $this->rememberMe ? 3600*24*30 : 0);
            }
        }
        return false;
    }

    /**
     * Finds admin by [[username]]
     *
     * @return User|null
     */
    public function getAdmin()
    {
        if ($this->_admin === false) {
            $this->_admin = User::findAdminByUsername($this->username);
        }

        return $this->_admin;
    }

    /**
     * Get attribute labels
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'password' => 'Password',
            'rememberMe' => 'Remember Me',
        ];
    }
}
