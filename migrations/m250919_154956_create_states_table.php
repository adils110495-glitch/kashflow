<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%states}}`.
 */
class m250919_154956_create_states_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%states}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->comment('State name'),
            'country_id' => $this->integer()->notNull()->comment('Foreign key to country table'),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('Status: 1=Active, 0=Inactive'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add indexes
        $this->createIndex('idx-states-name', '{{%states}}', 'name');
        $this->createIndex('idx-states-country_id', '{{%states}}', 'country_id');
        $this->createIndex('idx-states-status', '{{%states}}', 'status');

        // Add foreign key constraint
        $this->addForeignKey(
            'fk-states-country_id',
            '{{%states}}',
            'country_id',
            '{{%country}}',
            'id',
            'CASCADE'
        );

        // Batch insert states data for all countries
        $this->batchInsert('{{%states}}', 
            ['name', 'country_id', 'status'], 
            [
                // United States
                ['Alabama', 1, 1],
                ['Alaska', 1, 1],
                ['Arizona', 1, 1],
                ['Arkansas', 1, 1],
                ['California', 1, 1],
                ['Colorado', 1, 1],
                ['Connecticut', 1, 1],
                ['Delaware', 1, 1],
                ['Florida', 1, 1],
                ['Georgia', 1, 1],
                ['Hawaii', 1, 1],
                ['Idaho', 1, 1],
                ['Illinois', 1, 1],
                ['Indiana', 1, 1],
                ['Iowa', 1, 1],
                ['Kansas', 1, 1],
                ['Kentucky', 1, 1],
                ['Louisiana', 1, 1],
                ['Maine', 1, 1],
                ['Maryland', 1, 1],
                ['Massachusetts', 1, 1],
                ['Michigan', 1, 1],
                ['Minnesota', 1, 1],
                ['Mississippi', 1, 1],
                ['Missouri', 1, 1],
                ['Montana', 1, 1],
                ['Nebraska', 1, 1],
                ['Nevada', 1, 1],
                ['New Hampshire', 1, 1],
                ['New Jersey', 1, 1],
                ['New Mexico', 1, 1],
                ['New York', 1, 1],
                ['North Carolina', 1, 1],
                ['North Dakota', 1, 1],
                ['Ohio', 1, 1],
                ['Oklahoma', 1, 1],
                ['Oregon', 1, 1],
                ['Pennsylvania', 1, 1],
                ['Rhode Island', 1, 1],
                ['South Carolina', 1, 1],
                ['South Dakota', 1, 1],
                ['Tennessee', 1, 1],
                ['Texas', 1, 1],
                ['Utah', 1, 1],
                ['Vermont', 1, 1],
                ['Virginia', 1, 1],
                ['Washington', 1, 1],
                ['West Virginia', 1, 1],
                ['Wisconsin', 1, 1],
                ['Wyoming', 1, 1],
                
                // Canada
                ['Alberta', 2, 1],
                ['British Columbia', 2, 1],
                ['Manitoba', 2, 1],
                ['New Brunswick', 2, 1],
                ['Newfoundland and Labrador', 2, 1],
                ['Northwest Territories', 2, 1],
                ['Nova Scotia', 2, 1],
                ['Nunavut', 2, 1],
                ['Ontario', 2, 1],
                ['Prince Edward Island', 2, 1],
                ['Quebec', 2, 1],
                ['Saskatchewan', 2, 1],
                ['Yukon', 2, 1],
                
                // United Kingdom
                ['England', 3, 1],
                ['Scotland', 3, 1],
                ['Wales', 3, 1],
                ['Northern Ireland', 3, 1],
                
                // Australia
                ['New South Wales', 4, 1],
                ['Victoria', 4, 1],
                ['Queensland', 4, 1],
                ['Western Australia', 4, 1],
                ['South Australia', 4, 1],
                ['Tasmania', 4, 1],
                ['Australian Capital Territory', 4, 1],
                ['Northern Territory', 4, 1],
                
                // Germany
                ['Baden-Württemberg', 5, 1],
                ['Bavaria', 5, 1],
                ['Berlin', 5, 1],
                ['Brandenburg', 5, 1],
                ['Bremen', 5, 1],
                ['Hamburg', 5, 1],
                ['Hesse', 5, 1],
                ['Lower Saxony', 5, 1],
                ['Mecklenburg-Vorpommern', 5, 1],
                ['North Rhine-Westphalia', 5, 1],
                ['Rhineland-Palatinate', 5, 1],
                ['Saarland', 5, 1],
                ['Saxony', 5, 1],
                ['Saxony-Anhalt', 5, 1],
                ['Schleswig-Holstein', 5, 1],
                ['Thuringia', 5, 1],
                
                // India
                ['Andhra Pradesh', 6, 1],
                ['Arunachal Pradesh', 6, 1],
                ['Assam', 6, 1],
                ['Bihar', 6, 1],
                ['Chhattisgarh', 6, 1],
                ['Goa', 6, 1],
                ['Gujarat', 6, 1],
                ['Haryana', 6, 1],
                ['Himachal Pradesh', 6, 1],
                ['Jharkhand', 6, 1],
                ['Karnataka', 6, 1],
                ['Kerala', 6, 1],
                ['Madhya Pradesh', 6, 1],
                ['Maharashtra', 6, 1],
                ['Manipur', 6, 1],
                ['Meghalaya', 6, 1],
                ['Mizoram', 6, 1],
                ['Nagaland', 6, 1],
                ['Odisha', 6, 1],
                ['Punjab', 6, 1],
                ['Rajasthan', 6, 1],
                ['Sikkim', 6, 1],
                ['Tamil Nadu', 6, 1],
                ['Telangana', 6, 1],
                ['Tripura', 6, 1],
                ['Uttar Pradesh', 6, 1],
                ['Uttarakhand', 6, 1],
                ['West Bengal', 6, 1],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign key constraint
        $this->dropForeignKey('fk-states-country_id', '{{%states}}');
        
        // Drop table
        $this->dropTable('{{%states}}');
    }
}
