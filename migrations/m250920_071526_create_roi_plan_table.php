<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%roi_plan}}`.
 */
class m250920_071526_create_roi_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%roi_plan}}', [
            'id' => $this->primaryKey(),
            'rate' => $this->decimal(5, 2)->notNull()->comment('ROI rate percentage'),
            'frequency' => $this->tinyInteger()->notNull()->comment('1=daily, 2=weekly, 3=monthly, 4=yearly'),
            'tenure' => $this->tinyInteger()->notNull()->comment('Number of times (e.g., 2=twice, 3=thrice)'),
            'release_date' => $this->date()->notNull()->comment('Release date of the plan'),
            'status' => $this->integer()->defaultValue(1)->comment('1=active, 0=inactive'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%roi_plan}}');
    }
}
