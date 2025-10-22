<?php

use yii\db\Migration;

class m251021_182041_change_base_currency_to_inr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // First, update USD to not be base currency and set its exchange rate relative to INR
        $this->update('{{%currency}}', [
            'is_base' => 0,
            'exchange_rate' => 0.0120  // 1 USD = 0.012 INR (approximately 83 INR = 1 USD)
        ], ['code' => 'USD']);

        // Set INR as the base currency
        $this->update('{{%currency}}', [
            'is_base' => 1,
            'exchange_rate' => 1.0000
        ], ['code' => 'INR']);

        // Update all other currencies to be relative to INR instead of USD
        $currencyUpdates = [
            // Major currencies relative to INR
            'EUR' => 0.0110,    // 1 EUR = 0.011 INR (approximately 91 INR = 1 EUR)
            'GBP' => 0.0095,    // 1 GBP = 0.0095 INR (approximately 105 INR = 1 GBP)
            'JPY' => 0.00008,   // 1 JPY = 0.00008 INR (approximately 12,500 INR = 1 JPY)
            'CNY' => 0.0015,    // 1 CNY = 0.0015 INR (approximately 667 INR = 1 CNY)
            'KRW' => 0.00006,   // 1 KRW = 0.00006 INR (approximately 16,667 INR = 1 KRW)
            'SGD' => 0.016,     // 1 SGD = 0.016 INR (approximately 62 INR = 1 SGD)
            'HKD' => 0.0015,    // 1 HKD = 0.0015 INR (approximately 667 INR = 1 HKD)
            'THB' => 0.0024,    // 1 THB = 0.0024 INR (approximately 417 INR = 1 THB)
            'MYR' => 0.0054,    // 1 MYR = 0.0054 INR (approximately 185 INR = 1 MYR)
            'PHP' => 0.00022,   // 1 PHP = 0.00022 INR (approximately 4,545 INR = 1 PHP)
            'IDR' => 0.000005, // 1 IDR = 0.000005 INR (approximately 200,000 INR = 1 IDR)
            'VND' => 0.000003, // 1 VND = 0.000003 INR (approximately 333,333 INR = 1 VND)

            // European currencies
            'CHF' => 0.0106,    // 1 CHF = 0.0106 INR (approximately 94 INR = 1 CHF)
            'SEK' => 0.0012,   // 1 SEK = 0.0012 INR (approximately 833 INR = 1 SEK)
            'NOK' => 0.0012,    // 1 NOK = 0.0012 INR (approximately 833 INR = 1 NOK)
            'DKK' => 0.0015,    // 1 DKK = 0.0015 INR (approximately 667 INR = 1 DKK)
            'PLN' => 0.0048,    // 1 PLN = 0.0048 INR (approximately 208 INR = 1 PLN)
            'CZK' => 0.00027,  // 1 CZK = 0.00027 INR (approximately 3,704 INR = 1 CZK)
            'HUF' => 0.000033, // 1 HUF = 0.000033 INR (approximately 30,303 INR = 1 HUF)
            'RUB' => 0.0011,   // 1 RUB = 0.0011 INR (approximately 909 INR = 1 RUB)

            // American currencies
            'CAD' => 0.016,     // 1 CAD = 0.016 INR (approximately 62 INR = 1 CAD)
            'AUD' => 0.018,     // 1 AUD = 0.018 INR (approximately 56 INR = 1 AUD)
            'NZD' => 0.019,     // 1 NZD = 0.019 INR (approximately 53 INR = 1 NZD)
            'BRL' => 0.006,     // 1 BRL = 0.006 INR (approximately 167 INR = 1 BRL)
            'MXN' => 0.00071,   // 1 MXN = 0.00071 INR (approximately 1,408 INR = 1 MXN)
            'ARS' => 0.000015,  // 1 ARS = 0.000015 INR (approximately 66,667 INR = 1 ARS)
            'CLP' => 0.000011,  // 1 CLP = 0.000011 INR (approximately 90,909 INR = 1 CLP)

            // Middle East & African currencies
            'AED' => 0.0033,   // 1 AED = 0.0033 INR (approximately 303 INR = 1 AED)
            'SAR' => 0.0034,   // 1 SAR = 0.0034 INR (approximately 294 INR = 1 SAR)
            'QAR' => 0.0033,   // 1 QAR = 0.0033 INR (approximately 303 INR = 1 QAR)
            'KWD' => 0.0037,    // 1 KWD = 0.0037 INR (approximately 270 INR = 1 KWD)
            'BHD' => 0.0046,    // 1 BHD = 0.0046 INR (approximately 217 INR = 1 BHD)
            'OMR' => 0.0046,    // 1 OMR = 0.0046 INR (approximately 217 INR = 1 OMR)
            'JOD' => 0.0085,    // 1 JOD = 0.0085 INR (approximately 118 INR = 1 JOD)
            'EGP' => 0.036,     // 1 EGP = 0.036 INR (approximately 28 INR = 1 EGP)
            'ZAR' => 0.022,     // 1 ZAR = 0.022 INR (approximately 45 INR = 1 ZAR)
            'NGN' => 0.00013,   // 1 NGN = 0.00013 INR (approximately 7,692 INR = 1 NGN)
            'KES' => 0.0067,    // 1 KES = 0.0067 INR (approximately 149 INR = 1 KES)

            // Cryptocurrencies (keeping same relative values)
            'BTC' => 0.0000003, // 1 BTC = 0.0000003 INR (approximately 3,333,333 INR = 1 BTC)
            'ETH' => 0.0000048, // 1 ETH = 0.0000048 INR (approximately 208,333 INR = 1 ETH)
            'LTC' => 0.000018,  // 1 LTC = 0.000018 INR (approximately 55,556 INR = 1 LTC)
            'BCH' => 0.0000096, // 1 BCH = 0.0000096 INR (approximately 104,167 INR = 1 BCH)
            'XRP' => 0.018,     // 1 XRP = 0.018 INR (approximately 56 INR = 1 XRP)
            'ADA' => 0.024,     // 1 ADA = 0.024 INR (approximately 42 INR = 1 ADA)
            'DOT' => 0.0014,   // 1 DOT = 0.0014 INR (approximately 714 INR = 1 DOT)
            'LINK' => 0.00096,  // 1 LINK = 0.00096 INR (approximately 1,042 INR = 1 LINK)
            'SOL' => 0.00048,   // 1 SOL = 0.00048 INR (approximately 2,083 INR = 1 SOL)
            'MATIC' => 0.0096,  // 1 MATIC = 0.0096 INR (approximately 104 INR = 1 MATIC)
            'AVAX' => 0.00036,  // 1 AVAX = 0.00036 INR (approximately 2,778 INR = 1 AVAX)
            'USDT' => 0.012,    // 1 USDT = 0.012 INR (approximately 83 INR = 1 USDT)
            'USDC' => 0.012,    // 1 USDC = 0.012 INR (approximately 83 INR = 1 USDC)
            'BNB' => 0.0024,    // 1 BNB = 0.0024 INR (approximately 417 INR = 1 BNB)
        ];

        // Update each currency
        foreach ($currencyUpdates as $code => $rate) {
            $this->update('{{%currency}}', [
                'exchange_rate' => $rate
            ], ['code' => $code]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Revert USD back to base currency
        $this->update('{{%currency}}', [
            'is_base' => 1,
            'exchange_rate' => 1.0000
        ], ['code' => 'USD']);

        // Set INR back to non-base currency with original rate
        $this->update('{{%currency}}', [
            'is_base' => 0,
            'exchange_rate' => 83.0000
        ], ['code' => 'INR']);

        // Revert all other currencies to their original USD-based rates
        $originalRates = [
            'EUR' => 0.9200, 'GBP' => 0.7900, 'JPY' => 150.0000, 'CNY' => 7.2000,
            'KRW' => 1300.0000, 'SGD' => 1.3500, 'HKD' => 7.8000, 'THB' => 35.0000,
            'MYR' => 4.5000, 'PHP' => 55.0000, 'IDR' => 15500.0000, 'VND' => 24000.0000,
            'CHF' => 0.8800, 'SEK' => 10.5000, 'NOK' => 10.8000, 'DKK' => 6.8500,
            'PLN' => 4.0000, 'CZK' => 22.5000, 'HUF' => 360.0000, 'RUB' => 90.0000,
            'CAD' => 1.3500, 'AUD' => 1.5000, 'NZD' => 1.6000, 'BRL' => 5.0000,
            'MXN' => 17.0000, 'ARS' => 800.0000, 'CLP' => 900.0000,
            'AED' => 3.6700, 'SAR' => 3.7500, 'QAR' => 3.6400, 'KWD' => 0.3100,
            'BHD' => 0.3800, 'OMR' => 0.3850, 'JOD' => 0.7100, 'EGP' => 30.0000,
            'ZAR' => 18.0000, 'NGN' => 750.0000, 'KES' => 150.0000,
            'BTC' => 0.000025, 'ETH' => 0.0004, 'LTC' => 0.0015, 'BCH' => 0.0008,
            'XRP' => 1.5000, 'ADA' => 2.0000, 'DOT' => 0.1200, 'LINK' => 0.0800,
            'SOL' => 0.0400, 'MATIC' => 0.8000, 'AVAX' => 0.0300,
            'USDT' => 1.0000, 'USDC' => 1.0000, 'BNB' => 0.2000,
        ];

        foreach ($originalRates as $code => $rate) {
            $this->update('{{%currency}}', [
                'exchange_rate' => $rate
            ], ['code' => $code]);
        }
    }
}