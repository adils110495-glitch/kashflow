<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "currency".
 *
 * @property int $id
 * @property string $code Currency code (e.g., USD, EUR, INR)
 * @property string $name Currency name
 * @property string $symbol Currency symbol (e.g., $, €, ₹)
 * @property float $exchange_rate Exchange rate to base currency
 * @property int $is_base Is this the base currency
 * @property int $is_active Is currency active
 * @property int $decimal_places Number of decimal places
 * @property string $created_at
 * @property string $updated_at
 */
class Currency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%currency}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exchange_rate'], 'default', 'value' => 1.0000],
            [['is_base'], 'default', 'value' => 0],
            [['is_active'], 'default', 'value' => 1],
            [['decimal_places'], 'default', 'value' => 2],
            [['code', 'name', 'symbol'], 'required'],
            [['exchange_rate'], 'number'],
            [['is_base', 'is_active', 'decimal_places'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 3],
            [['name'], 'string', 'max' => 100],
            [['symbol'], 'string', 'max' => 10],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'symbol' => 'Symbol',
            'exchange_rate' => 'Exchange Rate',
            'is_base' => 'Is Base',
            'is_active' => 'Is Active',
            'decimal_places' => 'Decimal Places',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get active currencies
     * @return \yii\db\ActiveQuery
     */
    public static function getActiveCurrencies()
    {
        return static::find()->where(['is_active' => 1])->orderBy(['code' => SORT_ASC]);
    }

    /**
     * Get base currency (INR - Indian Rupee)
     * @return Currency|null
     */
    public static function getBaseCurrency()
    {
        return static::find()->where(['is_base' => 1, 'is_active' => 1])->one();
    }

    /**
     * Get currency by code
     * @param string $code
     * @return Currency|null
     */
    public static function getByCode($code)
    {
        return static::find()->where(['code' => strtoupper($code), 'is_active' => 1])->one();
    }

    /**
     * Get currency options for dropdown
     * @return array
     */
    public static function getCurrencyOptions()
    {
        $currencies = static::getActiveCurrencies()->all();
        $options = [];
        
        foreach ($currencies as $currency) {
            $options[$currency->id] = $currency->code . ' - ' . $currency->name . ' (' . $currency->symbol . ')';
        }
        
        return $options;
    }

    /**
     * Get currency options with codes as keys
     * @return array
     */
    public static function getCurrencyOptionsByCode()
    {
        $currencies = static::getActiveCurrencies()->all();
        $options = [];
        
        foreach ($currencies as $currency) {
            $options[$currency->code] = $currency->code . ' - ' . $currency->name . ' (' . $currency->symbol . ')';
        }
        
        return $options;
    }

    /**
     * Convert amount from this currency to base currency (INR)
     * @param float $amount
     * @return float
     */
    public function convertToBase($amount)
    {
        if ($this->is_base) {
            return $amount;
        }
        
        return $amount * $this->exchange_rate;
    }

    /**
     * Convert amount from base currency (INR) to this currency
     * @param float $amount
     * @return float
     */
    public function convertFromBase($amount)
    {
        if ($this->is_base) {
            return $amount;
        }
        
        return $amount / $this->exchange_rate;
    }

    /**
     * Convert amount from this currency to another currency
     * @param float $amount
     * @param Currency $toCurrency
     * @return float
     */
    public function convertTo($amount, $toCurrency)
    {
        // First convert to base currency (INR)
        $baseAmount = $this->convertToBase($amount);
        
        // Then convert to target currency
        return $toCurrency->convertFromBase($baseAmount);
    }

    /**
     * Format amount with currency symbol
     * @param float $amount
     * @param bool $showCode
     * @return string
     */
    public function formatAmount($amount, $showCode = false)
    {
        $formattedAmount = number_format($amount, $this->decimal_places);
        
        if ($showCode) {
            return $this->symbol . $formattedAmount . ' ' . $this->code;
        }
        
        return $this->symbol . $formattedAmount;
    }

    /**
     * Get display name with symbol
     * @return string
     */
    public function getDisplayName()
    {
        return $this->code . ' - ' . $this->name . ' (' . $this->symbol . ')';
    }

    /**
     * Check if currency is crypto
     * @return bool
     */
    public function isCrypto()
    {
        $cryptoCodes = ['BTC', 'ETH', 'LTC', 'BCH', 'XRP', 'ADA', 'DOT', 'LINK'];
        return in_array($this->code, $cryptoCodes);
    }

    /**
     * Check if currency is fiat
     * @return bool
     */
    public function isFiat()
    {
        return !$this->isCrypto();
    }

    /**
     * Update exchange rate
     * @param float $newRate
     * @return bool
     */
    public function updateExchangeRate($newRate)
    {
        if ($this->is_base) {
            return false; // Cannot update base currency rate
        }
        
        $this->exchange_rate = $newRate;
        return $this->save();
    }

    /**
     * Get exchange rate info
     * @return string
     */
    public function getExchangeRateInfo()
    {
        if ($this->is_base) {
            return 'Base Currency (INR)';
        }
        
        $baseCurrency = static::getBaseCurrency();
        if ($baseCurrency) {
            return '1 ' . $this->code . ' = ' . $this->exchange_rate . ' ' . $baseCurrency->code;
        }
        
        return 'Rate: ' . $this->exchange_rate;
    }
}
