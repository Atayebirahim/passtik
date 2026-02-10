# 🚀 VPS DEPLOYMENT GUIDE - Nginx + MySQL

## Complete setup guide for Ubuntu VPS with Nginx and MySQL

---

## 📋 Prerequisites

- Ubuntu 20.04/22.04 VPS
- Root or sudo access
- Domain name pointed to VPS IP
- SSH access

---

## ⚡ QUICK DEPLOYMENT (15 minutes)

### Step 1: Initial VPS Setup (5 min)

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath \
    unzip git curl certbot python3-certbot-nginx wireguard

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Step 2: Configure MySQL (2 min)

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE passtik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'passtik_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON passtik.* TO 'passtik_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Deploy Application (3 min)

```bash
# Clone or upload your application
cd /var/www
sudo git clone YOUR_REPO_URL passtik
# OR upload via SCP/SFTP

cd passtik

# Set ownership
sudo chown -R www-data:www-data /var/www/passtik
sudo chmod -R 775 storage bootstrap/cache

# Configure environment
cp .env.example .env
nano .env
```

**Update .env:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=passtik
DB_USERNAME=passtik_user
DB_PASSWORD=YOUR_STRONG_PASSWORD_HERE

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=noreply@your-domain.com

VPS_PUBLIC_IP=YOUR_VPS_IP_HERE
```

```bash
# Install dependencies and setup
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Configure Nginx (2 min)

```bash
sudo nano /etc/nginx/sites-available/passtik
```

**Add this configuration:**
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/passtik/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

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
# Enable site
sudo ln -s /etc/nginx/sites-available/passtik /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Step 5: Install SSL Certificate (1 min)

```bash
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### Step 6: Configure Firewall (1 min)

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 51820/udp
sudo ufw enable
```

### Step 7: Setup WireGuard Permissions (1 min)

```bash
sudo visudo -f /etc/sudoers.d/passtik-wireguard
```

Add:
```
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg-quick
Defaults:www-data !requiretty
```

### Step 8: Setup Cron Job (30 sec)

```bash
sudo crontab -e -u www-data
```

Add:
```
* * * * * cd /var/www/passtik && php artisan schedule:run >> /dev/null 2>&1
```

---

## ✅ VERIFICATION

```bash
# Check Nginx
sudo systemctl status nginx

# Check PHP-FPM
sudo systemctl status php8.2-fpm

# Check MySQL
sudo systemctl status mysql

# Test application
curl -I https://your-domain.com

# Check Laravel
cd /var/www/passtik
php artisan about
```

---

## 🔄 AUTOMATED DEPLOYMENT

After initial setup, use the deploy script:

```bash
cd /var/www/passtik
./deploy.sh
```

---

## 📊 NGINX OPTIMIZATION (Optional)

Edit `/etc/nginx/nginx.conf`:

```nginx
user www-data;
worker_processes auto;
pid /run/nginx.pid;

events {
    worker_connections 2048;
    multi_accept on;
}

http {
    # Basic Settings
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    client_max_body_size 20M;

    # Gzip Settings
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript 
               application/json application/javascript application/xml+rss;

    # Include configs
    include /etc/nginx/mime.types;
    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;
}
```

---

## 🗄️ MYSQL OPTIMIZATION (Optional)

Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
[mysqld]
max_connections = 200
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
query_cache_size = 32M
query_cache_limit = 2M
```

Restart MySQL:
```bash
sudo systemctl restart mysql
```

---

## 📈 PHP-FPM OPTIMIZATION (Optional)

Edit `/etc/php/8.2/fpm/pool.d/www.conf`:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

---

## 💾 BACKUP SCRIPT

Create `/var/www/passtik/backup.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/passtik"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u passtik_user -p'YOUR_PASSWORD' passtik | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/passtik/storage

# Keep only last 7 days
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
chmod +x backup.sh

# Add to crontab (daily at 2 AM)
sudo crontab -e
0 2 * * * /var/www/passtik/backup.sh >> /var/log/passtik-backup.log 2>&1
```

---

## 🔍 MONITORING

### Check Logs
```bash
# Nginx access log
sudo tail -f /var/log/nginx/access.log

# Nginx error log
sudo tail -f /var/log/nginx/error.log

# PHP-FPM log
sudo tail -f /var/log/php8.2-fpm.log

# Laravel log
tail -f /var/www/passtik/storage/logs/laravel.log
```

### Check Services
```bash
# All services status
sudo systemctl status nginx php8.2-fpm mysql

# Check ports
sudo netstat -tulpn | grep -E ':(80|443|3306|51820)'
```

---

## 🚨 TROUBLESHOOTING

### Nginx 502 Bad Gateway
```bash
# Check PHP-FPM is running
sudo systemctl status php8.2-fpm

# Check socket exists
ls -la /var/run/php/php8.2-fpm.sock

# Restart services
sudo systemctl restart php8.2-fpm nginx
```

### Permission Denied
```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/passtik

# Fix permissions
sudo chmod -R 775 /var/www/passtik/storage
sudo chmod -R 775 /var/www/passtik/bootstrap/cache
```

### Database Connection Failed
```bash
# Test MySQL connection
mysql -u passtik_user -p passtik

# Check .env settings
grep DB_ /var/www/passtik/.env
```

### SSL Certificate Issues
```bash
# Renew certificate
sudo certbot renew --dry-run

# Check certificate
sudo certbot certificates
```

---

## 🎯 PRODUCTION CHECKLIST

- [ ] VPS updated and secured
- [ ] Nginx installed and configured
- [ ] MySQL installed and secured
- [ ] PHP 8.2 and extensions installed
- [ ] Application deployed to /var/www/passtik
- [ ] .env configured with production settings
- [ ] Database created and migrated
- [ ] SSL certificate installed
- [ ] Firewall configured
- [ ] WireGuard permissions set
- [ ] Cron job configured
- [ ] Backup script configured
- [ ] All services running
- [ ] Application accessible via HTTPS
- [ ] Security headers verified

---

## 🎉 DEPLOYMENT COMPLETE!

Your Passtik application is now running on:
- **Web Server:** Nginx
- **Database:** MySQL
- **PHP:** PHP 8.2-FPM
- **SSL:** Let's Encrypt
- **VPN:** WireGuard

**Access your application:**
https://your-domain.com

**Deploy updates:**
```bash
cd /var/www/passtik
./deploy.sh
```

**Monitor logs:**
```bash
tail -f /var/www/passtik/storage/logs/laravel.log
```

---

## 📚 ADDITIONAL RESOURCES

- Nginx docs: https://nginx.org/en/docs/
- Laravel deployment: https://laravel.com/docs/deployment
- Let's Encrypt: https://letsencrypt.org/
- WireGuard: https://www.wireguard.com/

**Your VPS is production-ready!** 🚀
