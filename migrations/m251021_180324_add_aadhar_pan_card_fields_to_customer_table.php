<?php

use yii\db\Migration;

class m251021_180324_add_aadhar_pan_card_fields_to_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add Aadhar and PAN card fields to customer table
        $this->addColumn('{{%customer}}', 'aadhar_number', $this->string(12)->null()->comment('Aadhar card number'));
        $this->addColumn('{{%customer}}', 'aadhar_card_image', $this->string(255)->null()->comment('Aadhar card image path'));
        $this->addColumn('{{%customer}}', 'pan_number', $this->string(10)->null()->comment('PAN card number'));
        $this->addColumn('{{%customer}}', 'pan_card_image', $this->string(255)->null()->comment('PAN card image path'));
        
        // Add indexes for better performance
        $this->createIndex('idx-customer-aadhar-number', '{{%customer}}', 'aadhar_number');
        $this->createIndex('idx-customer-pan-number', '{{%customer}}', 'pan_number');
        
        // Add unique constraints
        $this->createIndex('idx-customer-aadhar-unique', '{{%customer}}', 'aadhar_number', true);
        $this->createIndex('idx-customer-pan-unique', '{{%customer}}', 'pan_number', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remove indexes
        $this->dropIndex('idx-customer-aadhar-unique', '{{%customer}}');
        $this->dropIndex('idx-customer-pan-unique', '{{%customer}}');
        $this->dropIndex('idx-customer-aadhar-number', '{{%customer}}');
        $this->dropIndex('idx-customer-pan-number', '{{%customer}}');
        
        // Remove Aadhar and PAN card columns
        $this->dropColumn('{{%customer}}', 'aadhar_number');
        $this->dropColumn('{{%customer}}', 'aadhar_card_image');
        $this->dropColumn('{{%customer}}', 'pan_number');
        $this->dropColumn('{{%customer}}', 'pan_card_image');
    }
}