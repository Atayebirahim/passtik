# Admin Panel Setup Guide

## 1. Run Migration
```bash
php artisan migrate
```

## 2. Make First User Admin
```bash
php artisan tinker
```

Then run:
```php
$user = User::where('email', 'your@email.com')->first();
$user->is_admin = true;
$user->save();
exit
```

Or directly:
```bash
php artisan tinker --execute="User::where('email', 'your@email.com')->first()->update(['is_admin' => true]);"
```

## 3. Access Admin Panel
- **Dashboard**: `/admin/dashboard`
- **User Management**: `/admin/users`
- **Subscriptions**: `/admin/subscriptions`

## Features

### Admin Dashboard (`/admin/dashboard`)
- **Statistics Cards**:
  - Total Users (with new users today)
  - Total Vouchers (active count)
  - Total Routers
  - Monthly Revenue (with pending subscriptions)

- **User Growth Chart**: 30-day visual chart
- **Plan Distribution**: Visual breakdown of Free/Pro/Enterprise users
- **Recent Users**: Last 5 registered users
- **Recent Subscription Requests**: Latest upgrade requests

### User Management (`/admin/users`)
- View all users with router/voucher counts
- Make/remove admin privileges
- Delete users (protected: can't delete last admin)
- See user plans and join dates

### Subscription Management (`/admin/subscriptions`)
- Approve/reject upgrade requests
- View user contact info (phone numbers)
- Track approval history

## Notifications
Professional toast notifications for:
- `alert_success` - Green with checkmark
- `alert_error` - Red with X
- `alert_warning` - Yellow with warning icon
- `alert_info` - Blue with info icon

Auto-dismiss after 5 seconds, manual close button.

## Security
- Admin middleware protects all admin routes
- Only users with `is_admin = true` can access
- 403 error for unauthorized access
- Last admin cannot be deleted

## Usage in Controllers
```php
return redirect()->back()->with('alert_success', 'Action completed!');
return redirect()->back()->with('alert_error', 'Something went wrong!');
return redirect()->back()->with('alert_warning', 'Please be careful!');
return redirect()->back()->with('alert_info', 'Here is some info!');
```
