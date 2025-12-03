# HESU Email Configuration - hesu.co.tz

## 📧 Quick Configuration for hesu.co.tz

### Option 1: SMTP Configuration (Recommended)

Add to your `.env` file:

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

**Alternative SMTP Settings:**
- If port 587 doesn't work, try port **465** with `MAIL_ENCRYPTION=ssl`
- If `smtp.hesu.co.tz` doesn't work, check with your hosting provider for the correct SMTP host

### Option 2: Using cPanel/Hosting SMTP

If your hosting provider uses cPanel or similar:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.hesu.co.tz
MAIL_PORT=587
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-cpanel-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

### Option 3: Using Mailgun with hesu.co.tz

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=hesu.co.tz
MAILGUN_SECRET=your-mailgun-secret-key
MAILGUN_ENDPOINT=api.mailgun.net
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

**Note:** You'll need to:
1. Sign up at https://www.mailgun.com
2. Verify domain `hesu.co.tz`
3. Add DNS records (SPF, DKIM, MX)
4. Get API secret from dashboard

---

## 🔧 After Configuration

1. **Clear config cache:**
   ```bash
   php artisan config:clear
   ```

2. **Test email:**
   ```bash
   php artisan test:email topic
   ```

3. **Check results:**
   - If using `log`: Check `storage/logs/laravel.log`
   - If using `smtp`: Check email inbox

---

## 📋 DNS Records for hesu.co.tz

For best email deliverability, configure these DNS records:

### SPF Record
```
TXT @ "v=spf1 include:mailgun.org ~all"
```
or for SMTP:
```
TXT @ "v=spf1 mx ~all"
```

### DKIM Record
(Get from Mailgun if using Mailgun, or from your email provider)

### MX Record (if using custom mail server)
```
MX @ mail.hesu.co.tz priority 10
```

---

## ✅ Current Default Settings

The system is now configured with these defaults:
- **From Address:** `noreply@hesu.co.tz`
- **From Name:** `HSE Management System`

These will be used if not specified in `.env` file.

---

## 🧪 Testing

Test email notifications:
```bash
# Test topic notification
php artisan test:email topic

# Test talk reminder
php artisan test:email talk

# Test to specific email
php artisan test:email topic --email=admin@hesu.co.tz
```

---

## 📞 Support

If emails are not sending:
1. Verify SMTP credentials with your hosting provider
2. Check firewall/port restrictions
3. Test SMTP connection using email client first
4. Check spam folder
5. Review logs: `storage/logs/laravel.log`

---

*Configuration updated for hesu.co.tz domain*

