# HESU Domain SMTP Configuration Guide

## Step 1: Get Your SMTP Credentials

You'll need the following information from your email hosting provider for `hesu.co.tz`:

1. **SMTP Server/Host** (common options):
   - `smtp.hesu.co.tz`
   - `mail.hesu.co.tz`
   - `smtp.your-hosting-provider.com`

2. **SMTP Port** (common options):
   - `587` (TLS/STARTTLS - Recommended)
   - `465` (SSL - Alternative)
   - `25` (Unencrypted - Not recommended)

3. **Email Account**:
   - Username: `noreply@hesu.co.tz` (or your email)
   - Password: Your email account password

4. **Encryption**:
   - `tls` (for port 587)
   - `ssl` (for port 465)

## Step 2: Update Your .env File

Open your `.env` file and add/update these lines:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hesu.co.tz
MAIL_PORT=587
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-email-password-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

### Alternative Configuration (if port 587 doesn't work):

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.hesu.co.tz
MAIL_PORT=465
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-email-password-here
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

## Step 3: Common Hosting Provider Configurations

### cPanel / Shared Hosting
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.hesu.co.tz
MAIL_PORT=587
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-cpanel-email-password
MAIL_ENCRYPTION=tls
```

### Microsoft 365 / Office 365
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-office365-password
MAIL_ENCRYPTION=tls
```

### Google Workspace (G Suite)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### Zoho Mail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.zoho.com
MAIL_PORT=587
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-zoho-password
MAIL_ENCRYPTION=tls
```

## Step 4: Apply Configuration

After updating `.env`, run:

```bash
php artisan config:clear
```

## Step 5: Test SMTP Connection

Run the test command:

```bash
php artisan smtp:test
```

Or test sending an email:

```bash
php artisan test:email test --email=laurianlawrence@hesu.co.tz
```

## Step 6: Troubleshooting

### If emails still don't send:

1. **Check SMTP credentials:**
   - Verify username and password are correct
   - Ensure email account exists and is active

2. **Try different ports:**
   - Port 587 with TLS
   - Port 465 with SSL
   - Port 25 (if others don't work, but less secure)

3. **Check firewall/network:**
   - Ensure port 587 or 465 is not blocked
   - Check if your hosting provider allows SMTP connections

4. **Check logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

5. **Test with telnet (Windows):**
   ```cmd
   telnet smtp.hesu.co.tz 587
   ```

## Common Issues

### "Connection timeout"
- Check if SMTP host is correct
- Verify port number
- Check firewall settings

### "Authentication failed"
- Verify username and password
- Check if email account is active
- Some providers require app passwords instead of regular passwords

### "Could not authenticate"
- Try different encryption (tls vs ssl)
- Try different port (587 vs 465)
- Contact your hosting provider for correct SMTP settings

## Need Help?

If you're unsure about your SMTP settings:
1. Contact your hosting provider
2. Check your hosting control panel (cPanel, Plesk, etc.)
3. Look for "Email Settings" or "SMTP Configuration" in your hosting dashboard

