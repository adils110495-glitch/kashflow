<?php

namespace app\services;

use Yii;
use app\models\Customer;
use app\models\CustomerActivity;
use yii\web\User;

/**
 * Activity Logger Service
 * 
 * Provides convenient methods for logging customer activities
 */
class ActivityLoggerService
{
    /**
     * Log customer login activity
     *
     * @param int $customerId
     * @param array|null $metadata
     * @return bool
     */
    public static function logLogin($customerId, $metadata = null)
    {
        return CustomerActivity::logActivity(
            $customerId,
            CustomerActivity::TYPE_LOGIN,
            'Customer logged in',
            $metadata
        );
    }

    /**
     * Log customer logout activity
     *
     * @param int $customerId
     * @param array|null $metadata
     * @return bool
     */
    public static function logLogout($customerId, $metadata = null)
    {
        return CustomerActivity::logActivity(
            $customerId,
            CustomerActivity::TYPE_LOGOUT,
            'Customer logged out',
            $metadata
        );
    }

    /**
     * Log customer registration activity
     *
     * @param int $customerId
     * @param array|null $metadata
     * @return bool
     */
    public static function logRegistration($customerId, $metadata = null)
    {
        return CustomerActivity::logActivity(
            $customerId,
            CustomerActivity::TYPE_REGISTRATION,
            'Customer registered',
            $metadata
        );
    }

    /**
     * Log profile update activity
     *
     * @param int $customerId
     * @param array $changedFields
     * @param array|null $metadata
     * @return bool
     */
    public static function logProfileUpdate($customerId, $changedFields = [], $metadata = null)
    {
        $description = 'Profile updated';
        if (!empty($changedFields)) {
            $description .= ': ' . implode(', ', $changedFields);
        }

        $activityMetadata = array_merge(
            ['changed_fields' => $changedFields],
            $metadata ?: []
        );

        return CustomerActivity::logActivity(
            $customerId,
            CustomerActivity::TYPE_PROFILE_UPDATE,
            $description,
            $activityMetadata
        );
    }

    /**
     * Log password change activity
     *
     * @param int $customerId
     * @param array|null $metadata
     * @return bool
     */
    public static function logPasswordChange($customerId, $metadata = null)
    {
        return CustomerActivity::logActivity(
            $customerId,
            CustomerActivity::TYPE_PASSWORD_CHANGE,
            'Password changed',
            $metadata
        );
    }

    /**
     * Log package change activity
     *
     * @param int $customerId
     * @param int $oldPackageId
     * @param int $newPackageId
     * @param string $changeType (upgrade|downgrade|change)
     * @param array|null $metadata
     * @return bool
     */
    public static function logPackageChange($customerId, $oldPackageId, $newPackageId, $changeType = 'change', $metadata = null)
    {
        $activityType = CustomerActivity::TYPE_PACKAGE_CHANGE;
        $description = 'Package changed';

        switch ($changeType) {
            case 'upgrade':
                $activityType = CustomerActivity::TYPE_PACKAGE_UPGRADE;
                $description = 'Package upgraded';
                break;
            case 'downgrade':
                $activityType = CustomerActivity::TYPE_PACKAGE_DOWNGRADE;
                $description = 'Package downgraded';
                break;
        }

        $activityMetadata = array_merge(
            [
                'old_package_id' => $oldPackageId,
                'new_package_id' => $newPackageId,
                'change_type' => $changeType
            ],
            $metadata ?: []
        );

        return CustomerActivity::logActivity(
            $customerId,
            $activityType,
            $description,
            $activityMetadata
        );
    }

    /**
     * Log payment activity
     *
     * @param int $customerId
     * @param string $status (success|failed)
     * @param float $amount
     * @param string|null $transactionId
     * @param array|null $metadata
     * @return bool
     */
    public static function logPayment($customerId, $status, $amount, $transactionId = null, $metadata = null)
    {
        $activityType = $status === 'success' 
            ? CustomerActivity::TYPE_PAYMENT_SUCCESS 
            : CustomerActivity::TYPE_PAYMENT_FAILED;

        $description = $status === 'success' 
            ? "Payment successful: $amount" 
            : "Payment failed: $amount";

        $activityMetadata = array_merge(
            [
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'status' => $status
            ],
            $metadata ?: []
        );

        return CustomerActivity::logActivity(
            $customerId,
            $activityType,
            $description,
            $activityMetadata
        );
    }

    /**
     * Log billing update activity
     *
     * @param int $customerId
     * @param array $changedFields
     * @param array|null $metadata
     * @return bool
     */
    public static function logBillingUpdate($customerId, $changedFields = [], $metadata = null)
    {
        $description = 'Billing information updated';
        if (!empty($changedFields)) {
            $description .= ': ' . implode(', ', $changedFields);
        }

        $activityMetadata = array_merge(
            ['changed_fields' => $changedFields],
            $metadata ?: []
        );

        return CustomerActivity::logActivity(
            $customerId,
            CustomerActivity::TYPE_BILLING_UPDATE,
            $description,
            $activityMetadata
        );
    }

