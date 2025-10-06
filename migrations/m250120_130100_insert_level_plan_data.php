<?php

use yii\db\Migration;

/**
 * Handles inserting initial data into table `{{%level_plan}}`.
 */
class m250120_130100_insert_level_plan_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('{{%level_plan}}', ['level', 'rate', 'no_of_directs', 'status'], [
            [1, 20.00, 0, 1],
            [2, 15.00, 3, 1],
            [3, 10.00, 0, 1],
            [4, 8.00, 0, 1],
            [5, 5.00, 0, 1],
            [6, 4.00, 0, 1],
            [7, 3.00, 0, 1],
            [8, 2.00, 0, 1],
            [9, 1.00, 0, 1],
            [10, 1.00, 0, 1],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%level_plan}}', ['level' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
    }
}