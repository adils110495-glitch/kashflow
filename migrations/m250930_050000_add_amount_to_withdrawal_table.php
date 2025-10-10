<?php

use yii\db\Migration;

/**
 * Handles adding amount column to table `{{%withdrawal}}`.
 */
class m250930_050000_add_amount_to_withdrawal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%withdrawal}}', 'amount', $this->decimal(15, 2)->notNull()->defaultValue(0)->comment('Withdrawal Amount'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%withdrawal}}', 'amount');
    }
}
