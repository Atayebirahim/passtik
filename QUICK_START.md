# 🚀 Quick Start Guide - Hybrid Voucher System

## ✅ System is Ready!

The hybrid voucher system has been successfully implemented. Here's how to use it:

## 1. Create Vouchers (Admin)

### Step 1: Navigate to Vouchers
```
Dashboard → Vouchers → Create Voucher
```

### Step 2: Fill the Form
- **Router**: Select your MikroTik router
- **Profile**: Choose hotspot profile
- **Quantity**: How many vouchers (1-100)
- **Code Length**: 4-12 characters
- **Voucher Type**: Numbers only or Mixed
- **Auth Type**: Same or different username/password
- **Duration**: 30 min to 30 days
- **Bandwidth**: 512k to 20M
- **Expires In**: 7 to 365 days

### Step 3: Generate
Click "Create Vouchers" - they will be created with **PENDING** status

## 2. Print & Distribute

### Print Vouchers
```
Vouchers → Print Pending
```

This generates a printable sheet with:
- Voucher codes
- Duration
- Bandwidth
- Expiration date

### Distribute to Customers
- Print and hand out
- Email codes
- Display on screen
- Share via messaging

## 3. Customer Redemption

### Redemption Page
```
https://your-domain.com/redeem
```

### Customer Steps:
1. Visit redemption page
2. Enter voucher code
3. Click "Activate Voucher"
4. Receive credentials:
   - Username
   - Password
   - Duration
   - Speed limit

### What Happens Behind the Scenes:
1. ✅ Laravel validates voucher
2. ✅ Checks if expired
3. ✅ Verifies not already used
4. ✅ Creates user on MikroTik
5. ✅ Updates voucher status to ACTIVE
6. ✅ Logs redemption details
7. ✅ Returns credentials

## 4. Customer Connection

### WiFi Connection:
1. Connect to your WiFi network
2. Hotspot login page appears
3. Enter username from redemption
4. Enter password from redemption
5. Click Login
6. Internet access granted!

### Session Details:
- Duration: As configured (e.g., 1 hour)
- Speed: As configured (e.g., 1 Mbps)
- Device: Bound to MAC address
- Expires: After duration or voucher expiry

## 5. View Reports

### Access Analytics
```
Vouchers → 📊 Reports
```

### Available Data:
- **Total Vouchers**: All created
- **Pending**: Not yet redeemed
- **Active**: Currently in use
- **Used**: Session completed
- **Expired**: Never redeemed

### Charts:
- Redemptions over last 30 days (line chart)
- Status distribution (pie chart)
- Recent redemptions table

### Filter Options:
- By router
- By date range
- By status

## 6. Manage Vouchers

### View All Vouchers
```
Vouchers → Select Router
```

### Search & Filter:
- Search by code
- Filter by status
- Sort by date

### Actions:
- **View**: See full details
- **Delete**: Remove voucher

### Status Meanings:
- 🟡 **PENDING**: Created, not redeemed
- 🟢 **ACTIVE**: Redeemed, in use
- 🟣 **USED**: Session completed
- 🔴 **EXPIRED**: Never redeemed, expired

## Common Scenarios

### Scenario 1: Bulk Voucher Creation
```
1. Create Vouchers
2. Quantity: 50
3. Duration: 1 hour
4. Bandwidth: 2M/2M
5. Expires: 30 days
6. Generate
7. Print all
8. Distribute
```

### Scenario 2: Premium Vouchers
```
1. Create Vouchers
2. Quantity: 10
3. Duration: 1 day
4. Bandwidth: 10M/10M
5. Expires: 90 days
6. Generate
7. Sell at premium price
```

### Scenario 3: Event Vouchers
```
1. Create Vouchers
2. Quantity: 100
3. Duration: 3 hours
4. Bandwidth: 5M/5M
5. Expires: 7 days
6. Generate
7. Distribute at event
```

## Security Features

### Automatic Protection:
- ✅ Rate limiting (5 attempts per 5 min)
- ✅ MAC binding (one device only)
- ✅ Expiration enforcement
- ✅ One-time use
- ✅ Failed attempt logging

### Manual Actions:
- Delete suspicious vouchers
- Review failed redemptions
- Monitor usage patterns

## Troubleshooting

### Voucher Won't Redeem
**Check:**
1. Is it expired?
2. Already used?
3. Correct code entered?
4. MikroTik online?

**Solution:**
- Check voucher status in admin
- Verify MikroTik connection
- Check Laravel logs

### Customer Can't Connect
**Check:**
1. Credentials correct?
2. WiFi network correct?
3. MikroTik hotspot running?
4. Session not expired?

**Solution:**
- Verify credentials from redemption
- Check MikroTik hotspot status
- Review active sessions

### Reports Not Showing
**Check:**
1. Router selected?
2. Vouchers created?
3. Any redemptions?

**Solution:**
- Select specific router
- Create test vouchers
- Try test redemption

## Best Practices

### 1. Voucher Creation
- Create in batches
- Set reasonable expiration
- Use appropriate duration
- Match bandwidth to plan

### 2. Distribution
- Print clearly
- Include instructions
- Provide support contact
- Set expectations

### 3. Monitoring
- Check reports daily
- Review failed attempts
- Monitor usage patterns
- Clean up expired vouchers

### 4. Customer Support
- Provide redemption URL
- Include WiFi name
- Explain duration limits
- Offer troubleshooting

## URLs to Remember

```
Admin Panel:     /vouchers
Create Vouchers: /vouchers/create
Reports:         /vouchers-reports
Print:           /vouchers/print/sheet
Redemption:      /redeem (public)
```

## Quick Commands

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Clear Cache
```bash
php artisan cache:clear
```

### View Database
```bash
php artisan tinker
>>> Voucher::count()
>>> VoucherRedemption::latest()->take(5)->get()
```

## Support

### Need Help?
1. Check `HYBRID_VOUCHER_SYSTEM.md` for details
2. Review `IMPLEMENTATION_SUMMARY.md`
3. Check Laravel logs
4. Verify MikroTik connection

### Common Issues:
- **Connection failed**: Check MikroTik credentials
- **Rate limited**: Wait 5 minutes
- **Expired voucher**: Create new one
- **MAC mismatch**: Voucher bound to different device

## Success! 🎉

Your hybrid voucher system is ready to use. Start creating vouchers and enjoy:
- ✅ Enhanced security
- ✅ Complete control
- ✅ Detailed analytics
- ✅ Professional management

Happy voucher management! 🚀
