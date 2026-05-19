<?php

namespace App\Repositories\Eloquent;

use App\DTOs\OrderData;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {}

    /**
     * Crea la orden.
     *
     * @param OrderData $orderData
     * @return Order
     */
    public function create(OrderData $orderData, float $totalAmount): Order
    {
        return Order::create([
            'user_id' => $orderData->userId,
            'product_id' => $orderData->productId,
            'total_amount' => $totalAmount,
            'status' => 'pending'
        ]);
    }
}
