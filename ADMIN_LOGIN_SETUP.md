# Admin Login System Setup (RBAC-based)

This document explains how to set up and use the admin login system for KashFlow using RBAC (Role-Based Access Control).

## Overview

The admin login system uses the existing `user` table with RBAC roles. It provides:

- Admin authentication using the existing `User` model with RBAC roles
- Role-based permissions (Super Admin, Admin, Moderator, Customer)
- Secure admin dashboard
- Separate admin login/logout functionality

## Setup Instructions

### 1. Run Database Migrations

First, run the migrations to set up RBAC and admin roles:

```bash
php yii migrate
```

This will:
- Set up RBAC tables (if not already done)
- Create `superadmin`, `customer`, `admin`, and `moderator` roles
- Create a default superadmin user

### 2. Create Default Admin User

Run the script to create a default admin user:

```bash
php create_admin_user.php
```

This will create:
- Username: `admin`
- Email: `admin@kashflow.com`
- Password: `admin123`
- Role: Admin

**Important**: Change the password after first login!

### 3. Access Admin Panel

Navigate to: `http://your-domain.com/admin-auth/login`

Or use the shorter URL: `http://your-domain.com/admin/login`

## Admin Routes

- `/admin-auth/login` - Admin login page
- `/admin-auth/logout` - Admin logout
- `/admin-auth/dashboard` - Redirect to admin dashboard
- `/admin/login` - Alias for admin login
- `/admin/logout` - Alias for admin logout
- `/admin` - Admin dashboard
- `/admin/dashboard` - Admin dashboard

## Admin Authentication

The admin authentication uses the existing `Yii::$app->user` component with RBAC checks:

```php
// Check if admin is logged in
!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()

// Get current admin user
Yii::$app->user->identity

// Check admin permissions
Yii::$app->user->identity->hasAdminPermission('permission_name')

// Check if super admin
Yii::$app->user->identity->isSuperAdmin()

// Check admin role
Yii::$app->user->identity->hasAdminRole('admin')
```

## User Model RBAC Methods

The `User` model provides RBAC-based admin methods:

### Admin Status Methods
- `isAdmin()` - Check if user has any admin role
- `isSuperAdmin()` - Check if user has superadmin role
- `isCustomer()` - Check if user has customer role
- `hasAdminRole($role)` - Check if user has specific admin role

### Permission Methods
- `hasAdminPermission($permission)` - Check if admin has specific permission
- `getUserRoles()` - Get all user roles
- `assignRole($role)` - Assign role to user
- `revokeRole($role)` - Revoke role from user

### Utility Methods
- `getAdminRoleText()` - Get admin role as text
- `getCustomer()` - Get customer profile if exists

### Static Methods
- `findAdmins()` - Find all admin users
- `findAdminByUsername($username)` - Find admin by username
- `findAdminByEmail($email)` - Find admin by email
- `createAdmin($username, $email, $password, $role)` - Create admin user

## Available Roles

- `superadmin` - Full access to everything (already exists)
- `admin` - Standard admin access
- `moderator` - Limited admin access
- `customer` - Customer access (already exists)

## Protecting Admin Controllers

To protect admin controllers, use RBAC checks with the user component:

```php
public function behaviors()
{
    return [
        'access' => [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'matchCallback' => function ($rule, $action) {
                        return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin();
                    },
                ],
            ],
            'denyCallback' => function ($rule, $action) {
                return Yii::$app->response->redirect(['admin-auth/login']);
            },
        ],
    ];
}
```

## Creating New Admin Users

You can create new admin users programmatically:

```php
use app\models\User;

// Create admin user
$admin = User::createAdmin('newadmin', 'newadmin@kashflow.com', 'secure_password', User::ROLE_ADMIN);

// Or manually
$user = new User();
$user->username = 'newadmin';
$user->email = 'newadmin@kashflow.com';
$user->setPassword('secure_password');
$user->generateAuthKey();
$user->status = 10; // Active status
$user->save();

// Assign admin role
$user->assignRole(User::ROLE_ADMIN);
```

## RBAC Management

You can manage roles using Yii's RBAC system:

```php
$auth = Yii::$app->authManager;

// Check if user has role
if ($auth->checkAccess($userId, 'admin')) {
    // User has admin role
}

// Get all roles for user
$roles = $auth->getRolesByUser($userId);

// Assign role to user
$role = $auth->getRole('admin');
$auth->assign($role, $userId);

// Revoke role from user
$auth->revoke($role, $userId);
```

## Security Features

- Uses existing user table with RBAC roles
- Password hashing using Yii2 security
- Auth key for "remember me" functionality
- Password reset tokens
- Role-based access control
- Separate admin login/logout

## Admin Dashboard

The admin dashboard provides:

- Overview statistics (customers, withdrawals, income)
- Quick action buttons
- Recent activities
- Recent customers and withdrawals

## Troubleshooting

### Admin Login Not Working

1. Check if RBAC tables exist: `SHOW TABLES LIKE '%auth%';`
2. Verify admin user exists: `SELECT * FROM user WHERE username = 'admin';`
3. Check admin user has correct role: Use RBAC management interface

### Permission Denied

1. Ensure admin is logged in: `!Yii::$app->user->isGuest`
2. Check admin role: `Yii::$app->user->identity->isAdmin()`
3. Check RBAC role assignment
4. Verify controller access control rules

### Database Connection Issues

1. Check database configuration in `config/db.php`
2. Ensure database server is running
3. Verify database credentials

## File Structure

```
models/
├── User.php                 # Extended User model with RBAC methods
├── AdminLoginForm.php       # Admin login form

controllers/
├── AdminAuthController.php  # Admin authentication
├── AdminDashboardController.php # Admin dashboard
├── AdminController.php      # Updated to use RBAC
└── AdminTicketController.php # Updated to use RBAC

components/
└── AdminUser.php            # Admin user component (optional)

views/
├── layouts/
│   └── admin-login.php      # Admin login layout
└── admin-auth/
    └── login.php            # Admin login view

migrations/
├── m250919_160707_create_rbac_roles_and_superadmin.php
└── m250119_000002_add_admin_moderator_roles.php
```

## Migration from Old System

If you're migrating from the old mixed admin/customer system:

1. Run the migrations to set up RBAC
2. Create admin users using the script
3. Update existing admin controllers to use RBAC checks
4. Test admin login functionality
5. Update any hardcoded admin checks

The system now uses RBAC for role management, providing a more flexible and scalable approach to user permissions.
