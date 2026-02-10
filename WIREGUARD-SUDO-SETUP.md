# WireGuard Sudo Permissions Setup

## Problem
The WireGuardService uses commands like `wg`, `wg-quick`, and `wg set` which require root privileges.

## Solution Options

### Option 1: Sudoers Configuration (Recommended)

Add specific sudo permissions for the web server user without requiring a password.

#### Step 1: Identify Web Server User
```bash
# For Apache
ps aux | grep apache2 | head -1

# For Nginx with PHP-FPM
ps aux | grep php-fpm | head -1

# Common users: www-data, apache, nginx
```

#### Step 2: Create Sudoers File
```bash
sudo visudo -f /etc/sudoers.d/passtik-wireguard
```

#### Step 3: Add Permissions
```
# Allow web server to run WireGuard commands without password
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg-quick
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg set *
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg show *
```

Replace `www-data` with your actual web server user.

#### Step 4: Update WireGuardService
The service already uses the commands correctly. No code changes needed.

### Option 2: Setcap (Alternative)

Give specific capabilities to the wg binary:

```bash
sudo setcap cap_net_admin+epi /usr/bin/wg
```

**Note:** This is less secure as it gives capabilities to all users.

### Option 3: Run Laravel Queue Worker as Root (Not Recommended)

Only use this for development/testing:

```bash
sudo php artisan queue:work
```

## Verification

Test if the setup works:

```bash
# Switch to web server user
sudo -u www-data wg show

# Should display WireGuard interfaces without errors
```

## Security Considerations

1. ✅ Only grant specific commands (wg, wg-quick)
2. ✅ Use NOPASSWD only for these specific commands
3. ✅ Restrict to specific interface (wg0) if possible
4. ✅ Regular security audits
5. ✅ Monitor sudo logs: `/var/log/auth.log`

## Troubleshooting

### Error: "sudo: no tty present"
Add to sudoers:
```
Defaults:www-data !requiretty
```

### Error: "Permission denied"
Check file permissions:
```bash
ls -la /etc/sudoers.d/passtik-wireguard
# Should be: -r--r----- root root
```

### Error: "Command not found"
Verify WireGuard installation:
```bash
which wg
which wg-quick
```

## Production Deployment

1. Apply sudoers configuration
2. Test with web server user
3. Restart web server
4. Test router creation in Passtik
5. Monitor logs for any permission issues

## Rollback

To remove permissions:
```bash
sudo rm /etc/sudoers.d/passtik-wireguard
```
