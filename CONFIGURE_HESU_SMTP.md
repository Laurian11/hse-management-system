# HESU SMTP Configuration - Ready to Apply

## Your SMTP Settings

Based on the information you provided:

- **SMTP Host:** mail.hesu.co.tz
- **SMTP Port:** 465
- **Encryption:** SSL (port 465 uses SSL)
- **Username:** noreply@hesu.co.tz
- **Password:** (Your email account password)

## Update Your .env File

Add or update these lines in your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.hesu.co.tz
MAIL_PORT=465
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-email-account-password-here
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

**Important:** Replace `your-email-account-password-here` with the actual password for the `noreply@hesu.co.tz` email account.

## After Updating .env

1. **Clear config cache:**
   ```bash
   php artisan config:clear
   ```

2. **Test SMTP connection:**
   ```bash
   php artisan smtp:test --email=laurianlawrence@hesu.co.tz
   ```

3. **Send a test email:**
   ```bash
   php artisan test:email test --email=laurianlawrence@hesu.co.tz --message="Test email from HSE Management System"
   ```

## Alternative: Port 587 with TLS

If port 465 doesn't work, you can also try port 587 with TLS:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.hesu.co.tz
MAIL_PORT=587
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-email-account-password-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

## Troubleshooting

If you encounter issues:

1. **Verify the email account exists and is active**
2. **Double-check the password is correct**
3. **Try port 587 with TLS if 465 doesn't work**
4. **Check firewall settings (port 465 or 587 should be open)**
5. **Check logs:** `storage/logs/laravel.log`

