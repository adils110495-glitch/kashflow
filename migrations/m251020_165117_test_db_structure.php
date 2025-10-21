<?php

use yii\db\Migration;

class m251020_165117_test_db_structure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251020_165117_test_db_structure cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251020_165117_test_db_structure cannot be reverted.\n";

        return false;
    }
    */
}
