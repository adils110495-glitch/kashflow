<?php

use yii\db\Migration;

class m251021_181110_create_currency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create currency table
        $this->createTable('{{%currency}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(3)->notNull()->comment('Currency code (e.g., USD, EUR, INR)'),
            'name' => $this->string(100)->notNull()->comment('Currency name'),
            'symbol' => $this->string(10)->notNull()->comment('Currency symbol (e.g., $, €, ₹)'),
            'exchange_rate' => $this->decimal(10, 4)->notNull()->defaultValue(1.0000)->comment('Exchange rate to base currency'),
            'is_base' => $this->boolean()->notNull()->defaultValue(false)->comment('Is this the base currency'),
            'is_active' => $this->boolean()->notNull()->defaultValue(true)->comment('Is currency active'),
            'decimal_places' => $this->integer()->notNull()->defaultValue(2)->comment('Number of decimal places'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add indexes
        $this->createIndex('idx-currency-code', '{{%currency}}', 'code', true);
        $this->createIndex('idx-currency-active', '{{%currency}}', 'is_active');
        $this->createIndex('idx-currency-base', '{{%currency}}', 'is_base');

        // Insert default currencies
        $this->insert('{{%currency}}', [
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
            'is_base' => true,
            'is_active' => true,
            'decimal_places' => 2,
        ]);

        $this->insert('{{%currency}}', [
            'code' => 'INR',
            'name' => 'Indian Rupee',
            'symbol' => '₹',
            'exchange_rate' => 83.0000,
            'is_base' => false,
            'is_active' => true,
            'decimal_places' => 2,
        ]);

        $this->insert('{{%currency}}', [
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'exchange_rate' => 0.9200,
            'is_base' => false,
            'is_active' => true,
            'decimal_places' => 2,
        ]);

        $this->insert('{{%currency}}', [
            'code' => 'GBP',
            'name' => 'British Pound',
            'symbol' => '£',
            'exchange_rate' => 0.7900,
            'is_base' => false,
            'is_active' => true,
            'decimal_places' => 2,
        ]);

        $this->insert('{{%currency}}', [
            'code' => 'BTC',
            'name' => 'Bitcoin',
            'symbol' => '₿',
            'exchange_rate' => 0.000025,
            'is_base' => false,
            'is_active' => true,
            'decimal_places' => 8,
        ]);

        $this->insert('{{%currency}}', [
            'code' => 'ETH',
            'name' => 'Ethereum',
            'symbol' => 'Ξ',
            'exchange_rate' => 0.0004,
            'is_base' => false,
            'is_active' => true,
            'decimal_places' => 6,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop indexes
        $this->dropIndex('idx-currency-base', '{{%currency}}');
        $this->dropIndex('idx-currency-active', '{{%currency}}');
        $this->dropIndex('idx-currency-code', '{{%currency}}');
        
        // Drop table
        $this->dropTable('{{%currency}}');
    }
}