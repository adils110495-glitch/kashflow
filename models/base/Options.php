<?php

namespace app\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "options".
 *
 * @property int $id
 * @property string $key_name
 * @property string|null $key_value
 * @property int $created_at
 * @property int $updated_at
 */
class Options extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%options}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key_name'], 'required'],
            [['key_value'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['key_name'], 'string', 'max' => 255],
            [['key_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key_name' => 'Key Name',
            'key_value' => 'Key Value',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
