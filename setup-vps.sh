#!/bin/bash

# Passtik VPS Quick Setup Script for Nginx + MySQL
# Run this on a fresh Ubuntu 20.04/22.04 VPS

set -e

echo "🚀 Passtik VPS Setup - Nginx + MySQL"
echo "====================================="
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Please run as root: sudo bash setup-vps.sh"
    exit 1
fi

# Get domain name
read -p "Enter your domain name (e.g., passtik.com): " DOMAIN
read -p "Enter your email for SSL certificate: " EMAIL
read -p "Enter MySQL root password: " -s MYSQL_ROOT_PASS
echo ""
read -p "Enter database password for passtik_user: " -s DB_PASS
echo ""

echo "📦 Installing packages..."
apt update && apt upgrade -y

# Install Nginx, MySQL, PHP
apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath \
    unzip git curl certbot python3-certbot-nginx wireguard ufw

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

echo "🗄️ Configuring MySQL..."
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '$MYSQL_ROOT_PASS';"
mysql -u root -p"$MYSQL_ROOT_PASS" -e "CREATE DATABASE passtik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p"$MYSQL_ROOT_PASS" -e "CREATE USER 'passtik_user'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -u root -p"$MYSQL_ROOT_PASS" -e "GRANT ALL PRIVILEGES ON passtik.* TO 'passtik_user'@'localhost';"
mysql -u root -p"$MYSQL_ROOT_PASS" -e "FLUSH PRIVILEGES;"

echo "📁 Setting up application directory..."
mkdir -p /var/www/passtik
chown -R www-data:www-data /var/www/passtik

echo "🌐 Configuring Nginx..."
cat > /etc/nginx/sites-available/passtik << EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root /var/www/passtik/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sf /etc/nginx/sites-available/passtik /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl restart nginx

echo "🔒 Configuring firewall..."
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw allow 51820/udp
echo "y" | ufw enable

echo "🔐 Setting up WireGuard permissions..."
cat > /etc/sudoers.d/passtik-wireguard << EOF
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg-quick
Defaults:www-data !requiretty
EOF
chmod 440 /etc/sudoers.d/passtik-wireguard

echo "📜 Installing SSL certificate..."
certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos -m $EMAIL

echo ""
echo "✅ VPS Setup Complete!"
echo ""
echo "Next steps:"
echo "1. Upload your application to /var/www/passtik"
echo "2. Configure .env file with:"
echo "   DB_PASSWORD=$DB_PASS"
echo "3. Run: cd /var/www/passtik && ./deploy.sh"
echo ""
echo "Your VPS is ready at: https://$DOMAIN"
