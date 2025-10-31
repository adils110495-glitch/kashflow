<?php

namespace app\components;

use Yii;
use yii\web\User as BaseUser;
use app\models\User;

/**
 * Admin User component for admin authentication using existing User model
 */
class AdminUser extends BaseUser
{
    /**
     * {@inheritdoc}
     */
    public $identityClass = User::class;

    /**
     * {@inheritdoc}
     */
    public $loginUrl = ['/admin-auth/login'];

    /**
     * {@inheritdoc}
     */
    public $returnUrlParam = '__adminReturnUrl';

    /**
     * {@inheritdoc}
     */
    public $absoluteAuthTimeoutParam = '__adminAbsoluteAuthTimeout';

    /**
     * {@inheritdoc}
     */
    public $authTimeoutParam = '__adminAuthTimeout';

    /**
     * {@inheritdoc}
     */
    public $idParam = '__adminId';

    /**
     * {@inheritdoc}
     */
    public $autoRenewCookieParam = '__adminAutoRenewCookie';

    /**
     * {@inheritdoc}
     */
    public $enableAutoLogin = true;

    /**
     * {@inheritdoc}
     */
    public $enableSession = true;

    /**
     * {@inheritdoc}
     */
    public $autoRenewCookie = true;

    /**
     * {@inheritdoc}
     */
    public $authTimeout = 3600;

    /**
     * {@inheritdoc}
     */
    public $absoluteAuthTimeout = 2592000; // 30 days

    /**
     * {@inheritdoc}
     */
    public $cookieParams = [
        'httpOnly' => true,
        'name' => 'admin_auth',
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        // Set custom cookie name for admin
        if (empty($this->cookieParams['name'])) {
            $this->cookieParams['name'] = 'admin_auth';
        }
    }

    /**
     * Check if current admin has specific permission
     * @param string $permission
     * @return bool
     */
    public function can($permission, $params = [], $allowCaching = true)
    {
        if ($this->isGuest) {
            return false;
        }

        /** @var User $identity */
        $identity = $this->identity;
        return $identity && $identity->hasAdminPermission($permission);
    }

    /**
     * Check if current admin is super admin
     * @return bool
     */
    public function isSuperAdmin()
    {
        if ($this->isGuest) {
            return false;
        }

        /** @var User $identity */
        $identity = $this->identity;
        return $identity && $identity->isSuperAdmin();
    }

    /**
     * Check if current admin has specific role
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        if ($this->isGuest) {
            return false;
        }

        /** @var User $identity */
        $identity = $this->identity;
        return $identity && $identity->hasAdminRole($role);
    }

    /**
     * Check if current user is admin
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->isGuest) {
            return false;
        }

        /** @var User $identity */
        $identity = $this->identity;
        return $identity && $identity->isAdmin();
    }
}
