<?php

namespace App\Http\Controllers;

use App\DTOs\OrderData;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request): JsonResponse {
        // 1. Transformamos la Request en un Objeto de Datos (DTO)
        $orderData = OrderData::fromRequest($request);

        // 2. Buscamos el producto usando el DTO
        $product = Product::find($request->product_id);

        // 3. Lógica de negocio (Pronto la moveremos a un Service/Action)
        if ($product->stock < $orderData->quantity) {
            return response()->json([
                'error' => 'No hay suficiente stock para completar la orden'
            ], 422);
        }

        // 4. Persistencia
        $order = Order::create([
            'user_id' => $orderData->userId,
            'product_id' => $orderData->productId,
            'total_amount' => $product->price * $orderData->quantity,
            'status' => 'pending'
        ]);

        // 5. Actualización de stock (Efecto secundario)
        $product->decrement('stock', $orderData->quantity);

        return response()->json([
            'message' => 'Orden Creada con Éxito',
            'data' => $order
        ], 201);
    }
}
