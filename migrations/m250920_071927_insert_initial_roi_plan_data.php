<?php

use yii\db\Migration;

class m250920_071927_insert_initial_roi_plan_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Insert initial ROI plan data: 8% rate, monthly frequency, twice tenure, 10th of the month
        $this->insert('{{%roi_plan}}', [
            'rate' => 8.00,
            'frequency' => 3, // Monthly (using constant value)
            'tenure' => 2, // Twice (using constant value)
            'release_date' => date('Y-m-10'), // 10th of current month
            'status' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Delete the inserted ROI plan data
        $this->delete('{{%roi_plan}}', [
            'rate' => 8.00,
            'frequency' => 3,
            'tenure' => 2,
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250920_071927_insert_initial_roi_plan_data cannot be reverted.\n";

        return false;
    }
    */
}
