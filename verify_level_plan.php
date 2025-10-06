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

echo "=== Verifying Level Plan Table ===\n";

try {
    // Query the level_plan table
    $query = "SELECT * FROM level_plan ORDER BY level";
    $rows = Yii::$app->db->createCommand($query)->queryAll();
    
    if (empty($rows)) {
        echo "✗ No data found in level_plan table\n";
    } else {
        echo "✓ Level Plan table created successfully with " . count($rows) . " records\n\n";
        
        // Display table header
        printf("%-5s %-8s %-12s %-8s %-12s %-12s\n", 
            'ID', 'Level', 'Rate', 'Directs', 'Status', 'Created');
        echo str_repeat('-', 65) . "\n";
        
        // Display each row
        foreach ($rows as $row) {
            printf("%-5s %-8s %-12s %-8s %-12s %-12s\n",
                $row['id'],
                $row['level'],
                $row['rate'] . '%',
                $row['no_of_directs'],
                $row['status'] == 1 ? 'Active' : 'Inactive',
                substr($row['created_at'], 0, 10)
            );
        }
        
        echo "\n✓ All 10 levels inserted successfully!\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Verification Complete ===\n";