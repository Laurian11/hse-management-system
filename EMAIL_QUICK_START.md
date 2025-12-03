# Email Notification Quick Start Guide

## 🚀 Quick Setup (5 Minutes)

### Step 1: Configure Email in .env

For **Gmail** (Development):
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

**Get Gmail App Password:**
1. Go to https://myaccount.google.com/security
2. Enable 2-Step Verification
3. Go to App Passwords: https://myaccount.google.com/apppasswords
4. Generate password for "Mail"
5. Use that password (not your regular password)

### Step 2: Clear Config Cache

```bash
php artisan config:clear
```

### Step 3: Test Email

```bash
# Test topic notification
php artisan test:email topic

# Test talk reminder
php artisan test:email talk

# Test to specific email
php artisan test:email topic --email=user@example.com
```

### Step 4: Check Results

**If using 'log' mailer:**
- Check: `storage/logs/laravel.log`

**If using 'smtp' mailer:**
- Check your email inbox
- Check spam folder if not received

---

## 📧 Available Notifications

### 1. Topic Created Notification
**When:** New toolbox talk topic is created  
**To:** HSE Officers  
**Test:** `php artisan test:email topic`

### 2. Talk Reminder Notification
**When:** Scheduled reminders (24h, 1h before)  
**To:** Supervisors & Employees  
**Test:** `php artisan test:email talk`

---

## 🔧 Common Configurations

### Development (Log to File)
```env
MAIL_MAILER=log
```
Emails saved to: `storage/logs/laravel.log`

### Production (SMTP) - hesu.co.tz
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hesu.co.tz
MAIL_PORT=587
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

### Production (Mailgun) - hesu.co.tz
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=hesu.co.tz
MAILGUN_SECRET=your-secret
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

---

## ⏰ Schedule Reminders

Add to crontab (`crontab -e`):
```bash
# 24-hour reminders at 9 AM daily
0 9 * * * cd /path-to-project && php artisan talks:send-reminders --type=24h

# 1-hour reminders every hour
0 * * * * cd /path-to-project && php artisan talks:send-reminders --type=1h
```

---

## 🐛 Troubleshooting

**Emails not sending?**
1. Check `.env` file has correct settings
2. Run: `php artisan config:clear`
3. Test: `php artisan test:email topic`
4. Check logs: `tail -f storage/logs/laravel.log`

**Gmail not working?**
- Use App Password (not regular password)
- Check 2FA is enabled
- Try different port: 465 with `ssl` encryption

**Queue not processing?**
- Start worker: `php artisan queue:work`
- Check failed: `php artisan queue:failed`

---

## 📚 Full Documentation

See `EMAIL_NOTIFICATION_SETUP.md` for complete guide.

