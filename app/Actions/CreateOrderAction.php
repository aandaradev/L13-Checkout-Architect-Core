<?php

namespace App\Actions;

use App\DTOs\OrderData;
use App\Events\OrderCreated;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    /**
     * Create a new class instance.
     * Inyectamos los contratos de los repositorios en el constructor.
     * Laravel automáticamente resolverá las implementaciones de Eloquent.
     */
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected OrderRepositoryInterface $orderRepository
    ) {}

    public function execute(OrderData $orderData): Order {
        // DB::transaction protege la integridad de tus datos
        $orderCreated = DB::transaction(function () use ($orderData) {
            // Buscamos el producto delegando el bloqueo al repositorio
            $product = $this->productRepository->findForUpdate($orderData->productId);

            // Regla de negocio: Validación de stock
            if ($product->stock < $orderData->quantity) {
                throw new InsufficientStockException("No hay stock suficiente para: {$product->name}");
            }

            // Calculamos el monto total en la capa de negocio
            (float) $totalAmount = $product->price * $orderData->quantity;

            // Creamos la orden abstrayendo Eloquent
            $order = $this->orderRepository->create($orderData, $totalAmount);

            // Reducimos el stock usando el repositorio
            $this->productRepository->decrementStock($product, $orderData->quantity);

            return $order;
        });

        // Se dispara el evento aquí. 🚀
        OrderCreated::dispatch($orderCreated);

        // Se retorna la orden creada.
        return $orderCreated;
    }
}
