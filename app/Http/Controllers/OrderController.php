<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * MÉTODO LEGACY: Demostración de Deuda Técnica.
     * Este método tiene demasiadas responsabilidades:
     * Validación, Lógica de Negocio y Persistencia.
     */
    public function store(Request $request) {
        // 1. Validación manual (🚩 Debería ser un FormRequest)
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Lógica de negocio mezclada (🚩 Debería estar en una Action o Service)
        $product = Product::find($request->product_id);
        if ($product->stock < $request->quantity) {
            return response()->json(['error' => 'No hay suficiente stock'], 422);
        }

        // 3. Cálculos financieros frágiles (🚩 Debería ser un Value Object)
        $total = $product->price * $request->quantity;

        // 4. Persistencia directa y falta de transacciones atómicas
        $order = Order::create([
            'user_id' => 1, // Hardcoded para el ejemplo
            'product_id' => $product->id,
            'total_amount' => $total,
            'status' => 'pending'
        ]);

        // 5. Mutación de stock sin control de concurrencia
        $product->decrement('stock', $request->quantity);
        return response()->json([
            'message' => 'Orden Creada',
            'data' => $order
        ], 201);
    }
}
