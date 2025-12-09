# ZKTeco TCP Protocol Implementation Guide

## Current Status

✅ **Fixed:** Port 4370 connection now uses TCP socket instead of HTTP
✅ **Working:** Device connection and status check via TCP
⚠️ **Pending:** TCP-based enrollment and attendance sync

## TCP vs HTTP API

### Port 4370 (TCP Socket)
- Direct socket connection
- Uses ZKTeco proprietary protocol
- Requires specific command packets
- More complex but more reliable for older devices

### Port 80 (HTTP API)
- REST API endpoints
- JSON responses
- Easier to implement
- Available on newer ZKTeco devices with web interface

## Current Implementation

### ✅ Working Features (TCP)
1. **Connection Test** - `connectViaSocket()`
   - Uses `fsockopen()` to establish socket connection
   - Verifies device is reachable
   - Updates device status

2. **Status Check** - `getDeviceStatus()`
   - Detects port 4370 → uses TCP
   - Returns connection status

### ⚠️ Pending Features (TCP Protocol Needed)

1. **Attendance Logs** - `getAttendanceLogs()`
   - Currently returns empty array for TCP connections
   - Needs TCP protocol implementation
   - Alternative: Use bridge service or ZKTeco SDK

2. **Employee Enrollment** - `enrollEmployee()`
   - Currently returns false for TCP connections
   - Needs TCP protocol implementation
   - Alternative: Use ZKTeco software for enrollment

3. **User Sync** - `syncUsersToDevice()`
   - Depends on enrollment working
   - Needs TCP protocol implementation

4. **Check Enrollment Status** - `checkEnrollmentStatus()`
   - Currently uses `biometric_template_id` as indicator
   - Could be improved with TCP protocol query

## TCP Protocol Overview

ZKTeco devices use a binary protocol over TCP:
- Commands are sent as binary packets
- Responses are also binary
- Requires knowledge of packet structure
- Different commands for different operations

### Common TCP Commands
- `CMD_CONNECT` - Connect to device
- `CMD_ACK_OK` - Acknowledge success
- `CMD_ACK_ERROR` - Acknowledge error
- `CMD_GET_USER` - Get user list
- `CMD_SET_USER` - Set user (enrollment)
- `CMD_DELETE_USER` - Delete user
- `CMD_GET_ATTENDANCE` - Get attendance logs

## Implementation Options

### Option 1: Use ZKTeco PHP SDK
```php
// Example using zkteco/zkteco-php library
use ZKTeco\ZKTeco;

$zk = new ZKTeco('192.168.40.68', 4370);
$zk->connect();
$users = $zk->getUser();
$attendances = $zk->getAttendance();
```

### Option 2: Implement Custom TCP Protocol
- Create packet builder/parser
- Implement command structure
- Handle binary responses
- More complex but full control

### Option 3: Use Bridge Service (Recommended)
- Local PHP service handles TCP communication
- Cloud Laravel app uses HTTP API
- Already implemented in `ZKTecoBridgeController`
- Best for production environments

### Option 4: Hybrid Approach
- Use HTTP API when available (port 80)
- Use bridge service for TCP devices (port 4370)
- Automatic fallback

## Recommended Next Steps

### Immediate (For Testing)
1. ✅ Connection fix applied - Test device connection
2. ⏳ Verify TCP connection works
3. ⏳ Check device status updates correctly

### Short Term (For Basic Functionality)
1. **Install ZKTeco PHP SDK** (if available)
   ```bash
   composer require zkteco/zkteco-php
   ```
2. **Or use Bridge Service** - Already set up
   - Configure local bridge service
   - Point devices to bridge
   - Bridge handles TCP, sends data via API

### Long Term (For Full Features)
1. **Implement TCP Protocol** - Custom implementation
2. **Or Integrate SDK** - Use existing library
3. **Enhance Bridge Service** - Full TCP support

## Bridge Service Integration

The system already has bridge service support:

### API Endpoints (Already Created)
- `POST /api/zkteco/sync` - Receive sync data from bridge
- `POST /api/zkteco/heartbeat` - Bridge heartbeat
- `GET /api/zkteco/bridge-status` - Get bridge status

### Bridge Service Setup
1. Install bridge service on local server
2. Configure devices in bridge
3. Bridge syncs data to cloud via API
4. Cloud app receives data via `ZKTecoBridgeController`

## Testing Checklist

- [x] Fix port 4370 connection (TCP instead of HTTP)
- [ ] Test device connection via TCP
- [ ] Verify device status updates
- [ ] Test attendance sync (may need bridge or TCP protocol)
- [ ] Test enrollment (may need ZKTeco software or TCP protocol)
- [ ] Verify bridge service integration (if using)

## Notes

- **For Production:** Bridge service is recommended for TCP devices
- **For Development:** Can use ZKTeco software for enrollment/testing
- **For Full Control:** Implement TCP protocol or use SDK
- **Current Status:** Connection works, data sync needs TCP protocol or bridge

---

**Status:** ✅ Connection Fixed | ⚠️ TCP Protocol Needed for Full Features

