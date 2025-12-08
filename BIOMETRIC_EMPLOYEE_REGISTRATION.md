# Biometric Employee Registration Guide

## Overview

This guide explains how to register employees for biometric attendance using ZKTeco K40 fingerprint devices. Registration involves enrolling employees' fingerprints on the device so they can use biometric check-in for toolbox talks and training sessions.

---

## Registration Methods

### Method 1: Bulk Registration (All Employees)

Register all active employees at once:

#### Using Artisan Command:
```bash
php artisan biometric:enroll-all
```

#### Using Service Directly:
```php
use App\Services\ZKTecoService;

$zkService = new ZKTecoService();
$results = $zkService->syncUsers();

// Results:
// [
//     'success' => 150,
//     'failed' => 2,
//     'errors' => ['Failed to enroll user: John Doe']
// ]
```

**What it does:**
- Gets all active users from your company
- Sends enrollment request to biometric device
- Device prompts each employee to scan their fingerprint
- Stores template ID in employee record

---

### Method 2: Individual Employee Registration

Register a single employee:

#### Using Artisan Command:
```bash
php artisan biometric:enroll {employee_id}
```

#### Using Service Directly:
```php
use App\Services\ZKTecoService;
use App\Models\User;

$user = User::find(1); // or User::where('employee_id_number', 'EMP-001')->first();
$zkService = new ZKTecoService();

if ($zkService->enrollFingerprint($user)) {
    echo "✅ Employee enrolled successfully!";
} else {
    echo "❌ Enrollment failed. Check device connection.";
}
```

---

### Method 3: Register by Employee ID Number

```bash
php artisan biometric:enroll-by-id EMP-001
```

---

## Step-by-Step Registration Process

### Prerequisites

1. **Device Setup**
   - ZKTeco K40 device connected to network
   - Device IP configured in `.env`:
     ```env
     ZKTECO_DEVICE_IP=192.168.1.201
     ZKTECO_PORT=4370
     ZKTECO_API_KEY=your_api_key
     ```

2. **Employee Data**
   - Employee must exist in system
   - Employee must have `employee_id_number`
   - Employee must be active (`is_active = true`)

### Registration Steps

#### Step 1: Test Device Connection

```bash
php artisan biometric:test-device
```

Expected output:
```
✅ Device connected successfully!
Device IP: 192.168.1.201
Response Time: 45ms
Status: Online
```

#### Step 2: Start Enrollment

**For Single Employee:**
```bash
php artisan biometric:enroll 1
```

**For All Employees:**
```bash
php artisan biometric:enroll-all
```

#### Step 3: Physical Fingerprint Scan

When enrollment starts:
1. Device will display employee name or ID
2. Employee places finger on scanner
3. Device captures fingerprint (may require 2-3 scans)
4. Device confirms enrollment success
5. System stores `biometric_template_id` in employee record

#### Step 4: Verify Registration

Check if employee is registered:
```bash
php artisan biometric:check {employee_id}
```

Or in code:
```php
$user = User::find(1);
if ($user->biometric_template_id) {
    echo "✅ Registered (Template ID: {$user->biometric_template_id})";
} else {
    echo "❌ Not registered";
}
```

---

## Registration Workflow

```
┌─────────────────┐
│  Start Enrollment│
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│ Connect to Device│
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│ Send Employee Data│
│ (ID, Name, Card #)│
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│ Device Ready    │
│ (Waiting for    │
│  Fingerprint)   │
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│ Employee Scans  │
│   Fingerprint   │
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│ Device Captures │
│  & Encrypts     │
│   Template      │
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│ Device Returns  │
│  Template ID    │
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│ System Stores   │
│ Template ID in  │
│ Employee Record │
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│ ✅ Registration │
│    Complete     │
└─────────────────┘
```

---

## Artisan Commands

### 1. Enroll All Employees

```bash
php artisan biometric:enroll-all
```

**Options:**
- `--company={id}` - Enroll only employees from specific company
- `--department={id}` - Enroll only employees from specific department
- `--force` - Re-enroll even if already registered

**Example:**
```bash
php artisan biometric:enroll-all --company=1 --force
```

### 2. Enroll Single Employee

```bash
php artisan biometric:enroll {employee_id}
```

**Example:**
```bash
php artisan biometric:enroll 123
```

### 3. Enroll by Employee ID Number

```bash
php artisan biometric:enroll-by-id {employee_id_number}
```

**Example:**
```bash
php artisan biometric:enroll-by-id EMP-001
```

### 4. Check Registration Status

```bash
php artisan biometric:check {employee_id}
```

**Output:**
```
Employee: John Doe (EMP-001)
Status: ✅ Registered
Template ID: TMP-12345
Registered At: 2025-12-07 10:30:00
```

### 5. Test Device Connection

```bash
php artisan biometric:test-device
```

### 6. List Registered Employees

```bash
php artisan biometric:list-registered
```

### 7. List Unregistered Employees

```bash
php artisan biometric:list-unregistered
```

### 8. Remove Employee from Device

```bash
php artisan biometric:remove {employee_id}
```

**Warning:** This removes the employee from the device but keeps the record in the system.

---

## Using the Service Class

### Enroll Single Employee

```php
use App\Services\ZKTecoService;
use App\Models\User;

$zkService = new ZKTecoService();
$user = User::find(1);

try {
    if ($zkService->enrollFingerprint($user)) {
        // Success - employee enrolled
        $user->refresh();
        echo "Template ID: " . $user->biometric_template_id;
    } else {
        // Failed - check device connection
        echo "Enrollment failed";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Enroll Multiple Employees

```php
use App\Services\ZKTecoService;
use App\Models\User;

