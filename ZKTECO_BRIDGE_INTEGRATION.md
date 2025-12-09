# ZKTeco Bridge Integration - Complete Guide

## Overview

This document describes the integration of your three ZKTeco devices with the HSE Management System using a hybrid approach (direct connection + optional bridge service).

## Your Device Configuration

### Device 1: CFS Warehouse
- **Name:** CFS Warehouse
- **Serial Number:** AEWD233960244
- **IP Address:** 192.168.60.251
- **Port:** 4370 (TCP) / 80 (HTTP)
- **Area:** CFS (Warehouse)
- **Location:** Warehouse area

### Device 2: HESU ICD (Main Device)
- **Name:** HESU ICD
- **Serial Number:** AEWD233960257
- **IP Address:** 192.168.40.68
- **Port:** 4370 (TCP) / 80 (HTTP)
- **Area:** ICD
- **Location:** ICD area (main device)

### Device 3: CFS Office
- **Name:** CFS Office
- **Serial Number:** ZTC8235000106
- **IP Address:** 192.168.40.201
- **Port:** 4370 (TCP) / 80 (HTTP)
- **Area:** CFS
- **Location:** Additional CFS device

## Integration Methods

### Method 1: Direct Connection (Primary - Already Implemented)

The system can connect directly to your devices using the `MultiDeviceZKTecoService`:

1. **Add Devices:**
   - Go to Settings → Biometric Attendance → Devices
   - Click "Add New Device"
   - Enter device details (IP, port, location)
   - Test connection

2. **Sync Employees:**
   - Use "Sync Users" button on each device
   - Maps employees to device user IDs

3. **Sync Attendance:**
   - Automatic sync every 5 minutes (via scheduled task)
   - Manual sync via "Sync Attendance" button

### Method 2: Bridge Service (Optional - For Offline Scenarios)

A local bridge service can be deployed on your local network to:
- Collect data when cloud is unavailable
- Queue data for later sync
- Provide redundancy

## API Endpoints

### Bridge Communication Endpoints

All endpoints require authentication via Sanctum token.

#### 1. Receive Sync Data
```
POST /api/zkteco/sync
Content-Type: application/json
Authorization: Bearer {token}
X-Bridge-Key: {bridge_key}

{
    "device": {
        "name": "CFS Warehouse",
        "serial": "AEWD233960244",
        "ip": "192.168.60.251",
        "area": "CFS"
    },
    "type": "attendance",
    "data": [
        {
            "user_id": 1,
            "timestamp": "2025-12-08 08:15:30",
            "verify_type": 1,
            "biometric_template_id": "12345",
            "device_log_id": "67890"
        }
    ],
    "bridge_id": "hesu_bridge_001",
    "timestamp": "2025-12-08T08:15:30Z"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Sync processed successfully",
    "device": "CFS Warehouse",
    "processed": 10,
    "timestamp": "2025-12-08T08:15:35Z"
}
```

#### 2. Bridge Heartbeat
```
POST /api/zkteco/heartbeat
Content-Type: application/json
Authorization: Bearer {token}
X-Bridge-Key: {bridge_key}

{
    "bridge_id": "hesu_bridge_001",
    "status": "online",
    "devices": [
        {
            "name": "CFS Warehouse",
            "ip": "192.168.60.251",
            "last_check": "2025-12-08T08:15:30Z"
        }
    ],
    "timestamp": "2025-12-08T08:15:30Z"
}
```

#### 3. Get Bridge Status
```
GET /api/zkteco/bridge-status
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "bridges": {
        "hesu_bridge_001": {
            "status": "online",
            "last_seen": "2025-12-08T08:15:30Z",
            "devices": [...]
        }
    }
}
```

## Environment Configuration

Add these to your `.env` file:

```env
# Bridge Configuration
BRIDGE_KEY_001=your-secure-bridge-key-here
BRIDGE_KEY_002=your-second-bridge-key-here

# API Token (for bridge authentication)
API_TOKEN=your-laravel-api-token
```

## Local Bridge Service Setup (Optional)

If you want to deploy a local bridge service, you can use the PHP bridge service provided in the analysis. The bridge service will:

