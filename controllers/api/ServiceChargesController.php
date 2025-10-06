<?php

namespace app\controllers\api;

/**
 * This is the class for REST controller "ServiceChargesController".
 */

use app\models\ServiceCharges;
use yii\rest\ActiveController;

class ServiceChargesController extends ActiveController
{
    public $modelClass = ServiceCharges::class;
}
