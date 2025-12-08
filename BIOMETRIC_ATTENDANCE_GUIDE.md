# Biometric Attendance System Guide

## Overview

The HSE Management System integrates with **ZKTeco K40** fingerprint devices to automatically track attendance for toolbox talks, training sessions, and other safety activities. This ensures accurate, tamper-proof attendance records.

---

## How It Works

### 1. **Device Integration**

The system connects to ZKTeco K40 biometric devices via:
- **HTTP API** (primary method) - Port 80
- **TCP Socket** (fallback) - Port 4370

**Configuration** (in `.env` or `config/services.php`):
```env
ZKTECO_DEVICE_IP=192.168.1.201
ZKTECO_PORT=4370
ZKTECO_API_KEY=your_api_key
```

### 2. **User Enrollment Process**

#### Step 1: Sync Users to Device
- System syncs all active users from the database to the biometric device
- Each user gets a unique **biometric template ID** stored on the device
- Template ID is linked to the user's `employee_id_number` in the system

#### Step 2: Fingerprint Enrollment
- Users place their finger on the device scanner
- Device captures and stores fingerprint template
- Template is encrypted and stored securely on the device
- System stores `biometric_template_id` in user record

**Code Example:**
```php
$zkService = new ZKTecoService();
$zkService->enrollFingerprint($user);
```

### 3. **Attendance Tracking Flow**

#### For Toolbox Talks:

1. **Talk Setup**
   - Supervisor creates a toolbox talk
   - Sets `biometric_required = true`
   - Specifies location (latitude/longitude) if needed
   - Links to ZKTeco device via `zk_device_id`

2. **During the Talk**
   - Employees scan their fingerprint on the device
   - Device records:
     - Timestamp
     - Template ID (fingerprint)
     - Device ID
     - Location (if GPS-enabled device)

3. **Automatic Processing**
   - System retrieves attendance logs from device
   - Time window: 30 minutes before talk start to talk end time
   - Matches logs to users by:
     - `biometric_template_id` (primary)
     - `employee_id_number` (fallback)
     - Card number (if available)

4. **Attendance Record Creation**
   - Creates `ToolboxTalkAttendance` record with:
     - Employee details
     - Check-in time (from device)
     - Check-in method: `biometric`
     - Device ID
     - Location coordinates
     - Attendance status: `present`

**Code Example:**
```php
$zkService = new ZKTecoService();
$results = $zkService->processToolboxTalkAttendance($toolboxTalk);
```

### 4. **Attendance Methods**

The system supports multiple check-in methods:

| Method | Description | Use Case |
|--------|-------------|----------|
| **Biometric** | Fingerprint scan | Primary method, most secure |
| **Manual** | Supervisor entry | Backup when device unavailable |
| **Mobile App** | Mobile check-in | Remote or field locations |
| **Video Conference** | Virtual attendance | Online sessions |

### 5. **Data Stored**

Each attendance record includes:

```php
[
    'toolbox_talk_id' => 1,
    'employee_id' => 123,
    'employee_name' => 'John Doe',
    'employee_id_number' => 'EMP-001',
    'attendance_status' => 'present', // present, absent, late, excused
    'check_in_time' => '2025-12-07 09:00:00',
    'check_in_method' => 'biometric',
    'biometric_template_id' => 'TMP-12345',
    'device_id' => '192.168.1.201',
    'check_in_latitude' => -6.7924,
    'check_in_longitude' => 39.2083,
    'is_supervisor' => false,
    'is_presenter' => false,
]
```

---

## Key Features

### ✅ Automatic Attendance Processing

- **Time Window Matching**: Captures attendance 30 minutes before talk start
- **Duplicate Prevention**: Prevents double-counting same attendance
- **User Matching**: Intelligent matching by template ID, employee ID, or card number

### ✅ Real-Time Updates

- Attendance statistics update automatically
- Attendance rate calculated: `(present / total) * 100`
- Dashboard reflects current attendance status

### ✅ Location Tracking

- GPS coordinates stored (if device supports)
- Useful for compliance and audit trails
- Helps verify attendance at correct location

### ✅ Audit Trail

- Complete log of all attendance events
- Device ID and timestamp recorded
- Cannot be tampered with (device-level security)

---

## Usage Examples

### 1. Enable Biometric for a Toolbox Talk

```php
$talk = ToolboxTalk::create([
    'title' => 'Safety Briefing',
    'biometric_required' => true,
    'zk_device_id' => '192.168.1.201',
    'latitude' => -6.7924,
    'longitude' => 39.2083,
    // ... other fields
]);
```

### 2. Process Attendance After Talk

```php
$zkService = new ZKTecoService();
$results = $zkService->processToolboxTalkAttendance($talk);

// Results:
// [
//     'processed' => 25,
//     'new_attendances' => 20,
//     'errors' => []
// ]
```

### 3. Check Device Status

