# FTP Configuration Guide

## Overview

Laravel supports FTP/SFTP for file storage. This guide shows how to configure FTP for your HSE Management System.

## Use Cases

1. **Remote File Storage** - Store files on remote FTP server
2. **Backup Storage** - Backup system files to FTP
3. **Document Archiving** - Archive documents to FTP server
4. **Multi-Server Deployment** - Share files across multiple servers

## Configuration

### Step 1: Add FTP Configuration to `config/filesystems.php`

Add FTP disk configuration:

```php
'disks' => [
    // ... existing disks ...
    
    'ftp' => [
        'driver' => 'ftp',
        'host' => env('FTP_HOST', 'ftp.example.com'),
        'username' => env('FTP_USERNAME'),
        'password' => env('FTP_PASSWORD'),
        'port' => env('FTP_PORT', 21),
        'root' => env('FTP_ROOT', '/'),
        'passive' => env('FTP_PASSIVE', true),
        'ssl' => env('FTP_SSL', false),
        'timeout' => env('FTP_TIMEOUT', 30),
    ],
    
    'sftp' => [
        'driver' => 'sftp',
        'host' => env('SFTP_HOST', 'sftp.example.com'),
        'username' => env('SFTP_USERNAME'),
        'password' => env('SFTP_PASSWORD'),
        'privateKey' => env('SFTP_PRIVATE_KEY'),
        'passphrase' => env('SFTP_PASSPHRASE'),
        'port' => env('SFTP_PORT', 22),
        'root' => env('SFTP_ROOT', '/'),
        'timeout' => env('SFTP_TIMEOUT', 30),
        'directoryPerm' => 0755,
    ],
],
```

### Step 2: Add Environment Variables to `.env`

```env
# FTP Configuration
FTP_HOST=ftp.hesu.co.tz
FTP_USERNAME=your_ftp_username
FTP_PASSWORD=your_ftp_password
FTP_PORT=21
FTP_ROOT=/public_html/storage
FTP_PASSIVE=true
FTP_SSL=false
FTP_TIMEOUT=30

# SFTP Configuration (More Secure)
SFTP_HOST=sftp.hesu.co.tz
SFTP_USERNAME=your_sftp_username
SFTP_PASSWORD=your_sftp_password
SFTP_PORT=22
SFTP_ROOT=/storage
SFTP_TIMEOUT=30
```

## Usage Examples

### Store File on FTP

```php
use Illuminate\Support\Facades\Storage;

// Store file on FTP
$path = Storage::disk('ftp')->put('incident-attachments', $file);

// Get file from FTP
$contents = Storage::disk('ftp')->get('incident-attachments/file.jpg');

// Download file from FTP
return Storage::disk('ftp')->download('incident-attachments/file.jpg');
```

### Backup to FTP

```php
// Backup database to FTP
$backupFile = storage_path('app/backups/database_' . date('Y-m-d') . '.sql');
Storage::disk('ftp')->put('backups/database_' . date('Y-m-d') . '.sql', file_get_contents($backupFile));
```

## Security Considerations

1. **Use SFTP instead of FTP** - SFTP is encrypted, FTP is not
2. **Store credentials securely** - Never commit `.env` file
3. **Use strong passwords** - Complex FTP passwords
4. **Limit FTP access** - Restrict to necessary directories only
5. **Enable SSL/TLS** - Use FTPS if FTP is required

## Common FTP Providers

### cPanel FTP
- Host: `ftp.yourdomain.com`
- Port: `21`
- Username: Your cPanel username
- Password: Your cPanel password

### FileZilla Server
- Host: Your server IP or domain
- Port: `21` (or custom)
- Username: Created in FileZilla
- Password: Set in FileZilla

### Cloud FTP Services
- AWS Transfer Family
- Azure FTP
- Google Cloud Storage (via FTP)

## Troubleshooting

### Connection Failed
- Check firewall allows FTP port (21)
- Verify credentials are correct
- Test with FTP client (FileZilla, WinSCP)
- Check if passive mode is required

### Permission Denied
- Verify FTP user has write permissions
- Check root directory path is correct
- Ensure directory exists on FTP server

### Timeout Issues
- Increase timeout value
- Check network connectivity
- Verify FTP server is accessible

## Testing FTP Connection

Create a test command:

```bash
php artisan tinker
```

Then test:

```php
Storage::disk('ftp')->put('test.txt', 'Hello FTP!');
Storage::disk('ftp')->exists('test.txt'); // Should return true
Storage::disk('ftp')->get('test.txt'); // Should return 'Hello FTP!'
```

