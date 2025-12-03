# Email Notification Setup Guide

## 📧 Current Email Notification System

The HSE Management System includes the following email notifications:

### 1. **Topic Created Notification**
- **Trigger**: When a new toolbox talk topic is created
- **Recipients**: HSE Department Officers
- **Content**: Topic details, representer information, review link

### 2. **Talk Reminder Notification**
- **Trigger**: Scheduled reminders for upcoming toolbox talks
- **Recipients**: Supervisors and department employees
- **Types**: 
  - 24 hours before talk
  - 1 hour before talk
  - When talk is scheduled

---

## ⚙️ Email Configuration

### Current Setup
By default, emails are logged to files (not actually sent). To enable real email sending, configure your `.env` file.

### Option 1: SMTP (Recommended for Development/Production)

Add to your `.env` file:

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

**Gmail Setup:**
1. Enable 2-Factor Authentication
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Use the app password (not your regular password)

**Other SMTP Providers:**
- **Outlook/Hotmail**: `smtp-mail.outlook.com`, Port 587
- **Yahoo**: `smtp.mail.yahoo.com`, Port 587
- **Custom SMTP**: Use your provider's SMTP settings

### Option 2: Mailgun (Production) - hesu.co.tz

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=hesu.co.tz
MAILGUN_SECRET=your-mailgun-secret
MAILGUN_ENDPOINT=api.mailgun.net
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

### Option 3: Postmark (Production) - hesu.co.tz

```env
MAIL_MAILER=postmark
POSTMARK_TOKEN=your-postmark-token
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

### Option 4: Resend (Production) - hesu.co.tz

```env
MAIL_MAILER=resend
RESEND_KEY=your-resend-key
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

### Option 5: SMTP with hesu.co.tz (Recommended for Production)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hesu.co.tz
MAIL_PORT=587
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

### Option 5: Log (Development/Testing)

Emails are logged to `storage/logs/laravel.log`:

```env
MAIL_MAILER=log
```

---

## 🧪 Testing Email Notifications

### Method 1: Test Command

Run the test command:

```bash
php artisan test:email
```

### Method 2: Test via Tinker

```bash
php artisan tinker
```

Then run:
```php
// Test topic notification
$topic = App\Models\ToolboxTalkTopic::first();
$user = App\Models\User::first();
$user->notify(new App\Notifications\TopicCreatedNotification($topic));

// Test talk reminder
$talk = App\Models\ToolboxTalk::first();
$user->notify(new App\Notifications\TalkReminderNotification($talk, '24h'));
```

### Method 3: Create Test Route (Temporary)

Add to `routes/web.php`:
```php
Route::get('/test-email', function() {
    $topic = App\Models\ToolboxTalkTopic::first();
    $user = App\Models\User::whereHas('role', function($q) {
        $q->where('name', 'hse_officer');
    })->first();
    
    if ($user && $topic) {
        $user->notify(new App\Notifications\TopicCreatedNotification($topic));
        return 'Email sent to ' . $user->email;
    }
    return 'No HSE officer or topic found';
})->middleware('auth');
```

---

## 📋 Notification Details

### Topic Created Notification

**When Sent:**
- Automatically when a new toolbox talk topic is created
- Sent to all HSE officers in the company

**Email Content:**
- Topic title
- Category
- Difficulty level
- Estimated duration
- Representer information (if assigned)
- Topic description
- Link to view topic

**Recipients:**
- Users with HSE Officer role
- Department HSE officers (from `hse_officer_id` field)

### Talk Reminder Notification

**When Sent:**
- Manually via command: `php artisan talks:send-reminders --type=24h`
- Can be scheduled via cron job

**Email Content:**
- Talk title
- Scheduled date and time
- Location
- Duration
- Description
- Link to view talk
- Biometric requirement notice (if applicable)

**Recipients:**
- Talk supervisor
- Department employees (if department is assigned)

---

## ⏰ Scheduling Email Reminders

### Setup Cron Job for Automatic Reminders

Add to your server's crontab (`crontab -e`):

```bash
# Send 24-hour reminders daily at 9 AM
0 9 * * * cd /path-to-project && php artisan talks:send-reminders --type=24h >> /dev/null 2>&1

# Send 1-hour reminders every hour
0 * * * * cd /path-to-project && php artisan talks:send-reminders --type=1h >> /dev/null 2>&1
```

### Windows Task Scheduler (Development)

Create a batch file `send-reminders.bat`:
```batch
@echo off
cd C:\xampp\htdocs\hse-management-system
php artisan talks:send-reminders --type=24h
```

Schedule it in Windows Task Scheduler.

---

## 🔧 Queue Configuration (For Better Performance)

Since notifications implement `ShouldQueue`, they're queued by default. Configure queues:

### Database Queue (Default)

```env
QUEUE_CONNECTION=database
```

Run migration:
```bash
php artisan queue:table
php artisan migrate
```

Start queue worker:
```bash
php artisan queue:work
```

### Redis Queue (Recommended for Production)

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## 📝 Email Templates

### Customizing Email Templates

Email templates are defined in notification classes:
- `app/Notifications/TopicCreatedNotification.php`
- `app/Notifications/TalkReminderNotification.php`

To customize, edit the `toMail()` method in each notification class.

### Creating Custom Email Views

1. Create view: `resources/views/emails/topic-created.blade.php`
2. Update notification:
```php
public function toMail(object $notifiable): MailMessage
{
    return (new MailMessage)
        ->view('emails.topic-created', ['topic' => $this->topic]);
}
```

---

## 🐛 Troubleshooting

### Emails Not Sending

1. **Check .env configuration:**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

2. **Check mail logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Test SMTP connection:**
   ```bash
   php artisan tinker
   Mail::raw('Test email', function($message) {
       $message->to('your-email@example.com')->subject('Test');
   });
   ```

### Gmail Issues

- Use App Password (not regular password)
- Enable "Less secure app access" (if not using App Password)
- Check if 2FA is enabled

### Queue Not Processing

- Ensure queue worker is running: `php artisan queue:work`
- Check failed jobs: `php artisan queue:failed`
- Retry failed jobs: `php artisan queue:retry all`

---

## 📊 Email Notification Status

### Current Implementation Status

✅ **Topic Created Notification** - Fully implemented
✅ **Talk Reminder Notification** - Fully implemented
✅ **Queue Support** - Implemented (ShouldQueue)
⏳ **Email Templates** - Using default Laravel templates
⏳ **Email Preferences** - Not implemented (all users receive all emails)

### Future Enhancements

- [ ] User email preferences (opt-in/opt-out)
- [ ] Custom email templates
- [ ] Email digests (daily/weekly summaries)
- [ ] SMS notifications (via Twilio)
- [ ] Push notifications
- [ ] Email analytics (open rates, click rates)

---

## 🔐 Security Notes

1. **Never commit `.env` file** - Contains sensitive credentials
2. **Use App Passwords** - For Gmail and similar services
3. **Rate Limiting** - Consider implementing rate limits for email sending
4. **SPF/DKIM Records** - Configure for production domains to prevent spam

---

## 📞 Support

For email configuration issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Test SMTP connection using tinker
3. Verify .env settings are correct
4. Check firewall/network settings

---

*Last Updated: December 2025*

