<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customer}}`.
 */
class m250019_161019_create_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%customer}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull()->unique(),
            'mobile_no' => $this->string(20)->notNull()->unique(),
            'referral_code' => $this->string(50),
            'country_id' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->defaultValue(1)->comment('1=Active, 0=Inactive'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Add foreign key constraint to country table
        $this->addForeignKey(
            'fk-customer-country_id',
            '{{%customer}}',
            'country_id',
            '{{%country}}',
            'id',
            'CASCADE'
        );

        // Add foreign key constraint to user table
        $this->addForeignKey(
            'fk-customer-user_id',
            '{{%customer}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // Create indexes for better performance
        $this->createIndex('idx-customer-email', '{{%customer}}', 'email');
        $this->createIndex('idx-customer-mobile_no', '{{%customer}}', 'mobile_no');
        $this->createIndex('idx-customer-status', '{{%customer}}', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys first
        $this->dropForeignKey('fk-customer-country_id', '{{%customer}}');
        $this->dropForeignKey('fk-customer-user_id', '{{%customer}}');
        
        // Drop indexes
        $this->dropIndex('idx-customer-email', '{{%customer}}');
        $this->dropIndex('idx-customer-mobile_no', '{{%customer}}');
        $this->dropIndex('idx-customer-status', '{{%customer}}');
        
        // Drop table
        $this->dropTable('{{%customer}}');
    }
}
