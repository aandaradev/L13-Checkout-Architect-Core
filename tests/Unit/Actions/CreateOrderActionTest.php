<?php

namespace Tests\Unit\Actions;

use App\Actions\CreateOrderAction;
use App\DTOs\OrderData;
use App\Events\OrderCreated;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateOrderActionTest extends TestCase
{
    /**
     * Creamos el test para el escenario 1: El Caso Triste (Falta de Stock).
     * Verificamos si el producto no tiene stock suficiente, interrumpiendo el flujo y lanzando la excepción InsufficientStockException.
     *
     * @return void
     */
    public function test_it_throws_an_exception_if_product_has_insufficient_stock(): void
    {
        // 1. Aislamiento: Evitamos que se disparen listeners reales (correos, logs, etc.)
        Event::fake();

        // 2. PREPARACIÓN (Arrange)
        // Instanciamos el modelo de Producto con un stock limitado (3 unidades).
        // Asignamos el ID manualmente para simular un registro existente sin tocar la base de datos.
        $product = new Product(['name' => 'Laptop', 'price' => 600, 'stock' => 3]);
        $product->id = 1;

        // Definimos el DTO de entrada solicitando una cantidad superior al stock disponible (5 unidades).
        $orderData = new OrderData(productId: 1, quantity: 5);

        // Creamos los Mocks de las dependencias para controlar el comportamiento del flujo.
        $productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);

        // Definimos la expectativa: Se debe buscar el producto, pero NUNCA se debe crear una orden
        // ni decrementar el stock debido al fallo de validación previo.
        $productRepositoryMock->expects($this->once())
            ->method('findForUpdate')
            ->with($orderData->productId)
            ->willReturn($product);

        $orderRepositoryMock->expects($this->never())->method('create');
        $productRepositoryMock->expects($this->never())->method('decrementStock');

        // 3. CONFIGURACIÓN DE EXPECTATIVAS (Assert Expectations)
        // En PHPUnit, debemos declarar la excepción esperada ANTES de ejecutar el código que la lanza.
        $this->expectException(InsufficientStockException::class);
        $this->expectExceptionMessage("No hay stock suficiente para: Laptop");

        // 4. EJECUCIÓN (Act)
        $action = new CreateOrderAction($productRepositoryMock, $orderRepositoryMock);
        $action->execute($orderData);
    }

    /**
     * Creamos el test para el escenario 2: El Caso Feliz (Compra Exitosa).
     * Verificamos si el producto tiene stock suficiente, al encontrar disponibilidad en stock el Action realiza todo el flujo correcto.
     *
     * @return void
     */
    public function test_it_creates_an_order_successfully_when_stock_is_sufficient(): void
    {
        // 1. Evitamos que los eventos reales (como enviar correos) se ejecuten durante el test.
        Event::fake();

        // 2. PREPARACIÓN (Arrange)
        // Definimos el objeto de datos de entrada (DTO) para la prueba.
        $orderData = new OrderData(
            productId: 1,
            quantity: 2 // El cliente quiere comprar 2 unidades del producto.
        );

        // Creamos instancias de modelos para simular los datos existentes.
        // Nota: Asignamos el 'id' manualmente porque en un test unitario no interactuamos con la DB.
        $product = new Product(['name' => 'Computadora', 'price' => 700, 'stock' => 10]);
        $product->id = 1;

        $order = new Order(['total_amount' => 1400]);
        $order->id = 99;

        // Creamos los dobles de prueba (Mocks) para aislar la lógica del Action de la persistencia real.
        $productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);

        // Definimos que el repositorio debe buscar el producto con bloqueo (lockForUpdate).
        $productRepositoryMock->expects($this->once())
            ->method('findForUpdate')
            ->with($orderData->productId)
            ->willReturn($product);

        // Definimos que el repositorio debe crear la orden con el total calculado (700 * 2 = 1400).
        $orderRepositoryMock->expects($this->once())
            ->method('create')
            ->with($orderData, 1400.0)
            ->willReturn($order);

        // Definimos que se debe llamar al método para reducir el stock tras la compra.
        $productRepositoryMock->expects($this->once())
            ->method('decrementStock')
            ->with($product, $orderData->quantity);

        // 3. EJECUCIÓN (Act)
        // Instanciamos el Action inyectando los mocks y ejecutamos la lógica de negocio.
        $action = new CreateOrderAction($productRepositoryMock, $orderRepositoryMock);
        $result = $action->execute($orderData);

        // 4. VERIFICACIÓN (Assert)
        // Comprobamos que el resultado de la acción coincida con la orden esperada.
        $this->assertEquals(99, $result->id);
        $this->assertEquals(1400, $result->total_amount);

        // Verificamos que se haya disparado el evento OrderCreated con la orden correcta.
        Event::assertDispatched(OrderCreated::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });
    }
}
