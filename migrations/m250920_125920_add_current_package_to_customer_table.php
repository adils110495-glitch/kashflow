<?php

use yii\db\Migration;

class m250920_125920_add_current_package_to_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add current_package column
        $this->addColumn('{{%customer}}', 'current_package', $this->integer()->notNull()->defaultValue(1)->comment('Current package ID'));
        
        // Add index for better performance
        $this->createIndex('idx-customer-current_package', '{{%customer}}', 'current_package');
        
        // Add foreign key constraint
        $this->addForeignKey(
            'fk-customer-current_package',
            '{{%customer}}',
            'current_package',
            '{{%package}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign key constraint
        $this->dropForeignKey('fk-customer-current_package', '{{%customer}}');
        
        // Drop index
        $this->dropIndex('idx-customer-current_package', '{{%customer}}');
        
        // Drop column
        $this->dropColumn('{{%customer}}', 'current_package');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250920_125920_add_current_package_to_customer_table cannot be reverted.\n";

        return false;
    }
    */
}
