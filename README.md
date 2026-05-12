# L13 Checkout Architect Core 🚀

Este proyecto es una demostración de evolución arquitectónica en Laravel 13. Partimos de un sistema monolítico tradicional (Legacy) y lo transformamos paso a paso hacia una **Arquitectura Limpia (Clean Architecture)**.

## 🏗️ Fases del Proyecto

1. **Legacy Monolith (Actual):** Código con lógica mezclada en controladores, validaciones manuales y alta deuda técnica.
2. **Refactorización DTO  (En progreso):** Implementación de Data Transfer Objects e Inmutabilidad.
3. **Domain Driven Design:** Separación de lógica de negocio en Actions y Services.

## 🛠️ Tecnologías

- **Framework:** Laravel 13.x
- **Lenguaje:** PHP 8.4+ (Strict Types)
- **Base de Datos:** SQLite / MySQL
- **Flujo de Git:** GitFlow (Ramas: `stable`, `staging`, `feature/`)

## 🚀 Instalación local

1. Clonar el repositorio.
2. Ejecutar `composer install`.
3. Configurar el `.env` y ejecutar `php artisan migrate`.
4. (Opcional) Usar Tinker para crear datos de prueba: `App\Models\Product::create(...)`.

## 🧪 Pruebas de API

Para probar el endpoint de órdenes en la fase legacy:

```bash
curl -X POST http://127.0.0.1:8000/api/orders \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"product_id": 1, "quantity": 2}'
```
