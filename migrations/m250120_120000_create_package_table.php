<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%package}}`.
 */
class m250120_120000_create_package_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%package}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->comment('Package name'),
            'amount' => $this->decimal(10, 2)->notNull()->comment('Package amount'),
            'fee' => $this->decimal(10, 2)->notNull()->comment('Package fee'),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('Status: 1=Active, 0=Inactive'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add indexes
        $this->createIndex('idx-package-name', '{{%package}}', 'name');
        $this->createIndex('idx-package-status', '{{%package}}', 'status');

        // Insert initial package data
        $this->batchInsert('{{%package}}', 
            ['name', 'amount', 'fee', 'status'], 
            [
                ['Free', 0.00, 0.00, 1],
                ['Starter', 5000.00, 1000.00, 1],
                ['Premium', 10000.00, 2000.00, 2],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%package}}');
    }
}