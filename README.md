# 🍰 Sistema de Gestión para Pastelería

Sistema integral de gestión para pastelería desarrollado con **Laravel 12** y **Filament v3**. Incluye gestión de inventarios, producción, pedidos, clientes y auditoría completa.

<p align="center">
<img src="https://img.shields.io/badge/Laravel-12.38.1-red" alt="Laravel 12.38.1">
<img src="https://img.shields.io/badge/PHP-8.2.12-blue" alt="PHP 8.2.12">
<img src="https://img.shields.io/badge/Filament-v3-orange" alt="Filament v3">
<img src="https://img.shields.io/badge/PostgreSQL-16-blue" alt="PostgreSQL">
<img src="https://img.shields.io/badge/Estado-En%20Desarrollo-yellow" alt="En Desarrollo">
</p>

---

## 📋 Casos de Uso Implementados

### 🎂 Producción y Recetas (UC-25 a UC-33)
| UC | Nombre | Estado |
|----|--------|--------|
| UC-25 | Registrar Receta | ✅ Completo |
| UC-26 | Modificar Receta | ✅ Completo |
| UC-27 | Anular Receta | ✅ Completo |
| UC-28 | Buscar Receta | ✅ Completo |
| UC-29 | Registrar Orden de Producción | ✅ Completo |
| UC-30 | Modificar Orden de Producción | ✅ Completo |
| UC-31 | Anular Orden de Producción | ✅ Completo |
| UC-32 | Consultar Calendario de Producción | ✅ Completo |
| UC-33 | Finalizar Orden de Producción | ⏳ Pendiente |

**Documentación**: [UC_25_33_IMPLEMENTACION.md](docs/UC_25_33_IMPLEMENTACION.md)

### 📦 Gestión de Stock (UC-34 a UC-37)
| UC | Nombre | Estado |
|----|--------|--------|
| UC-34 | Buscar Insumo | ✅ Completo |
| UC-35 | Registrar Movimiento de Stock | ✅ Completo |
| UC-36 | Consultar Reporte Stock Crítico | ✅ Completo |
| UC-37 | Consultar Historial de Movimientos | ✅ Completo |

**Características**:
- Sistema de conversión de unidades automático
- Alertas de stock crítico configurables
- Exportación de reportes (Excel/PDF)
- Auditoría completa de movimientos

**Documentación**: Ver commits recientes

### 👥 Gestión de Clientes (UC-38 a UC-42)
| UC | Nombre | Estado |
|----|--------|--------|
| UC-38 | Buscar y Listar Clientes | ✅ Completo |
| UC-39 | Registrar Cliente | ✅ Completo |
| UC-40 | Modificar Cliente con Auditoría | ✅ Completo |
| UC-41 | Anular/Activar Cliente | ✅ Completo |
| UC-42 | Consultar Historial de Pedidos | ✅ Completo |

**Características**:
- Búsqueda multi-campo (nombre, email, teléfono)
- Auditoría obligatoria con justificación
- Validaciones de estado (no anular con pedidos pendientes)
- Estadísticas de fidelización (total gastado, producto favorito)
- Historial completo de pedidos con filtros avanzados

**Documentación**: [UC_38_42_IMPLEMENTACION.md](docs/UC_38_42_IMPLEMENTACION.md)

### 🏭 Gestión de Proveedores (UC-44 a UC-49)
| UC | Nombre | Estado |
|----|--------|--------|
| UC-44 | Buscar Proveedor | ✅ Completo |
| UC-45 | Registrar Proveedor | ✅ Completo |
| UC-46 | Modificar Proveedor con Auditoría | ✅ Completo |
| UC-47 | Anular/Activar Proveedor | ✅ Completo |
| UC-48 | Consultar Historial de Compras | ✅ Completo |
| UC-49 | Emitir Reporte de Desempeño | ⏳ Básico (70%) |

**Características**:
- Búsqueda multi-campo (razón social, CUIT, contacto, email)
- Auditoría obligatoria con justificación
- Validaciones de estado (no anular con OC pendientes)
- Gestión de condiciones comerciales
- Historial completo de órdenes de compra con filtros avanzados
- Estadísticas de compras (total gastado, OC pendientes, última compra)
- Relación many-to-many con insumos (precios, unidades, tiempos de entrega)

**Documentación**: [UC_44_49_IMPLEMENTACION.md](docs/UC_44_49_IMPLEMENTACION.md)

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
