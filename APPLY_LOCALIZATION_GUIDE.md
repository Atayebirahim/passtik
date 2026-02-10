# Apply Localization to Remaining Pages - Step by Step

## Quick Pattern for Any Blade File

### Step 1: Update @section directives
```blade
<!-- BEFORE -->
@section('title', 'Page Title - Passtik')
@section('page-title', 'Page Title')

<!-- AFTER -->
@section('title', __('messages.page_key') . ' - ' . __('messages.app_name'))
@section('page-title', __('messages.page_key'))
```

### Step 2: Replace all hardcoded text
```blade
<!-- BEFORE -->
<button>Create</button>
<label>Email Address</label>
<span>Active</span>

<!-- AFTER -->
<button>{{ __('messages.create') }}</button>
<label>{{ __('messages.email') }}</label>
<span>{{ __('messages.active') }}</span>
```

### Step 3: Add RTL-aware classes
```blade
<!-- BEFORE -->
<div class="ml-4">

<!-- AFTER -->
<div class="{{ $isRtl ? 'mr-4' : 'ml-4' }}">
```

## Quick Pattern for Any Controller

### Replace all user-facing strings
```php
// BEFORE
return back()->with('alert_success', 'Item created successfully');
return back()->with('alert_error', 'Access denied');

// AFTER
return back()->with('alert_success', __('messages.item_created'));
return back()->with('alert_error', __('messages.access_denied'));
```

### For messages with variables
```php
// BEFORE
return back()->with('alert_success', $count . ' items created');

// AFTER
return back()->with('alert_success', __('messages.items_created_count', ['count' => $count]));
```

## Files to Update (Priority Order)

### 1. Router Views (High Priority)
- [ ] `/resources/views/routers/index.blade.php`
- [ ] `/resources/views/routers/show.blade.php`
- [ ] `/resources/views/routers/edit.blade.php`
- [ ] `/resources/views/routers/manage.blade.php`
- [ ] `/resources/views/routers/profiles.blade.php`

### 2. Voucher Views (High Priority)
- [ ] `/resources/views/vouchers/index.blade.php`
- [ ] `/resources/views/vouchers/show.blade.php`
- [ ] `/resources/views/vouchers/edit.blade.php`
- [ ] `/resources/views/vouchers/print.blade.php`
- [ ] `/resources/views/vouchers/redeem.blade.php`
- [ ] `/resources/views/vouchers/reports.blade.php`

### 3. Admin Views (High Priority)
- [ ] `/resources/views/admin/dashboard.blade.php`
- [ ] `/resources/views/admin/users.blade.php`
- [ ] `/resources/views/admin/users-show.blade.php`
- [ ] `/resources/views/admin/users-edit.blade.php`
- [ ] `/resources/views/admin/routers.blade.php`
- [ ] `/resources/views/admin/vouchers.blade.php`
- [ ] `/resources/views/admin/subscriptions.blade.php`
- [ ] `/resources/views/admin/settings.blade.php`

### 4. Other Views (Medium Priority)
- [ ] `/resources/views/subscription/upgrade.blade.php`
- [ ] `/resources/views/legal/terms.blade.php`
- [ ] `/resources/views/legal/privacy.blade.php`
- [ ] `/resources/views/landing.blade.php`
- [ ] `/resources/views/welcome.blade.php`

### 5. Auth Views (Already Done ✅)
- [x] `/resources/views/auth/login.blade.php`
- [x] `/resources/views/auth/register.blade.php`
- [ ] `/resources/views/auth/forgot-password.blade.php`
- [ ] `/resources/views/auth/reset-password.blade.php`
- [ ] `/resources/views/auth/verify-email.blade.php`

### 6. Controllers
- [ ] `/app/Http/Controllers/RouterController.php`
- [ ] `/app/Http/Controllers/AdminController.php`
- [ ] `/app/Http/Controllers/SubscriptionController.php`
- [x] `/app/Http/Controllers/VoucherController.php` ✅

