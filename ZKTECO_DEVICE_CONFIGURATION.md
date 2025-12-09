# ZKTeco Device Configuration - HESU

## Device License Information

**Software Version:** 9.0.1 (Build:20240617.19506)  
**Customer Code:** 138533  
**Authorized Company:** HESU  
**Authorized Date:** September 24, 2024  
**Expiry Date:** Permanent  

### License Limits
- **Device Count:** 3/10 (3 devices authorized, 10 maximum)
- **Employee Count:** 475/1000 (475 employees authorized, 1000 maximum)
- **APP Count:** 0/1

## Device Configuration

### Supported Device Types
- ZKTeco K40 (Primary)
- ZKTeco K50
- Other ZKTeco models with Software Version 9.0.1+

### Connection Methods
1. **HTTP API** (Primary) - Port 80
2. **TCP Socket** (Fallback) - Port 4370

### Network Configuration
Each device should be configured with:
- Static IP address (recommended)
- Port: 4370 (TCP) or 80 (HTTP)
- Network connectivity to the server

## Integration Steps

### 1. Device Setup in System

For each of your 3 devices, add them to the system:

1. Go to **Biometric Attendance → Devices**
2. Click **Add Device**
3. Enter device details:
   - **Device Name:** e.g., "Site A - Main Gate"
   - **Serial Number:** From device label
   - **Device Type:** ZKTeco K40 (or your model)
   - **IP Address:** Device's IP on network
   - **Port:** 4370 (or 80 for HTTP)
   - **Location:** Physical location name
   - **Company:** HESU

### 2. Test Connection

After adding each device:
1. Click **Test Connection** button
2. Verify device responds
3. Check device status shows "Active"

### 3. Sync Employees

Since you have 475 employees authorized:
1. Go to device details page
2. Click **Sync Users** button
3. System will enroll all active employees to the device
4. Employees will need to scan their fingerprint on the device

### 4. Configure Work Hours

Set work hours for each device:
- **Start Time:** e.g., 08:00
- **End Time:** e.g., 17:00
- **Grace Period:** 15 minutes (recommended)

## API Compatibility

The system uses HTTP API calls compatible with ZKTeco Software Version 9.0.1:

### Endpoints Used
- `GET /api/test` - Connection test
- `GET /api/status` - Device status
- `GET /api/users` - Get enrolled users
- `GET /api/attendance` - Get attendance logs
- `POST /api/enroll` - Enroll user fingerprint
- `DELETE /api/users/{id}` - Remove user

### API Key (if required)
If your devices require API authentication:
1. Get API key from device settings
2. Enter in device configuration form
3. System will include in all API requests

## Device Limits

### Current Usage
- **Devices:** 3 configured (can add up to 7 more)
- **Employees:** 475 authorized (can add up to 525 more)

### Recommendations
1. **Device Distribution:**
   - Site A: Main entrance device
   - Site B: Secondary location device
   - Site C: Third location device

2. **Employee Enrollment:**
   - Enroll all 475 employees to all 3 devices
   - Or assign specific employees to specific devices based on location

## Troubleshooting

### Device Not Connecting
1. Verify IP address is correct
2. Check device is on same network or accessible via VPN
3. Test with `ping` command
4. Verify firewall allows port 4370/80
5. Check device web interface is accessible

### Employees Not Syncing
1. Verify employee count doesn't exceed 1000 limit
2. Check device has available slots
3. Ensure employees have `biometric_template_id` in system
4. Try syncing one device at a time

### Attendance Not Recording
1. Verify `daily_attendance_enabled` is checked
2. Check auto-sync is enabled
3. Verify work hours are configured correctly
4. Check device time is synchronized

## Maintenance

### Regular Tasks
1. **Daily:** Check device status (auto-checked every 5 minutes)
2. **Weekly:** Review attendance sync logs
3. **Monthly:** Verify all employees are enrolled

### Device Updates
- Software updates should be done carefully
- Test connection after updates
- Verify API compatibility maintained

## Support

For ZKTeco device-specific issues:
- Check device web interface: `http://[device-ip]`
- Review device logs
- Contact ZKTeco support if needed

For system integration issues:
- Check application logs: `storage/logs/laravel.log`
- Review device sync results in device details page
- Test connection manually via device page

