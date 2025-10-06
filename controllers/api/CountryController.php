<?php

namespace app\controllers\api;

/**
 * This is the class for REST controller "CountryController".
 */

use app\models\Country;
use yii\rest\ActiveController;

class CountryController extends ActiveController
{
    public $modelClass = Country::class;
}
