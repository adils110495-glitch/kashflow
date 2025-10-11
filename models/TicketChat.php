<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "ticket_chat".
 *
 * @property int $id
 * @property int $ticket_id
 * @property string $sender_type
 * @property int $sender_id
 * @property string $message
 * @property bool $is_read
 * @property string $created_at
 *
 * @property Ticket $ticket
 */
class TicketChat extends ActiveRecord
{
    // Sender type constants
    const SENDER_CUSTOMER = 'customer';
    const SENDER_ADMIN = 'admin';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket_chat';
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
                'updatedAtAttribute' => null,
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
            [['ticket_id', 'sender_type', 'sender_id', 'message'], 'required'],
            [['ticket_id', 'sender_id'], 'integer'],
            [['message'], 'string'],
            [['is_read'], 'boolean'],
            [['sender_type'], 'string', 'max' => 20],
            [['sender_type'], 'in', 'range' => [self::SENDER_CUSTOMER, self::SENDER_ADMIN]],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::class, 'targetAttribute' => ['ticket_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket_id' => 'Ticket ID',
            'sender_type' => 'Sender Type',
            'sender_id' => 'Sender ID',
            'message' => 'Message',
            'is_read' => 'Is Read',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Ticket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::class, ['id' => 'ticket_id']);
    }

    /**
     * Get chat messages for a specific ticket
     * @param int $ticketId
     * @return \yii\db\ActiveQuery
     */
    public static function getTicketChats($ticketId)
    {
        return self::find()
            ->where(['ticket_id' => $ticketId])
            ->orderBy(['created_at' => SORT_ASC]);
    }

    /**
     * Add a chat message to a ticket
     * @param int $ticketId
     * @param string $senderType
     * @param int $senderId
     * @param string $message
     * @return array
     */
    public static function addMessage($ticketId, $senderType, $senderId, $message)
    {
        $chat = new self();
        $chat->ticket_id = $ticketId;
        $chat->sender_type = $senderType;
        $chat->sender_id = $senderId;
        $chat->message = $message;
        $chat->is_read = false;

        if ($chat->save()) {
            return [
                'success' => true,
                'chat' => $chat,
                'message' => 'Message sent successfully.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to send message: ' . implode(', ', $chat->getFirstErrors())
        ];
    }

    /**
     * Mark messages as read for a specific ticket and sender type
     * @param int $ticketId
     * @param string $senderType
     * @return bool
     */
    public static function markAsRead($ticketId, $senderType)
    {
        return self::updateAll(
            ['is_read' => true],
            ['ticket_id' => $ticketId, 'sender_type' => $senderType, 'is_read' => false]
        ) > 0;
    }

    /**
     * Get unread message count for a ticket
     * @param int $ticketId
     * @param string $senderType
     * @return int
     */
    public static function getUnreadCount($ticketId, $senderType)
    {
        return self::find()
            ->where(['ticket_id' => $ticketId, 'sender_type' => $senderType, 'is_read' => false])
            ->count();
    }

    /**
     * Get sender name based on sender type and ID
     * @return string
     */
    public function getSenderName()
    {
        if ($this->sender_type === self::SENDER_CUSTOMER) {
            $customer = Customer::findOne($this->sender_id);
            return $customer ? $customer->name : 'Customer';
        } else {
            $user = \dektrium\user\models\User::findOne($this->sender_id);
            return $user ? $user->username : 'Admin';
        }
    }

    /**
     * Get formatted time
     * @return string
     */
    public function getFormattedTime()
    {
        return date('M d, Y \a\t h:i A', strtotime($this->created_at));
    }
}
