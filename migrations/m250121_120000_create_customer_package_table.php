<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customer_package}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%customer}}`
 * - `{{%package}}`
 */
class m250121_120000_create_customer_package_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%customer_package}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'package_id' => $this->integer()->notNull(),
            'date' => $this->date()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `customer_id`
        $this->createIndex(
            '{{%idx-customer_package-customer_id}}',
            '{{%customer_package}}',
            'customer_id'
        );

        // add foreign key for table `{{%customer}}`
        $this->addForeignKey(
            '{{%fk-customer_package-customer_id}}',
            '{{%customer_package}}',
            'customer_id',
            '{{%customer}}',
            'id',
            'CASCADE'
        );

        // creates index for column `package_id`
        $this->createIndex(
            '{{%idx-customer_package-package_id}}',
            '{{%customer_package}}',
            'package_id'
        );

        // add foreign key for table `{{%package}}`
        $this->addForeignKey(
            '{{%fk-customer_package-package_id}}',
            '{{%customer_package}}',
            'package_id',
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
        // drops foreign key for table `{{%customer}}`
        $this->dropForeignKey(
            '{{%fk-customer_package-customer_id}}',
            '{{%customer_package}}'
        );

        // drops index for column `customer_id`
        $this->dropIndex(
            '{{%idx-customer_package-customer_id}}',
            '{{%customer_package}}'
        );

        // drops foreign key for table `{{%package}}`
        $this->dropForeignKey(
            '{{%fk-customer_package-package_id}}',
            '{{%customer_package}}'
        );

        // drops index for column `package_id`
        $this->dropIndex(
            '{{%idx-customer_package-package_id}}',
            '{{%customer_package}}'
        );

        $this->dropTable('{{%customer_package}}');
    }
}