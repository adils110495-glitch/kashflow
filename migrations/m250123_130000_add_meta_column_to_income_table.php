<?php

use yii\db\Migration;

/**
 * Class m250123_130000_add_meta_column_to_income_table
 */
class m250123_130000_add_meta_column_to_income_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%income}}', 'meta', $this->text()->null()->comment('Additional metadata for income record'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%income}}', 'meta');
    }
}