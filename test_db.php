<?php
// Define YII constants
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require 'vendor/autoload.php';
require 'vendor/yiisoft/yii2/Yii.php';
$config = require 'config/web.php';

$app = new yii\web\Application($config);

try {
    $count = app\models\Country::find()->where(['status' => 1])->count();
    echo 'Active countries count: ' . $count . PHP_EOL;
    
    if ($count > 0) {
        $first = app\models\Country::find()->where(['status' => 1])->one();
        echo 'First country: ' . $first->name . PHP_EOL;
        echo 'Country ID: ' . $first->id . PHP_EOL;
    }
    
    // Test RegistrationForm validation
    $model = new app\models\RegistrationForm();
    $countries = $model->getCountriesForSelect2();
    echo 'Countries for Select2: ' . count($countries) . PHP_EOL;
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    echo 'Trace: ' . $e->getTraceAsString() . PHP_EOL;
}