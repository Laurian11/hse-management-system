# HSE Management System - User Credentials

## Default Login Credentials

After running the database seeder, the following users are available:

### 🔑 Super Administrator
- **Email**: `admin@hse.com`
- **Password**: `password`
- **Role**: Super Administrator
- **Access**: Full system access, including company management

### 👤 Administrator
- **Email**: `john@hse.com`
- **Password**: `password`
- **Role**: Administrator
- **Access**: Company administration (except company management)

### 🛡️ HSE Officer
- **Email**: `sarah@hse.com`
- **Password**: `password`
- **Role**: HSE Officer
- **Access**: Health & Safety operations

### 👷 Employee
- **Email**: `mike@hse.com`
- **Password**: `password`
- **Role**: Administrator (for testing)
- **Access**: Standard employee access

---

## Setup Instructions

### 1. Run Database Migrations
```bash
php artisan migrate
```

### 2. Seed Database with Users
```bash
php artisan db:seed --class=UserSeeder
```

Or seed all data:
```bash
php artisan db:seed
```

This will create:
- Default company: "HSE Management Demo"
- All roles and permissions
- 4 demo users with credentials above

---

## Login URL

Access the login page at:
```
http://localhost:8000/login
```

Or if using a custom domain:
```
http://your-domain.com/login
```

---

## Creating New Users

### Via Seeder (Development)
Edit `database/seeders/UserSeeder.php` and add new users to the `$users` array.

### Via Application (Production)
1. Login as Super Admin or Administrator
2. Navigate to Admin → Users
3. Click "Create User"
4. Fill in user details
5. Assign role and company
6. Set password

### Via Tinker (Quick Testing)
```bash
php artisan tinker
```

```php
$company = App\Models\Company::first();
$role = App\Models\Role::where('name', 'admin')->first();

$user = App\Models\User::create([
    'name' => 'New User',
    'email' => 'newuser@example.com',
    'password' => Hash::make('password'),
    'company_id' => $company->id,
    'role_id' => $role->id,
    'is_active' => true,
    'email_verified_at' => now(),
]);
```

---

## Password Requirements

- Minimum length: 8 characters (Laravel default)
- Can be changed in `config/auth.php`
- Passwords are hashed using bcrypt

---

## Security Notes

⚠️ **Important**: 
- Default passwords are for **development/testing only**
- Change all passwords in production
- Use strong, unique passwords
- Enable two-factor authentication if available

---

## Role Permissions

### Super Administrator
- Full system access
- Company management
- User management
- All modules

### Administrator
- Company administration
- User management (within company)
- All HSE modules
- Reports and analytics

### HSE Officer
- Incident management
- Toolbox talks
- Safety communications
- View reports

### Employee
- View assigned incidents
- Attend toolbox talks
- Receive safety communications
- Submit feedback

---

## Troubleshooting

### Cannot Login
1. Verify user exists: `php artisan tinker` → `User::where('email', 'admin@hse.com')->first()`
2. Check if user is active: `User::where('email', 'admin@hse.com')->first()->is_active`
3. Reset password: See "Reset Password" section below

### User Not Found
Run the seeder:
```bash
php artisan db:seed --class=UserSeeder
```

### Reset Password
```bash
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'admin@hse.com')->first();
$user->password = Hash::make('newpassword');
$user->save();
```

---

## Quick Access Commands

### List All Users
```bash
php artisan tinker
```
```php
App\Models\User::all(['name', 'email', 'is_active']);
```

### Create User via Command
```bash
php artisan tinker
```
```php
$user = new App\Models\User();
$user->name = 'Test User';
$user->email = 'test@example.com';
$user->password = Hash::make('password');
$user->company_id = 1;
$user->role_id = 1;
$user->is_active = true;
$user->email_verified_at = now();
$user->save();
```

---

*Last Updated: December 2025*