```php
$zkService = new ZKTecoService();
$status = $zkService->getDeviceStatus();

// Returns:
// [
//     'status' => 'online',
//     'device_type' => 'ZKTeco K40',
//     'ip' => '192.168.1.201'
// ]
```

### 4. Sync All Users to Device

```php
$zkService = new ZKTecoService();
$results = $zkService->syncUsers();

// Results:
// [
//     'success' => 150,
//     'failed' => 2,
//     'errors' => ['Failed to enroll user: John Doe']
// ]
```

---

## Configuration

### Device Setup

1. **Connect Device to Network**
   - Ensure ZKTeco K40 is on same network as server
   - Note the device IP address (default: 192.168.1.201)

2. **Configure Device**
   - Enable HTTP API (if supported)
   - Set API key (if required)
   - Configure time sync

3. **Update System Configuration**

   **Option A: Environment Variables** (`.env`):
   ```env
   ZKTECO_DEVICE_IP=192.168.1.201
   ZKTECO_PORT=4370
   ZKTECO_API_KEY=your_api_key_here
   ```

   **Option B: Config File** (`config/services.php`):
   ```php
   'zkteco' => [
       'device_ip' => env('ZKTECO_DEVICE_IP', '192.168.1.201'),
       'port' => env('ZKTECO_PORT', 4370),
       'api_key' => env('ZKTECO_API_KEY'),
   ],
   ```

### User Enrollment

1. **Automatic Sync**
   - Run: `php artisan zkteco:sync-users`
   - Or use: `$zkService->syncUsers()`

2. **Manual Enrollment**
   - User places finger on device
   - System calls: `$zkService->enrollFingerprint($user)`
   - Template ID stored in user record

---

## Troubleshooting

### Device Not Connecting

**Symptoms:**
- "Connection failed" errors
- Device status shows "offline"

**Solutions:**
1. Check device IP address is correct
2. Verify device is on same network
3. Test connection: `$zkService->testConnection()`
4. Check firewall allows port 4370 or 80
5. Try HTTP API first, then socket fallback

### Users Not Found

**Symptoms:**
- Attendance logs show "User not found"

**Solutions:**
1. Ensure users are synced to device: `syncUsers()`
2. Verify `employee_id_number` matches device records
3. Check `biometric_template_id` is set in user record
4. Re-enroll user if needed

### Attendance Not Processing

**Symptoms:**
- Attendance logs exist but not imported

**Solutions:**
1. Check time window (30 min before to end time)
2. Verify `biometric_required = true` on talk
3. Run manual processing: `processToolboxTalkAttendance($talk)`
4. Check for duplicate prevention (already processed)

### Fingerprint Not Recognized

**Symptoms:**
- Device doesn't recognize user

**Solutions:**
1. Re-enroll fingerprint on device
2. Ensure finger is clean and dry
3. Try different finger (multiple templates allowed)
4. Check device sensor is clean

---

## Security & Privacy

### Data Protection

- **Fingerprint Templates**: Encrypted on device
- **Template IDs**: Stored securely, not actual fingerprints
- **API Keys**: Protected in environment variables
- **Audit Trail**: Complete logging of all operations

### Compliance

- **GDPR**: Personal data handled securely
- **Local Regulations**: Complies with data protection laws
- **Access Control**: Only authorized users can access attendance data

---

## API Reference

### ZKTecoService Methods

| Method | Description | Returns |
|--------|-------------|---------|
| `connect()` | Connect to device | `bool` |
| `getUsers()` | Get all users from device | `array` |
| `getAttendanceLogs($from, $to)` | Get attendance logs | `array` |
| `enrollFingerprint($user)` | Enroll user fingerprint | `bool` |
| `deleteUser($user)` | Remove user from device | `bool` |
| `syncUsers()` | Sync all users to device | `array` |
| `processToolboxTalkAttendance($talk)` | Process talk attendance | `array` |
| `getDeviceStatus()` | Check device status | `array` |
| `testConnection()` | Test connectivity | `array` |

---

## Best Practices

1. **Regular Sync**: Sync users to device regularly (weekly/monthly)
2. **Device Maintenance**: Clean device sensor regularly
3. **Backup Records**: Keep attendance logs backed up
4. **Monitor Status**: Check device status before important talks
5. **Fallback Method**: Always have manual entry as backup
6. **Time Sync**: Ensure device time is synchronized with server
7. **Network Stability**: Use stable network connection for device

---

## Support

For issues or questions:
- Check logs: `storage/logs/laravel.log`
- Test connection: `php artisan zkteco:test`
- Review device documentation: ZKTeco K40 manual
- Contact system administrator

---

## Summary

The biometric attendance system provides:
- ✅ **Accurate** attendance tracking
- ✅ **Tamper-proof** records
- ✅ **Automatic** processing
- ✅ **Real-time** updates
- ✅ **Compliance** ready
- ✅ **Secure** data handling

This ensures reliable attendance tracking for safety compliance and audit purposes.

