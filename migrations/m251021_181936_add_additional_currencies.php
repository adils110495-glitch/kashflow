<?php

use yii\db\Migration;

class m251021_181936_add_additional_currencies extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add major world currencies using INSERT IGNORE to avoid duplicates
        $currencies = [
            // Asian Currencies
            ['JPY', 'Japanese Yen', '¥', 150.0000, 0, 1, 0],
            ['CNY', 'Chinese Yuan', '¥', 7.2000, 0, 1, 2],
            ['KRW', 'South Korean Won', '₩', 1300.0000, 0, 1, 0],
            ['SGD', 'Singapore Dollar', 'S$', 1.3500, 0, 1, 2],
            ['HKD', 'Hong Kong Dollar', 'HK$', 7.8000, 0, 1, 2],
            ['THB', 'Thai Baht', '฿', 35.0000, 0, 1, 2],
            ['MYR', 'Malaysian Ringgit', 'RM', 4.5000, 0, 1, 2],
            ['PHP', 'Philippine Peso', '₱', 55.0000, 0, 1, 2],
            ['IDR', 'Indonesian Rupiah', 'Rp', 15500.0000, 0, 1, 0],
            ['VND', 'Vietnamese Dong', '₫', 24000.0000, 0, 1, 0],

            // European Currencies
            ['CHF', 'Swiss Franc', 'CHF', 0.8800, 0, 1, 2],
            ['SEK', 'Swedish Krona', 'kr', 10.5000, 0, 1, 2],
            ['NOK', 'Norwegian Krone', 'kr', 10.8000, 0, 1, 2],
            ['DKK', 'Danish Krone', 'kr', 6.8500, 0, 1, 2],
            ['PLN', 'Polish Zloty', 'zł', 4.0000, 0, 1, 2],
            ['CZK', 'Czech Koruna', 'Kč', 22.5000, 0, 1, 2],
            ['HUF', 'Hungarian Forint', 'Ft', 360.0000, 0, 1, 0],
            ['RUB', 'Russian Ruble', '₽', 90.0000, 0, 1, 2],

            // American Currencies
            ['CAD', 'Canadian Dollar', 'C$', 1.3500, 0, 1, 2],
            ['AUD', 'Australian Dollar', 'A$', 1.5000, 0, 1, 2],
            ['NZD', 'New Zealand Dollar', 'NZ$', 1.6000, 0, 1, 2],
            ['BRL', 'Brazilian Real', 'R$', 5.0000, 0, 1, 2],
            ['MXN', 'Mexican Peso', '$', 17.0000, 0, 1, 2],
            ['ARS', 'Argentine Peso', '$', 800.0000, 0, 1, 2],
            ['CLP', 'Chilean Peso', '$', 900.0000, 0, 1, 0],

            // Middle East & African Currencies
            ['AED', 'UAE Dirham', 'د.إ', 3.6700, 0, 1, 2],
            ['SAR', 'Saudi Riyal', '﷼', 3.7500, 0, 1, 2],
            ['QAR', 'Qatari Riyal', '﷼', 3.6400, 0, 1, 2],
            ['KWD', 'Kuwaiti Dinar', 'د.ك', 0.3100, 0, 1, 3],
            ['BHD', 'Bahraini Dinar', 'د.ب', 0.3800, 0, 1, 3],
            ['OMR', 'Omani Rial', '﷼', 0.3850, 0, 1, 3],
            ['JOD', 'Jordanian Dinar', 'د.ا', 0.7100, 0, 1, 3],
            ['EGP', 'Egyptian Pound', '£', 30.0000, 0, 1, 2],
            ['ZAR', 'South African Rand', 'R', 18.0000, 0, 1, 2],
            ['NGN', 'Nigerian Naira', '₦', 750.0000, 0, 1, 2],
            ['KES', 'Kenyan Shilling', 'KSh', 150.0000, 0, 1, 2],

            // Additional Cryptocurrencies
            ['LTC', 'Litecoin', 'Ł', 0.0015, 0, 1, 6],
            ['BCH', 'Bitcoin Cash', 'BCH', 0.0008, 0, 1, 6],
            ['XRP', 'Ripple', 'XRP', 1.5000, 0, 1, 2],
            ['ADA', 'Cardano', '₳', 2.0000, 0, 1, 2],
            ['DOT', 'Polkadot', 'DOT', 0.1200, 0, 1, 4],
            ['LINK', 'Chainlink', 'LINK', 0.0800, 0, 1, 4],
            ['SOL', 'Solana', 'SOL', 0.0400, 0, 1, 4],
            ['MATIC', 'Polygon', 'MATIC', 0.8000, 0, 1, 2],
            ['AVAX', 'Avalanche', 'AVAX', 0.0300, 0, 1, 4],
            ['USDT', 'Tether', 'USDT', 1.0000, 0, 1, 2],
            ['USDC', 'USD Coin', 'USDC', 1.0000, 0, 1, 2],
            ['BNB', 'Binance Coin', 'BNB', 0.2000, 0, 1, 4],
        ];

        // Insert currencies using raw SQL with IGNORE to avoid duplicates
        foreach ($currencies as $currency) {
            $sql = "INSERT IGNORE INTO {{%currency}} (code, name, symbol, exchange_rate, is_base, is_active, decimal_places) VALUES ('{$currency[0]}', '{$currency[1]}', '{$currency[2]}', {$currency[3]}, {$currency[4]}, {$currency[5]}, {$currency[6]})";
            $this->execute($sql);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remove all added currencies
        $currencyCodes = [
            'JPY', 'CNY', 'KRW', 'SGD', 'HKD', 'THB', 'MYR', 'PHP', 'IDR', 'VND',
            'CHF', 'SEK', 'NOK', 'DKK', 'PLN', 'CZK', 'HUF', 'RUB',
            'CAD', 'AUD', 'NZD', 'BRL', 'MXN', 'ARS', 'CLP',
            'AED', 'SAR', 'QAR', 'KWD', 'BHD', 'OMR', 'JOD', 'EGP', 'ZAR', 'NGN', 'KES',
            'LTC', 'BCH', 'XRP', 'ADA', 'DOT', 'LINK', 'SOL', 'MATIC', 'AVAX', 'USDT', 'USDC', 'BNB'
        ];

        foreach ($currencyCodes as $code) {
            $this->delete('{{%currency}}', ['code' => $code]);
        }
    }
}