<?php

// Define Yii constants
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
defined('YII_ENV_DEV') or define('YII_ENV_DEV', true);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/console.php';
$application = new yii\console\Application($config);

use app\models\Country;

// Test if countries exist
$countries = Country::find()->where(['status' => 1])->all();

echo "Total countries found: " . count($countries) . "\n";

if (count($countries) > 0) {
    echo "First 5 countries:\n";
    foreach (array_slice($countries, 0, 5) as $country) {
        echo "- {$country->name} ({$country->country_code}) {$country->flag} {$country->mobile_code}\n";
    }
} else {
    echo "No countries found in database!\n";
}

// Test the getCountriesForSelect2 method
use app\models\RegistrationForm;
$form = new RegistrationForm();
$select2Data = $form->getCountriesForSelect2();

echo "\nSelect2 data count: " . count($select2Data) . "\n";
if (count($select2Data) > 0) {
    echo "First Select2 item: " . json_encode($select2Data[0]) . "\n";
}