---
name: spatie-permission-development
description: Activate whenever developing features that require authorization, defining capabilities, or working with Spatie Laravel Permission. Ensures proper use of permissions over roles in business logic.
---

# Spatie Laravel Permission Guidelines

This application uses `spatie/laravel-permission` (v7+) to manage user roles and capabilities. You must follow these guidelines whenever implementing authorization logic.

## 1. Always Check Permissions, Not Roles
Never write business logic that checks for a specific role (e.g., `if ($user->hasRole('editor'))` or `@role('editor')`). 
Always check for a specific **permission** (e.g., `if ($user->can('articles:edit'))` or `@can('articles:edit')`).

Roles are strictly used to group permissions together to make assignments easier. Business logic must be completely decoupled from the concept of roles.

## 2. Permission Naming Convention
Permissions must follow the `module:action` syntax for consistency and readability:
- `users:manage`
- `orders:view`
- `products:create`
- `settings:manage`

## 3. Super Admin Concept
The system implements a global Gateway (`Gate::before`) in `AppServiceProvider`. Any user with the `admin` role automatically passes all permission checks. There is no need to manually assign individual permissions to the `admin` role.

## 4. Route Protection
Use the package's middleware to protect routes in `web.php` or `api.php`. Prefer using the `permission` middleware over the `role` middleware:
```php
Route::middleware(['permission:orders:view'])->group(function () {
    // ...
});
```

## 5. UI/Frontend Checks
When working on Blade views (including Livewire/Volt), use the `@can` and `@cannot` directives provided by Laravel, which integrate natively with the Spatie package:
```html
@can('products:delete')
    <flux:button wire:click="delete">Eliminar</flux:button>
@endcan
```

## 6. Modifying Roles/Permissions via Code
Always use the provided Eloquent models (`Spatie\Permission\Models\Role` and `Spatie\Permission\Models\Permission`) to create or assign abilities. Avoid raw DB inserts. Ensure you run `app()[PermissionRegistrar::class]->forgetCachedPermissions()` if you suspect cache issues during dynamic creation.
