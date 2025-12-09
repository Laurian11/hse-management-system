# HESU ZKTeco Device Setup - Complete Integration

## Your Device Configuration

### Device 1: CFS Warehouse
- **Name:** CFS Warehouse
- **Serial Number:** AEWD233960244
- **IP Address:** 192.168.60.251
- **Port:** 4370
- **Area:** CFS (Warehouse)
- **Location:** Warehouse area

### Device 2: HESU ICD (Main Device)
- **Name:** HESU ICD
- **Serial Number:** AEWD233960257
- **IP Address:** 192.168.40.68
- **Port:** 4370
- **Area:** ICD
- **Location:** ICD area (main device)

### Device 3: CFS Office
- **Name:** CFS Office
- **Serial Number:** ZTC8235000106
- **IP Address:** 192.168.40.201
- **Port:** 4370
- **Area:** CFS
- **Location:** Additional CFS device

## Integration Architecture

```
┌─────────────────────────────────────────────────────────┐
│           CLOUD LARAVEL APPLICATION                    │
│           (yourdomain.com)                             │
│                                                         │
│  • Multi-Device Biometric System                       │
│  • Daily Attendance Tracking                           │
│  • Manpower Reports                                    │
│  • Settings Dashboard                                  │
└─────────────────────────▲───────────────────────────────┘
                          │ HTTPS API
                          │
┌─────────────────────────▼───────────────────────────────┐
│           LOCAL NETWORK (Your Office)                   │
│                                                         │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐ │
│  │ ZKTeco      │    │   LOCAL     │    │   FTP       │ │
│  │ Devices     │───▶│   BRIDGE    │───▶│   SERVER    │ │
│  │ (3 devices) │    │   SERVICE   │    │   (Optional)│ │
│  └─────────────┘    └─────────────┘    └─────────────┘ │
│      192.168.40.68         (PHP Service)               │
│      192.168.40.201                                     │
│      192.168.60.251                                     │
└─────────────────────────────────────────────────────────┘
```

## System Integration Points

### 1. Direct Device Communication (Primary)
- System connects directly to devices via HTTP API (port 80) or TCP (port 4370)
- Real-time sync every 5 minutes
- Automatic attendance processing

### 2. Bridge Service (Optional - For Offline Scenarios)
- Local bridge service can sync via FTP
- Queues data when cloud is unavailable
- Auto-syncs when connection restored

### 3. Hybrid Approach (Recommended)
- Direct connection when available
- FTP fallback when direct connection fails
- Bridge service for redundancy

## Next Steps

1. **Add Devices to System:**
   - Go to Settings → Biometric Attendance → Devices
   - Add each of the 3 devices with their IP addresses
   - Configure work hours for each location

2. **Sync Employees:**
   - Sync all 475 employees to each device
   - Map employee codes to device user IDs

3. **Test Connection:**
   - Test each device connection
   - Verify attendance sync works

4. **Monitor:**
   - Check Settings Dashboard for device status
   - Review daily attendance reports
   - Monitor sync logs

## Device-Specific Configuration

### CFS Warehouse (192.168.60.251)
- Work Hours: 08:00 - 17:00
- Grace Period: 15 minutes
- Expected Employees: ~150 (CFS staff)

### HESU ICD (192.168.40.68)
- Work Hours: 08:00 - 17:00
- Grace Period: 15 minutes
- Expected Employees: ~200 (ICD staff)

### CFS Office (192.168.40.201)
- Work Hours: 08:00 - 17:00
- Grace Period: 15 minutes
- Expected Employees: ~125 (Office staff)

## API Endpoints Available

- `POST /api/zkteco/sync` - Receive sync data from bridge
- `POST /api/zkteco/heartbeat` - Bridge heartbeat
- `GET /api/zkteco/bridge-status` - Check bridge status

## Security

- API authentication via Sanctum
- Bridge key verification
- Encrypted data transmission
- Role-based access control

