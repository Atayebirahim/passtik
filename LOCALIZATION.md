# Localization and RTL Support Implementation

## Overview
Complete localization and RTL (Right-to-Left) support has been added to the Passtik application.

## Supported Languages
- **English (en)** - Default, LTR
- **Arabic (ar)** - RTL
- **Spanish (es)** - LTR
- **French (fr)** - LTR

## Features Implemented

### 1. Middleware
- `SetLocale` middleware automatically detects and sets the locale
- Stores locale preference in session
- Shares RTL status with all views
- Supports URL parameter `?lang=xx` for language switching

### 2. Translation Files
Location: `/lang/{locale}/messages.php`

All UI strings are translated including:
- Navigation menus
- Form labels
- Buttons and actions
- Status messages
- Validation messages
- Error messages

### 3. RTL Support
- Automatic direction detection for RTL languages (ar, he, fa, ur)
- CSS adjustments for RTL layouts
- Proper text alignment
- Mirrored margins and paddings
- Language switcher position adjustment

### 4. Language Switcher
- Available on all pages
- Dropdown menu with flag/language name
- Persists selection across pages
- Visual indicator for current language

## Usage

### In Blade Templates
```blade
{{ __('messages.key') }}
```

### Adding New Translations
1. Add key to `/lang/en/messages.php`
2. Add translations to other language files
3. Use in templates with `__('messages.key')`

### Checking Current Locale
```php
app()->getLocale()
```

### Checking if RTL
```blade
{{ $isRtl ? 'rtl-class' : 'ltr-class' }}
```

## Files Modified

### Core Files
- `/app/Http/Middleware/SetLocale.php` - New middleware
- `/bootstrap/app.php` - Middleware registration
- `/config/app.php` - Locale configuration

### Layout Files
- `/resources/views/layouts/app.blade.php` - Main layout with RTL
- `/resources/views/layouts/admin.blade.php` - Admin layout with RTL

### Auth Files
- `/resources/views/auth/login.blade.php` - Login page
- `/resources/views/auth/register.blade.php` - Register page

### Translation Files
- `/lang/en/messages.php` - English translations
- `/lang/ar/messages.php` - Arabic translations
- `/lang/es/messages.php` - Spanish translations
- `/lang/fr/messages.php` - French translations

## CSS Classes for RTL

### Automatic RTL Adjustments
```css
[dir="rtl"] { text-align: right; }
[dir="rtl"] .lg\:ml-64 { margin-left: 0; margin-right: 16rem; }
[dir="rtl"] .transform.-translate-x-full { transform: translateX(100%); }
[dir="rtl"] .lg\:translate-x-0 { transform: translateX(0); }
[dir="rtl"] .left-0 { left: auto; right: 0; }
```

### Conditional Classes in Blade
```blade
class="{{ $isRtl ? 'mr-2' : 'ml-2' }}"
```

## Adding More Languages

1. Create new language directory:
```bash
mkdir -p lang/de
```

2. Copy and translate messages file:
```bash
cp lang/en/messages.php lang/de/messages.php
```

3. Add to language switcher in layouts:
```blade
<a href="?lang=de">Deutsch</a>
```

4. If RTL language, add to RTL array in SetLocale middleware:
```php
protected $rtlLocales = ['ar', 'he', 'fa', 'ur', 'new_rtl_lang'];
```

## Environment Configuration

Add to `.env`:
```env
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

## Testing

1. Visit any page and click language switcher
2. Select different language
3. Verify all text is translated
4. For Arabic, verify RTL layout
5. Check that selection persists across pages

## Best Practices

1. Always use translation keys, never hardcode text
2. Keep translation keys descriptive
3. Group related translations
4. Test all languages before deployment
5. Ensure RTL layouts don't break UI
6. Use conditional classes for directional spacing

## Troubleshooting

### Translations not showing
- Clear view cache: `php artisan view:clear`
- Check translation key exists in all language files
- Verify middleware is registered

### RTL layout issues
- Check CSS RTL overrides
- Verify `dir` attribute on html tag
- Test with browser RTL tools

### Language not persisting
- Check session configuration
- Verify middleware is in web group
- Clear session: `php artisan session:flush`
