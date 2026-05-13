# L13 Checkout Architect Core 🚀

Este proyecto es una demostración de evolución arquitectónica en Laravel 13. Partimos de un sistema monolítico tradicional (Legacy) y lo transformamos paso a paso hacia una **Arquitectura Limpia (Clean Architecture)**.

## 🏗️ Fases del Proyecto

- [x] **Fase 1: Monolito Legacy** (Controlador con toda la lógica).
- [x] **Fase 2: Arquitectura de Capas**
  - [x] Implementación de **Form Requests** para validación.
  - [x] Introducción de **DTOs** para el transporte de datos inmutables.
  - [x] Creación de **Actions** para desacoplar la lógica de negocio.
- [x] **Fase 3: Blindaje de Dominio y Consistencia**
  - [x] Manejo de excepciones personalizadas (`InsufficientStockException`).
  - [x] Implementación de **Transacciones de Base de Datos** (Atomicidad).
  - [x] Uso de **Locks** para evitar condiciones de carrera (Race Conditions).
- [ ] **Fase 4: Desacoplamiento de Persistencia**
  - [ ] Implementación del **Patrón Repositorio**.
- [ ] **Fase 5: Eventos y Efectos Secundarios**
  - [ ] Sistema de notificaciones (Email/Slack) mediante **Eventos y Listeners**.

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

## 🧪 Pruebas de API (Arquitectura Refactorizada)

La API ahora utiliza **Actions**, **DTOs** y **Excepciones de Dominio**.

### 1. Crear una orden con éxito (201 Created)

```bash
curl -X POST http://127.0.0.1:8000/api/orders \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"product_id": 1, "quantity": 2}'
```

### 2. Error por falta de stock (422 Unprocessable Entity)

Este error es gestionado por la excepción personalizada InsufficientStockException.

```bash
curl -X POST http://127.0.0.1:8000/api/orders \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"product_id": 1, "quantity": 9999}'
```

### 3. Error de validación (422 Unprocessable Entity)

Gestionado automáticamente por `StoreOrderRequest`.

```bash
curl -X POST http://127.0.0.1:8000/api/orders \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"product_id": 999, "quantity": 0}'
```
