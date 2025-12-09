# Multi-Device Biometric Attendance System

## Overview

This system enables configuration and management of multiple ZKTeco biometric devices across different company locations for daily manpower tracking and attendance management.

## Features

### 1. **Device Management**
- Configure multiple biometric devices per company/location
- Device settings: IP address, port, API key, connection type
- Location mapping (name, address, GPS coordinates)
- Work hours configuration (start time, end time, grace period)
- Device status monitoring (online/offline)
- Auto-sync configuration

### 2. **Daily Attendance Tracking**
- Automatic check-in/check-out from biometric devices
- Manual check-in/check-out for corrections
- Work hours calculation (total, overtime, late minutes)
- Attendance status (present, absent, late, half-day, on leave, sick leave)
- Location-based attendance tracking

### 3. **Manpower Reporting**
- **Daily Report**: Total employees, present, absent, attendance rate by device and department
- **Weekly Report**: Day-by-day breakdown, average attendance
- **Monthly Report**: Daily and weekly summaries
- **Location Report**: Compare attendance across different locations/devices
- Export to Excel/PDF

### 4. **Integration with Existing System**
- Uses existing employee biometric data (employees already registered)
- Separate from toolbox talk attendance
- Company group filtering support
- Works with parent-sister company structure

## Database Structure

### `biometric_devices` Table
- Device configuration (name, serial number, type, IP, port)
- Company and location mapping
- Work hours settings
- Sync configuration
- Status tracking

### `daily_attendance` Table
- Employee attendance records
- Check-in/check-out times
- Work hours calculation
- Status flags (late, overtime, absent)
- Location data

## Usage

### 1. Configure Devices
1. Go to **Biometric Devices** → **Create New Device**
2. Enter device details (IP, location, company)
3. Configure work hours
4. Test connection
5. Sync employees to device

### 2. Automatic Sync
- System syncs attendance every 5 minutes automatically
- Full sync at end of day (11:55 PM)
- Manual sync available from device page

### 3. View Attendance
- **Daily Attendance**: View all attendance records with filters
- **Dashboard**: Overview of today's attendance statistics
- **Manpower Reports**: Generate various reports

### 4. Manual Corrections
- Manual check-in/check-out for corrections
- Edit attendance records
- Add notes and remarks

## Commands

```bash
# Sync attendance from all devices
php artisan attendance:sync-daily

# Sync specific device
php artisan attendance:sync-daily --device=1

# Sync for specific date
php artisan attendance:sync-daily --date=2025-12-08
```

## Scheduled Tasks

- **Every 5 minutes**: Auto-sync attendance from all active devices
- **Daily at 11:55 PM**: Full sync for end of day

## API Endpoints

### Device Management
- `GET /biometric-devices` - List all devices
- `POST /biometric-devices` - Create new device
- `GET /biometric-devices/{id}` - View device details
- `POST /biometric-devices/{id}/test-connection` - Test device connection
- `POST /biometric-devices/{id}/sync-users` - Sync employees to device
- `POST /biometric-devices/{id}/sync-attendance` - Manual sync attendance

### Daily Attendance
- `GET /daily-attendance` - List attendance records
- `GET /daily-attendance/dashboard` - Attendance dashboard
- `POST /daily-attendance/manual-check-in` - Manual check-in
- `POST /daily-attendance/manual-check-out` - Manual check-out
- `GET /daily-attendance/export/excel` - Export to Excel

### Manpower Reports
- `GET /manpower-reports` - Reports dashboard
- `GET /manpower-reports/daily` - Daily report
- `GET /manpower-reports/weekly` - Weekly report
- `GET /manpower-reports/monthly` - Monthly report
- `GET /manpower-reports/location` - Location comparison report

## Integration Notes

1. **Employee Biometric Data**: System uses existing `biometric_template_id` from employees table
2. **Company Groups**: Supports parent-sister company filtering
3. **Separate from Toolbox Talks**: Daily attendance is separate from toolbox talk attendance
4. **Device Selection**: Each toolbox talk can still link to a specific device via `zk_device_id`

## Next Steps

1. Create views for device management
2. Create views for daily attendance
3. Create views for manpower reports
4. Add sidebar navigation links
5. Test with actual devices

