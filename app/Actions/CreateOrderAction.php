<?php

namespace App\Actions;

use App\DTOs\OrderData;
use App\Models\Order;
use App\Models\Product;
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
        //Buscamos el producto, con findOrFail para el manejo de errores.
        $product = Product::findOrFail($orderData->productId);

        //Validamos si hay existencia del producto en el stock.
        if ($product->stock < $orderData->quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'No hay suficiente stock para completar la orden.'
            ]);
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
    }
}
