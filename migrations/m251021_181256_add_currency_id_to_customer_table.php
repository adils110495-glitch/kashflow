<?php

use yii\db\Migration;

class m251021_181256_add_currency_id_to_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add currency_id column to customer table
        $this->addColumn('{{%customer}}', 'currency_id', $this->integer()->null()->comment('Preferred currency ID'));
        
        // Add index for better performance
        $this->createIndex('idx-customer-currency-id', '{{%customer}}', 'currency_id');
        
        // Add foreign key constraint
        $this->addForeignKey(
            'fk-customer-currency-id',
            '{{%customer}}',
            'currency_id',
            '{{%currency}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        
        // Set default currency (USD) for existing customers
        $defaultCurrency = $this->db->createCommand('SELECT id FROM {{%currency}} WHERE code = "USD" AND is_active = 1')->queryScalar();
        if ($defaultCurrency) {
            $this->update('{{%customer}}', ['currency_id' => $defaultCurrency]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remove foreign key constraint
        $this->dropForeignKey('fk-customer-currency-id', '{{%customer}}');
        
        // Remove index
        $this->dropIndex('idx-customer-currency-id', '{{%customer}}');
        
        // Remove currency_id column
        $this->dropColumn('{{%customer}}', 'currency_id');
    }
}