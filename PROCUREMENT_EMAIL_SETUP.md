# Procurement Email Notification Setup

## 📧 Automatic Email Notifications for Procurement Requests

The system automatically sends email notifications to procurement team members when procurement requests are created or submitted.

## ⚙️ Configuration

### Step 1: Configure Procurement Email Addresses

Add to your `.env` file:

```env
# Procurement notification emails (comma-separated)
PROCUREMENT_NOTIFICATION_EMAILS=procurement@company.com,procurement-team@company.com

# Enable/disable auto-send notifications (default: true)
PROCUREMENT_AUTO_SEND_NOTIFICATIONS=true

# Configure when to send notifications
PROCUREMENT_NOTIFY_ON_CREATED=true    # Send when request is created
PROCUREMENT_NOTIFY_ON_SUBMITTED=true  # Send when status changes to 'submitted'
PROCUREMENT_NOTIFY_ON_UPDATED=false  # Send when request is updated
```

### Step 2: Clear Config Cache

```bash
php artisan config:clear
```

## 📬 When Notifications Are Sent

### 1. When Request is Created
- **Trigger**: New procurement request is created
- **Action**: `created`
- **Default**: ✅ Enabled
- **Email Subject**: "New Procurement Request: {REFERENCE_NUMBER}"

### 2. When Request is Submitted
- **Trigger**: Request status changes to `submitted`
- **Action**: `submitted`
- **Default**: ✅ Enabled
- **Email Subject**: "New Procurement Request Submitted: {REFERENCE_NUMBER}"

### 3. When Request is Updated
- **Trigger**: Request is updated (status change)
- **Action**: `updated`
- **Default**: ❌ Disabled
- **Email Subject**: "Procurement Request Updated: {REFERENCE_NUMBER}"

## 📋 Email Content

Each notification includes:
- Reference Number
- Item Name
- Category
- Quantity & Unit
- Priority
- Status
- Estimated Cost (if provided)
- Required By Date (if provided)
- Requested By (user name and email)
- Department (if assigned)
- Justification & Description
- Link to view the request

## 🧪 Testing

### Test via Tinker

```bash
php artisan tinker
```

```php
// Create a test request
$request = App\Models\ProcurementRequest::first();

// Send notification manually
$emails = ['procurement@company.com'];
foreach ($emails as $email) {
    Notification::route('mail', $email)
        ->notify(new App\Notifications\ProcurementRequestNotification($request, 'created'));
}
```

### Test via Route (Temporary)

Add to `routes/web.php`:

```php
Route::get('/test-procurement-email', function() {
    $request = App\Models\ProcurementRequest::first();
    if ($request) {
        $emails = config('procurement.notification_emails');
        if ($emails) {
            $emailList = array_map('trim', explode(',', $emails));
            foreach ($emailList as $email) {
                Notification::route('mail', $email)
                    ->notify(new App\Notifications\ProcurementRequestNotification($request, 'created'));
            }
            return 'Email sent to: ' . implode(', ', $emailList);
        }
        return 'No procurement emails configured';
    }
    return 'No procurement request found';
})->middleware('auth');
```

## 🔧 Email Configuration

Make sure your email is configured in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

For development/testing, you can use:

```env
MAIL_MAILER=log
```

This will log emails to `storage/logs/laravel.log` instead of sending them.

## 📝 Notes

- Notifications are queued (background processing) for better performance
- Make sure queue worker is running: `php artisan queue:work`
- Multiple email addresses are supported (comma-separated)
- Emails are validated before sending
- Notifications respect the configuration settings in `config/procurement.php`

