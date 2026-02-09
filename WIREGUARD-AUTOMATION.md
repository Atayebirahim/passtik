# WireGuard VPN Automation for Passtik

## Overview

Automated VPN setup that connects your VPS to MikroTik routers securely without port forwarding.

## Architecture

```
Customer WiFi → MikroTik Router ←→ WireGuard VPN ←→ VPS (passtik.net)
                (10.0.0.X)          Encrypted      (10.0.0.1)
```

## How It Works

### 1. **VPS Initial Setup** (One-time)
```bash
cd /var/www/passtik
chmod +x setup-vps-wireguard.sh
sudo ./setup-vps-wireguard.sh
```

This installs WireGuard and creates the VPN server at `10.0.0.1/24`.

### 2. **User Adds Router** (Automated)
When user clicks "Add Router" in Passtik:

1. **Laravel generates unique VPN config**:
   - Assigns IP: `10.0.0.11`, `10.0.0.12`, etc.
   - Generates WireGuard keypair for router
   - Stores encrypted private key in database

2. **VPS peer added automatically**:
   - `WireGuardService::addPeerToVps()` runs
   - Executes: `wg set wg0 peer <PUBLIC_KEY> allowed-ips <IP>/32`
   - Saves config: `wg-quick save wg0`

3. **User downloads MikroTik script**:
   - Pre-configured with all keys and IPs
   - Ready to paste into router

### 3. **MikroTik Setup** (User runs script)
User pastes script in WinBox → System → Scripts → Run:

```routeros
/interface wireguard add name=wireguard-passtik private-key="..."
/interface wireguard peers add endpoint-address=passtik.net ...
/ip address add address=10.0.0.11/24 interface=wireguard-passtik
/ip service set api address=10.0.0.0/24 port=8728 disabled=no
```

### 4. **Connection Established** (Automatic)
- MikroTik connects to VPS via WireGuard
- VPN tunnel encrypted and persistent
- Laravel can now reach router at `10.0.0.X:8728`

## Key Components

### WireGuardService.php
```php
generatePeerConfig($routerId)  // Creates keys and IP for new router
addPeerToVps($publicKey, $ip)  // Adds peer to VPS WireGuard
removePeerFromVps($publicKey)  // Removes peer when router deleted
generateMikrotikScript()       // Creates ready-to-run script
```

### RouterController.php
```php
store()   // Auto-generates VPN config when router added
show()    // Displays downloadable setup script
destroy() // Auto-removes VPN peer when router deleted
```

### Database Fields
```
routers.vpn_ip           // 10.0.0.11, 10.0.0.12, etc.
routers.vpn_public_key   // Router's public key
routers.vpn_private_key  // Router's private key (encrypted)
```

## Security Features

✅ **No port forwarding** - Router doesn't need public IP  
✅ **Encrypted tunnel** - All API traffic through WireGuard  
✅ **Private network** - API only accessible via VPN (10.0.0.0/24)  
✅ **Key encryption** - Private keys encrypted in database  
✅ **Auto cleanup** - Peers removed when router deleted  

## User Workflow

1. User logs into Passtik
2. Clicks "Add Router"
3. Enters router name and credentials
4. Downloads generated script
5. Pastes script in MikroTik WinBox
6. VPN connects automatically
7. User can now manage vouchers

## Verification Commands

### On VPS:
```bash
# Check WireGuard status
sudo wg show

# See connected peers
sudo wg show wg0 peers

# Test connection to router
ping 10.0.0.11
telnet 10.0.0.11 8728
```

### On MikroTik:
```routeros
# Check WireGuard interface
/interface wireguard print

# Check peer status
/interface wireguard peers print

# Test VPS connection
/ping 10.0.0.1
```

## Troubleshooting

### VPN not connecting:
```bash
# On VPS
sudo systemctl status wg-quick@wg0
sudo wg show
sudo journalctl -u wg-quick@wg0 -f

# On MikroTik
/log print where topics~"wireguard"
/interface wireguard peers print detail
```

### API not reachable:
```bash
# Verify VPN IP
ping 10.0.0.11

# Check API port
telnet 10.0.0.11 8728

# Test from Laravel
php artisan tinker
>>> $client = new \RouterOS\Client(['host' => '10.0.0.11', ...]);
```

## Advantages Over Port Forwarding

| Feature | Port Forwarding | WireGuard VPN |
|---------|----------------|---------------|
| Setup complexity | High | Automated |
| Security | Exposed port | Encrypted tunnel |
| Works behind NAT | No | Yes |
| Multiple locations | Complex | Easy |
| Firewall friendly | No | Yes |
| Maintenance | Manual | Automatic |

## Scaling

Each router gets unique IP in `10.0.0.0/24` range:
- VPS: `10.0.0.1`
- Router 1: `10.0.0.11`
- Router 2: `10.0.0.12`
- Router 3: `10.0.0.13`
- ... up to 244 routers

For more routers, expand to `10.0.0.0/16` (65,534 routers).

## Files Created

- `app/Services/WireGuardService.php` - VPN automation logic
- `resources/views/routers/show.blade.php` - Script download page
- `setup-vps-wireguard.sh` - VPS initial setup
- `database/migrations/*_add_vpn_fields_to_routers_table.php` - VPN fields
- `storage/wireguard/privatekey` - VPS private key
- `storage/wireguard/publickey` - VPS public key

## Next Steps

1. Run VPS setup script
2. Run migration: `php artisan migrate`
3. Add a test router
4. Download and run MikroTik script
5. Verify connection with `wg show`
