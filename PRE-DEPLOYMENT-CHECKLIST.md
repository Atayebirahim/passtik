# Pre-Deployment Checklist for Passtik with WireGuard

## 1. VPS Configuration

### Add VPS Public IP to .env
```bash
# Edit .env on VPS
VPS_PUBLIC_IP=your.vps.public.ip
# Or use domain
VPS_PUBLIC_IP=passtik.net
```

### Run WireGuard Setup Script
```bash
cd /var/www/passtik
chmod +x setup-vps-wireguard.sh
sudo ./setup-vps-wireguard.sh
```

### Verify WireGuard is Running
```bash
sudo systemctl status wg-quick@wg0
sudo wg show
```

### Configure Sudo Permissions for www-data
```bash
sudo visudo
# Add this line:
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg, /usr/bin/wg-quick
```

### Open Firewall Port
```bash
sudo ufw allow 51820/udp
sudo ufw status
```

## 2. Laravel Configuration

### Run Migrations
```bash
php artisan migrate
```

### Set Correct Permissions
```bash
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
sudo chmod 700 storage/wireguard
sudo chmod 600 storage/wireguard/privatekey
```

### Test WireGuard Service
```bash
php artisan tinker
>>> $wg = new \App\Services\WireGuardService();
>>> $wg->getVpsPublicKey();
```

## 3. Security Checks

- [ ] APP_KEY is set in .env
- [ ] APP_ENV=production in .env
- [ ] APP_DEBUG=false in .env
- [ ] Database credentials are secure
- [ ] storage/wireguard/privatekey has 600 permissions
- [ ] VPN subnet (10.0.0.0/24) doesn't conflict with existing networks
- [ ] Firewall allows port 51820/udp
- [ ] www-data has sudo access to wg commands only

## 4. MikroTik Requirements

### Customer Must Have:
- MikroTik RouterOS 7.0+ (WireGuard support)
- Admin access to router
- Ability to run scripts in WinBox

### Verify MikroTik Supports WireGuard:
```routeros
/interface wireguard print
# Should not show "bad command name"
```

## 5. Testing Workflow

### Test 1: Add Router
1. Login to Passtik
2. Add new router
3. Check no errors
4. Verify VPN peer added: `sudo wg show`

### Test 2: Download Script
1. Click router name
2. Download .rsc script
3. Verify script contains correct IPs and keys

### Test 3: MikroTik Connection
1. Run script on MikroTik
2. Check peer status: `/interface wireguard peers print`
3. Ping VPS: `/ping 10.0.0.1`
4. Test API: Try creating voucher

### Test 4: Delete Router
1. Delete router from Passtik
2. Verify peer removed: `sudo wg show`

## 6. Common Issues

### Issue: "WireGuard is not installed"
```bash
sudo apt update
sudo apt install wireguard
```

### Issue: "Failed to add peer to WireGuard"
```bash
# Check sudo permissions
sudo -u www-data sudo wg show
# Should work without password
```

### Issue: "The payload is invalid" (decryption error)
```bash
# Old routers without VPN keys
# Solution: Delete and re-add router
```

### Issue: VPN not connecting from MikroTik
```bash
# On VPS, check firewall
sudo ufw status
sudo ufw allow 51820/udp

# Check WireGuard logs
sudo journalctl -u wg-quick@wg0 -f

# On MikroTik, check logs
/log print where topics~"wireguard"
```

## 7. Monitoring

### Check VPN Status
```bash
# See all connected peers
sudo wg show

# See specific peer
sudo wg show wg0 peers

# Check interface
ip addr show wg0
```

### Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

## 8. Backup

### Backup WireGuard Keys
```bash
sudo cp -r storage/wireguard /backup/wireguard-$(date +%Y%m%d)
```

### Backup WireGuard Config
```bash
sudo cp /etc/wireguard/wg0.conf /backup/wg0.conf-$(date +%Y%m%d)
```

## 9. Production Deployment Commands

```bash
# On VPS
cd /var/www/passtik
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx

# Verify WireGuard
sudo systemctl status wg-quick@wg0
sudo wg show
```

## 10. Post-Deployment Verification

- [ ] Landing page loads: https://passtik.net
- [ ] Can login
- [ ] Can add router without errors
- [ ] `sudo wg show` shows new peer
- [ ] Can download MikroTik script
- [ ] Script contains correct VPS IP
- [ ] Can delete router
- [ ] Peer removed from `sudo wg show`

## Ready for Production? ✓

All checks passed? Deploy with confidence!
