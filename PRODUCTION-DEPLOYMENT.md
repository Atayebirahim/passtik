# Production Deployment Guide - Passtik SaaS

## ✅ All Production Features Implemented

### Authentication & Security
- ✅ User registration with email verification
- ✅ Login with remember me
- ✅ Password reset via email
- ✅ Rate limiting on all auth endpoints
- ✅ CSRF protection
- ✅ Session management

### Legal & Compliance
- ✅ Terms of Service page
- ✅ Privacy Policy page

### Core Features
- ✅ Router management with WireGuard VPN
- ✅ Voucher generation and redemption
- ✅ MikroTik API integration
- ✅ Real-time analytics
- ✅ Bulk operations

## Deployment Steps

### 1. VPS Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install dependencies
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd wireguard git composer

# Clone repository
cd /var/www
git clone https://github.com/Atayebirahim/passtik.git
cd passtik

# Install dependencies
composer install --no-dev --optimize-autoloader
```

### 2. Configure Environment

```bash
# Copy and edit .env
cp .env.example .env
nano .env
```

**Production .env:**
```env
APP_NAME=Passtik
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://www.passtik.net

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=passtik
DB_USERNAME=passtik_user
DB_PASSWORD=<strong-password>

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@passtik.net
MAIL_FROM_NAME="${APP_NAME}"

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database

VPS_PUBLIC_IP=www.passtik.net
```

### 3. Database Setup

```bash
# Create database
sudo mysql
```

```sql
CREATE DATABASE passtik;
CREATE USER 'passtik_user'@'localhost' IDENTIFIED BY '<strong-password>';
GRANT ALL PRIVILEGES ON passtik.* TO 'passtik_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Run migrations
php artisan key:generate
php artisan migrate --force
```

### 4. WireGuard Setup

```bash
chmod +x setup-vps-wireguard.sh
sudo ./setup-vps-wireguard.sh

# Configure sudo for www-data
sudo visudo
# Add: www-data ALL=(ALL) NOPASSWD: /usr/bin/wg, /usr/bin/wg-quick
```

### 5. Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/passtik
```

```nginx
server {
    listen 80;
    server_name www.passtik.net passtik.net;
    return 301 https://www.passtik.net$request_uri;
}

server {
    listen 443 ssl http2;
    server_name www.passtik.net;
    root /var/www/passtik/public;

    ssl_certificate /etc/letsencrypt/live/www.passtik.net/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/www.passtik.net/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/passtik /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 6. SSL Certificate

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d www.passtik.net -d passtik.net
```

### 7. Permissions

```bash
sudo chown -R www-data:www-data /var/www/passtik
sudo chmod -R 755 /var/www/passtik
sudo chmod -R 775 /var/www/passtik/storage
sudo chmod -R 775 /var/www/passtik/bootstrap/cache
sudo chmod 700 /var/www/passtik/storage/wireguard
sudo chmod 600 /var/www/passtik/storage/wireguard/privatekey
```

### 8. Optimize Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 9. Setup Queue Worker

```bash
sudo nano /etc/systemd/system/passtik-worker.service
```

```ini
[Unit]
Description=Passtik Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/passtik
ExecStart=/usr/bin/php /var/www/passtik/artisan queue:work --sleep=3 --tries=3
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable passtik-worker
sudo systemctl start passtik-worker
```

### 10. Firewall

```bash
sudo ufw allow 'Nginx Full'
sudo ufw allow 51820/udp
sudo ufw enable
```

## Post-Deployment Checklist

- [ ] Landing page loads: https://www.passtik.net
- [ ] Can register new account
- [ ] Email verification works
- [ ] Can login
- [ ] Password reset works
- [ ] Can add router
- [ ] WireGuard peer added: `sudo wg show`
- [ ] Can download MikroTik script
- [ ] Can generate vouchers
- [ ] Redeem page accessible
- [ ] Terms/Privacy pages load
- [ ] Rate limiting works (try 6+ login attempts)

## Monitoring

```bash
# Check logs
tail -f storage/logs/laravel.log

# Check WireGuard
sudo wg show

# Check queue
php artisan queue:monitor

# Check services
sudo systemctl status nginx php8.2-fpm passtik-worker wg-quick@wg0
```

## Backup Strategy

```bash
# Database backup
mysqldump -u passtik_user -p passtik > backup-$(date +%Y%m%d).sql

# WireGuard keys
sudo cp -r storage/wireguard /backup/

# Full backup
tar -czf passtik-backup-$(date +%Y%m%d).tar.gz /var/www/passtik
```

## Maintenance

```bash
# Update code
cd /var/www/passtik
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl restart php8.2-fpm nginx passtik-worker
```

## 🎉 Your SaaS is Production Ready!

All critical features implemented:
- ✅ Email verification
- ✅ Password reset
- ✅ Rate limiting
- ✅ Legal pages
- ✅ WireGuard VPN
- ✅ Security hardening
- ✅ Error handling
- ✅ Queue workers
- ✅ SSL/HTTPS
- ✅ Optimized performance

Deploy with confidence!