1. Connect to your 3 ZKTeco devices
2. Collect attendance data
3. Upload to cloud Laravel app via API
4. Queue data if cloud is unavailable

### Bridge Service Requirements

- PHP 7.4+
- Composer
- Access to ZKTeco devices on local network
- FTP server (optional, for fallback)

### Bridge Service Configuration

Create `.env` file in bridge service directory:

```env
# Bridge Configuration
BRIDGE_ID=hesu_bridge_001
BRIDGE_KEY=your-secure-bridge-key-here

# Cloud API Configuration
CLOUD_API_URL=https://yourdomain.com
API_TOKEN=your-laravel-api-token

# FTP Configuration (Local - Optional)
FTP_HOST=localhost
FTP_USER=zkteco
FTP_PASS=secure_password_here

# Sync Settings
SYNC_INTERVAL=300  # 5 minutes
MAX_RETRIES=3
RETRY_DELAY=60     # 1 minute
```

## Device Setup Steps

### Step 1: Add Devices to System

1. Navigate to **Settings → Biometric Attendance → Devices**
2. Click **"Add New Device"**
3. Fill in device information:
   - **Device Name:** CFS Warehouse
   - **IP Address:** 192.168.60.251
   - **Port:** 4370 (or 80 for HTTP)
   - **Location:** Warehouse
   - **Company:** Select your company
   - **Work Start Time:** 08:00:00
   - **Work End Time:** 17:00:00
   - **Grace Period:** 15 minutes
4. Click **"Test Connection"** to verify
5. Repeat for all 3 devices

### Step 2: Sync Employees

For each device:
1. Go to device details page
2. Click **"Sync Users"** button
3. System will sync all active employees to the device
4. Employees will be mapped by `employee_id_number`

### Step 3: Configure Auto-Sync

The system automatically syncs attendance every 5 minutes via scheduled task:
- Check `routes/console.php` for `daily-attendance:sync-all` command
- Runs automatically via Laravel scheduler

### Step 4: Monitor

1. Check **Settings Dashboard** for device status
2. View **Daily Attendance** for attendance records
3. Check **Manpower Reports** for analytics

## Troubleshooting

### Device Not Connecting

1. **Check Network:**
   - Verify device IP is accessible from server
   - Check firewall rules
   - Test with `ping` command

2. **Check Device Settings:**
   - Verify HTTP API is enabled (for port 80)
   - Check device network configuration
   - Verify port is correct (4370 for TCP, 80 for HTTP)

3. **Check System Logs:**
   - View Laravel logs: `storage/logs/laravel.log`
   - Check for connection errors

### Data Not Syncing

1. **Check Scheduled Tasks:**
   - Verify Laravel scheduler is running
   - Check `php artisan schedule:list`
   - Manually run: `php artisan daily-attendance:sync-all`

2. **Check Employee Mapping:**
   - Verify employees have `employee_id_number`
   - Check biometric mappings in employee records

3. **Check API Authentication:**
   - Verify bridge key is correct
   - Check Sanctum token is valid

### Bridge Service Issues

1. **Check Bridge Status:**
   - Call `/api/zkteco/bridge-status` endpoint
   - Verify bridge is sending heartbeats

2. **Check Logs:**
   - Bridge service logs: `logs/bridge.log`
   - Error logs: `logs/error.log`

3. **Check Network:**
   - Verify bridge can reach cloud API
   - Check firewall rules
   - Test API endpoint manually

## Security Considerations

1. **API Security:**
   - Use HTTPS for all API calls
   - Implement rate limiting
   - Use strong bridge keys
   - Rotate keys regularly

2. **Data Protection:**
   - Encrypt sensitive data
   - Regular backups
   - Access control based on roles

3. **Network Security:**
   - Firewall rules for device communication
   - VPN for additional security (optional)
   - Regular security audits

## Next Steps

1. **Add your 3 devices** to the system
2. **Sync employees** to each device
3. **Test connections** and verify data sync
4. **Monitor** daily attendance and reports
5. **Deploy bridge service** (optional, for redundancy)

## Support

For issues or questions:
- Check system logs
- Review device configuration
- Test API endpoints manually
- Contact system administrator

