<?php

namespace app\controllers\api;

/**
 * This is the class for REST controller "StatesController".
 */

use app\models\States;
use yii\rest\ActiveController;

class StatesController extends ActiveController
{
    public $modelClass = States::class;
}