## Common Translation Keys Reference

### Actions
```
create, edit, delete, save, cancel, submit, back, view, print, download, upload
```

### Status
```
active, inactive, pending, expired, used, available, success, error, warning
```

### Navigation
```
dashboard, routers, vouchers, users, settings, profile, reports
```

### Forms
```
name, email, password, search, filter, select, all, none
```

### Router
```
router_name, router_ip, local_ip, public_ip, api_user, api_password
add_router, edit_router, router_details, test_connection, manage_router
```

### Voucher
```
voucher_code, create_vouchers, quantity, duration, bandwidth
expires_at, expires_in_days, auth_type, code_length
```

### Time
```
minutes, hours, days, weeks, months, years
minute, hour, day, week, month, year
```

## Adding New Translation Keys

If you need a key that doesn't exist:

1. Add to `/lang/en/messages.php`:
```php
'new_key' => 'English Text',
```

2. Add to `/lang/ar/messages.php`:
```php
'new_key' => 'النص العربي',
```

3. Add to `/lang/es/messages.php`:
```php
'new_key' => 'Texto en Español',
```

4. Add to `/lang/fr/messages.php`:
```php
'new_key' => 'Texte en Français',
```

## Testing Each Page

After updating a page:

1. Visit the page in browser
2. Click language switcher
3. Test all 4 languages (en, ar, es, fr)
4. For Arabic, verify RTL layout
5. Check all buttons and labels
6. Test form submissions
7. Verify error/success messages

## Common Patterns

### Table Headers
```blade
<th>{{ __('messages.name') }}</th>
<th>{{ __('messages.status') }}</th>
<th>{{ __('messages.actions') }}</th>
```

### Buttons
```blade
<button>{{ __('messages.create') }}</button>
<button>{{ __('messages.save') }}</button>
<button>{{ __('messages.cancel') }}</button>
```

### Status Badges
```blade
<span class="badge">
    {{ __('messages.' . strtolower($status)) }}
</span>
```

### Empty States
```blade
<p>{{ __('messages.no_data') }}</p>
```

### Confirmation Dialogs
```javascript
Swal.fire({
    title: '{{ __("messages.confirm") }}',
    text: '{{ __("messages.delete_confirmation") }}',
    confirmButtonText: '{{ __("messages.yes") }}',
    cancelButtonText: '{{ __("messages.no") }}'
});
```

## Batch Update Script

For quick updates, you can use find/replace:

```bash
# Replace common patterns
find resources/views -name "*.blade.php" -type f -exec sed -i 's/Create/{{ __("messages.create") }}/g' {} \;
find resources/views -name "*.blade.php" -type f -exec sed -i 's/Edit/{{ __("messages.edit") }}/g' {} \;
find resources/views -name "*.blade.php" -type f -exec sed -i 's/Delete/{{ __("messages.delete") }}/g' {} \;
```

**Note**: Always review changes after batch operations!

## Validation

After completing all updates:

```bash
# Clear all caches
php artisan view:clear
php artisan cache:clear
php artisan config:clear

# Test the application
php artisan serve
```

## Checklist for Each File

- [ ] All hardcoded text replaced with `__('messages.key')`
- [ ] RTL-aware classes added where needed
- [ ] Language switcher present (if standalone page)
- [ ] Tested in all 4 languages
- [ ] RTL layout verified for Arabic
- [ ] No console errors
- [ ] Forms work correctly
- [ ] Messages display properly

## Need Help?

1. Check existing translated files for examples
2. Review `LOCALIZATION.md` for detailed guide
3. Check `LOCALIZATION_QUICK_REF.md` for quick reference
4. Look at translation files in `/lang/` directory

---

**Pro Tip**: Start with one file, test it thoroughly, then apply the same pattern to similar files. This ensures consistency and reduces errors.
