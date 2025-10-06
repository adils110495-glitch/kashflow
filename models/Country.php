<?php

namespace app\models;

use Yii;
use app\models\base\Country as BaseCountry;

/**
 * This is the model class for table "country".
 *
 * @property int $id
 * @property string $name
 * @property string $language
 * @property string $lang_code
 * @property string $country_code
 * @property string $mobile_code
 * @property string $flag
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Country extends BaseCountry
{


    /**
     * Get active countries
     */
    public static function getActiveCountries()
    {
        return self::find()->where(['status' => 1])->orderBy('name ASC')->all();
    }
}