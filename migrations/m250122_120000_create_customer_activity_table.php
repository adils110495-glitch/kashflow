<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customer_activity}}`.
 */
class m250122_120000_create_customer_activity_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%customer_activity}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'activity_type' => $this->string(50)->notNull()->comment('Type of activity (login, logout, profile_update, package_change, etc.)'),
            'activity_description' => $this->text()->comment('Detailed description of the activity'),
            'ip_address' => $this->string(45)->comment('IP address of the customer'),
            'user_agent' => $this->text()->comment('Browser/device information'),
            'metadata' => $this->json()->comment('Additional activity metadata in JSON format'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add foreign key constraint
        $this->addForeignKey(
            'fk-customer_activity-customer_id',
            '{{%customer_activity}}',
            'customer_id',
            '{{%customer}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Add indexes for better performance
        $this->createIndex(
            'idx-customer_activity-customer_id',
            '{{%customer_activity}}',
            'customer_id'
        );

        $this->createIndex(
            'idx-customer_activity-activity_type',
            '{{%customer_activity}}',
            'activity_type'
        );

        $this->createIndex(
            'idx-customer_activity-created_at',
            '{{%customer_activity}}',
            'created_at'
        );

        // Composite index for common queries
        $this->createIndex(
            'idx-customer_activity-customer_type_date',
            '{{%customer_activity}}',
            ['customer_id', 'activity_type', 'created_at']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign key constraint
        $this->dropForeignKey(
            'fk-customer_activity-customer_id',
            '{{%customer_activity}}'
        );

        // Drop indexes
        $this->dropIndex(
            'idx-customer_activity-customer_id',
            '{{%customer_activity}}'
        );

        $this->dropIndex(
            'idx-customer_activity-activity_type',
            '{{%customer_activity}}'
        );

        $this->dropIndex(
            'idx-customer_activity-created_at',
            '{{%customer_activity}}'
        );

        $this->dropIndex(
            'idx-customer_activity-customer_type_date',
            '{{%customer_activity}}'
        );

        // Drop table
        $this->dropTable('{{%customer_activity}}');
    }
}