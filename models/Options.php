<?php

namespace app\models;

use app\models\base\Options as BaseOptions;

/**
 * This is the model class for table "options".
 *
 * @property int $id
 * @property string $key_name
 * @property string|null $key_value
 * @property int $created_at
 * @property int $updated_at
 */
class Options extends BaseOptions
{
    /**
     * Get option value by key name
     * @param string $keyName
     * @param mixed $defaultValue
     * @return mixed
     */
    public static function getValue($keyName, $defaultValue = null)
    {
        $option = self::findOne(['key_name' => $keyName]);
        return $option ? $option->key_value : $defaultValue;
    }

    /**
     * Set option value by key name
     * @param string $keyName
     * @param mixed $value
     * @return bool
     */
    public static function setValue($keyName, $value)
    {
        $option = self::findOne(['key_name' => $keyName]);
        
        if (!$option) {
            $option = new self();
            $option->key_name = $keyName;
        }
        
        $option->key_value = $value;
        return $option->save();
    }

    /**
     * Get all options as key-value array
     * @return array
     */
    public static function getAllAsArray()
    {
        $options = self::find()->all();
        $result = [];
        
        foreach ($options as $option) {
            $result[$option->key_name] = $option->key_value;
        }
        
        return $result;
    }

    /**
     * Delete option by key name
     * @param string $keyName
     * @return bool
     */
    public static function deleteByKey($keyName)
    {
        $option = self::findOne(['key_name' => $keyName]);
        return $option ? $option->delete() : false;
    }
}
