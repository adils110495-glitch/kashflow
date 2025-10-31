<?php

use yii\db\Migration;

class m251029_120000_add_balance_to_customer extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%customer}}', 'balance', $this->decimal(15, 2)->notNull()->defaultValue(0.00));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%customer}}', 'balance');
    }
}


