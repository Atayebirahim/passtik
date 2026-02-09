# Production Deployment Instructions for passtik.net

## Files Updated for Production

✅ hotspot/login.html - Updated to https://www.passtik.net/redeem
✅ resources/views/vouchers/redeem.blade.php - Updated API to https://www.passtik.net/api/vouchers/redeem

## Deployment Steps

### 1. Upload Updated Files to VPS

```bash
# On your local machine
cd /opt/lampp/htdocs/passtik
scp resources/views/vouchers/redeem.blade.php root@YOUR_VPS_IP:/var/www/passtik/resources/views/vouchers/
scp hotspot/login.html root@YOUR_VPS_IP:/root/
```

### 2. On VPS - Clear Laravel Cache

```bash
ssh root@YOUR_VPS_IP
cd /var/www/passtik
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 3. Configure MikroTik

**Add Walled Garden:**
```
/ip hotspot walled-garden
add dst-host=www.passtik.net comment="Passtik Server"
add dst-host=passtik.net comment="Passtik Server Alt"
```

**Upload login.html:**
- Open WinBox → Connect to MikroTik
- Go to Files menu
- Upload the updated login.html from /root/login.html
- Move to hotspot directory

**Set Hotspot Profile:**
```
/ip hotspot profile
set [find] html-directory=hotspot
```

### 4. Test the System

**Test 1: Access Website**
- Visit: https://www.passtik.net
- Should load Laravel app

**Test 2: Redeem Page**
- Visit: https://www.passtik.net/redeem
- Should show voucher redemption form

**Test 3: Create Voucher**
- Login to admin panel
- Create a test voucher
- Note the voucher code

**Test 4: Redeem Voucher**
- Go to https://www.passtik.net/redeem
- Enter voucher code
- Should activate and show credentials

**Test 5: MikroTik Login**
- Connect phone to MikroTik hotspot WiFi
- Should redirect to https://www.passtik.net/redeem
- Click "Get Voucher Code" link
- Redeem a voucher
- Use voucher code to login to WiFi

### 5. Verify MikroTik User Created

```
/ip hotspot user print
```

Should see the redeemed voucher code in the list.

## Troubleshooting

**Issue: Can't access passtik.net from hotspot**
```
/ip hotspot walled-garden print
```
Verify passtik.net is in the list.

**Issue: SSL certificate error**
```bash
certbot certificates
systemctl status nginx
```

**Issue: Voucher not creating on MikroTik**
- Check router credentials in database
- Test MikroTik API connection from VPS:
```bash
cd /var/www/passtik
php artisan tinker
# Then test connection
```

**Issue: 500 error**
```bash
tail -f /var/www/passtik/storage/logs/laravel.log
```

## Production Checklist

- [x] SSL certificate installed
- [x] Domain pointing to VPS
- [x] Files updated with production URL
- [ ] Laravel deployed to VPS
- [ ] Database configured
- [ ] MikroTik walled garden configured
- [ ] login.html uploaded to MikroTik
- [ ] Test voucher creation
- [ ] Test voucher redemption
- [ ] Test WiFi login

## Support

For issues, check:
1. Laravel logs: /var/www/passtik/storage/logs/laravel.log
2. Nginx logs: /var/log/nginx/error.log
3. MikroTik logs: /log print

## Next Steps

1. Upload files to VPS
2. Configure MikroTik walled garden
3. Upload login.html to MikroTik
4. Test complete flow
5. Monitor logs for any errors
