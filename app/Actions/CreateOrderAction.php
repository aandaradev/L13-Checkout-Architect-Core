<?php

namespace App\Actions;

use App\DTOs\OrderData;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateOrderAction
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function execute(OrderData $orderData): Order {
        // DB::transaction protege la integridad de tus datos
        return DB::transaction(function () use ($orderData) {
            // lockForUpdate() bloquea la fila en la DB hasta que termine la transacción.
            // Evita que dos personas compren el último producto al mismo tiempo.
            // Buscamos el producto, con findOrFail para el manejo de errores.
            $product = Product::lockForUpdate()->findOrFail($orderData->productId);

            //Validamos si hay existencia del producto en el stock.
            if ($product->stock < $orderData->quantity) {
                throw new InsufficientStockException("No hay stock suficiente para: {$product->name}");
            }

            //Creamos la orden.
            $order = Order::create([
                'user_id' => $orderData->userId,
                'product_id' => $orderData->productId,
                'total_amount' => $product->price * $orderData->quantity,
                'status' => 'pending'
            ]);

            //Actualizamos el stock
            $product->decrement('stock', $orderData->quantity);

            return $order;
        });
    }
}
