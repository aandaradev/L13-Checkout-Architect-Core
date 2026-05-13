<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrderAction;
use App\DTOs\OrderData;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * Store a newly created order.
     * Inyectamos el Action directamente en el método.
     */
    public function store(StoreOrderRequest $request, CreateOrderAction $action): JsonResponse {
        // 1. Transformamos la Request en un Objeto de Datos (DTO)
        $orderData = OrderData::fromRequest($request);

        // 2. Ejecutamos la lógica de negocio a través del Action
        $order = $action->execute($orderData);

        // 3. Respuesta estándar
        return response()->json([
            'message' => 'Orden Creada con Éxito',
            'data' => $order
        ], 201);
    }
}
