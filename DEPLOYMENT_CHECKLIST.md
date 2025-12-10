# Deployment Checklist

## Pre-Deployment

- [x] All code committed and pushed to repository
- [x] Documentation consolidated
- [x] No sensitive data in code
- [x] All tests passing (if applicable)
- [x] Environment variables documented

## cPanel Setup

- [ ] Access cPanel account
- [ ] Create database and user
- [ ] Upload project files to `public_html`
- [ ] Set file permissions (755 for folders, 644 for files)
- [ ] Configure `.env` file with production values
- [ ] Point document root to `public/` folder
- [ ] Install dependencies (`composer install --no-dev`)
- [ ] Build frontend assets (`npm run build`)
- [ ] Run database migrations (`php artisan migrate --force`)
- [ ] Generate application key (`php artisan key:generate`)
- [ ] Optimize application (`php artisan optimize`)

## Scheduled Tasks

- [ ] Configure cron job for Laravel scheduler
- [ ] Configure queue worker (if using queues)
- [ ] Test scheduled tasks

## Security

- [ ] Set `APP_DEBUG=false`
- [ ] Generate strong `APP_KEY`
- [ ] Secure database credentials
- [ ] Enable HTTPS/SSL
- [ ] Configure firewall (if available)
- [ ] Set up backups

## Post-Deployment

- [ ] Test homepage loads
- [ ] Test user login
- [ ] Test database connection
- [ ] Test file uploads
- [ ] Test scheduled tasks
- [ ] Check error logs
- [ ] Monitor performance
- [ ] Test biometric device connection (if applicable)

## Quick Commands Reference

```bash
# Clear all caches
php artisan optimize:clear

# Cache everything
php artisan optimize

# Run migrations
php artisan migrate --force

# Check logs
tail -f storage/logs/laravel.log
```

---

**See CPANEL_DEPLOYMENT_GUIDE.md for detailed instructions.**

