# cPanel Deployment Guide

## Pre-Deployment Checklist

### 1. Code Preparation
- [x] All changes committed and pushed to repository
- [x] Documentation consolidated
- [x] No sensitive data in code (use .env)
- [x] All tests passing (if applicable)

### 2. Environment Configuration

#### Required Environment Variables

Create/update `.env` file on cPanel with the following:

```env
APP_NAME="HSE Management System"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Session & Cache
SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Mail Configuration (if using email)
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# ZKTeco Biometric Configuration
ZKTECO_DEVICE_IP=192.168.1.201
ZKTECO_PORT=4370
ZKTECO_API_KEY=
ZKTECO_TIMEOUT=10
ZKTECO_CONNECT_TIMEOUT=30
ZKTECO_NON_STANDARD_TIMEOUT=30
```

### 3. cPanel Setup Steps

#### Step 1: Access cPanel
1. Log into your cPanel account
2. Navigate to **File Manager**

#### Step 2: Upload Files
1. Go to `public_html` directory (or your domain's root)
2. Upload all project files via:
   - **Option A**: File Manager → Upload
   - **Option B**: Git Version Control (if available)
   - **Option C**: SSH/SFTP

#### Step 3: Set Permissions
Set correct file permissions:
```bash
# Via SSH (if available)
chmod -R 755 storage bootstrap/cache
chmod -R 644 .env
```

Or via File Manager:
- `storage/` folder: 755
- `bootstrap/cache/` folder: 755
- `.env` file: 644

#### Step 4: Install Dependencies

**Via SSH (Recommended):**
```bash
cd /home/username/public_html
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

**Via cPanel Terminal (if available):**
Same commands as above

**Manual (if no SSH):**
- Upload `vendor/` folder from local (after running `composer install --no-dev`)
- Upload compiled assets from `public/build/`

#### Step 5: Configure Database

1. **Create Database:**
   - Go to **MySQL Databases** in cPanel
   - Create new database: `your_database_name`
   - Create new user: `your_database_user`
   - Set password: `your_database_password`
   - Add user to database with ALL PRIVILEGES

2. **Run Migrations:**
   ```bash
   php artisan migrate --force
   ```

3. **Seed Database (if needed):**
   ```bash
   php artisan db:seed
   ```

#### Step 6: Generate Application Key
```bash
php artisan key:generate
```

#### Step 7: Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

#### Step 8: Configure Web Server

**For Apache (.htaccess should be in public/):**

Ensure `public/.htaccess` exists:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Point Document Root to `public/` folder:**
1. Go to **cPanel → Domains → Your Domain**
2. Set Document Root to: `/home/username/public_html/public`
3. Or use **Subdomain** feature if main domain is used elsewhere

#### Step 9: Configure Scheduled Tasks (Cron Jobs)

1. Go to **cPanel → Cron Jobs**
2. Add new cron job:

```bash
# Run Laravel scheduler every minute
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

Or if using full path:
```bash
* * * * * /usr/local/bin/php /home/username/public_html/artisan schedule:run >> /dev/null 2>&1
```

**Note:** Replace `/usr/local/bin/php` with your PHP path (check via `which php` in SSH)

#### Step 10: Configure Queue Workers (Optional)

If using queues, set up Supervisor or use cron:

**Via Cron (Simple):**
```bash
# Process queue every minute
* * * * * cd /home/username/public_html && php artisan queue:work --once >> /dev/null 2>&1
```

**Via Supervisor (Recommended for production):**
1. SSH into server
2. Install Supervisor (if not available)
3. Create config file: `/etc/supervisor/conf.d/hse-queue.conf`

```ini
[program:hse-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/username/public_html/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=username
numprocs=2
redirect_stderr=true
stdout_logfile=/home/username/public_html/storage/logs/queue-worker.log
stopwaitsecs=3600
```

### 4. Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated
- [ ] Database credentials secure
- [ ] File permissions set correctly (755 for folders, 644 for files)
- [ ] `.env` file not publicly accessible
- [ ] HTTPS enabled (SSL certificate installed)
- [ ] Firewall configured (if available)
- [ ] Regular backups configured

### 5. Post-Deployment Verification

#### Test These Features:
1. **Homepage loads:** `https://yourdomain.com`
2. **Login works:** Test user authentication
3. **Database connection:** Check if data loads
4. **File uploads:** Test image/document uploads
5. **Scheduled tasks:** Verify cron jobs are running
6. **Queue processing:** Test queue jobs (if enabled)
7. **Biometric sync:** Test device connection (if applicable)

#### Check Logs:
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# View queue logs (if using)
tail -f storage/logs/queue-worker.log
```

### 6. Common cPanel Issues & Solutions

#### Issue 1: 500 Internal Server Error
**Solution:**
- Check file permissions
- Check `.env` configuration
- Check `storage/logs/laravel.log` for errors
- Verify PHP version (should be 8.2+)

#### Issue 2: Database Connection Failed
**Solution:**
- Verify database credentials in `.env`
- Check database user has proper permissions
- Ensure database exists
- Check if `localhost` should be `127.0.0.1` (some hosts require this)

#### Issue 3: Permission Denied
**Solution:**
```bash
chmod -R 755 storage bootstrap/cache
chown -R username:username storage bootstrap/cache
```

#### Issue 4: Composer Not Found
**Solution:**
- Use cPanel's PHP Selector to enable Composer
- Or use full path: `/usr/local/bin/composer`
- Or upload `vendor/` folder manually

#### Issue 5: Node/npm Not Available
**Solution:**
- Build assets locally and upload `public/build/` folder
- Or use cPanel's Node.js selector (if available)

#### Issue 6: Cron Jobs Not Running
**Solution:**
- Verify cron path is correct
- Check email notifications from cron
- Test cron manually: `php artisan schedule:run`
- Ensure PHP path is correct

### 7. Performance Optimization

#### Enable OPcache (if available):
Add to `php.ini` or via cPanel PHP Selector:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

#### Enable Gzip Compression:
Add to `.htaccess`:
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>
```

#### Cache Static Assets:
Add to `.htaccess`:
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### 8. Backup Strategy

#### Automated Backups:
1. **Database Backups:**
   - Use cPanel's **Backup** feature
   - Schedule daily backups
   - Or use Laravel backup package

2. **File Backups:**
   - Backup `storage/` folder regularly
   - Backup `.env` file (securely)
   - Backup uploaded files

#### Manual Backup Commands:
```bash
# Backup database
mysqldump -u username -p database_name > backup.sql

# Backup files
tar -czf backup-files.tar.gz storage/ .env
```

### 9. Monitoring

#### Set Up Monitoring:
1. **Error Tracking:**
   - Monitor `storage/logs/laravel.log`
   - Set up email alerts for errors

2. **Performance Monitoring:**
   - Use cPanel's resource usage tools
   - Monitor database queries
   - Check queue processing

3. **Uptime Monitoring:**
   - Use external services (UptimeRobot, etc.)
   - Monitor critical endpoints

### 10. Rollback Plan

If deployment fails:

1. **Restore Database:**
   ```bash
   mysql -u username -p database_name < backup.sql
   ```

2. **Restore Files:**
   - Revert to previous Git commit
   - Or restore from backup

3. **Clear Caches:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

### 11. Quick Deployment Script

Create `deploy.sh` for future deployments:

```bash
#!/bin/bash
cd /home/username/public_html

# Pull latest changes
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "Deployment complete!"
```

Make executable:
```bash
chmod +x deploy.sh
```

### 12. Post-Deployment Tasks

- [ ] Test all major features
- [ ] Verify scheduled tasks are running
- [ ] Check error logs
- [ ] Monitor performance
- [ ] Set up backups
- [ ] Configure SSL certificate
- [ ] Update DNS if needed
- [ ] Test email functionality
- [ ] Verify file uploads work
- [ ] Test biometric device connection (if applicable)

---

## Quick Reference Commands

```bash
# Clear all caches
php artisan optimize:clear

# Cache everything
php artisan optimize

# Run migrations
php artisan migrate --force

# Check queue status
php artisan queue:work --once

# Test scheduled tasks
php artisan schedule:run

# View logs
tail -f storage/logs/laravel.log
```

---

## Support

If you encounter issues:
1. Check `storage/logs/laravel.log`
2. Verify `.env` configuration
3. Check file permissions
4. Verify PHP version (8.2+)
5. Check cPanel error logs
6. Contact hosting support if needed

---

**Last Updated:** December 10, 2025  
**Version:** 1.0

