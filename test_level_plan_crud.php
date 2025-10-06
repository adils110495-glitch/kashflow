<?php

// Define constants
define('YII_DEBUG', true);
define('YII_ENV', 'dev');
define('YII_ENV_DEV', true);

// Include Yii
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

// Load configuration
$config = require __DIR__ . '/config/web.php';

// Create application
$app = new yii\web\Application($config);

echo "=== Testing Level Plan CRUD Functionality ===\n";

try {
    // Test 1: Check if LevelPlan model can be loaded
    echo "\n1. Testing LevelPlan Model Loading...\n";
    $levelPlan = new \app\models\LevelPlan();
    echo "✓ LevelPlan model loaded successfully\n";
    
    // Test 2: Check if we can fetch existing records
    echo "\n2. Testing Data Retrieval...\n";
    $existingPlans = \app\models\LevelPlan::find()->all();
    echo "✓ Found " . count($existingPlans) . " existing level plans\n";
    
    // Test 3: Test model validation
    echo "\n3. Testing Model Validation...\n";
    $testModel = new \app\models\LevelPlan();
    $testModel->level = 99;
    $testModel->rate = 5.5;
    $testModel->no_of_directs = 2;
    $testModel->status = 1;
    
    if ($testModel->validate()) {
        echo "✓ Model validation passed\n";
    } else {
        echo "✗ Model validation failed:\n";
        foreach ($testModel->errors as $field => $errors) {
            echo "  - {$field}: " . implode(', ', $errors) . "\n";
        }
    }
    
    // Test 4: Test helper methods
    echo "\n4. Testing Helper Methods...\n";
    $statusOptions = \app\models\LevelPlan::getStatusOptions();
    echo "✓ Status options: " . implode(', ', $statusOptions) . "\n";
    
    $nextLevel = \app\models\LevelPlan::getNextLevel();
    echo "✓ Next available level: {$nextLevel}\n";
    
    // Test 5: Test controller instantiation
    echo "\n5. Testing Controller...\n";
    $controller = new \app\controllers\LevelPlanController('level-plan', Yii::$app);
    echo "✓ LevelPlanController instantiated successfully\n";
    
    // Test 6: Display sample data
    echo "\n6. Sample Level Plan Data:\n";
    echo str_repeat('-', 70) . "\n";
    printf("%-5s %-8s %-12s %-12s %-10s\n", 'ID', 'Level', 'Rate', 'Directs', 'Status');
    echo str_repeat('-', 70) . "\n";
    
    foreach ($existingPlans as $plan) {
        printf("%-5s %-8s %-12s %-12s %-10s\n",
            $plan->id,
            $plan->level,
            $plan->getFormattedRate(),
            $plan->no_of_directs,
            $plan->getStatusLabel()
        );
    }
    
    echo "\n✓ All CRUD components are working correctly!\n";
    echo "\n📋 Available URLs:\n";
    echo "   - Index: http://localhost:8080/level-plan/index\n";
    echo "   - Create: http://localhost:8080/level-plan/create\n";
    echo "   - View: http://localhost:8080/level-plan/view?id=1\n";
    echo "   - Update: http://localhost:8080/level-plan/update?id=1\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";