<?php

use yii\db\Migration;

class m250920_104513_add_kashflow_company_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('company', [
            'name' => 'KashFlow',
            'short_name' => 'KF',
            'email' => 'info@kashflow.online',
            'phone_no' => '123234234',
            'logo' => null,
            'website_link' => 'https://www.kashflow.online',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('company', ['name' => 'KashFlow']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250920_104513_add_kashflow_company_data cannot be reverted.\n";

        return false;
    }
    */
}
