<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%reward_plan}}`.
 */
class m251029_130000_create_reward_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%reward_plan}}', [
            'id' => $this->primaryKey(),
            'business_amount' => $this->decimal(15, 2)->notNull()->comment('Required business amount'),
            'reward' => $this->string(255)->notNull()->comment('Reward name/description'),
            'reward_amount' => $this->decimal(15, 2)->notNull()->defaultValue(0)->comment('Reward amount'),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('1=Active, 0=Inactive'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-reward_plan-status', '{{%reward_plan}}', 'status');
        $this->createIndex('idx-reward_plan-business_amount', '{{%reward_plan}}', 'business_amount');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%reward_plan}}');
    }
}


