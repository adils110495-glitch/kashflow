<?php

/**
 * Script to create default admin user using RBAC
 * Run this script after running the migrations
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../load_env.php';

$config = require __DIR__ . '/../config/console.php';
$app = new yii\console\Application($config);

use app\models\User;

echo "Creating default admin user...\n";

try {
    // Check if admin already exists
    $existingAdmin = User::findAdminByUsername('admin');
    if ($existingAdmin) {
        echo "Admin user 'admin' already exists.\n";
        exit(0);
    }

    // Create admin user
    $admin = User::createAdmin('admin', 'admin@kashflow.com', 'admin123', User::ROLE_ADMIN);

    if ($admin) {
        echo "Admin created successfully!\n";
        echo "Username: admin\n";
        echo "Email: admin@kashflow.com\n";
        echo "Password: admin123\n";
        echo "Role: Admin\n";
        echo "Please change the password after first login!\n";
    } else {
        echo "Failed to create admin.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
