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
- [x] **Fase 4: Desacoplamiento de Persistencia**
  - [x] Implementación del **Patrón Repositorio** (Principio de Inversión de Dependencias).
  - [x] Creación de contratos (`ProductRepositoryInterface` y `OrderRepositoryInterface`) para aislar el dominio.
  - [x] Desarrollo de implementaciones concretas con Eloquent (`EloquentProductRepository`, `EloquentOrderRepository`).
  - [x] Uso de `RepositoryServiceProvider` para la inyección automática de dependencias en el contenedor IoC.
  - [x] Refactorización completa de `CreateOrderAction` para operar únicamente a través de abstracciones, eliminando acoplamiento directo con el ORM.
- [x] **Fase 5: Eventos y Efectos Secundarios (EDA)**
  - [x] Diseño e implementación de la Arquitectura Orientada a Eventos para el desacoplamiento de procesos secundarios.
  - [x] Creación del evento de dominio `OrderCreated` encargado de transportar el estado de la orden persistida.
  - [x] Creación del Listener `SendOrderConfirmation` para gestionar de forma aislada la lógica de notificaciones mediante el sistema nativo de *Event Discovery* de Laravel 13.
  - [x] Refactorización de `CreateOrderAction` para disparar el evento quirúrgicamente tras la transacción, respetando el Principio de Responsabilidad Única (SRP).
- [x] **Fase 6: Pruebas Automatizadas (Testing Suite)**
  - [x] Diseño e implementación de pruebas unitarias puras y aisladas con PHPUnit para blindar la lógica de negocio sin depender de la base de datos física.
  - [x] Simulación quirúrgica de contratos y dependencias utilizando los mecanismos nativos de *Mocking* de PHPUnit (`createMock`).
  - [x] Cobertura total de los flujos del dominio:
    - **Caso 1 (Éxito):** Verificación de creación de órdenes con montos correctos y disparo del evento de dominio `OrderCreated`.
    - **Caso 2 (Excepción):** Interrupción controlada del flujo mediante el lanzamiento y aserción de la excepción `InsufficientStockException`.
  - [x] Implementación de pruebas de integración HTTP (*Feature Tests*) para la validación perimetral de peticiones:
    - **Caso 3 (Validación API):** Verificación del rechazo automático de payloads corruptos por parte de `StoreOrderRequest` con código de estado HTTP `422 Unprocessable Entity`.

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

### 🧪 Ejecución de la Suite de Pruebas Automatizadas (PHPUnit)

Si en lugar de pruebas manuales con `curl` deseas ejecutar toda la batería de pruebas unitarias y de integración que blindan estos 3 casos, corre el siguiente comando en tu terminal:

```bash
# Ejecutar toda la suite (Unit + Feature)
php artisan test

# Filtrar por pruebas del núcleo de negocio (Casos 1 y 2)
php artisan test --filter=CreateOrderActionTest

# Filtrar por pruebas de validación HTTP (Caso 3)
php artisan test --filter=OrderApiValidationTest
```
