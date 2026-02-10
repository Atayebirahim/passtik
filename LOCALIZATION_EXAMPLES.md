# 🎨 Localization Visual Examples - Before & After

## Example 1: Simple Button

### ❌ BEFORE
```blade
<button class="btn">Create Router</button>
```

### ✅ AFTER
```blade
<button class="btn">{{ __('messages.add_router') }}</button>
```

### 🌍 RESULT
- English: "Add Router"
- Arabic: "إضافة موجه"
- Spanish: "Agregar Enrutador"
- French: "Ajouter un Routeur"

---

## Example 2: Form Label

### ❌ BEFORE
```blade
<label for="email">Email Address</label>
<input type="email" id="email" name="email">
```

### ✅ AFTER
```blade
<label for="email">{{ __('messages.email') }}</label>
<input type="email" id="email" name="email">
```

### 🌍 RESULT
- English: "Email"
- Arabic: "البريد الإلكتروني"
- Spanish: "Correo Electrónico"
- French: "Email"

---

## Example 3: Status Badge

### ❌ BEFORE
```blade
<span class="badge">Active</span>
```

### ✅ AFTER
```blade
<span class="badge">{{ __('messages.active') }}</span>
```

### 🌍 RESULT
- English: "Active"
- Arabic: "نشط"
- Spanish: "Activo"
- French: "Actif"

---

## Example 4: Page Title

### ❌ BEFORE
```blade
@section('title', 'Create Vouchers - Passtik')
@section('page-title', 'Create Vouchers')
```

### ✅ AFTER
```blade
@section('title', __('messages.create_vouchers') . ' - ' . __('messages.app_name'))
@section('page-title', __('messages.create_vouchers'))
```

### 🌍 RESULT
- English: "Create Vouchers - Passtik"
- Arabic: "إنشاء قسائم - باستيك"
- Spanish: "Crear Cupones - Passtik"
- French: "Créer des Bons - Passtik"

---

## Example 5: Controller Message

### ❌ BEFORE
```php
return back()->with('alert_success', 'Router created successfully!');
```

### ✅ AFTER
```php
return back()->with('alert_success', __('messages.router_created'));
```

### 🌍 RESULT
- English: "Router created successfully"
- Arabic: "تم إنشاء الموجه بنجاح"
- Spanish: "Enrutador creado exitosamente"
- French: "Routeur créé avec succès"

---

## Example 6: Message with Variable

### ❌ BEFORE
```php
return back()->with('alert_success', $count . ' vouchers created successfully!');
```

### ✅ AFTER
```php
return back()->with('alert_success', __('messages.vouchers_created_count', ['count' => $count]));
```

### 🌍 RESULT (with count = 5)
- English: "5 vouchers created successfully"
- Arabic: "تم إنشاء 5 قسيمة بنجاح"
- Spanish: "5 cupones creados exitosamente"
- French: "5 bons créés avec succès"

---

## Example 7: RTL-Aware Margin

### ❌ BEFORE
```blade
<div class="ml-4">Content</div>
```

### ✅ AFTER
```blade
<div class="{{ $isRtl ? 'mr-4' : 'ml-4' }}">Content</div>
```

### 🌍 RESULT
- English/Spanish/French: `margin-left: 1rem`
- Arabic: `margin-right: 1rem` (mirrored for RTL)

---

## Example 8: Navigation Menu

### ❌ BEFORE
```blade
<a href="{{ route('routers.index') }}">
    <svg>...</svg>
    <span>Routers</span>
</a>
<a href="{{ route('vouchers.index') }}">
    <svg>...</svg>
    <span>Vouchers</span>
</a>
```

### ✅ AFTER
```blade
<a href="{{ route('routers.index') }}">
    <svg>...</svg>
    <span>{{ __('messages.routers') }}</span>
</a>
<a href="{{ route('vouchers.index') }}">
    <svg>...</svg>
    <span>{{ __('messages.vouchers') }}</span>
</a>
```

### 🌍 RESULT
- English: "Routers" | "Vouchers"
- Arabic: "الموجهات" | "القسائم"
- Spanish: "Enrutadores" | "Cupones"
- French: "Routeurs" | "Bons"

---

## Example 9: Table Headers

### ❌ BEFORE
```blade
<thead>
    <tr>
        <th>Name</th>
        <th>Status</th>
        <th>Created</th>
        <th>Actions</th>
    </tr>
</thead>
```

### ✅ AFTER
```blade
<thead>
    <tr>
        <th>{{ __('messages.name') }}</th>
        <th>{{ __('messages.status') }}</th>
        <th>{{ __('messages.date') }}</th>
        <th>{{ __('messages.actions') }}</th>
    </tr>
</thead>
```

### 🌍 RESULT
- English: "Name | Status | Date | Actions"
- Arabic: "الاسم | الحالة | التاريخ | الإجراءات"
- Spanish: "Nombre | Estado | Fecha | Acciones"
- French: "Nom | Statut | Date | Actions"

