# MikroTik Hotspot Files

## Upload Instructions

### Method 1: WinBox (Recommended)
1. Open WinBox and connect to your MikroTik
2. Go to **Files** menu
3. Drag and drop `login.html` to the Files window
4. In Terminal, run:
   ```
   /ip hotspot profile
   set [find] html-directory=hotspot
   ```

### Method 2: FTP
1. Enable FTP on MikroTik:
   ```
   /ip service enable ftp
   ```
2. Connect via FTP client (FileZilla, etc.)
   - Host: Your MikroTik IP
   - Username: admin
   - Password: Your admin password
3. Upload `login.html` to `/hotspot/` folder

### Method 3: Web Interface
1. Access MikroTik WebFig: http://YOUR_MIKROTIK_IP
2. Go to **Files**
3. Upload `login.html`
4. Move it to hotspot directory via Terminal

## What This Does
- Automatically redirects users to: http://192.168.1.99:8000/redeem
- Shows fallback login form if Laravel is unavailable
- Maintains MikroTik authentication security
- Uses session storage to prevent redirect loops

## After Upload
1. Clear browser cache on test device
2. Connect to WiFi
3. Should auto-redirect to voucher redemption page
4. After redeeming voucher, use credentials to login

## Troubleshooting
- If redirect doesn't work, clear browser cache and sessionStorage
- Verify IP binding is set: `/ip hotspot ip-binding print`
- Check Laravel is running: `php artisan serve --host=192.168.1.99 --port=8000`
