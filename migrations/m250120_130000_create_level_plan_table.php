<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%level_plan}}`.
 */
class m250120_130000_create_level_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%level_plan}}', [
            'id' => $this->primaryKey(),
            'level' => $this->integer()->notNull()->comment('Level number'),
            'rate' => $this->decimal(5,2)->notNull()->comment('Rate percentage'),
            'no_of_directs' => $this->integer()->notNull()->defaultValue(0)->comment('Number of direct referrals required'),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('Status: 1=Active, 0=Inactive'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add unique index on level
        $this->createIndex(
            'idx-level_plan-level',
            '{{%level_plan}}',
            'level',
            true
        );

        // Add index on status
        $this->createIndex(
            'idx-level_plan-status',
            '{{%level_plan}}',
            'status'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%level_plan}}');
    }
}