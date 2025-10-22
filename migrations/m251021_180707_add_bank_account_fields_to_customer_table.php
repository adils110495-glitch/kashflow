<?php

use yii\db\Migration;

class m251021_180707_add_bank_account_fields_to_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add bank account fields to customer table
        $this->addColumn('{{%customer}}', 'bank_account_number', $this->string(20)->null()->comment('Bank account number'));
        $this->addColumn('{{%customer}}', 'bank_account_holder_name', $this->string(255)->null()->comment('Bank account holder name'));
        $this->addColumn('{{%customer}}', 'bank_name', $this->string(255)->null()->comment('Bank name'));
        $this->addColumn('{{%customer}}', 'bank_ifsc_code', $this->string(11)->null()->comment('Bank IFSC code'));
        $this->addColumn('{{%customer}}', 'bank_branch_name', $this->string(255)->null()->comment('Bank branch name'));
        $this->addColumn('{{%customer}}', 'bank_account_type', $this->string(20)->null()->comment('Bank account type (Savings/Current)'));
        
        // Add indexes for better performance
        $this->createIndex('idx-customer-bank-account-number', '{{%customer}}', 'bank_account_number');
        $this->createIndex('idx-customer-bank-ifsc', '{{%customer}}', 'bank_ifsc_code');
        
        // Add unique constraint for bank account number
        $this->createIndex('idx-customer-bank-account-unique', '{{%customer}}', 'bank_account_number', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remove indexes
        $this->dropIndex('idx-customer-bank-account-unique', '{{%customer}}');
        $this->dropIndex('idx-customer-bank-account-number', '{{%customer}}');
        $this->dropIndex('idx-customer-bank-ifsc', '{{%customer}}');
        
        // Remove bank account columns
        $this->dropColumn('{{%customer}}', 'bank_account_number');
        $this->dropColumn('{{%customer}}', 'bank_account_holder_name');
        $this->dropColumn('{{%customer}}', 'bank_name');
        $this->dropColumn('{{%customer}}', 'bank_ifsc_code');
        $this->dropColumn('{{%customer}}', 'bank_branch_name');
        $this->dropColumn('{{%customer}}', 'bank_account_type');
    }
}