---

## Example 10: Empty State

### ❌ BEFORE
```blade
<div class="empty-state">
    <p>No routers found</p>
    <button>Create Router</button>
</div>
```

### ✅ AFTER
```blade
<div class="empty-state">
    <p>{{ __('messages.no_data') }}</p>
    <button>{{ __('messages.add_router') }}</button>
</div>
```

### 🌍 RESULT
- English: "No data available" | "Add Router"
- Arabic: "لا توجد بيانات" | "إضافة موجه"
- Spanish: "No hay datos" | "Agregar Enrutador"
- French: "Aucune donnée" | "Ajouter un Routeur"

---

## Example 11: Dropdown Options

### ❌ BEFORE
```blade
<select name="duration">
    <option value="30">30 minutes</option>
    <option value="60">1 hour</option>
    <option value="1440">1 day</option>
</select>
```

### ✅ AFTER
```blade
<select name="duration">
    <option value="30">30 {{ __('messages.minutes') }}</option>
    <option value="60">1 {{ __('messages.hour') }}</option>
    <option value="1440">1 {{ __('messages.day') }}</option>
</select>
```

### 🌍 RESULT
- English: "30 Minutes | 1 Hour | 1 Day"
- Arabic: "30 دقائق | 1 ساعة | 1 يوم"
- Spanish: "30 Minutos | 1 Hora | 1 Día"
- French: "30 Minutes | 1 Heure | 1 Jour"

---

## Example 12: HTML Direction

### ❌ BEFORE
```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Passtik</title>
</head>
```

### ✅ AFTER
```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <title>{{ __('messages.app_name') }}</title>
</head>
```

### 🌍 RESULT
- English: `<html lang="en" dir="ltr">`
- Arabic: `<html lang="ar" dir="rtl">`
- Spanish: `<html lang="es" dir="ltr">`
- French: `<html lang="fr" dir="ltr">`

---

## Example 13: Language Switcher

### ❌ BEFORE
```blade
<!-- No language switcher -->
```

### ✅ AFTER
```blade
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open">
        <svg>...</svg>
        <span>{{ strtoupper($currentLocale ?? 'en') }}</span>
    </button>
    <div x-show="open" @click.away="open = false">
        <a href="?lang=en">English</a>
        <a href="?lang=ar">العربية</a>
        <a href="?lang=es">Español</a>
        <a href="?lang=fr">Français</a>
    </div>
</div>
```

### 🌍 RESULT
Beautiful dropdown showing:
- Current language (EN/AR/ES/FR)
- All available languages
- Persists selection across pages

---

## Example 14: Conditional Text Alignment

### ❌ BEFORE
```blade
<div class="text-left">
    <h1>Welcome</h1>
</div>
```

### ✅ AFTER
```blade
<div class="{{ $isRtl ? 'text-right' : 'text-left' }}">
    <h1>{{ __('messages.welcome') }}</h1>
</div>
```

### 🌍 RESULT
- English/Spanish/French: Left-aligned "Welcome"
- Arabic: Right-aligned "مرحباً"

---

## Example 15: Error Message

### ❌ BEFORE
```php
if (!$router) {
    return back()->with('alert_error', 'Router not found or access denied.');
}
```

### ✅ AFTER
```php
if (!$router) {
    return back()->with('alert_error', __('messages.access_denied'));
}
```

### 🌍 RESULT
- English: "Access denied"
- Arabic: "تم رفض الوصول"
- Spanish: "Acceso denegado"
- French: "Accès refusé"

---

## 🎯 KEY TAKEAWAYS

1. **Always use `__('messages.key')`** instead of hardcoded text
2. **Add RTL-aware classes** for margins, padding, and positioning
3. **Test in all languages** after making changes
4. **Check Arabic RTL layout** to ensure proper mirroring
5. **Use existing files as templates** for consistency

---

## 📊 VISUAL COMPARISON

### English (LTR)
```
┌─────────────────────────────────┐
│ [☰] Dashboard          [EN ▼]  │
├─────────────────────────────────┤
│ ← Sidebar    Content →          │
│   Routers    Create Router      │
│   Vouchers   [Save] [Cancel]    │
└─────────────────────────────────┘
```

### Arabic (RTL)
```
┌─────────────────────────────────┐
│  [▼ AR]          لوحة التحكم [☰] │
├─────────────────────────────────┤
│          ← المحتوى    الشريط ←   │
│      إضافة موجه    الموجهات     │
│    [إلغاء] [حفظ]    القسائم     │
└─────────────────────────────────┘
```

Notice how everything is mirrored in RTL!

---

## 🚀 READY TO APPLY?

Use these examples as templates when updating remaining pages. The pattern is consistent and easy to follow!

**Next Step**: Open `APPLY_LOCALIZATION_GUIDE.md` for step-by-step instructions.
