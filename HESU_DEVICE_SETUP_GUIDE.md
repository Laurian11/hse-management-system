# HESU ZKTeco Device Setup Guide

## Your Device Information

**Software Version:** 9.0.1 (Build:20240617.19506)  
**Customer Code:** 138533  
**Authorized Company:** HESU  
**License Status:** Permanent (No expiry)  

### Current License Usage
- ✅ **Devices:** 3/10 (You can add 7 more devices)
- ✅ **Employees:** 475/1000 (You can add 525 more employees)
- ✅ **APP Count:** 0/1

## Quick Setup Steps

### Step 1: Add Your 3 Devices

For each of your 3 ZKTeco devices:

1. **Get Device IP Address**
   - Access device web interface: `http://[device-ip]`
   - Or check device network settings
   - Note the IP address (e.g., `192.168.1.201`)

2. **Add Device to System**
   - Go to: **Biometric Attendance → Devices → Add Device**
   - Fill in:
     - **Device Name:** e.g., "Site A - Main Entrance"
     - **Serial Number:** From device label/sticker
     - **Device Type:** ZKTeco K40 (or your model)
     - **Company:** HESU
     - **Location Name:** e.g., "Site A", "Building B", etc.
     - **IP Address:** Device's IP (e.g., `192.168.1.201`)
     - **Port:** 
       - `80` if using HTTP API (recommended for v9.0.1+)
       - `4370` if using TCP Socket
     - **Connection Type:** "Both (HTTP & TCP)" (recommended)
     - **Work Start Time:** e.g., `08:00`
     - **Work End Time:** e.g., `17:00`
     - **Grace Period:** `15` minutes

3. **Test Connection**
   - Click **Test Connection** button
   - Should show "Device is online and connected!"

### Step 2: Sync Employees

Since you have 475 employees authorized:

1. **For Each Device:**
   - Go to device details page
   - Click **Sync Users** button
   - System will attempt to enroll all active employees

2. **Employee Enrollment Process:**
   - System sends enrollment request to device
   - Employee needs to scan fingerprint on device
   - Device stores fingerprint template
   - System links template to employee record

3. **Bulk Enrollment:**
   - You can sync all 475 employees to all 3 devices
   - Or assign specific employees to specific devices based on location

### Step 3: Configure Auto-Sync

1. **Enable Auto-Sync:**
   - On each device, ensure "Enable Auto Sync" is checked
   - Set sync interval: `5` minutes (recommended)

2. **Enable Daily Attendance:**
   - Check "Enable Daily Attendance Tracking" on each device
   - This allows automatic check-in/check-out recording

3. **System Will:**
   - Sync attendance every 5 minutes automatically
   - Full sync at end of day (11:55 PM)
   - Record check-in and check-out times
   - Calculate work hours, overtime, late minutes

## Device Configuration Examples

### Device 1: Site A - Main Entrance
```
Device Name: Site A - Main Entrance
IP Address: 192.168.1.201
Port: 80 (HTTP API)
Location: Site A
Work Hours: 08:00 - 17:00
Grace Period: 15 minutes
```

### Device 2: Site B - Secondary Gate
```
Device Name: Site B - Secondary Gate
IP Address: 192.168.1.202
Port: 80 (HTTP API)
Location: Site B
Work Hours: 08:00 - 17:00
Grace Period: 15 minutes
```

### Device 3: Site C - Warehouse
```
Device Name: Site C - Warehouse
IP Address: 192.168.1.203
Port: 80 (HTTP API)
Location: Site C
Work Hours: 08:00 - 17:00
Grace Period: 15 minutes
```

## API Key (If Required)

If your devices require API authentication:

1. **Get API Key:**
   - Access device web interface
   - Go to Settings → API Configuration
   - Generate or copy API key

2. **Add to Device:**
   - In device configuration form
   - Paste API key in "API Key" field
   - System will include in all API requests

## Testing

### Test Device Connection
```bash
# From server, test if device is reachable
ping 192.168.1.201

# Test HTTP API
curl http://192.168.1.201/api/test

# Or use system's test button
```

### Test Employee Enrollment
1. Go to device details page
2. Click "Sync Users"
3. Check results:
   - Success: Number of employees enrolled
   - Failed: List of employees that failed

### Test Attendance Sync
1. Have an employee scan fingerprint on device
2. Wait 5 minutes (or manually sync)
3. Go to Daily Attendance page
4. Verify attendance record appears

## Troubleshooting

### Device Not Connecting
- ✅ Check IP address is correct
- ✅ Verify device is on same network
- ✅ Test with browser: `http://[device-ip]`
- ✅ Check firewall allows port 80/4370
- ✅ Verify device is powered on

### Employees Not Syncing
- ✅ Check employee count doesn't exceed 1000
- ✅ Verify employees are active in system
- ✅ Check device has available slots
- ✅ Try syncing one device at a time

### Attendance Not Recording
- ✅ Verify "Daily Attendance Enabled" is checked
- ✅ Check "Auto Sync Enabled" is checked
- ✅ Verify work hours are configured
- ✅ Check device time is synchronized
- ✅ Review sync logs in device details

## Monitoring

### Daily Checks
- View attendance dashboard: `/daily-attendance/dashboard`
- Check device status: All devices should show "Active"
- Review today's attendance: Present, Late, Absent counts

### Weekly Reviews
- Generate weekly report: `/manpower-reports/weekly`
- Review attendance trends
- Check for any sync errors

### Monthly Reports
- Generate monthly report: `/manpower-reports/monthly`
- Export to Excel/PDF
- Analyze attendance patterns

## Support

### System Logs
- Application logs: `storage/logs/laravel.log`
- Device sync logs: Visible in device details page
- Attendance sync results: Shown after each sync

### Device Web Interface
- Access: `http://[device-ip]`
- View device status, users, attendance logs
- Configure device settings

## Next Steps

1. ✅ Add all 3 devices to system
2. ✅ Test connection for each device
3. ✅ Sync employees to devices
4. ✅ Verify attendance recording works
5. ✅ Set up monitoring and reports

Your system is ready to track attendance for all 475 employees across 3 locations!

