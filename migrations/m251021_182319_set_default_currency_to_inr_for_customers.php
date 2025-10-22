<?php

use yii\db\Migration;

class m251021_182319_set_default_currency_to_inr_for_customers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Get INR currency ID
        $inrCurrencyId = $this->db->createCommand('SELECT id FROM {{%currency}} WHERE code = "INR" AND is_active = 1')->queryScalar();
        
        if ($inrCurrencyId) {
            // Update all customers who don't have a currency set to use INR
            $this->update('{{%customer}}', ['currency_id' => $inrCurrencyId], ['currency_id' => null]);
            
            // Also update customers who might have USD as default to INR
            $usdCurrencyId = $this->db->createCommand('SELECT id FROM {{%currency}} WHERE code = "USD" AND is_active = 1')->queryScalar();
            if ($usdCurrencyId) {
                $this->update('{{%customer}}', ['currency_id' => $inrCurrencyId], ['currency_id' => $usdCurrencyId]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Get USD currency ID
        $usdCurrencyId = $this->db->createCommand('SELECT id FROM {{%currency}} WHERE code = "USD" AND is_active = 1')->queryScalar();
        
        if ($usdCurrencyId) {
            // Revert customers back to USD
            $this->update('{{%customer}}', ['currency_id' => $usdCurrencyId], ['currency_id' => null]);
        }
    }
}