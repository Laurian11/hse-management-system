#!/bin/bash

# ============================================
# HSE Management System - Deployment Script
# ============================================
# This script automates the deployment process
# Usage: ./deploy.sh [environment]
# Example: ./deploy.sh production

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT=${1:-production}
APP_DIR="/var/www/hse-management-system"
BACKUP_DIR="/var/backups/hse-management"
DATE=$(date +%Y%m%d_%H%M%S)

# Functions
print_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

check_requirements() {
    print_info "Checking requirements..."
    
    # Check if running as correct user
    if [ "$EUID" -eq 0 ]; then
        print_error "Please do not run as root. Use the application user."
        exit 1
    fi
    
    # Check PHP
    if ! command -v php &> /dev/null; then
        print_error "PHP is not installed"
        exit 1
    fi
    
    PHP_VERSION=$(php -r 'echo PHP_VERSION;')
    print_info "PHP Version: $PHP_VERSION"
    
    # Check Composer
    if ! command -v composer &> /dev/null; then
        print_error "Composer is not installed"
        exit 1
    fi
    
    # Check Node.js
    if ! command -v node &> /dev/null; then
        print_error "Node.js is not installed"
        exit 1
    fi
    
    # Check if in correct directory
    if [ ! -f "artisan" ]; then
        print_error "Not in Laravel application directory"
        exit 1
    fi
    
    print_info "Requirements check passed"
}

backup_database() {
    print_info "Creating database backup..."
    
    # Load database credentials from .env
    source .env
    DB_NAME=${DB_DATABASE}
    DB_USER=${DB_USERNAME}
    DB_PASS=${DB_PASSWORD}
    
    if [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
        print_warning "Database credentials not found. Skipping backup."
        return
    fi
    
    mkdir -p "$BACKUP_DIR"
    
    if [ "$DB_CONNECTION" = "mysql" ]; then
        mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$BACKUP_DIR/db_backup_$DATE.sql.gz"
        print_info "Database backup created: $BACKUP_DIR/db_backup_$DATE.sql.gz"
    elif [ "$DB_CONNECTION" = "pgsql" ]; then
        PGPASSWORD="$DB_PASS" pg_dump -U "$DB_USER" "$DB_NAME" | gzip > "$BACKUP_DIR/db_backup_$DATE.sql.gz"
        print_info "Database backup created: $BACKUP_DIR/db_backup_$DATE.sql.gz"
    else
        print_warning "Database backup not supported for $DB_CONNECTION"
    fi
}

maintenance_mode() {
    print_info "Enabling maintenance mode..."
    php artisan down --render="errors::503" --retry=60 || true
}

deploy_code() {
    print_info "Deploying code..."
    
    # Pull latest code
    print_info "Pulling latest code from repository..."
    git pull origin main || git pull origin master
    
    # Install PHP dependencies
    print_info "Installing PHP dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # Install Node dependencies
    print_info "Installing Node dependencies..."
    npm ci --production
    
    # Build assets
    print_info "Building production assets..."
    npm run build
}

run_migrations() {
    print_info "Running database migrations..."
    php artisan migrate --force
}

optimize_application() {
    print_info "Optimizing application..."
    
    # Clear caches
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    
    # Cache configuration
    php artisan config:cache
    
    # Cache routes
    php artisan route:cache
    
    # Cache views
    php artisan view:cache
    
    # Optimize autoloader
    composer dump-autoload --optimize --classmap-authoritative
    
    print_info "Application optimized"
}

restart_services() {
    print_info "Restarting services..."
    
    # Restart queue workers if using Supervisor
    if command -v supervisorctl &> /dev/null; then
        print_info "Restarting queue workers..."
        sudo supervisorctl restart hse-queue-worker:* || print_warning "Queue workers not found"
    fi
    
    # Restart PHP-FPM if using Nginx
    if systemctl is-active --quiet php8.2-fpm || systemctl is-active --quiet php-fpm; then
        print_info "Restarting PHP-FPM..."
        sudo systemctl restart php8.2-fpm || sudo systemctl restart php-fpm
    fi
    
    print_info "Services restarted"
}

disable_maintenance_mode() {
    print_info "Disabling maintenance mode..."
    php artisan up
}

verify_deployment() {
    print_info "Verifying deployment..."
    
    # Check application status
    php artisan about
    
    # Check migrations
    php artisan migrate:status
    
    # Test queue connection
    php artisan queue:monitor || print_warning "Queue monitoring not available"
    
    print_info "Deployment verification completed"
}

# Main deployment flow
main() {
    print_info "Starting deployment to $ENVIRONMENT environment..."
    print_info "Deployment started at: $(date)"
    
    # Pre-deployment checks
    check_requirements
    
    # Backup
    backup_database
    
    # Enable maintenance mode
    maintenance_mode
    
    # Deploy
    deploy_code
    run_migrations
    optimize_application
    
    # Restart services
    restart_services
    
    # Disable maintenance mode
    disable_maintenance_mode
    
    # Verify
    verify_deployment
    
    print_info "Deployment completed successfully!"
    print_info "Deployment finished at: $(date)"
}

# Run main function
main