$zkService = new ZKTecoService();
$employees = User::where('company_id', 1)
                 ->where('is_active', true)
                 ->get();

$results = [
    'success' => 0,
    'failed' => 0,
    'errors' => []
];

foreach ($employees as $employee) {
    try {
        if ($zkService->enrollFingerprint($employee)) {
            $results['success']++;
        } else {
            $results['failed']++;
            $results['errors'][] = "Failed: {$employee->name}";
        }
    } catch (\Exception $e) {
        $results['failed']++;
        $results['errors'][] = "Error: {$employee->name} - " . $e->getMessage();
    }
}

echo "Success: {$results['success']}\n";
echo "Failed: {$results['failed']}\n";
```

### Check Registration Status

```php
$user = User::find(1);

if ($user->biometric_template_id) {
    echo "✅ Registered";
    echo "Template ID: {$user->biometric_template_id}";
} else {
    echo "❌ Not registered";
}
```

---

## Best Practices

### 1. **Pre-Registration Checklist**

- [ ] Device is connected and online
- [ ] Device time is synchronized
- [ ] Employee has valid `employee_id_number`
- [ ] Employee is active in system
- [ ] Device sensor is clean

### 2. **During Registration**

- [ ] Ensure employee's finger is clean and dry
- [ ] Use index or middle finger (most reliable)
- [ ] Scan 2-3 times for better accuracy
- [ ] Wait for device confirmation before next employee

### 3. **After Registration**

- [ ] Verify template ID is stored
- [ ] Test fingerprint recognition
- [ ] Document any issues
- [ ] Update employee record if needed

### 4. **Maintenance**

- [ ] Re-enroll employees with recognition issues
- [ ] Clean device sensor regularly
- [ ] Sync new employees monthly
- [ ] Remove terminated employees from device

---

## Troubleshooting

### Device Not Responding

**Symptoms:**
- Connection timeout
- "Device not found" error

**Solutions:**
1. Check device IP address in `.env`
2. Verify device is on same network
3. Test connection: `php artisan biometric:test-device`
4. Check firewall settings
5. Restart device if needed

### Enrollment Fails

**Symptoms:**
- Enrollment returns false
- No template ID stored

**Solutions:**
1. Check device is in enrollment mode
2. Verify employee data is correct
3. Ensure device has storage space
4. Try re-enrolling
5. Check device logs

### Fingerprint Not Captured

**Symptoms:**
- Device doesn't respond to scan
- "Scan failed" message

**Solutions:**
1. Clean device sensor
2. Ensure finger is clean and dry
3. Try different finger
4. Press finger firmly on sensor
5. Wait for device ready signal

### Template ID Not Stored

**Symptoms:**
- Enrollment succeeds but no template ID

**Solutions:**
1. Check database connection
2. Verify employee record exists
3. Check for database errors in logs
4. Manually update template ID if needed

---

## Data Storage

### Employee Record Fields

After successful registration, these fields are updated:

```php
$user->biometric_template_id = 'TMP-12345'; // Template ID from device
$user->save();
```

### Device Storage

- Fingerprint templates encrypted on device
- Template IDs linked to employee ID numbers
- Templates cannot be extracted (security feature)

---

## Security Considerations

1. **Template Encryption**: Fingerprints encrypted on device
2. **No Raw Data**: System only stores template IDs, not actual fingerprints
3. **Access Control**: Only authorized users can enroll employees
4. **Audit Trail**: All enrollment actions logged
5. **Data Privacy**: Complies with GDPR and local regulations

---

## API Reference

### ZKTecoService Methods

| Method | Description | Parameters | Returns |
|--------|-------------|------------|---------|
| `enrollFingerprint($user)` | Enroll single employee | `User $user` | `bool` |
| `syncUsers()` | Enroll all active users | None | `array` |
| `deleteUser($user)` | Remove from device | `User $user` | `bool` |
| `testConnection()` | Test device connection | None | `array` |
| `getDeviceStatus()` | Get device status | None | `array` |

---

## Example Scenarios

### Scenario 1: New Employee Onboarding

```bash
# 1. Create employee in system
# 2. Test device connection
php artisan biometric:test-device

# 3. Enroll new employee
php artisan biometric:enroll-by-id EMP-123

# 4. Verify registration
php artisan biometric:check 123
```

### Scenario 2: Bulk Registration for New Company

```bash
# 1. Test device
php artisan biometric:test-device

# 2. Enroll all employees
php artisan biometric:enroll-all --company=1

# 3. Check results
php artisan biometric:list-unregistered --company=1
```

### Scenario 3: Re-enroll Employee with Issues

```bash
# 1. Remove from device
php artisan biometric:remove 123

# 2. Re-enroll
php artisan biometric:enroll 123 --force

# 3. Test recognition
# (Have employee scan fingerprint on device)
```

---

## Summary

Biometric employee registration involves:

1. ✅ **Device Setup** - Connect and configure ZKTeco device
2. ✅ **Employee Data** - Ensure employee exists in system
3. ✅ **Enrollment** - Scan fingerprint on device
4. ✅ **Verification** - Confirm template ID stored
5. ✅ **Testing** - Verify fingerprint recognition works

Once registered, employees can use biometric check-in for:
- Toolbox talks
- Training sessions
- Safety briefings
- Any activity requiring attendance

---

## Support

For issues or questions:
- Check logs: `storage/logs/laravel.log`
- Test device: `php artisan biometric:test-device`
- Review device manual: ZKTeco K40 documentation
- Contact system administrator

