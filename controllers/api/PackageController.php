<?php

namespace app\controllers\api;

/**
 * This is the class for REST controller "PackageController".
 */

use app\models\Package;
use yii\rest\ActiveController;

class PackageController extends ActiveController
{
    public $modelClass = Package::class;
}
