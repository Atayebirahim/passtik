# 🚀 VPS DEPLOYMENT - QUICK REFERENCE

## For Ubuntu VPS with Nginx + MySQL

---

## ⚡ FASTEST DEPLOYMENT (5 minutes)

### Option 1: Automated Setup Script

```bash
# On your VPS (as root)
sudo bash setup-vps.sh
```

This will:
- Install Nginx, MySQL, PHP 8.2
- Configure database
- Setup Nginx virtual host
- Install SSL certificate
- Configure firewall
- Setup WireGuard permissions

Then upload your app and run:
```bash
cd /var/www/passtik
./deploy.sh
```

---

## 📋 MANUAL DEPLOYMENT (15 minutes)

### 1. Install Stack (3 min)
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql \
    php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd \
    php8.2-bcmath unzip git curl certbot python3-certbot-nginx wireguard

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Setup Database (2 min)
```bash
sudo mysql
```
```sql
CREATE DATABASE passtik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'passtik_user'@'localhost' IDENTIFIED BY 'YOUR_PASSWORD';
GRANT ALL PRIVILEGES ON passtik.* TO 'passtik_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Deploy App (3 min)
```bash
cd /var/www
sudo git clone YOUR_REPO passtik
cd passtik
sudo chown -R www-data:www-data .
sudo chmod -R 775 storage bootstrap/cache

cp .env.example .env
nano .env  # Update DB_PASSWORD, APP_URL, etc.

composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan config:cache
```

### 4. Configure Nginx (2 min)
```bash
sudo nano /etc/nginx/sites-available/passtik
```

Paste:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/passtik/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/passtik /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 5. Install SSL (1 min)
```bash
sudo certbot --nginx -d your-domain.com
```

### 6. Configure Firewall (1 min)
```bash
sudo ufw allow 22,80,443/tcp
sudo ufw allow 51820/udp
sudo ufw enable
```

### 7. Setup WireGuard (1 min)
```bash
sudo visudo -f /etc/sudoers.d/passtik-wireguard
```
Add:
```
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg-quick
Defaults:www-data !requiretty
```

### 8. Setup Cron (30 sec)
```bash
sudo crontab -e -u www-data
```
Add:
```
* * * * * cd /var/www/passtik && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔄 UPDATE DEPLOYMENT

After initial setup, just run:
```bash
cd /var/www/passtik
./deploy.sh
```

---

## ✅ VERIFY

```bash
# Check services
sudo systemctl status nginx php8.2-fpm mysql

# Test app
curl -I https://your-domain.com

# Check Laravel
cd /var/www/passtik
php artisan about
```

---

## 📁 FILE LOCATIONS

- **App:** `/var/www/passtik`
- **Nginx config:** `/etc/nginx/sites-available/passtik`
- **PHP-FPM config:** `/etc/php/8.2/fpm/pool.d/www.conf`
- **MySQL config:** `/etc/mysql/mysql.conf.d/mysqld.cnf`
- **Logs:** `/var/www/passtik/storage/logs/laravel.log`

---

## 🚨 TROUBLESHOOTING

**502 Bad Gateway:**
```bash
sudo systemctl restart php8.2-fpm nginx
```

**Permission denied:**
```bash
sudo chown -R www-data:www-data /var/www/passtik
sudo chmod -R 775 storage bootstrap/cache
```

**Database error:**
```bash
# Check .env
grep DB_ /var/www/passtik/.env

# Test connection
mysql -u passtik_user -p passtik
```

---

## 📚 FULL DOCUMENTATION

- **Complete guide:** VPS-DEPLOYMENT-NGINX.md
- **Automated script:** setup-vps.sh
- **Deploy script:** deploy.sh

---

## 🎯 DEPLOYMENT PATHS

### Path 1: Automated (Fastest)
```bash
sudo bash setup-vps.sh  # On VPS
# Upload app
./deploy.sh             # Deploy
```

### Path 2: Manual (Full Control)
Follow steps 1-8 above

### Path 3: Hybrid
```bash
sudo bash setup-vps.sh  # Setup VPS
# Then customize as needed
```

---

## 🎉 DONE!

Your app is now running on:
- **Nginx** (web server)
- **PHP 8.2-FPM** (PHP processor)
- **MySQL** (database)
- **Let's Encrypt** (SSL)
- **WireGuard** (VPN)

**Access:** https://your-domain.com

**Deploy updates:** `./deploy.sh`

**Monitor:** `tail -f storage/logs/laravel.log`

---

**VPS deployment complete!** 🚀
