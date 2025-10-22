<?php

use yii\db\Migration;

class m251021_175110_add_withdrawal_method_to_withdrawal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add withdrawal_method column to withdrawal table
        $this->addColumn('{{%withdrawal}}', 'withdrawal_method', $this->string(20)->notNull()->defaultValue('UPI')->comment('Withdrawal method: UPI, Cash, Crypto'));
        
        // Add index for better performance
        $this->createIndex('idx-withdrawal-method', '{{%withdrawal}}', 'withdrawal_method');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remove index
        $this->dropIndex('idx-withdrawal-method', '{{%withdrawal}}');
        
        // Remove withdrawal_method column
        $this->dropColumn('{{%withdrawal}}', 'withdrawal_method');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251021_175110_add_withdrawal_method_to_withdrawal_table cannot be reverted.\n";

        return false;
    }
    */
}
