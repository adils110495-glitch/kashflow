<?php

use yii\db\Migration;

class m250919_160707_create_rbac_roles_and_superadmin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Create customer role
        $customer = $auth->createRole('customer');
        $customer->description = 'Customer role with basic permissions';
        $auth->add($customer);

        // Create superadmin role
        $superadmin = $auth->createRole('superadmin');
        $superadmin->description = 'Super administrator with full permissions';
        $auth->add($superadmin);

        // Create superadmin user
        $user = new \dektrium\user\models\User(['scenario' => 'register']);
        $user->username = 'superadmin';
        $user->email = 'superadmin@kashflow.com';
        $user->password_hash = Yii::$app->security->generatePasswordHash('SuperAdmin123!');
        $user->confirmed_at = time();
        $user->created_at = time();
        $user->updated_at = time();
        if (!$user->save(false)) {
            throw new \Exception('Failed to create superadmin user: ' . json_encode($user->errors));
        }

        // Assign superadmin role to the user
        $auth->assign($superadmin, $user->id);

        echo "RBAC roles created and superadmin user assigned successfully.\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        // Remove role assignments
        $auth->removeAll();

        // Remove roles
        $customer = $auth->getRole('customer');
        if ($customer) {
            $auth->remove($customer);
        }

        $superadmin = $auth->getRole('superadmin');
        if ($superadmin) {
            $auth->remove($superadmin);
        }

        // Remove superadmin user
        $user = \dektrium\user\models\User::findOne(['username' => 'superadmin']);
        if ($user) {
            $user->delete();
        }

        echo "RBAC roles and superadmin user removed successfully.\n";
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250919_160707_create_rbac_roles_and_superadmin cannot be reverted.\n";

        return false;
    }
    */
}
