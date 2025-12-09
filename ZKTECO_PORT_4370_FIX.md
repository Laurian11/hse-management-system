# ZKTeco Port 4370 Connection Fix

## Issue
Connection timeout error when trying to connect to ZKTeco device on port 4370:
```
cURL error 28: Connection timed out after 5004 milliseconds 
for http://192.168.40.68:4370/api/status
```

## Root Cause
The code was attempting to use **HTTP API** on port **4370**, but:
- **Port 4370** = **TCP Socket** connection (not HTTP)
- **Port 80** = **HTTP API** connection

ZKTeco devices use different protocols:
- **TCP Socket (Port 4370)**: Direct socket connection for device communication
- **HTTP API (Port 80)**: REST API for newer devices with web interface

## Solution
Updated `MultiDeviceZKTecoService` to:
1. **Detect port 4370** and use TCP socket connection directly (skip HTTP)
2. **Only use HTTP API** for port 80 or when `connection_type` is 'http' or 'both'
3. **Fallback logic**: Try HTTP first (if applicable), then TCP if HTTP fails

## Changes Made

### 1. `connectToDevice()` Method
- Checks if port is 4370 → uses TCP socket directly
- Only attempts HTTP for port 80 or when connection_type allows it
- Proper fallback to TCP if HTTP fails

### 2. `getDeviceStatus()` Method
- Detects port 4370 → uses TCP socket connection
- Returns appropriate status for TCP connections
- Only uses HTTP API for port 80

### 3. `getAttendanceLogs()` Method
- Skips HTTP API for port 4370 (TCP only)
- Logs warning when HTTP API is not available

### 4. `enrollEmployee()` Method
- Detects port 4370 → logs warning (HTTP enrollment not available)
- TODO: Implement TCP-based enrollment protocol if needed

### 5. `checkEnrollmentStatus()` Method
- For TCP connections, checks `biometric_template_id` as enrollment indicator
- Only uses HTTP API for port 80

### 6. `removeEmployee()` Method
- For TCP connections, clears local `biometric_template_id` reference
- Only uses HTTP API for port 80

## Device Configuration

For your 3 ZKTeco devices:

### Device 1: CFS Warehouse (192.168.40.68:4370)
- **Port:** 4370 (TCP)
- **Connection Type:** `tcp` or `both`
- **Status:** ✅ Fixed - Now uses TCP socket connection

### Device 2: HESU ICD
- Check if it uses port 80 (HTTP) or 4370 (TCP)
- Configure accordingly

### Device 3: CFS Office
- Check if it uses port 80 (HTTP) or 4370 (TCP)
- Configure accordingly

## Testing

1. **Test Connection:**
   - Go to device details page
   - Click "Test Connection"
   - Should now connect successfully via TCP socket

2. **Verify Status:**
   - Device status should show "online" when TCP connection succeeds
   - Connection type will be indicated in status response

## Notes

- **TCP Socket Connection**: Uses `fsockopen()` to establish direct socket connection
- **HTTP API**: Only available on port 80 for devices with web interface
- **Enrollment**: For port 4370 devices, enrollment may need to be done via ZKTeco software or implement TCP protocol
- **Attendance Sync**: For port 4370, attendance data may need to be synced via TCP protocol or bridge service

## Next Steps

1. ✅ Connection fix applied
2. ⏳ Test connection to device at 192.168.40.68:4370
3. ⏳ Verify attendance sync works (may need TCP protocol implementation)
4. ⏳ Consider implementing TCP-based enrollment if needed

---

**Status:** ✅ Fixed - Port 4370 now uses TCP socket connection instead of HTTP

