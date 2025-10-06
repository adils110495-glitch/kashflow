<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%income}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%customer}}`
 */
class m250123_120000_create_income_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%income}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'date' => $this->date()->notNull(),
            'type' => $this->tinyInteger()->notNull(),
            'level' => $this->integer()->null(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `customer_id`
        $this->createIndex(
            '{{%idx-income-customer_id}}',
            '{{%income}}',
            'customer_id'
        );

        // add foreign key for table `{{%customer}}`
        $this->addForeignKey(
            '{{%fk-income-customer_id}}',
            '{{%income}}',
            'customer_id',
            '{{%customer}}',
            'id',
            'CASCADE'
        );

        // creates index for column `type`
        $this->createIndex(
            '{{%idx-income-type}}',
            '{{%income}}',
            'type'
        );

        // creates index for column `date`
        $this->createIndex(
            '{{%idx-income-date}}',
            '{{%income}}',
            'date'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%customer}}`
        $this->dropForeignKey(
            '{{%fk-income-customer_id}}',
            '{{%income}}'
        );

        // drops index for column `customer_id`
        $this->dropIndex(
            '{{%idx-income-customer_id}}',
            '{{%income}}'
        );

        // drops index for column `type`
        $this->dropIndex(
            '{{%idx-income-type}}',
            '{{%income}}'
        );

        // drops index for column `date`
        $this->dropIndex(
            '{{%idx-income-date}}',
            '{{%income}}'
        );

        $this->dropTable('{{%income}}');
    }
}