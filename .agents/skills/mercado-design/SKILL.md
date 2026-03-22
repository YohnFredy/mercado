---
name: mercado-design
description: "Activar este skill siempre que se diseñe, cree o edite una vista Blade o componente UI (frontend). Asegura el uso de la paleta de colores oficial de la aplicación y prohíbe el uso de modo oscuro."
---

# Mercado Design System

Always use this skill when creating or modifying any UI components, layouts, or Blade views for this application. You must strictly follow these design rules:

## 1. Official Color Palette
Use only the following custom colors defined in `resources/css/app.css`. Do not use generic colors when a brand color fits:

**Brand Colors:**
- **Primary:** `#00A63D` (Use classes like `bg-primary`, `text-primary`, `border-primary`)
- **Secondary:** `#A68700` (Use classes like `bg-secondary`, `text-secondary`, `border-secondary`)
- **Acento:** `#A60700` (Use classes like `bg-acento`, `text-acento`, `border-acento`)
- **Fondo:** `#FFFFFF` (Use classes like `bg-fondo`, `text-fondo`)

**Grayscale / Text Colors:**
- `gray-50`: `#fafafa`
- `gray-100`: `#f5f5f5`
- `gray-200`: `#e5e5e5`
- `gray-300`: `#d4d4d4`
- `gray-400`: `#a3a3a3`
- `gray-500`: `#737373`
- `gray-600`: `#525252`
- `gray-700`: `#404040`
- `gray-800`: `#262626`
- `gray-900`: `#171717`
- `gray-950`: `#0a0a0a`

Use classes like `text-gray-900`, `bg-gray-100`, `border-gray-200` accordingly.

## 2. Public Storefront Rules (Outside Admin)
**CRITICAL:** Do NOT design for dark mode on any public-facing pages.
- Never use Tailwind's `dark:` variant on public pages.
- Ensure that the layout and components look perfect strictly in standard (light) mode.
- Use the official Brand Colors (Primary, Secondary, Acento, Fondo) freely to create vibrant designs.

## 3. Admin Panel Rules (Inside /admin)
**CRITICAL:** The Administrator Dashboard MUST use dark mode.
- ALWAYS optimize the Admin Panel (`/admin`) to function natively in dark mode.
- Allow the user's Appearance toggle (Light/Dark/System) to control the Admin layout freely.
- Ensure all admin UI elements adapt beautifully to Tailwind's dark mode grayscale variables (`dark:bg-zinc-800`, `dark:text-white`).
```html
<div class="bg-secondary text-fondo p-4">
    <!-- Contenido -->
</div>
```
