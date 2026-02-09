# Hybrid Voucher System Implementation Guide

## Overview
This implementation provides a secure, scalable voucher management system with validation in Laravel and authentication on MikroTik devices.

## Features Implemented

### ✅ Full Control
- Vouchers are generated in Laravel database only
- MikroTik users are created ONLY when voucher is redeemed
- Complete control over voucher lifecycle

### ✅ Better Security
- Rate limiting (5 attempts per 5 minutes)
- MAC address binding (one device per voucher)
- Expiration dates for unredeemed vouchers
- Status tracking (pending, active, used, expired)

### ✅ Rich Analytics
- Track redemption time, IP address, device type
- User agent logging
- Complete audit trail in `voucher_redemptions` table

### ✅ Prevent Abuse
- One-time use enforcement
- Device binding after first redemption
- Automatic expiration handling
- Failed redemption logging

### ✅ Flexible Business Logic
- Configurable duration (5 min to 30 days)
- Bandwidth limits per voucher
- Expiration dates (1-365 days)
- Multiple status states

### ✅ Better UX
- Public redemption page at `/redeem`
- Clear success/error messages
- Connection instructions after redemption

### ✅ Full Reports
- Dashboard with statistics
- Redemption charts (last 30 days)
- Status distribution pie chart
- Recent redemptions table
- Filter by router

## Database Changes

### New Voucher Fields
```
- password: Separate password (can differ from code)
- duration: Session duration in minutes
- bandwidth: Speed limit (e.g., "1M/1M")
- status: pending|active|used|expired
- expires_at: When voucher expires if not redeemed
- redeemed_at: When voucher was activated
- redeemed_ip: IP address of redemption
- device_mac: MAC address of device
- device_info: User agent string
```

### New Table: voucher_redemptions
```
- voucher_id: Foreign key to vouchers
- ip_address: Redemption IP
- mac_address: Device MAC
- user_agent: Browser/device info
- device_type: mobile|tablet|desktop
- status: success|failed
- error_message: Failure reason
- metadata: Additional JSON data
```

## Usage Flow

### 1. Generate Vouchers (Admin)
```
1. Go to Vouchers → Create Voucher
2. Select router, profile, quantity
3. Set duration, bandwidth, expiration
4. Vouchers created in database ONLY (not on MikroTik yet)
```

### 2. Distribute Vouchers
```
- Print vouchers (pending status)
- Share codes with customers
- Vouchers remain inactive until redeemed
```

### 3. Customer Redeems Voucher
```
1. Customer visits /redeem page
2. Enters voucher code
3. System validates:
   - Code exists
   - Status is pending
   - Not expired
   - Device not already bound
4. If valid:
   - Creates user on MikroTik
   - Updates voucher status to active
   - Logs redemption details
   - Shows connection credentials
```

### 4. Customer Connects
```
1. Connect to WiFi network
2. Use provided username/password
3. MikroTik authenticates and grants access
4. Session limited by duration and bandwidth
```

## API Endpoints

### Public Redemption
```
POST /api/vouchers/redeem
Body: { "code": "ABC123", "mac": "00:11:22:33:44:55" }
Response: { "success": true, "data": { "username": "ABC123", "password": "ABC123", "duration": "1h", "bandwidth": "1M/1M" } }
```

## Reports & Analytics

### Access Reports
```
Navigate to: Vouchers → 📊 Reports
```

### Available Metrics
- Total vouchers
- Pending (not redeemed)
- Active (currently in use)
- Used (session completed)
- Expired (never redeemed)
- Redemptions by day (chart)
- Status distribution (pie chart)
- Recent redemptions table

## Migration Steps

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Update Existing Vouchers (Optional)
```php
// If you have existing vouchers, update them:
DB::table('vouchers')->update([
    'status' => 'pending',
    'duration' => 60,
    'bandwidth' => '1M/1M',
    'expires_at' => now()->addDays(30)
]);
```

### 3. Configure MikroTik
```
# No special configuration needed
# Vouchers will be created via API when redeemed
```

## Security Features

### Rate Limiting
- 5 redemption attempts per IP per 5 minutes
- Prevents brute force attacks

### MAC Binding
- First redemption binds voucher to device MAC
- Subsequent attempts from different MAC are rejected

### Expiration
- Unredeemed vouchers expire after configured days
- Automatic status update to 'expired'

### Audit Trail
- Every redemption attempt logged
- Success and failure tracking
- IP, MAC, device info recorded

## Customization

### Change Redemption Page URL
Edit `routes/web.php`:
```php
Route::get('/your-custom-url', [VoucherController::class, 'redeemPage']);
```

### Adjust Rate Limits
Edit `VoucherController::redeem()`:
```php
if (RateLimiter::tooManyAttempts($key, 10)) { // Change 5 to 10
```

### Custom Duration Options
Edit `resources/views/vouchers/create.blade.php`:
```php
<option value="custom_minutes">Custom Duration</option>
```

## Troubleshooting

### Voucher Not Activating
1. Check MikroTik connection
2. Verify router credentials
3. Check Laravel logs: `storage/logs/laravel.log`

### Rate Limit Issues
```bash
# Clear rate limiter cache
php artisan cache:clear
```

### Database Issues
```bash
# Rollback and re-run migrations
php artisan migrate:rollback
php artisan migrate
```

## Benefits Over Old System

| Feature | Old System | New Hybrid System |
|---------|-----------|-------------------|
| Voucher Creation | Immediately on MikroTik | Database only |
| Activation | Pre-activated | On-demand |
| Security | Basic | Rate limiting, MAC binding |
| Analytics | Limited | Comprehensive |
| Abuse Prevention | Minimal | Multiple layers |
| Expiration | Manual | Automatic |
| Audit Trail | None | Complete |
| Reports | Basic | Advanced charts |

## Next Steps

1. ✅ Run migrations
2. ✅ Test voucher creation
3. ✅ Test redemption flow
4. ✅ Review reports
5. ✅ Customize redemption page branding
6. ✅ Set up automated cleanup (optional)

## Support

For issues or questions, check:
- Laravel logs: `storage/logs/laravel.log`
- MikroTik logs: `/log print`
- Database: Check `vouchers` and `voucher_redemptions` tables
