## Resumen rápido

Este repositorio es una aplicación Laravel (PHP 8.2, Laravel 12) con frontend manejado por Vite/Tailwind/Alpine. El objetivo de este documento es dar a agentes de IA instrucciones prácticas y dirigidas para ser productivos inmediatamente: dónde buscar lógica importante, cómo ejecutar flujos locales y patrones específicos del proyecto.

## Arquitectura y límites principales

- Backend: Laravel (carpeta `app/`). Controladores en `app/Http/Controllers/`, modelos en `app/` o `app/Models/` y proveedores en `app/Providers/`.
- Rutas: `routes/web.php` (rutas de la aplicación) y `routes/auth.php` (autenticación). Cuando añadas rutas públicas, regístralas en `routes/web.php`.
- Vistas: `resources/views/` (subcarpetas por responsabilidad: `admin/`, `encargado/`, `layouts/`, `components/`). Ejemplo: la plantilla de encargado está en `resources/views/layouts/encargado.blade.php`.
- Assets: `resources/js/` y `resources/css/`. El proyecto usa Vite (`vite.config.js`) y la integración `laravel-vite-plugin`.
- Base de datos: migraciones en `database/migrations/` y seeders en `database/seeders/` (por ejemplo `EncargadoUserSeeder.php`).
- Jobs / Colas: el proyecto usa colas; el flujo de desarrollo las ejecuta con `php artisan queue:listen` o el script `composer dev` que los arranca en paralelo.

## Flujo de desarrollo local (comandos específicos)

Usar `cmd.exe` en Windows (ejemplos):

1) instalar dependencias (si es necesario):
   - `composer install`
   - `npm install`

2) preparar entorno (si falta `.env`):
   - `copy .env.example .env`
   - `php artisan key:generate`

3) flujos útiles:
   - Desarrollo full (arranca servidor, cola y Vite en paralelo): `composer run-script dev` ó `composer dev` (este script ejecuta `php artisan serve`, `php artisan queue:listen`, `php artisan pail` y `npm run dev` en conjunto).
   - Servir sólo backend: `php artisan serve`.
   - Assets (Vite): `npm run dev` (watch) | `npm run build` (producción).
   - Tests: `composer test` (ejecuta `php artisan test`). También se puede usar `vendor\bin\phpunit` o `php artisan test` directamente.

Nota: el `composer.json` incluye los scripts `dev` y `test` ya configurados; preferirlos para replicar el entorno del equipo.

## Dependencias e integraciones externas notables

- Pagos: `mercadopago/dx-php` (buscar integraciones en `app/Http/Controllers/` o en servicios específicos).
- Exportación/Impresión: `maatwebsite/excel` y `barryvdh/laravel-dompdf` — busca controladores o comandos que generen informes.
- Herramientas dev: `laravel/pail` (usado en `composer dev` para logs/monitor), `laravel/vite-plugin`, `vite`, `tailwindcss`.

## Convenciones y patrones del proyecto (detallados)

- Vistas por rol/área: se organizan por carpetas (`admin/`, `encargado/`, `profile/`, `roles/`). Mantén esa separación cuando agregues nuevas vistas.
- Layouts y componentes Blade: `resources/views/layouts/` y `resources/views/components/`. Reutiliza componentes Blade para elementos UI comunes.
- Seeds y roles: hay un seeder `EncargadoUserSeeder.php` y una migración que añade `role` a `users` (mira `2025_10_16_add_role_to_users_table.php`) — el sistema parece usar un campo `role` simple en users en vez de paquetes de roles/permissions.
- Colas: los trabajos se ejecutan con `php artisan queue:listen` en desarrollo; no hay configuración compleja de workers publicada en el repo.

## Reglas prácticas para agentes (qué cambiar y cómo)

- Cambios de rutas: modificar `routes/web.php` y crear el controlador en `app/Http/Controllers/`. Añadir vistas en `resources/views/<area>/` y actualizar enlaces en los layouts.
- Cambios front-end: actualizar `resources/js/app.js` o `resources/css/app.css` y ejecutar `npm run dev` para ver cambios inmediatos.
- Migraciones/DB: añadir migraciones en `database/migrations/` y seeders en `database/seeders/`. Para pruebas rápidas usar `php artisan migrate --seed` (advertir si el entorno de CI no permite esto).

## Comprobaciones rápidas tras cambios (antes de PR)

1) Ejecutar tests: `composer test` (o `php artisan test`).
2) Compilar assets opcionalmente: `npm run build` para validar errores de Vite/Tailwind.
3) Revisar que no haya cambios de tipo de contrato en controladores públicos (si cambias endpoints, documenta en la PR el nuevo shape JSON).

## Archivos clave que revisar/editar (ejemplos)

- `routes/web.php` — rutas principales
- `app/Http/Controllers/` — lógica de controladores
- `resources/views/layouts/encargado.blade.php` — ejemplo de layout por rol
- `database/seeders/EncargadoUserSeeder.php` — ejemplo de seeder/role
- `vite.config.js`, `package.json` — configuración de frontend
- `composer.json` — scripts útiles: `dev`, `test`

## Preguntas que el agente debe plantear si procede

- ¿Deseas que los cambios incluyan migraciones en producción? (si no, indicar que sean reversibles)
- ¿Debo añadir tests automáticos (Feature/Unit) para la nueva funcionalidad?

---
Si quieres, actualizo o amplío secciones concretas (por ejemplo: estructura de APIs, ejemplos de controladores/requests, o pasos para CI). ¿Qué te parece que mejoremos primero?
