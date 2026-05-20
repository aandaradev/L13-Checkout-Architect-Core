<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmation
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     * El método handle es el que Laravel ejecuta automáticamente.
     */
    public function handle(OrderCreated $event): void
    {
        // Accedemos a la orden que viene dentro del evento.
        $order = $event->order;

        // Simulamos la tarea secundaria escribiendo en los logs.
        Log::info("📨 [EDA] Correo de confirmación enviado para la Orden ID: {$order->id}");
        Log::info("🔹 Detalles: El usuario {$order->user_id} compró el producto ID {$order->product_id} por un monto de {$order->total_amount}");
    }
}
