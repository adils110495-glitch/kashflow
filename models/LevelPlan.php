<?php

namespace app\models;

use Yii;
use app\models\base\LevelPlan as BaseLevelPlan;

/**
 * This is the model class for table "level_plan".
 *
 * @property int $id
 * @property int $level
 * @property string $rate
 * @property int $no_of_directs
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class LevelPlan extends BaseLevelPlan
{
    /**
     * Get active level plans
     * @return \yii\db\ActiveQuery
     */
    public static function getActiveLevels()
    {
        return self::find()->where(['status' => 1])->orderBy('level ASC');
    }

    /**
     * Get all level plans as array for dropdown
     * @return array
     */
    public static function getLevelOptions()
    {
        return self::find()
            ->select(['level', 'level'])
            ->where(['status' => 1])
            ->orderBy('level ASC')
            ->indexBy('level')
            ->column();
    }

    /**
     * Get level plan by level number
     * @param int $level
     * @return LevelPlan|null
     */
    public static function findByLevel($level)
    {
        return self::findOne(['level' => $level, 'status' => 1]);
    }

    /**
     * Get the next available level number
     * @return int
     */
    public static function getNextLevel()
    {
        $maxLevel = self::find()->max('level');
        return $maxLevel ? $maxLevel + 1 : 1;
    }

    /**
     * Check if level exists
     * @param int $level
     * @param int|null $excludeId
     * @return bool
     */
    public static function levelExists($level, $excludeId = null)
    {
        $query = self::find()->where(['level' => $level]);
        if ($excludeId) {
            $query->andWhere(['!=', 'id', $excludeId]);
        }
        return $query->exists();
    }
}