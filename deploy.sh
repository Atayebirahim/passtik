#!/bin/bash

# Passtik Production Deployment Script
# This script helps deploy the application to production safely

set -e  # Exit on error

echo "🚀 Passtik Production Deployment"
echo "================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -eq 0 ]; then 
    echo -e "${RED}❌ Do not run this script as root${NC}"
    exit 1
fi

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${RED}❌ .env file not found${NC}"
    echo "Please copy .env.example to .env and configure it"
    exit 1
fi

# Check APP_ENV
APP_ENV=$(grep "^APP_ENV=" .env | cut -d '=' -f2)
if [ "$APP_ENV" != "production" ]; then
    echo -e "${YELLOW}⚠️  Warning: APP_ENV is not set to 'production'${NC}"
    read -p "Continue anyway? (y/N) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Check APP_DEBUG
APP_DEBUG=$(grep "^APP_DEBUG=" .env | cut -d '=' -f2)
if [ "$APP_DEBUG" = "true" ]; then
    echo -e "${RED}❌ APP_DEBUG is set to 'true' in production!${NC}"
    exit 1
fi

# Check APP_KEY
APP_KEY=$(grep "^APP_KEY=" .env | cut -d '=' -f2)
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo -e "${RED}❌ APP_KEY is not set${NC}"
    echo "Run: php artisan key:generate"
    exit 1
fi

# Check DB_PASSWORD
DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d '=' -f2)
if [ -z "$DB_PASSWORD" ]; then
    echo -e "${RED}❌ DB_PASSWORD is not set${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Environment checks passed${NC}"
echo ""

# Maintenance mode
echo "📝 Enabling maintenance mode..."
php artisan down --retry=60 || true

# Pull latest code (if using git)
if [ -d .git ]; then
    echo "📥 Pulling latest code..."
    git pull origin main || git pull origin master
fi

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Clear caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Optimize application
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Set permissions
echo "🔒 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chmod 600 .env

# Check WireGuard
if command -v wg &> /dev/null; then
    echo "🔐 Checking WireGuard..."
    if sudo -n wg show &> /dev/null; then
        echo -e "${GREEN}✅ WireGuard is accessible${NC}"
    else
        echo -e "${YELLOW}⚠️  WireGuard sudo permissions may not be configured${NC}"
        echo "See PRODUCTION-READY-GUIDE.md for setup instructions"
    fi
else
    echo -e "${YELLOW}⚠️  WireGuard not installed${NC}"
fi

# Restart services
echo "🔄 Restarting services..."
if command -v systemctl &> /dev/null; then
    if systemctl is-active --quiet nginx; then
        sudo systemctl restart nginx
        sudo systemctl restart php8.2-fpm
        echo -e "${GREEN}✅ Nginx and PHP-FPM restarted${NC}"
    elif systemctl is-active --quiet apache2; then
        sudo systemctl restart apache2
        echo -e "${GREEN}✅ Apache restarted${NC}"
    fi
fi

# Disable maintenance mode
echo "✅ Disabling maintenance mode..."
php artisan up

echo ""
echo -e "${GREEN}🎉 Deployment completed successfully!${NC}"
echo ""
echo "Next steps:"
echo "1. Test the application: https://your-domain.com"
echo "2. Check logs: tail -f storage/logs/laravel.log"
echo "3. Monitor for errors"
echo ""
echo "For first-time deployment, see PRODUCTION-READY-GUIDE.md"
