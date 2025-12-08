# ⚠️ URGENT: Update Your .env File

## Current Issue
Your `.env` file still has incorrect SMTP settings. You need to update it with the correct HESU settings.

## What to Change

### Find these lines in your `.env` file:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hesu.co.tz
MAIL_PORT=587
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=...
MAIL_ENCRYPTION=...
```

### Change them to:
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.hesu.co.tz
MAIL_PORT=465
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-actual-password-here
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

## Key Changes:
1. ✅ `MAIL_HOST` → Change from `smtp.hesu.co.tz` to `mail.hesu.co.tz`
2. ✅ `MAIL_PORT` → Change from `587` to `465`
3. ✅ `MAIL_ENCRYPTION` → Set to `ssl` (not `tls`)
4. ✅ `MAIL_PASSWORD` → Enter your actual email password
5. ✅ `MAIL_FROM_ADDRESS` → Set to `noreply@hesu.co.tz`
6. ✅ `MAIL_FROM_NAME` → Set to `"HSE Management System"`

## After Updating .env:

1. **Clear config cache:**
   ```bash
   php artisan config:clear
   ```

2. **Test the connection:**
   ```bash
   php artisan smtp:test --email=laurianlawrence@hesu.co.tz
   ```

3. **Send a test email:**
   ```bash
   php artisan test:email test --email=laurianlawrence@hesu.co.tz
   ```

## Complete .env Mail Section (Copy & Paste):

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.hesu.co.tz
MAIL_PORT=465
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-password-here
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

**Remember:** Replace `your-password-here` with the actual password for `noreply@hesu.co.tz`

