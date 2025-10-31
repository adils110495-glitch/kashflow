<?php

use yii\db\Migration;

/**
 * Handles dropping karaje_file column from table `{{%fund_request}}`.
 */
class m251020_220000_drop_karaje_file_from_fund_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //$this->dropColumn('{{%fund_request}}', 'karaje_file');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%fund_request}}', 'karaje_file', $this->string(255)->comment('Karaje document file path'));
    }
}
