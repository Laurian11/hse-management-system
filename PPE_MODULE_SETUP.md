# PPE Management Module - Setup Guide

## 📋 Overview

The PPE (Personal Protective Equipment) Management Module is a comprehensive system for tracking, managing, and ensuring compliance with PPE requirements across your organization.

## ✨ Features

### Core Functionality
- **Inventory Management** - Track PPE items, stock levels, and suppliers
- **Issuance & Returns** - Manage PPE assignments to employees
- **Inspections** - Schedule and track PPE condition inspections
- **Compliance Reports** - Generate reports on PPE usage and compliance
- **Supplier Management** - Maintain supplier database and procurement records

### Enhanced Features
- **Dashboard with Charts** - Visual analytics for monthly issuances and category distribution
- **Automated Alerts** - Daily alerts for expiring items, low stock, and overdue inspections
- **Stock Adjustment** - Quick stock management with audit trail
- **Export Functionality** - CSV export for inventory data
- **Photo Upload** - Document defects with photos during inspections

## 🚀 Setup Instructions

### 1. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `ppe_suppliers`
- `ppe_items`
- `ppe_issuances`
- `ppe_inspections`
- `ppe_compliance_reports`

### 2. Create Storage Link (For Photo Uploads)

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`, allowing uploaded photos to be accessible via the web.

### 3. Configure Scheduled Tasks

The module includes automated daily tasks that run at 8:30 AM:

- **Expiry Alerts** - Notifies about PPE items expiring within 7 days
- **Low Stock Alerts** - Alerts when items fall below minimum stock levels
- **Inspection Alerts** - Reminds about overdue inspections
- **Auto-Update Expired** - Automatically marks expired issuances

To enable these tasks, ensure your cron job is set up:

```bash
# Add to crontab (Linux/Mac)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1

# Or use Windows Task Scheduler (Windows)
# Create a task to run: php artisan schedule:run
```

### 4. Queue Configuration (Optional - For Email Notifications)

If you plan to enable email notifications, configure queues:

```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

## 📖 Usage Guide

### Creating PPE Items

1. Navigate to **PPE Management > Inventory**
2. Click **New Item**
3. Fill in the required information:
   - Name, Category, Type
   - Stock quantities (Total, Minimum level)
   - Supplier information
   - Expiry and inspection settings
4. Save the item

### Issuing PPE

1. Navigate to **PPE Management > Issuances & Returns**
2. Click **New Issuance**
3. Select the PPE item and employee
4. Set issue date and expiry/replacement dates
5. Save the issuance

### Conducting Inspections

1. Navigate to **PPE Management > Inspections**
2. Click **New Inspection**
3. Select the issuance to inspect
4. Fill in inspection details:
   - Condition assessment
   - Findings and defects
   - Upload defect photos (if any)
   - Action taken
5. Mark compliance status
6. Save the inspection

### Stock Adjustment

1. Navigate to **PPE Management > Inventory**
2. Open an item's detail page
3. Use the **Stock Adjustment** form in the sidebar
4. Choose adjustment type (Add/Remove/Set)
5. Enter quantity and reason
6. Submit the adjustment

### Exporting Data

1. Navigate to **PPE Management > Inventory**
2. Apply any filters (search, category, status)
3. Click **Export** button
4. Download the CSV file

## 📊 Dashboard Metrics

The PPE Dashboard displays:

- **Total Items** - Number of PPE items in inventory
- **Low Stock Items** - Items below minimum stock level
- **Active Issuances** - Currently issued PPE items
- **Expired Issuances** - PPE items that have expired
- **Expiring Soon** - Items expiring within 7 days
- **Overdue Inspections** - Inspections past due date
- **Non-Compliant** - Failed inspections
- **Total Suppliers** - Active suppliers

### Charts

- **Monthly Issuances** - Line chart showing issuance trends over 6 months
- **Category Distribution** - Doughnut chart showing item distribution by category

## 🔔 Alert System

The automated alert service runs daily and checks:

1. **Expiring Items** - PPE items with replacement due date within 7 days
2. **Low Stock** - Items where available quantity < minimum stock level
3. **Overdue Inspections** - Issuances requiring inspection that are past due

Currently, alerts are logged. To enable email notifications:

1. Create notification classes (similar to existing notifications)
2. Uncomment the notification code in `PPEAlertService.php`
3. Configure email settings in `.env`

## 📁 File Storage

Inspection photos are stored in:
```
storage/app/public/ppe-inspections/
```

Ensure the storage link is created (see Setup Step 2).

## 🔒 Security & Data Isolation

All PPE data is automatically scoped to the user's company:
- Items, issuances, inspections are company-specific
- Users can only access their company's data
- All queries use the `forCompany()` scope

## 🛠️ Troubleshooting

### Photos Not Displaying

1. Ensure storage link exists: `php artisan storage:link`
2. Check file permissions on `storage/app/public/ppe-inspections/`
3. Verify `APP_URL` in `.env` is correct

### Alerts Not Running

1. Check cron job is configured: `php artisan schedule:list`
2. Verify scheduled task in `routes/console.php`
3. Check Laravel logs: `storage/logs/laravel.log`

### Export Not Working

1. Ensure PHP has write permissions
2. Check disk space
3. Verify CSV headers are correct

## 📝 API Endpoints (Future)

The module is ready for API integration. Future endpoints could include:

- `GET /api/ppe/items` - List PPE items
- `POST /api/ppe/issuances` - Create issuance
- `GET /api/ppe/inspections` - List inspections
- `POST /api/ppe/inspections` - Create inspection

## 🎯 Best Practices

1. **Regular Inspections** - Schedule inspections based on item type and usage
2. **Stock Monitoring** - Set appropriate minimum stock levels
3. **Supplier Management** - Keep supplier information up to date
4. **Photo Documentation** - Always upload photos for damaged items
5. **Compliance Tracking** - Regularly review compliance reports

## 📞 Support

For issues or questions:
1. Check the logs: `storage/logs/laravel.log`
2. Review the code documentation
3. Contact the development team

---

**Last Updated:** December 2025
**Module Version:** 1.0.0

