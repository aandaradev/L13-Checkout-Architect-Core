<?php

namespace App\Repositories\Contracts;

use App\DTOs\OrderData;
use App\Models\Order;

interface OrderRepositoryInterface
{
    /**
     * Crea una nueva orden requiriendo los datos del DTO y el monto total calculado.
     *
     * @param OrderData $orderData
     * @param float $totalAmount
     * @return Order
     */
    public function create(OrderData $orderData, float $totalAmount): Order;
}
