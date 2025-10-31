<?php

namespace app\models;

use dektrium\user\models\User as BaseUser;
use Yii;
use yii\base\NotSupportedException;

class User extends BaseUser
{
    // Admin role constants
    const ROLE_SUPER_ADMIN = 'superadmin';
    const ROLE_ADMIN = 'admin';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_CUSTOMER = 'customer';

    /**
     * Check if user is an admin (has admin role)
     * @return bool
     */
    public function isAdmin()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        
        return Yii::$app->authManager->checkAccess($this->id, self::ROLE_SUPER_ADMIN) ||
               Yii::$app->authManager->checkAccess($this->id, self::ROLE_ADMIN) ||
               Yii::$app->authManager->checkAccess($this->id, self::ROLE_MODERATOR);
    }

    /**
     * Check if user is super admin
     * @return bool
     */
    public function isSuperAdmin()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        
        return Yii::$app->authManager->checkAccess($this->id, self::ROLE_SUPER_ADMIN);
    }

    /**
     * Check if user has specific admin role
     * @param string $role
     * @return bool
     */
    public function hasAdminRole($role)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        
        return Yii::$app->authManager->checkAccess($this->id, $role);
    }

    /**
     * Check if admin has specific permission
     * @param string $permission
     * @return bool
     */
    public function hasAdminPermission($permission)
    {
        if (!$this->isAdmin()) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true; // Super admin has all permissions
        }

        return Yii::$app->authManager->checkAccess($this->id, $permission);
    }

    /**
     * Get user roles
     * @return array
     */
    public function getUserRoles()
    {
        if (Yii::$app->user->isGuest) {
            return [];
        }
        
        $roles = Yii::$app->authManager->getRolesByUser($this->id);
        return array_keys($roles);
    }

    /**
     * Get admin role text
     * @return string
     */
    public function getAdminRoleText()
    {
        $roles = $this->getUserRoles();
        
        if (in_array(self::ROLE_SUPER_ADMIN, $roles)) {
            return 'Super Admin';
        } elseif (in_array(self::ROLE_ADMIN, $roles)) {
            return 'Admin';
        } elseif (in_array(self::ROLE_MODERATOR, $roles)) {
            return 'Moderator';
        }
        
        return 'Customer';
    }

    /**
     * Get available admin roles
     * @return array
     */
    public static function getAdminRoleOptions()
    {
        return [
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_MODERATOR => 'Moderator',
        ];
    }

    /**
     * Find admin users
     * @return \yii\db\ActiveQuery
     */
    public static function findAdmins()
    {
        $adminUserIds = [];
        $auth = Yii::$app->authManager;
        
        // Get all users with admin roles
        $roles = [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_MODERATOR];
        foreach ($roles as $role) {
            $userIds = $auth->getUserIdsByRole($role);
            $adminUserIds = array_merge($adminUserIds, $userIds);
        }
        
        return static::find()->where(['id' => array_unique($adminUserIds)]);
    }

    /**
     * Find admin by username (admin only)
     * @param string $username
     * @return static|null
     */
    public static function findAdminByUsername($username)
    {
        $user = static::findOne(['username' => $username]);
        if ($user && $user->isAdmin()) {
            return $user;
        }
        return null;
    }

    /**
     * Find admin by email (admin only)
     * @param string $email
     * @return static|null
     */
    public static function findAdminByEmail($email)
    {
        $user = static::findOne(['email' => $email]);
        if ($user && $user->isAdmin()) {
            return $user;
        }
        return null;
    }

    /**
     * Create admin user
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $role
     * @return bool
     */
    public static function createAdmin($username, $email, $password, $role = self::ROLE_ADMIN)
    {
        $user = new static();
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->status = 10; // Active status
        
        if ($user->save()) {
            // Assign role using RBAC
            $auth = Yii::$app->authManager;
            $roleObj = $auth->getRole($role);
            if ($roleObj) {
                $auth->assign($roleObj, $user->id);
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user is a customer
     * @return bool
     */
    public function isCustomer()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        
        return Yii::$app->authManager->checkAccess($this->id, self::ROLE_CUSTOMER) ||
               Customer::find()->where(['user_id' => $this->id])->exists();
    }

    /**
     * Get customer profile if exists
     * @return Customer|null
     */
    public function getCustomer()
    {
        return Customer::find()->where(['user_id' => $this->id])->one();
    }

    /**
     * Assign role to user
     * @param string $role
     * @return bool
     */
    public function assignRole($role)
    {
        $auth = Yii::$app->authManager;
        $roleObj = $auth->getRole($role);
        
        if ($roleObj && !$auth->checkAccess($this->id, $role)) {
            return $auth->assign($roleObj, $this->id);
        }
        
        return false;
    }

    /**
     * Revoke role from user
     * @param string $role
     * @return bool
     */
    public function revokeRole($role)
    {
        $auth = Yii::$app->authManager;
        $roleObj = $auth->getRole($role);
        
        if ($roleObj && $auth->checkAccess($this->id, $role)) {
            return $auth->revoke($roleObj, $this->id);
        }
        
        return false;
    }
}
