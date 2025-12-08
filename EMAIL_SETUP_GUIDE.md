# Email Setup Guide - Send Real Emails

## Current Status
Your email system is currently set to **LOG MODE**, which means emails are written to log files instead of being sent.

## To Send Real Emails

### Option 1: Gmail SMTP (Recommended for Testing)

1. **Enable 2-Factor Authentication** on your Gmail account
2. **Generate App Password:**
   - Go to: https://myaccount.google.com/apppasswords
   - Select "Mail" and "Other (Custom name)"
   - Enter "HSE Management System"
   - Copy the 16-character password

3. **Update your `.env` file:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

4. **Clear config cache:**
```bash
php artisan config:clear
```

5. **Test sending:**
```bash
php artisan test:email test --email=laurianlawrence@hesu.co.tz
```

### Option 2: Other SMTP Providers

**Outlook/Hotmail:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

**Yahoo:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yahoo.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

**Custom SMTP (e.g., HESU domain):**
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

### Option 3: Mailtrap (For Development Testing)

1. Sign up at https://mailtrap.io
2. Get your SMTP credentials
3. Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

## View Logged Emails (Current Setup)

Since you're in LOG mode, emails are saved to:
- **File:** `storage/logs/laravel.log`
- Look for entries with "To:", "Subject:", and email content

## Troubleshooting

1. **Clear config cache after changing .env:**
   ```bash
   php artisan config:clear
   ```

2. **Test connection:**
   ```bash
   php artisan test:email test --email=laurianlawrence@hesu.co.tz
   ```

3. **Check for errors:**
   - Check `storage/logs/laravel.log` for error messages
   - Verify SMTP credentials are correct
   - Ensure firewall allows SMTP connections (port 587)

4. **Queue Configuration (if using queues):**
   ```env
   QUEUE_CONNECTION=database
   ```
   Then run: `php artisan queue:work`

