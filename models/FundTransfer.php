<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "fund_transfer".
 *
 * @property int $id
 * @property int $from_customer_id Customer sending the funds
 * @property int $to_customer_id Customer receiving the funds
 * @property float $amount Transfer amount
 * @property string $transfer_date Date of transfer
 * @property int $status Transfer status: 0=pending, 1=approved, 2=rejected
 * @property int $transfer_type Transfer type: 1=customer_to_customer, 2=admin_transfer
 * @property string|null $comment Transfer comment/description
 * @property string|null $admin_comment Admin comment for approval/rejection
 * @property int|null $processed_by Admin who processed the transfer
 * @property int|null $processed_at Timestamp when processed
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Customer $fromCustomer
 * @property User $processedBy
 * @property Customer $toCustomer
 */
class FundTransfer extends \yii\db\ActiveRecord
{
    // Status constants
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_PENDING_RECEIVER_APPROVAL = 3;
    const STATUS_RECEIVER_APPROVED = 4;
    const STATUS_RECEIVER_REJECTED = 5;

    // Transfer type constants
    const TYPE_CUSTOMER_TO_CUSTOMER = 1;
    const TYPE_ADMIN_TRANSFER = 2;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fund_transfer';
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
            [['from_customer_id', 'to_customer_id', 'amount', 'transfer_date'], 'required'],
            [['from_customer_id', 'to_customer_id', 'status', 'transfer_type', 'processed_by', 'processed_at'], 'integer'],
            [['amount'], 'number', 'min' => 0.01],
            [['transfer_date'], 'date', 'format' => 'php:Y-m-d'],
            [['comment', 'admin_comment'], 'string'],
            [['status'], 'default', 'value' => self::STATUS_PENDING],
            [['transfer_type'], 'default', 'value' => self::TYPE_CUSTOMER_TO_CUSTOMER],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_PENDING_RECEIVER_APPROVAL, self::STATUS_RECEIVER_APPROVED, self::STATUS_RECEIVER_REJECTED]],
            [['transfer_type'], 'in', 'range' => [self::TYPE_CUSTOMER_TO_CUSTOMER, self::TYPE_ADMIN_TRANSFER]],
            [['from_customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['from_customer_id' => 'id']],
            [['to_customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['to_customer_id' => 'id']],
            [['processed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['processed_by' => 'id']],
            [['to_customer_id'], 'compare', 'compareAttribute' => 'from_customer_id', 'operator' => '!=', 'message' => 'Cannot transfer to the same customer.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_customer_id' => 'From Customer',
            'to_customer_id' => 'To Customer',
            'amount' => 'Amount',
            'transfer_date' => 'Transfer Date',
            'status' => 'Status',
            'transfer_type' => 'Transfer Type',
            'comment' => 'Comment',
            'admin_comment' => 'Admin Comment',
            'processed_by' => 'Processed By',
            'processed_at' => 'Processed At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[FromCustomer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'from_customer_id']);
    }

    /**
     * Gets query for [[ProcessedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcessedBy()
    {
        return $this->hasOne(User::class, ['id' => 'processed_by']);
    }

    /**
     * Gets query for [[ToCustomer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'to_customer_id']);
    }

    /**
     * Get status labels
     * @return array
     */
    public static function getStatusLabels()
    {
        return [
            self::STATUS_PENDING => 'Pending Admin Approval',
            self::STATUS_APPROVED => 'Approved by Admin',
            self::STATUS_REJECTED => 'Rejected by Admin',
            self::STATUS_PENDING_RECEIVER_APPROVAL => 'Pending Receiver Approval',
            self::STATUS_RECEIVER_APPROVED => 'Approved by Receiver',
            self::STATUS_RECEIVER_REJECTED => 'Rejected by Receiver',
        ];
    }

    /**
     * Get transfer type labels
     * @return array
     */
    public static function getTransferTypeLabels()
    {
        return [
            self::TYPE_CUSTOMER_TO_CUSTOMER => 'Customer to Customer',
            self::TYPE_ADMIN_TRANSFER => 'Admin Transfer',
        ];
    }

    /**
     * Get status label
     * @return string
     */
    public function getStatusLabel()
    {
        $labels = self::getStatusLabels();
        return $labels[$this->status] ?? 'Unknown';
    }

    /**
     * Get transfer type label
     * @return string
     */
    public function getTransferTypeLabel()
    {
        $labels = self::getTransferTypeLabels();
        return $labels[$this->transfer_type] ?? 'Unknown';
    }

    /**
     * Check if transfer is pending
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if transfer is approved
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if transfer is rejected
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if transfer is pending receiver approval
     * @return bool
     */
    public function isPendingReceiverApproval()
    {
        return $this->status === self::STATUS_PENDING_RECEIVER_APPROVAL;
    }

    /**
     * Check if transfer is receiver approved
     * @return bool
     */
    public function isReceiverApproved()
    {
        return $this->status === self::STATUS_RECEIVER_APPROVED;
    }

    /**
     * Check if transfer is receiver rejected
     * @return bool
     */
    public function isReceiverRejected()
    {
        return $this->status === self::STATUS_RECEIVER_REJECTED;
    }

    /**
     * Check if transfer can be approved by receiver
     * @return bool
     */
    public function canBeApprovedByReceiver()
    {
        return $this->status === self::STATUS_PENDING_RECEIVER_APPROVAL;
    }

    /**
     * Get formatted amount
     * @return string
     */
    public function getFormattedAmount()
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get formatted transfer date
     * @return string
     */
    public function getFormattedTransferDate()
    {
        return date('M d, Y', strtotime($this->transfer_date));
    }

    /**
     * Get formatted processed date
     * @return string
     */
    public function getFormattedProcessedDate()
    {
        return $this->processed_at ? date('M d, Y H:i', $this->processed_at) : 'Not Processed';
    }

    /**
     * Process transfer (approve or reject)
     * @param int $status
     * @param string $adminComment
     * @return bool
     */
    public function processTransfer($status, $adminComment = '')
    {
        if (!$this->isPending()) {
            return false; // Can only process pending transfers
        }

        $this->status = $status;
        $this->admin_comment = $adminComment;
        $this->processed_by = Yii::$app->user->id;
        $this->processed_at = time();

        if ($this->save()) {
            // If approved by admin, move to pending receiver approval (don't execute yet)
            if ($status === self::STATUS_APPROVED) {
                $this->executeTransfer();
            }
            return true;
        }

        return false;
    }

    /**
     * Process receiver approval (approve or reject)
     * @param int $status
     * @param string $receiverComment
     * @return bool
     */
    public function processReceiverApproval($status, $receiverComment = '')
    {
        if (!$this->canBeApprovedByReceiver()) {
            return false; // Can only process pending receiver approval
        }

        $this->status = $status;
        $this->admin_comment = $receiverComment; // Using admin_comment field for receiver comment
        $this->processed_by = Yii::$app->user->id;
        $this->processed_at = time();

        if ($this->save()) {
            // If receiver approved, execute the transfer
            if ($status === self::STATUS_RECEIVER_APPROVED) {
                $this->executeTransfer();
            }
            return true;
        }

        return false;
    }

    /**
     * Execute the fund transfer
     * @return bool
     */
    private function executeTransfer()
    {
        try {
            // Create debit entry for sender
            $debitResult = \app\models\Ledger::createDebit(
                $this->from_customer_id,
                $this->amount,
                $this->processed_by,
                \app\models\Ledger::TYPE_TRANSFER_OUT,
                $this->transfer_date
            );

            // Create credit entry for receiver
            $creditResult = \app\models\Ledger::createCredit(
                $this->to_customer_id,
                $this->amount,
                $this->processed_by,
                \app\models\Ledger::TYPE_TRANSFER_IN,
                $this->transfer_date
            );

            if ($debitResult && $creditResult) {
                Yii::info("Successfully executed fund transfer {$this->id}: Amount {$this->amount} from customer {$this->from_customer_id} to customer {$this->to_customer_id}");
                return true;
            } else {
                Yii::error("Failed to execute fund transfer {$this->id}: Ledger entries failed");
                return false;
            }
        } catch (\Exception $e) {
            Yii::error("Exception while executing fund transfer {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get customer's outgoing transfers
     * @param int $customerId
     * @param int $limit
     * @return static[]
     */
    public static function getCustomerOutgoingTransfers($customerId, $limit = 10)
    {
        return static::find()
            ->where(['from_customer_id' => $customerId])
            ->with(['toCustomer'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Get customer's incoming transfers
     * @param int $customerId
     * @param int $limit
     * @return static[]
     */
    public static function getCustomerIncomingTransfers($customerId, $limit = 10)
    {
        return static::find()
            ->where(['to_customer_id' => $customerId])
            ->with(['fromCustomer'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Get pending transfers for admin
     * @return static[]
     */
    public static function getPendingTransfers()
    {
        return static::find()
            ->where(['status' => self::STATUS_PENDING])
            ->with(['fromCustomer', 'toCustomer'])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
    }

    /**
     * Get transfers pending receiver approval
     * @return static[]
     */
    public static function getPendingReceiverApprovalTransfers()
    {
        return static::find()
            ->where(['status' => self::STATUS_PENDING_RECEIVER_APPROVAL])
            ->with(['fromCustomer', 'toCustomer'])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
    }

    /**
     * Get customer's transfers pending receiver approval
     * @param int $customerId
     * @return static[]
     */
    public static function getCustomerPendingReceiverApprovalTransfers($customerId)
    {
        return static::find()
            ->where(['to_customer_id' => $customerId, 'status' => self::STATUS_PENDING_RECEIVER_APPROVAL])
            ->with(['fromCustomer'])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
    }

    /**
     * Get all transfers for admin
     * @param int $limit
     * @return static[]
     */
    public static function getAllTransfers($limit = 50)
    {
        return static::find()
            ->with(['fromCustomer', 'toCustomer', 'processedBy'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Check if customer has sufficient balance for transfer
     * @param int $customerId
     * @param float $amount
     * @return bool
     */
    public static function hasSufficientBalance($customerId, $amount)
    {
        $customer = Customer::findOne($customerId);
        if (!$customer) {
            return false;
        }

        $balance = $customer->getLedgerBalance();
        return $balance >= $amount;
    }
}
