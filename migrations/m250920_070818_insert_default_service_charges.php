<?php

use yii\db\Migration;

class m250920_070818_insert_default_service_charges extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%service_charges}}', [
            'name' => 'Service Charge',
            'rate' => 20.00,
            'status' => 1,
        ]);

        $this->insert('{{%service_charges}}', [
            'name' => 'Withdrawal Charge',
            'rate' => 5.00,
            'status' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%service_charges}}', ['name' => 'Service Charge']);
        $this->delete('{{%service_charges}}', ['name' => 'Withdrawal Charge']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250920_070818_insert_default_service_charges cannot be reverted.\n";

        return false;
    }
    */
}
