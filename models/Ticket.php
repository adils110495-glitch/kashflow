<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "ticket".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $subject
 * @property string $description
 * @property int $status
 * @property string $priority
 * @property int|null $action_by
 * @property string|null $action_time
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Customer $customer
 */
class Ticket extends ActiveRecord
{
    // Status constants (now using tinyint)
    const STATUS_OPEN = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_RESOLVED = 3;
    const STATUS_CLOSED = 4;

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket';
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
            [['customer_id', 'subject', 'description'], 'required'],
            [['customer_id', 'action_by'], 'integer'],
            [['description'], 'string'],
            [['subject'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => [self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_RESOLVED, self::STATUS_CLOSED]],
            [['priority'], 'in', 'range' => [self::PRIORITY_LOW, self::PRIORITY_MEDIUM, self::PRIORITY_HIGH, self::PRIORITY_URGENT]],
            [['action_time'], 'safe'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Ticket ID',
            'customer_id' => 'Customer',
            'subject' => 'Subject',
            'description' => 'Description',
            'status' => 'Status',
            'priority' => 'Priority',
            'action_by' => 'Action By',
            'action_time' => 'Action Time',
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
     * Gets query for [[TicketChats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicketChats()
    {
        return $this->hasMany(TicketChat::class, ['ticket_id' => 'id']);
    }

    /**
     * Get status label with Bootstrap styling
     * @return string
     */
    public function getStatusLabel()
    {
        $statusLabels = [
            self::STATUS_OPEN => '<span class="badge badge-warning">Open</span>',
            self::STATUS_IN_PROGRESS => '<span class="badge badge-info">In Progress</span>',
            self::STATUS_RESOLVED => '<span class="badge badge-success">Resolved</span>',
            self::STATUS_CLOSED => '<span class="badge badge-secondary">Closed</span>',
        ];
        
        return $statusLabels[$this->status] ?? '<span class="badge badge-light">Unknown</span>';
    }

    /**
     * Check if customer can communicate (ticket is not closed)
     * @return bool
     */
    public function canCustomerCommunicate()
    {
        return $this->status !== self::STATUS_CLOSED;
    }

    /**
     * Get priority label with Bootstrap styling
     * @return string
     */
    public function getPriorityLabel()
    {
        $priorityLabels = [
            self::PRIORITY_LOW => '<span class="badge badge-light">Low</span>',
            self::PRIORITY_MEDIUM => '<span class="badge badge-primary">Medium</span>',
            self::PRIORITY_HIGH => '<span class="badge badge-warning">High</span>',
            self::PRIORITY_URGENT => '<span class="badge badge-danger">Urgent</span>',
        ];
        
        return $priorityLabels[$this->priority] ?? '<span class="badge badge-light">' . ucfirst($this->priority) . '</span>';
    }

    /**
     * Get tickets for a specific customer
     * @param int $customerId
     * @return \yii\db\ActiveQuery
     */
    public static function getCustomerTickets($customerId)
    {
        return self::find()
            ->where(['customer_id' => $customerId])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * Create a new ticket
     * @param int $customerId
     * @param string $subject
     * @param string $description
     * @param string $priority
     * @return array
     */
    public static function createTicket($customerId, $subject, $description, $priority = self::PRIORITY_MEDIUM)
    {
        $ticket = new self();
        $ticket->customer_id = $customerId;
        $ticket->subject = $subject;
        $ticket->description = $description;
        $ticket->priority = $priority;
        $ticket->status = self::STATUS_OPEN;

        if ($ticket->save()) {
            // Log activity
            $customer = Customer::findOne($customerId);
            if ($customer) {
                $customer->logActivity('support_ticket', "Support ticket created: $subject", [
                    'ticket_id' => $ticket->id,
                    'subject' => $subject,
                    'priority' => $priority
                ]);
            }

            return [
                'success' => true,
                'ticket' => $ticket,
                'message' => 'Ticket created successfully. We will respond to you soon.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to create ticket: ' . implode(', ', $ticket->getFirstErrors())
        ];
    }

    /**
     * Get ticket statistics for customer
     * @param int $customerId
     * @return array
     */
    public static function getCustomerTicketStats($customerId)
    {
        $total = self::find()->where(['customer_id' => $customerId])->count();
        $open = self::find()->where(['customer_id' => $customerId, 'status' => self::STATUS_OPEN])->count();
        $inProgress = self::find()->where(['customer_id' => $customerId, 'status' => self::STATUS_IN_PROGRESS])->count();
        $resolved = self::find()->where(['customer_id' => $customerId, 'status' => self::STATUS_RESOLVED])->count();
        $closed = self::find()->where(['customer_id' => $customerId, 'status' => self::STATUS_CLOSED])->count();

        return [
            'total' => $total,
            'open' => $open,
            'in_progress' => $inProgress,
            'resolved' => $resolved,
            'closed' => $closed,
        ];
    }
}
