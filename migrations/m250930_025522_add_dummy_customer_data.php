<?php

use yii\db\Migration;

class m250930_025522_add_dummy_customer_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Insert dummy users first
        $this->insertUsers();
        
        // Insert dummy customers
        $this->insertCustomers();
        
        // Insert customer packages
        $this->insertCustomerPackages();
        
        // Insert customer activities
        $this->insertCustomerActivities();
        
        echo "Dummy customer data inserted successfully.\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Delete in reverse order to maintain foreign key constraints
        $this->delete('customer_activity', ['customer_id' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
        $this->delete('customer_package', ['customer_id' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
        $this->delete('customer', ['id' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
        $this->delete('user', ['id' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11]]);
        
        echo "Dummy customer data removed successfully.\n";
    }

    private function insertUsers()
    {
        $users = [
            [2, 'john_doe', 'john.doe@example.com', Yii::$app->security->generatePasswordHash('password123'), time(), time(), time()],
            [3, 'jane_smith', 'jane.smith@example.com', Yii::$app->security->generatePasswordHash('password123'), time(), time(), time()],
            [4, 'mike_wilson', 'mike.wilson@example.com', Yii::$app->security->generatePasswordHash('password123'), time(), time(), time()],
            [5, 'sarah_jones', 'sarah.jones@example.com', Yii::$app->security->generatePasswordHash('password123'), time(), time(), time()],
            [6, 'david_brown', 'david.brown@example.com', Yii::$app->security->generatePasswordHash('password123'), time(), time(), time()],
            [7, 'lisa_davis', 'lisa.davis@example.com', Yii::$app->security->generatePasswordHash('password123'), time(), time(), time()],
            [8, 'tom_miller', 'tom.miller@example.com', Yii::$app->security->generatePasswordHash('password123'), time(), time(), time()],
            [9, 'amy_taylor', 'amy.taylor@example.com', Yii::$app->security->generatePasswordHash('password123'), time(), time(), time()],
            [10, 'chris_anderson', 'chris.anderson@example.com', Yii::$app->security->generatePasswordHash('password123'), time(), time(), time()],
            [11, 'emma_thomas', 'emma.thomas@example.com', Yii::$app->security->generatePasswordHash('password123'), time(), time(), time()],
        ];

        foreach ($users as $user) {
            $this->insert('user', [
                'id' => $user[0],
                'username' => $user[1],
                'email' => $user[2],
                'password_hash' => $user[3],
                'confirmed_at' => $user[4],
                'created_at' => $user[5],
                'updated_at' => $user[6],
            ]);
        }
    }

    private function insertCustomers()
    {
        $customers = [
            [1, 2, 'John Doe', 'john.doe@example.com', '+1234567890', 'superadmin', 1, 1, 1, time(), time()],
            [2, 3, 'Jane Smith', 'jane.smith@example.com', '+1234567891', 'superadmin', 1, 1, 1, time(), time()],
            [3, 4, 'Mike Wilson', 'mike.wilson@example.com', '+1234567892', 'john_doe', 1, 1, 1, time(), time()],
            [4, 5, 'Sarah Jones', 'sarah.jones@example.com', '+1234567893', 'john_doe', 1, 1, 1, time(), time()],
            [5, 6, 'David Brown', 'david.brown@example.com', '+1234567894', 'jane_smith', 1, 1, 1, time(), time()],
            [6, 7, 'Lisa Davis', 'lisa.davis@example.com', '+1234567895', 'mike_wilson', 1, 1, 1, time(), time()],
            [7, 8, 'Tom Miller', 'tom.miller@example.com', '+1234567896', 'sarah_jones', 1, 1, 1, time(), time()],
            [8, 9, 'Amy Taylor', 'amy.taylor@example.com', '+1234567897', 'david_brown', 1, 1, 1, time(), time()],
            [9, 10, 'Chris Anderson', 'chris.anderson@example.com', '+1234567898', 'lisa_davis', 1, 1, 1, time(), time()],
            [10, 11, 'Emma Thomas', 'emma.thomas@example.com', '+1234567899', 'tom_miller', 1, 1, 1, time(), time()],
        ];

        foreach ($customers as $customer) {
            $this->insert('customer', [
                'id' => $customer[0],
                'user_id' => $customer[1],
                'name' => $customer[2],
                'email' => $customer[3],
                'mobile_no' => $customer[4],
                'referral_code' => $customer[5],
                'country_id' => $customer[6],
                'current_package' => $customer[7],
                'status' => $customer[8],
                'created_at' => $customer[9],
                'updated_at' => $customer[10],
            ]);
        }
    }

    private function insertCustomerPackages()
    {
        $packages = [
            [1, 1, 1, '2024-01-15', 1, time(), time()],
            [2, 2, 1, '2024-01-16', 1, time(), time()],
            [3, 3, 1, '2024-01-17', 1, time(), time()],
            [4, 4, 1, '2024-01-18', 1, time(), time()],
            [5, 5, 1, '2024-01-19', 1, time(), time()],
            [6, 6, 1, '2024-01-20', 1, time(), time()],
            [7, 7, 1, '2024-01-21', 1, time(), time()],
            [8, 8, 1, '2024-01-22', 1, time(), time()],
            [9, 9, 1, '2024-01-23', 1, time(), time()],
            [10, 10, 1, '2024-01-24', 1, time(), time()],
        ];

        foreach ($packages as $package) {
            $this->insert('customer_package', [
                'id' => $package[0],
                'customer_id' => $package[1],
                'package_id' => $package[2],
                'date' => $package[3],
                'status' => $package[4],
                'created_at' => $package[5],
                'updated_at' => $package[6],
            ]);
        }
    }

    private function insertCustomerActivities()
    {
        $activities = [
            [1, 1, 'registration', 'Customer registered successfully', '192.168.1.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"source": "website"}', time(), time()],
            [2, 2, 'registration', 'Customer registered successfully', '192.168.1.2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"source": "website"}', time(), time()],
            [3, 3, 'registration', 'Customer registered successfully', '192.168.1.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"source": "referral"}', time(), time()],
            [4, 4, 'registration', 'Customer registered successfully', '192.168.1.4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"source": "referral"}', time(), time()],
            [5, 5, 'registration', 'Customer registered successfully', '192.168.1.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"source": "referral"}', time(), time()],
            [6, 6, 'registration', 'Customer registered successfully', '192.168.1.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"source": "referral"}', time(), time()],
            [7, 7, 'registration', 'Customer registered successfully', '192.168.1.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"source": "referral"}', time(), time()],
            [8, 8, 'registration', 'Customer registered successfully', '192.168.1.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"source": "referral"}', time(), time()],
            [9, 9, 'registration', 'Customer registered successfully', '192.168.1.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"source": "referral"}', time(), time()],
            [10, 10, 'registration', 'Customer registered successfully', '192.168.1.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"source": "referral"}', time(), time()],
            [11, 1, 'package_upgrade', 'Upgraded to Premium package', '192.168.1.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"package_id": 1, "amount": 100}', time(), time()],
            [12, 2, 'package_upgrade', 'Upgraded to Premium package', '192.168.1.2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"package_id": 1, "amount": 100}', time(), time()],
            [13, 3, 'referral', 'Referred new customer', '192.168.1.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"referred_customer": "mike_wilson"}', time(), time()],
            [14, 4, 'referral', 'Referred new customer', '192.168.1.4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"referred_customer": "sarah_jones"}', time(), time()],
            [15, 5, 'referral', 'Referred new customer', '192.168.1.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '{"referred_customer": "david_brown"}', time(), time()],
        ];

        foreach ($activities as $activity) {
            $this->insert('customer_activity', [
                'id' => $activity[0],
                'customer_id' => $activity[1],
                'activity_type' => $activity[2],
                'activity_description' => $activity[3],
                'ip_address' => $activity[4],
                'user_agent' => $activity[5],
                'metadata' => $activity[6],
                'created_at' => $activity[7],
                'updated_at' => $activity[8],
            ]);
        }
    }
}