    /**
     * Log support ticket activity
     *
     * @param int $customerId
     * @param string $ticketId
     * @param string $subject
     * @param array|null $metadata
     * @return bool
     */
    public static function logSupportTicket($customerId, $ticketId, $subject, $metadata = null)
    {
        $description = "Support ticket created: $subject";

        $activityMetadata = array_merge(
            [
                'ticket_id' => $ticketId,
                'subject' => $subject
            ],
            $metadata ?: []
        );

        return CustomerActivity::logActivity(
            $customerId,
            CustomerActivity::TYPE_SUPPORT_TICKET,
            $description,
            $activityMetadata
        );
    }

    /**
     * Log email verification activity
     *
     * @param int $customerId
     * @param string $email
     * @param array|null $metadata
     * @return bool
     */
    public static function logEmailVerification($customerId, $email, $metadata = null)
    {
        $description = "Email verified: $email";

        $activityMetadata = array_merge(
            ['email' => $email],
            $metadata ?: []
        );

        return CustomerActivity::logActivity(
            $customerId,
            CustomerActivity::TYPE_EMAIL_VERIFICATION,
            $description,
            $activityMetadata
        );
    }

    /**
     * Log password reset activity
     *
     * @param int $customerId
     * @param array|null $metadata
     * @return bool
     */
    public static function logPasswordReset($customerId, $metadata = null)
    {
        return CustomerActivity::logActivity(
            $customerId,
            CustomerActivity::TYPE_PASSWORD_RESET,
            'Password reset requested',
            $metadata
        );
    }

    /**
     * Log account status change activity
     *
     * @param int $customerId
     * @param string $action (suspension|reactivation)
     * @param string|null $reason
     * @param array|null $metadata
     * @return bool
     */
    public static function logAccountStatusChange($customerId, $action, $reason = null, $metadata = null)
    {
        $activityType = $action === 'suspension' 
            ? CustomerActivity::TYPE_ACCOUNT_SUSPENSION 
            : CustomerActivity::TYPE_ACCOUNT_REACTIVATION;

        $description = $action === 'suspension' 
            ? 'Account suspended' 
            : 'Account reactivated';

        if ($reason) {
            $description .= ": $reason";
        }

        $activityMetadata = array_merge(
            [
                'action' => $action,
                'reason' => $reason
            ],
            $metadata ?: []
        );

        return CustomerActivity::logActivity(
            $customerId,
            $activityType,
            $description,
            $activityMetadata
        );
    }

    /**
     * Log data export activity
     *
     * @param int $customerId
     * @param string $exportType
     * @param array|null $metadata
     * @return bool
     */
    public static function logDataExport($customerId, $exportType, $metadata = null)
    {
        $description = "Data exported: $exportType";

        $activityMetadata = array_merge(
            ['export_type' => $exportType],
            $metadata ?: []
        );

        return CustomerActivity::logActivity(
            $customerId,
            CustomerActivity::TYPE_DATA_EXPORT,
            $description,
            $activityMetadata
        );
    }

    /**
     * Log account deletion activity
     *
     * @param int $customerId
     * @param string|null $reason
     * @param array|null $metadata
     * @return bool
     */
    public static function logAccountDeletion($customerId, $reason = null, $metadata = null)
    {
        $description = 'Account deletion requested';
        if ($reason) {
            $description .= ": $reason";
        }

        $activityMetadata = array_merge(
            ['reason' => $reason],
            $metadata ?: []
        );

        return CustomerActivity::logActivity(
            $customerId,
            CustomerActivity::TYPE_ACCOUNT_DELETION,
            $description,
            $activityMetadata
        );
    }

    /**
     * Log custom activity
     *
     * @param int $customerId
     * @param string $activityType
     * @param string $description
     * @param array|null $metadata
     * @return bool
     */
    public static function logCustomActivity($customerId, $activityType, $description, $metadata = null)
    {
        return CustomerActivity::logActivity(
            $customerId,
            $activityType,
            $description,
            $metadata
        );
    }

    /**
     * Get current customer ID from session
     *
     * @return int|null
     */
    protected static function getCurrentCustomerId()
    {
        if (Yii::$app->user->isGuest) {
            return null;
        }

        $identity = Yii::$app->user->identity;
        if ($identity && isset($identity->customer)) {
            return $identity->customer->id;
        }

        return null;
    }

    /**
     * Log activity for current logged-in customer
     *
     * @param string $activityType
     * @param string $description
     * @param array|null $metadata
     * @return bool
     */
    public static function logCurrentCustomerActivity($activityType, $description, $metadata = null)
    {
        $customerId = self::getCurrentCustomerId();
        if (!$customerId) {
            return false;
        }

        return CustomerActivity::logActivity(
            $customerId,
            $activityType,
            $description,
            $metadata
        );
    }
}