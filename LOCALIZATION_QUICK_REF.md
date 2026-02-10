# Localization Quick Reference

## Common Translation Keys

### Navigation
```blade
{{ __('messages.dashboard') }}
{{ __('messages.routers') }}
{{ __('messages.vouchers') }}
{{ __('messages.admin_dashboard') }}
{{ __('messages.settings') }}
```

### Actions
```blade
{{ __('messages.create') }}
{{ __('messages.edit') }}
{{ __('messages.delete') }}
{{ __('messages.save') }}
{{ __('messages.cancel') }}
{{ __('messages.submit') }}
{{ __('messages.back') }}
```

### Status
```blade
{{ __('messages.active') }}
{{ __('messages.inactive') }}
{{ __('messages.pending') }}
{{ __('messages.expired') }}
{{ __('messages.success') }}
{{ __('messages.error') }}
```

### Forms
```blade
{{ __('messages.email') }}
{{ __('messages.password') }}
{{ __('messages.name') }}
{{ __('messages.search') }}
{{ __('messages.filter') }}
```

## RTL Conditional Classes

### Margins
```blade
class="{{ $isRtl ? 'mr-4' : 'ml-4' }}"
class="{{ $isRtl ? 'ml-4' : 'mr-4' }}"
```

### Text Alignment
```blade
class="{{ $isRtl ? 'text-right' : 'text-left' }}"
```

### Positioning
```blade
class="{{ $isRtl ? 'left-0' : 'right-0' }}"
class="{{ $isRtl ? 'right-0' : 'left-0' }}"
```

## Language Switcher Component

```blade
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
        </svg>
        <span class="text-sm">{{ strtoupper($currentLocale ?? 'en') }}</span>
    </button>
    <div x-show="open" @click.away="open = false" class="absolute {{ $isRtl ? 'left-0' : 'right-0' }} mt-2 w-40 bg-white rounded-lg shadow-lg py-2 z-50">
        <a href="?lang=en" class="block px-4 py-2 text-sm hover:bg-gray-100">English</a>
        <a href="?lang=ar" class="block px-4 py-2 text-sm hover:bg-gray-100">العربية</a>
        <a href="?lang=es" class="block px-4 py-2 text-sm hover:bg-gray-100">Español</a>
        <a href="?lang=fr" class="block px-4 py-2 text-sm hover:bg-gray-100">Français</a>
    </div>
</div>
```

## Controller Messages

```php
// Success
return back()->with('alert_success', __('messages.router_created'));

// Error
return back()->with('alert_error', __('messages.access_denied'));

// With parameters
return back()->with('alert_success', __('messages.vouchers_created_count', ['count' => $count]));
```

## Validation Messages

```php
'required' => __('messages.required'),
'email' => __('messages.email_invalid'),
'min' => __('messages.min', ['min' => 8]),
```

## HTML Direction

```blade
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
```

## Checking Locale

```php
// In PHP
if (app()->getLocale() === 'ar') {
    // Arabic specific code
}

// In Blade
@if(app()->getLocale() === 'ar')
    <!-- Arabic specific content -->
@endif
```

## Adding New Translation Key

1. Add to `/lang/en/messages.php`:
```php
'new_key' => 'English Text',
```

2. Add to `/lang/ar/messages.php`:
```php
'new_key' => 'النص العربي',
```

3. Add to other language files

4. Use in template:
```blade
{{ __('messages.new_key') }}
```

## Common Patterns

### Button with Icon
```blade
<button class="flex items-center gap-2">
    <svg>...</svg>
    <span>{{ __('messages.create') }}</span>
</button>
```

### Form Label
```blade
<label class="block text-sm font-medium">
    {{ __('messages.email') }}
</label>
```

### Status Badge
```blade
<span class="px-2 py-1 rounded">
    {{ __('messages.' . $status) }}
</span>
```

### Conditional Text
```blade
{{ $isRtl ? __('messages.rtl_text') : __('messages.ltr_text') }}
```
