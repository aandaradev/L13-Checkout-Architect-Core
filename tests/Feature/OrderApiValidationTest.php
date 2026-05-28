<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Creamos el test para el escenario 3: El Caso de Validación HTTP.
     * Verifica que la API devuelve un error 422 (Unprocessable Entity) cuando los datos de entrada son inválidos.
     *
     * @return void
     */
    public function test_it_return_422_unprocessable_entity_if_validation_fails_for_invalid_data(): void
    {
        // 1. PREPARACIÓN (Arrange)
        // Definimos un payload con datos que violan las reglas de negocio/validación:
        // - 'product_id' 999: Simulamos un ID que no existe en la base de datos.
        // - 'quantity' 0: Violamos la regla de que la cantidad debe ser al menos 1.
        $payload = [
            'product_id' => 999,
            'quantity' => 0
        ];

        // 2. EJECUCIÓN (Act)
        // Realizamos una petición POST JSON al endpoint de creación de órdenes.
        $response = $this->postJson('/api/orders', $payload);

        // 3. VERIFICACIÓN (Assert)
        // Verificamos que el servidor devuelva un código de estado 422 (Unprocessable Entity).
        $response->assertStatus(422);

        // Comprobamos que la respuesta contenga las claves de error para los campos que fallaron.
        $response->assertJsonValidationErrors(['product_id', 'quantity']);
    }
}
