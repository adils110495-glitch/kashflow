<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property string $name
 * @property string|null $short_name
 * @property string|null $email
 * @property string|null $phone_no
 * @property string|null $logo
 * @property string|null $website_link
 * @property string $created_at
 * @property string $updated_at
 */
class Company extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['short_name'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['phone_no'], 'string', 'max' => 20],
            [['logo', 'website_link'], 'string', 'max' => 500],
            [['website_link'], 'url'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Company Name',
            'short_name' => 'Short Name',
            'email' => 'Email',
            'phone_no' => 'Phone Number',
            'logo' => 'Logo',
            'website_link' => 'Website Link',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}