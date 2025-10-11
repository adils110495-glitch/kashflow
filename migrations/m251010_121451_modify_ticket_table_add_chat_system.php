<?php

use yii\db\Migration;

/**
 * Handles modifying the ticket table and adding chat system.
 */
class m251010_121451_modify_ticket_table_add_chat_system extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Remove admin_response field
        $this->dropColumn('{{%ticket}}', 'admin_response');
        
        // Change status from string to tinyint
        $this->alterColumn('{{%ticket}}', 'status', $this->tinyInteger()->notNull()->defaultValue(1));
        
        // Add action_by and action_time fields
        $this->addColumn('{{%ticket}}', 'action_by', $this->integer()->null());
        $this->addColumn('{{%ticket}}', 'action_time', $this->timestamp()->null());
        
        // Create ticket_chat table for conversation system
        $this->createTable('{{%ticket_chat}}', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer()->notNull(),
            'sender_type' => $this->string(20)->notNull(), // 'customer' or 'admin'
            'sender_id' => $this->integer()->notNull(),
            'message' => $this->text()->notNull(),
            'is_read' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Add foreign key constraints for ticket_chat
        $this->addForeignKey(
            'fk-ticket_chat-ticket_id',
            '{{%ticket_chat}}',
            'ticket_id',
            '{{%ticket}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Add indexes for better performance
        $this->createIndex(
            'idx-ticket_chat-ticket_id',
            '{{%ticket_chat}}',
            'ticket_id'
        );

        $this->createIndex(
            'idx-ticket_chat-sender_type',
            '{{%ticket_chat}}',
            'sender_type'
        );

        $this->createIndex(
            'idx-ticket_chat-created_at',
            '{{%ticket_chat}}',
            'created_at'
        );

        // Update existing tickets to set default status
        $this->update('{{%ticket}}', ['status' => 1], ['status' => 'open']);
        $this->update('{{%ticket}}', ['status' => 2], ['status' => 'in_progress']);
        $this->update('{{%ticket}}', ['status' => 3], ['status' => 'resolved']);
        $this->update('{{%ticket}}', ['status' => 4], ['status' => 'closed']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop ticket_chat table
        $this->dropForeignKey(
            'fk-ticket_chat-ticket_id',
            '{{%ticket_chat}}'
        );

        $this->dropIndex(
            'idx-ticket_chat-ticket_id',
            '{{%ticket_chat}}'
        );

        $this->dropIndex(
            'idx-ticket_chat-sender_type',
            '{{%ticket_chat}}'
        );

        $this->dropIndex(
            'idx-ticket_chat-created_at',
            '{{%ticket_chat}}'
        );

        $this->dropTable('{{%ticket_chat}}');

        // Remove added columns
        $this->dropColumn('{{%ticket}}', 'action_by');
        $this->dropColumn('{{%ticket}}', 'action_time');
        
        // Revert status column to string
        $this->alterColumn('{{%ticket}}', 'status', $this->string(20)->notNull()->defaultValue('open'));
        
        // Add back admin_response field
        $this->addColumn('{{%ticket}}', 'admin_response', $this->text()->null());
    }
}
